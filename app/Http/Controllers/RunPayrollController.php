<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\MasterPayroll;
use App\Models\Presence;
use App\Models\PayrollHistory;
use App\Models\PayrollHistoryDetail;
use App\Models\AttendanceDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RunPayrollController extends Controller
{
    public function index()
    {
        return view('pages.run_payroll.index');
    }

    public function employee()
    {
        return view('pages.run_payroll.employee');
    }

    public function storeselectkar(Request $request)
    {
        $validatedData = $request->validate([
            'employees' => 'required|array',
            'employees.*.id' => 'required|exists:employees,id',
            'employees.*.nama_lengkap' => 'required|string',
            'employees.*.nomor_induk_karyawan' => 'required|string',
        ]);

        $employeeIds = collect($validatedData['employees'])->pluck('id')->toArray();

        session(['selected_employee_ids' => $employeeIds]);

        return response()->json([
            'success' => true,
            'message' => 'ID karyawan berhasil disimpan ke dalam session.',
            'data' => session('selected_employee_ids'),
        ]);
    }

    public function getSelectedEmployees()
    {
        $selectedEmployeeIds = session('selected_employee_ids', []);
        $employees = Employee::whereIn('id', $selectedEmployeeIds)->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

    ##RUN PAYROLL PROCESS##
    public function store(Request $request)
    {
        //Log::info("Request: " . json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'employee_ids' => 'required|array|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            $description = $request->input('description');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $employeeIds = $request->input('employee_ids');
            
            $lockedPayrolls = PayrollHistory::where('start_periode', '<=', $endDate)
                ->where('end_periode', '>=', $startDate)
                ->where('locking', true)
                ->whereHas('detailPayroll', function ($query) use ($employeeIds) {
                    $query->whereIn('employee_id', $employeeIds);
                })
                ->exists();

            if ($lockedPayrolls) {
                return response()->json([
                    'message' => 'Payroll process cannot continue. One or more employees have locked payrolls in this period.'
                ], 422);
            }

            //Log::info("Description: $description, Start Date: $startDate, End Date: $endDate, Employee IDs: " . json_encode($employeeIds));
            $activeTransactions = MasterPayroll::where('efektif_date', '<=', $endDate)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereNull('end_date')
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere('end_date', '>', $startDate);
            })
            ->with(['detailPayroll' => function ($query) use ($employeeIds) {
                $query->whereIn('id_employee', $employeeIds)
                    ->with(['employee', 'component']);
            }])
            ->get();

            //Log::info("Active Transactions: " . json_encode($activeTransactions, JSON_PRETTY_PRINT));

            $combinedData = collect([]);

            foreach ($activeTransactions as $settingTunjangan) {
                $groupedData = [
                    'transaksi' => $settingTunjangan,
                    'karyawan' => $settingTunjangan->detailPayroll->map(function ($tunjanganKaryawan) {
                        return [
                            'karyawan' => $tunjanganKaryawan->employee,
                            'param_componen' => $tunjanganKaryawan->component,
                            'nilai' => $tunjanganKaryawan->amount,
                        ];
                    }),
                ];
                //Log::info("Grouped Data: " . json_encode($groupedData, JSON_PRETTY_PRINT));

                $groupedData = collect([$groupedData]);
                $groupedData = $this->calculatePresensiAndSalaries($groupedData, $startDate, $endDate);
                //Log::info("Grouped Data FINAL: " . json_encode($groupedData, JSON_PRETTY_PRINT));

               foreach ($groupedData->first()['karyawan'] as $employeeData) {
                $employeeId = $employeeData['karyawan']->id;
            
                if (!$combinedData->has($employeeId)) {
                    $combinedData->put($employeeId, [
                        'karyawan' => $employeeData['karyawan'],
                        'components' => [
                            'salary' => 0,
                            'allowance' => [],
                            'benefit' => [],
                            'deduction' => [],
                        ],
                        'presensi' => [],
                        'total_overtime' => 0,
                        'total_pendapatan' => 0,
                        'total_potongan' => 0,
                        'gaji_bruto' => 0,
                        'gaji_bersih' => 0,
                    ]);
                }
            
                $existingData = $combinedData->get($employeeId);
            
                // Update komponen salary
                $existingData['components']['salary'] += $employeeData['components']['salary'];
            
                // Update allowance dan deduction
                foreach (['allowance', 'deduction'] as $type) {
                        foreach ($employeeData['components'][$type] as $key => $value) {
                            if (!isset($existingData['components'][$type][$key])) {
                                $existingData['components'][$type][$key] = 0;
                            }
                            $existingData['components'][$type][$key] += $value;
                        }
                    }
                
                    // Update total pendapatan dan potongan
                    $existingData['total_overtime'] += $employeeData['total_overtime'] ?? 0;
                    $existingData['total_pendapatan'] += $employeeData['total_pendapatan'] ?? 0;
                    $existingData['total_potongan'] += $employeeData['total_potongan'] ?? 0;
                    $existingData['gaji_bruto'] += $employeeData['gaji_bruto'] ?? 0;
                    $existingData['gaji_bersih'] += $employeeData['gaji_bersih'] ?? 0;
                
                    // Group and merge presensi data by tanggal
                    $presensiGroupedByTanggal = collect($existingData['presensi'])
                    ->mergeRecursive(collect($employeeData['presensi']))
                    ->groupBy('tanggal')
                    ->map(function ($items, $tanggal) {
                        // Gabungkan semua presensi untuk tanggal yang sama
                        $mergedPresence = [
                            'tanggal' => $tanggal,
                            'earnings' => [],
                            'deductions' => [],
                            'overtime' => [
                                'overtime_hours' => "00:00:00",
                                'overtime_earnings' => 0,
                            ],
                            'deduction_reason' => null,
                        ];

                        // Variabel untuk total overtime dalam detik
                        $totalOvertimeInSeconds = 0;

                        foreach ($items as $item) {
                            // Gabungkan earnings
                            foreach ($item['earnings'] ?? [] as $key => $value) {
                                if (!isset($mergedPresence['earnings'][$key])) {
                                    $mergedPresence['earnings'][$key] = 0;
                                }
                                $mergedPresence['earnings'][$key] += $value;
                            }

                            // Gabungkan deductions
                            foreach ($item['deductions'] ?? [] as $key => $value) {
                                if (!isset($mergedPresence['deductions'][$key])) {
                                    $mergedPresence['deductions'][$key] = 0;
                                }
                                $mergedPresence['deductions'][$key] += $value;
                            }

                            // Gabungkan overtime
                            if (!empty($item['overtime']['overtime_hours'])) {
                                [$hours, $minutes, $seconds] = array_map('intval', explode(':', $item['overtime']['overtime_hours']));
                                $totalOvertimeInSeconds += ($hours * 3600) + ($minutes * 60) + $seconds;
                            }
                            $mergedPresence['overtime']['overtime_earnings'] += $item['overtime']['overtime_earnings'] ?? 0;

                            // Ambil alasan deduksi jika ada
                            if (!empty($item['deduction_reason']) && empty($mergedPresence['deduction_reason'])) {
                                $mergedPresence['deduction_reason'] = $item['deduction_reason'];
                            }
                        }

                        // Konversi total overtime menjadi format HH:mm:ss
                        $hours = floor($totalOvertimeInSeconds / 3600);
                        $minutes = floor(($totalOvertimeInSeconds % 3600) / 60);
                        $seconds = $totalOvertimeInSeconds % 60;
                        $mergedPresence['overtime']['overtime_hours'] = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

                        return $mergedPresence;
                    })
                    ->values()
                    ->all();

                $existingData['presensi'] = $presensiGroupedByTanggal;

                    $combinedData->put($employeeId, $existingData);
                }
            
            }

            //Log::info("Combined Data First: " . json_encode($combinedData, JSON_PRETTY_PRINT));
            $this->insertCombinedPayrollData($combinedData, $startDate, $endDate , $description, $user);

            return response()->json(['success' => true, 'message' => 'Payroll process completed successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing payroll. Please try again later. ',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function calculatePresensiAndSalaries($groupedData, $startDate, $endDate)
    {
        return $groupedData->map(function ($item) use ($startDate, $endDate) {
            $employeeData = $item['karyawan']->map(function ($tunjanganKaryawan) use ($startDate, $endDate) {
                //Log::info("Tunjangan Karyawan: " . json_encode($tunjanganKaryawan, JSON_PRETTY_PRINT));
                $employee = $tunjanganKaryawan['karyawan'];
                $employeeId = $employee->id;
                $nilaiPerHari = $tunjanganKaryawan['nilai'];
                $componentType = $tunjanganKaryawan['param_componen']->componen;
                $componentName = $tunjanganKaryawan['param_componen']->name;
                //$idComponent = $tunjangankaryawan['param_componen']->id;

                $attendanceDetails = [];
                $totalEarnings = 0;
                $totalDeductions = 0;
                $totalOvertimes = 0;

                // Hanya jalankan perhitungan presensi jika komponen bukan Deduction
                if ($componentType !== 'Deduction') {
                    $attendance = Presence::calculateAttendanceAndDeductions(
                        $employeeId,
                        $startDate,
                        $endDate,
                        $nilaiPerHari,
                        $componentType
                    );

                    $attendanceDetails = $attendance['attendance_details'];
                    $totalEarnings = $attendance['total_earnings'];
                    $totalDeductions = $attendance['total_deductions'];
                    $totalOvertimes = $attendance['total_overtime_earnings'];
                }

                Log::info("attendanceDetails: " . json_encode($attendanceDetails, JSON_PRETTY_PRINT));
                Log::info("Total penghasilan: $totalEarnings, Total potongan: $totalDeductions");

                $components = [
                    'salary' => 0,
                    'allowance' => [],
                    'deduction' => [],
                ];

                switch ($componentType) {
                    case 'Salary':
                        $components['salary'] = $totalEarnings;
                        break;

                    case 'Allowance':
                        if (!isset($components['allowance'][$componentName])) {
                            $components['allowance'][$componentName] = 0;
                        }
                        $components['allowance'][$componentName] += $totalEarnings;
                        break;

                    case 'Deduction':
                        if (!isset($components['deduction'][$componentName])) {
                            $components['deduction'][$componentName] = 0;
                        }
                        $components['deduction'][$componentName] += $nilaiPerHari;
                        break;

                    default:
                        Log::warning("Unknown component type '{$componentType}' for employee ID {$employeeId}");
                }

                $totalPendapatan = $components['salary'] + $totalOvertimes + $this->flattenComponents($components['allowance']);
                $totalPotongan = $this->flattenComponents($components['deduction']);
                $gajiBruto = $totalPendapatan;
                $gajiBersih = $gajiBruto - $totalPotongan;

                return [
                    'karyawan' => $tunjanganKaryawan['karyawan'],
                    'param_componen' => $tunjanganKaryawan['param_componen'],
                    'nilai' => $tunjanganKaryawan['nilai'],
                    'presensi' => $attendanceDetails,
                    'salary' => $components['salary'],
                    'components' => $components,
                    'total_overtime' => $totalOvertimes,
                    'total_pendapatan' => $totalPendapatan,
                    'total_potongan' => $totalPotongan,
                    'gaji_bruto' => $gajiBruto,
                    'gaji_bersih' => $gajiBersih,
                ];
            });

            return [
                'transaksi' => $item['transaksi'],
                'karyawan' => $employeeData,
            ];
        });
    }

    private function insertCombinedPayrollData($combinedData, $startDate, $endDate, $description, $user)
    {
        //Log::info("Combined Dataaaaa: " . json_encode($combinedData, JSON_PRETTY_PRINT));

        DB::beginTransaction();

        try {
            $prefix = 'MA';
            $id_transaksi_payment = PayrollHistory::generateIdTransaksiPayment($prefix);
            $amount_transaksi = $combinedData->sum(function ($employeeData) {
                return $employeeData['gaji_bersih'];
            });

            $payrollHistory = PayrollHistory::create([
                'id_transaksi_payment' => $id_transaksi_payment,
                'start_periode' => $startDate,
                'end_periode' => $endDate,
                'amount_transaksi' => $amount_transaksi,
                'total_karyawan' => $combinedData->count(),
                'status_payroll' => 'Pending',
                'description' => $description,
                'id_user' => $user->id,
                'locking' => false,
            ]);

            foreach ($combinedData as $employeeData) {
                $payrollHistoryDetail = PayrollHistoryDetail::create([
                    'id_payroll_history' => $payrollHistory->id,
                    'id_transaksi_payment' => $id_transaksi_payment,
                    'employee_id' => $employeeData['karyawan']->id,
                    'salary' => $employeeData['components']['salary'],
                    'allowance' => json_encode($this->convertComponentToNamedArray($employeeData['components']['allowance'])),
                    'deduction' => json_encode($this->convertComponentToNamedArray($employeeData['components']['deduction'])),
                    'total_overtime' => $employeeData['total_overtime'],
                    'total_pendapatan' => $employeeData['total_pendapatan'],
                    'total_potongan' => $employeeData['total_potongan'],
                    'gaji_bruto' => $employeeData['gaji_bruto'],
                    'gaji_bersih' => $employeeData['gaji_bersih']
                ]);

                foreach ($employeeData['presensi'] as $attendanceDetail) {
                    AttendanceDetail::create([
                        'id_payroll_history_detail' => $payrollHistoryDetail->id,
                        'tanggal' => Carbon::parse($attendanceDetail['tanggal'])->format('Y-m-d'),
                        'earnings' => $attendanceDetail['earnings'],
                        'deductions' => $attendanceDetail['deductions'],
                        'overtime_earnings' => $attendanceDetail['overtime']['overtime_earnings'] ?? 0,
                        'overtime_hours' => $attendanceDetail['overtime']['overtime_hours'] ?? '00:00:00',
                        'deduction_reason' => $attendanceDetail['deduction_reason'] ?? null
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to insert payroll data: " . $e->getMessage());
            throw new \Exception("Failed to insert payroll data: " . $e->getMessage());
        }
    }

    private function convertComponentToNamedArray($component)
    {
        $namedArray = [];
        foreach ($component as $name => $value) {
            $namedArray[] = [
                'nama' => $name,
                'nilai' => number_format((float)$value, 0, ',', '.')
            ];
        }
        return $namedArray;
    }

    private function flattenComponents($components)
    {
        $flattened = [];
        foreach ($components as $key => $value) {
            if (is_array($value)) {
                $flattened[] = array_sum($value);
            } else {
                $flattened[] = $value;
            }
        }
        return array_sum($flattened);
    }

    ##END RUN PAYROLL PROCESS##



}
