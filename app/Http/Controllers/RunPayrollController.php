<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use App\Models\MasterPayroll;
use App\Models\Presence;
use App\Models\PayrollHistory;
use App\Models\PayrollHistoryDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

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
            $description = $request->input('description');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $employeeIds = $request->input('employee_ids');

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
                            'total_pendapatan' => 0,
                            'total_potongan' => 0,
                            'gaji_bruto' => 0,
                            'gaji_bersih' => 0,
                        ]);
                    }

                    $existingData = $combinedData->get($employeeId);

                    $existingData['components']['salary'] += $employeeData['components']['salary'];
                    
                    foreach ($employeeData['components']['allowance'] as $key => $value) {
                        if (!isset($existingData['components']['allowance'][$key])) {
                            $existingData['components']['allowance'][$key] = 0;
                        }
                        $existingData['components']['allowance'][$key] += $value;
                    }
                    foreach ($employeeData['components']['deduction'] as $key => $value) {
                        if (!isset($existingData['components']['deduction'][$key])) {
                            $existingData['components']['deduction'][$key] = 0;
                        }
                        $existingData['components']['deduction'][$key] += $value;
                    }

                    $existingData['total_pendapatan'] += $employeeData['total_pendapatan'] ?? 0;
                    $existingData['total_potongan'] += $employeeData['total_potongan'] ?? 0;
                    $existingData['gaji_bruto'] += $employeeData['gaji_bruto'] ?? 0;
                    $existingData['gaji_bersih'] += $employeeData['gaji_bersih'] ?? 0;
                    $combinedData->put($employeeId, $existingData);
                }
            }

            $this->insertCombinedPayrollData($combinedData, $startDate, $endDate , $description);

            return response()->json(['success' => true, 'message' => 'Payroll process completed successfully.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing payroll. Please try again later.'
            ], 500);
        }
    }

    // private function calculatePresensiAndSalaries($groupedData, $startDate, $endDate)
    // {
    //     return $groupedData->map(function ($item) use ($startDate, $endDate) {
    //         $employeeData = $item['karyawan']->map(function ($tunjanganKaryawan) use ($startDate, $endDate) {
    //             $employee = $tunjanganKaryawan['karyawan'];
    //             $employeeId = $employee->id;

    //             $jumlahHariKerja = Presence::getTotalHadir($employeeId, $startDate, $endDate);
    //             Log::info("Jumlah hari kerja: $jumlahHariKerja");

    //             if (!$jumlahHariKerja) {
    //                 Log::warning("No presensi data found for employee ID: $employeeId in period: $startDate to $endDate");
    //                 return [
    //                     'karyawan' => $tunjanganKaryawan['karyawan'],
    //                     'param_componen' => $tunjanganKaryawan['param_componen'],
    //                     'nilai' => $tunjanganKaryawan['nilai'],
    //                     'presensi' => null,
    //                     'salary' => 0,
    //                     'components' => [
    //                         'salary' => 0,
    //                         'allowance' => [],
    //                         'deduction' => [],
    //                     ],
    //                     'total_pendapatan' => 0,
    //                     'total_potongan' => 0,
    //                     'gaji_bruto' => 0,
    //                     'gaji_bersih' => 0,
    //                 ];
    //             }

    //             $components = [
    //                 'salary' => 0,
    //                 'allowance' => [],
    //                 'deduction' => [],
    //             ];
    
                // $componentName = $tunjanganKaryawan['param_componen']->name;
                // $componentType = $tunjanganKaryawan['param_componen']->componen;
               
                // $nilai = $tunjanganKaryawan['nilai'];
                // $hasil = $nilai * $jumlahHariKerja;

                // switch ($componentType) {
                //     case 'Salary':
                //         $components['salary'] += $hasil;
                //         break;
    
                //     case 'Allowance':
                //         if (!isset($components['allowance'][$componentName])) {
                //             $components['allowance'][$componentName] = 0;
                //         }
                //         $components['allowance'][$componentName] += $hasil;
                //         break;
    
                //     case 'Deduction':
                //         if (!isset($components['deduction'][$componentName])) {
                //             $components['deduction'][$componentName] = 0;
                //         }
                //         $components['deduction'][$componentName] += $nilai;
                //         break;
    
                //     default:
                //         Log::warning("Unknown component type '{$componentType}' for employee ID {$employeeId}");
                // }

    //             // Hitung total pendapatan dan potongan
    //             $totalPendapatan = $components['salary'] + array_sum($components['allowance']);
    //             $totalPotongan = array_sum($components['deduction']);

    //             $gajiBruto = $totalPendapatan;
    //             $gajiBersih = $gajiBruto - $totalPotongan;

    //             return [
    //                 'karyawan' => $tunjanganKaryawan['karyawan'],
    //                 'param_componen' => $tunjanganKaryawan['param_componen'],
    //                 'nilai' => $tunjanganKaryawan['nilai'],
    //                 'presensi' => [
    //                     'working_days' => $jumlahHariKerja,
    //                 ],
    //                 'salary' => $components['salary'],
    //                 'components' => $components,
    //                 'total_pendapatan' => $totalPendapatan,
    //                 'total_potongan' => $totalPotongan,
    //                 'gaji_bruto' => $gajiBruto,
    //                 'gaji_bersih' => $gajiBersih,
    //             ];
    //         });

    //         return [
    //             'transaksi' => $item['transaksi'],
    //             'karyawan' => $employeeData,
    //         ];
    //     });
    // }

    private function calculatePresensiAndSalaries($groupedData, $startDate, $endDate)
    {
        return $groupedData->map(function ($item) use ($startDate, $endDate) {
            $employeeData = $item['karyawan']->map(function ($tunjanganKaryawan) use ($startDate, $endDate) {
                $employee = $tunjanganKaryawan['karyawan'];
                $employeeId = $employee->id;
                $nilaiPerHari = $tunjanganKaryawan['nilai'];
                $componentType = $tunjanganKaryawan['param_componen']->componen;
                $componentName = $tunjanganKaryawan['param_componen']->name;

                $attendance = [
                    'attendance_details' => [],
                    'total_earnings' => 0,
                    'total_deductions' => 0,
                ];

                if ($componentType === 'Salary') {
                    $attendance = Presence::calculateAttendanceAndDeductions($employeeId, $startDate, $endDate, $nilaiPerHari, $componentType);
                }
    
                $attendanceDetails = $attendance['attendance_details'];
                $totalEarnings = $attendance['total_earnings'];
                $totalDeductions = $attendance['total_deductions'];

                //Log::info("Total penghasilan: $totalEarnings, Total potongan: $totalDeductions");

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
                        $components['deduction'][$componentName] += $totalDeductions;
                        break;

                    default:
                        Log::warning("Unknown component type '{$componentType}' for employee ID {$employeeId}");
                }

                $totalPendapatan = $components['salary'] + array_sum($components['allowance']);
                $totalPotongan = array_sum($components['deduction']);
                $gajiBruto = $totalPendapatan;
                $gajiBersih = $gajiBruto - $totalPotongan;

                return [
                    'karyawan' => $tunjanganKaryawan['karyawan'],
                    'param_componen' => $tunjanganKaryawan['param_componen'],
                    'nilai' => $tunjanganKaryawan['nilai'],
                    'presensi' => $attendanceDetails,
                    'salary' => $components['salary'],
                    'components' => $components,
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





    private function insertCombinedPayrollData($combinedData, $startDate, $endDate, $description)
    {
        //Log::info("Combined Dataaaaa: " . json_encode($combinedData, JSON_PRETTY_PRINT));

        DB::beginTransaction();

        try {
            $prefix = 'MA';
            $id_transaksi_payment = PayrollHistory::generateIdTransaksiPayment($prefix);
            $amount_transaksi = $combinedData->sum(function ($employeeData) {
                return $employeeData['components']['salary'] 
                    + array_sum($employeeData['components']['allowance'])
                    - array_sum($employeeData['components']['deduction']);
            });

            $payrollHistory = PayrollHistory::create([
                'id_transaksi_payment' => $id_transaksi_payment,
                'start_periode' => $startDate,
                'end_periode' => $endDate,
                'amount_transaksi' => $amount_transaksi,
                'total_karyawan' => $combinedData->count(),
                'status_payroll' => 'Pending',
                'description' => $description
            ]);

            foreach ($combinedData as $employeeData) {
                PayrollHistoryDetail::create([
                    'id_payroll_history' => $payrollHistory->id,
                    'id_transaksi_payment' => $id_transaksi_payment,
                    'employee_id' => $employeeData['karyawan']->id,
                    'salary' => $employeeData['components']['salary'],
                    'allowance' => json_encode($this->convertComponentToNamedArray($employeeData['components']['allowance'])),
                    'deduction' => json_encode($this->convertComponentToNamedArray($employeeData['components']['deduction'])),
                    'total_pendapatan' => $employeeData['total_pendapatan'],
                    'total_potongan' => $employeeData['total_potongan'],
                    'gaji_bruto' => $employeeData['gaji_bruto'],
                    'gaji_bersih' => $employeeData['gaji_bersih']
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to insert payroll data: " . $e->getMessage());
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
##END RUN PAYROLL PROCESS##



}
