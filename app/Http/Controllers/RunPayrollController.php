<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

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
    public function RunPayrollNew(Request $request)
    {
        $description = $request->input('description');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        //Log::info("Start Date: $startDate, End Date: $endDate");
        $activeTransactions = SettingTunjangan::where('efektif_date', '<=', $endDate)
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereNull('end_date')
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere('end_date', '>', $startDate);
        })
        ->with(['tunjanganKaryawans.getEmploy', 'tunjanganKaryawans.parcom'])
        ->get();
        //Log::info("Active Transactions: " . json_encode($activeTransactions, JSON_PRETTY_PRINT));

        $combinedData = collect([]);

        foreach ($activeTransactions as $settingTunjangan) {
            $groupedData = [
                'transaksi' => $settingTunjangan,
                'karyawan' => $settingTunjangan->tunjanganKaryawans->map(function ($tunjanganKaryawan) {
                    return [
                        'karyawan' => $tunjanganKaryawan->getEmploy,
                        'param_componen' => $tunjanganKaryawan->parcom,
                        'nilai' => $tunjanganKaryawan->nilai,
                    ];
                }),
            ];

            $groupedData = collect([$groupedData]);
            $groupedData = $this->calculatePresensiAndSalaries($groupedData, $startDate, $endDate);

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
                foreach ($employeeData['components']['benefit'] as $key => $value) {
                    if (!isset($existingData['components']['benefit'][$key])) {
                        $existingData['components']['benefit'][$key] = 0;
                    }
                    $existingData['components']['benefit'][$key] += $value;
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

        // Log the combined data
        //Log::info('Combined Data: ' . json_encode($combinedData, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Payroll process completed successfully.']);
    }

    private function calculatePresensiAndSalaries($groupedData, $startDate, $endDate)
    {
        // $endDate = '2024-07-20';
        // $startDate = '2024-06-21';

        // $paramPeriode = ParamPeriode::where('status', '=', 'Aktif')->first();
        // $formattedStartDate = intval($paramPeriode->startdate);
        // $formattedEndDate = intval($paramPeriode->enddate);

        // $endDate = Carbon::now()->format('Y-m') . '-' . $formattedEndDate;
        // $startDate = Carbon::parse($endDate)->subMonth()->format('Y-m') . '-' . $formattedStartDate;

        return $groupedData->map(function ($item) use ($startDate, $endDate) {
            $employeeData = $item['karyawan']->map(function ($tunjanganKaryawan) use ($startDate, $endDate) {
                $employee = $tunjanganKaryawan['karyawan'];
                $employeeId = $tunjanganKaryawan['karyawan']->id;

                $rekapPresensi = RekapAbsensi::where('id_karyawan', $employeeId)
                    ->where('mulai_periode', '=', $startDate)
                    ->where('akhir_periode', '=', $endDate)
                    ->first();
                //Log::info("Rekap Presensisssssssss: " . json_encode($rekapPresensi, JSON_PRETTY_PRINT));
                if (!$rekapPresensi) {
                    Log::warning("No presensi data found for employee ID: $employeeId in period: $startDate to $endDate");
                    return [
                        'karyawan' => $tunjanganKaryawan['karyawan'],
                        'param_componen' => $tunjanganKaryawan['param_componen'],
                        'nilai' => $tunjanganKaryawan['nilai'],
                        'presensi' => null,
                        'salary' => 0,
                        'components' => [
                            'salary' => 0,
                            'allowance' => [],
                            'benefit' => [],
                            'deduction' => [],
                        ]
                    ];
                }

                $prorate = $employee->getprorate($employeeId);
                $workingDays = $rekapPresensi->jumlah_hari_kerja;
                $prorateDays = $rekapPresensi->jumlah_hari_prorate;
                // Logging values for presensi and prorate calculations
                //Log::info("Employee ID: $employeeId - Prorate: $prorate, Working Days: $workingDays, Prorate Days: $prorateDays");

                $components = [
                    'salary' => 0,
                    'allowance' => [],
                    'benefit' => [],
                    'deduction' => [],
                ];

                $statusTunjangan = $tunjanganKaryawan['param_componen']->status_tunjangan;
                $nilai = $tunjanganKaryawan['nilai'];
                $hasil = 0;
                if ($prorateDays > 0 && $workingDays > 0) {
                    $hasil = ($statusTunjangan === 'Tetap') ? ($nilai / $prorate) * $prorateDays : ($nilai / $prorate) * $workingDays;
                }
                $rumustetap = 0;
                $rumustetap = ($nilai / $prorate) * $prorateDays;

                //Log::info("Employee ID: $employeeId - Component Name: " . $tunjanganKaryawan['param_componen']->nama . " - Calculated Value: $hasil");

                
    
                $componentName = $tunjanganKaryawan['param_componen']->nama;
                switch ($tunjanganKaryawan['param_componen']->komponen) {
                    case 'Salary':
                        $components['salary'] += $rumustetap;
                        break;
                    case 'Allowance':
                        if (!isset($components['allowance'][$componentName])) {
                            $components['allowance'][$componentName] = 0;
                        }
                        if ($statusTunjangan === 'Flat') {
                            $components['allowance'][$componentName] += $nilai;
                        } else {
                            $components['allowance'][$componentName] += $hasil;
                        }
                        break;
                    case 'Benefit':
                        if (!isset($components['benefit'][$componentName])) {
                            $components['benefit'][$componentName] = 0;
                        }
                        $components['benefit'][$componentName] += $nilai;
                        break;
                    case 'Deduction':
                        if (!isset($components['deduction'][$componentName])) {
                            $components['deduction'][$componentName] = 0;
                        }
                        $components['deduction'][$componentName] += $nilai;
                        break;
                }

                $totalPendapatan = $components['salary'] + array_sum($components['allowance']) + array_sum($components['benefit']);
                $totalPotongan = array_sum($components['deduction']);
                $gajiBruto = $totalPendapatan;
                $gajiBersih = $gajiBruto - $totalPotongan;

                //Log::info("Employee ID: $employeeId - Total Pendapatan: $totalPendapatan, Total Potongan: $totalPotongan, Gaji Bruto: $gajiBruto, Gaji Bersih: $gajiBersih");

                return [
                    'karyawan' => $tunjanganKaryawan['karyawan'],
                    'param_componen' => $tunjanganKaryawan['param_componen'],
                    'nilai' => $tunjanganKaryawan['nilai'],
                    'presensi' => [
                        'prorate' => $prorate,
                        'working_days' => $workingDays,
                    ],
                    'salary' => $hasil,
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
        //$startDate = '2024-06-21';
        //$endDate = '2024-07-20';

        // $paramPeriode = ParamPeriode::where('status', '=', 'Aktif')->first();
        // $formattedStartDate = intval($paramPeriode->startdate);
        // $formattedEndDate = intval($paramPeriode->enddate);

        // $endDate = Carbon::now()->format('Y-m') . '-' . $formattedEndDate;
        // $startDate = Carbon::parse($endDate)->subMonth()->format('Y-m') . '-' . $formattedStartDate;

        DB::beginTransaction();

        try {
            $prefix = 'MA';
            $id_transaksi_payment = PayrollHistory::generateIdTransaksiPayment($prefix);
            // $id_transaksi_payment = $this->generateTransaksiPaymentId();
            // $description = 'Payroll untuk periode ' . $startDate . ' ke ' . $endDate;
            $amount_transaksi = $combinedData->sum(function ($employeeData) {
                return $employeeData['components']['salary'] 
                    + array_sum($employeeData['components']['allowance'])
                    + array_sum($employeeData['components']['benefit'])
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
                    'id_transaksi_payment' => $id_transaksi_payment,
                    'id_karyawan' => $employeeData['karyawan']->id,
                    'salary' => $employeeData['components']['salary'],
                    'allowance' => json_encode($this->convertComponentToNamedArray($employeeData['components']['allowance'])),
                    'benefit' => json_encode($this->convertComponentToNamedArray($employeeData['components']['benefit'])),
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
