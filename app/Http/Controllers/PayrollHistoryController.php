<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\PayrollHistory;
use App\Models\PayrollHistoryDetail;
use App\Models\AttendanceDetail;
use App\Models\Group;
use App\Models\DeductionGroup;
use Illuminate\Support\Facades\Log;

class PayrollHistoryController extends Controller
{
    public function index()
    {
        $payrollHistories = PayrollHistory::where('locking', false)
            ->orderBy('start_periode', 'desc')
            ->get();

        log::info('CEK HISTORY PAYROLL DEDTAIL : ' . json_encode($payrollHistories, JSON_PRETTY_PRINT));
        return view('pages.payroll_history.index', compact('payrollHistories'));
    }

    public function showGroupTotals($id)
    {
        session(['id_payroll_histories' => $id]);
        Log::info('cek id cik bro : ' . json_encode($id, JSON_PRETTY_PRINT));

        try {
            $payrollHistoryDetail = PayrollHistory::findOrFail($id);

            $groups = Group::with([
                'members.member',
                'members.member.payrollHistoryDetails' => function ($query) use ($id) {
                    $query->where('id_payroll_history', $id);
                },
                'leader.payrollHistoryDetails' => function ($query) use ($id) {
                    $query->where('id_payroll_history', $id);
                },
                'deductionGroups' => function ($query) use ($id) {
                    $query->where('id_payroll_history', $id);
                }
            ])->get();

            Log::info("Data :" . json_encode($groups, JSON_PRETTY_PRINT));

            $result = $groups->map(function ($group) {
                $totalSalary = 0;
                $totalAllowance = 0;
                $totalDeduction = 0;
                $totalOvertime = 0;
                $grossSalary = 0;
                $netSalary = 0;
                $deductionGroupTotal = 0;

                if ($group->leader) {
                    foreach ($group->leader->payrollHistoryDetails as $payroll) {
                        $totalSalary += $payroll->salary;

                        $allowance = $payroll->allowance;
                        $totalAllowance += is_array($allowance) ? array_sum(array_map(function ($item) {
                            return isset($item['nilai']) ? floatval(str_replace('.', '', $item['nilai'])) : 0;
                        }, $allowance)) : 0;

                        $deduction = $payroll->deduction;
                        $totalDeduction += is_array($deduction) ? array_sum(array_map(function ($item) {
                            return isset($item['nilai']) ? floatval(str_replace('.', '', $item['nilai'])) : 0;
                        }, $deduction)) : 0;

                        $totalOvertime += $payroll->total_overtime;

                        $grossSalary += $payroll->gaji_bruto;
                        $netSalary += $payroll->gaji_bersih;
                    }
                }

                // Include members' payroll details
                foreach ($group->members as $member) {
                    foreach ($member->member->payrollHistoryDetails as $payroll) {
                        $totalSalary += $payroll->salary;

                        $allowance = $payroll->allowance;
                        $totalAllowance += is_array($allowance) ? array_sum(array_map(function ($item) {
                            return isset($item['nilai']) ? floatval(str_replace('.', '', $item['nilai'])) : 0;
                        }, $allowance)) : 0;

                        $deduction = $payroll->deduction;
                        $totalDeduction += is_array($deduction) ? array_sum(array_map(function ($item) {
                            return isset($item['nilai']) ? floatval(str_replace('.', '', $item['nilai'])) : 0;
                        }, $deduction)) : 0;

                        $totalOvertime += $payroll->total_overtime;

                        $grossSalary += $payroll->gaji_bruto;
                        $netSalary += $payroll->gaji_bersih;
                    }
                }

                foreach ($group->deductionGroups as $deductionGroup) {
                    $deductionGroupTotal += $deductionGroup->amount;
                }
    
                $netSalaryAfterDeductionGroup = $grossSalary - $deductionGroupTotal;

                return [
                    'groupid' => $group->id,
                    'group_name' => $group->name,
                    'group_code' => $group->code,
                    'leader' => $group->leader ? $group->leader->name : '-',
                    'total_salary' => $totalSalary,
                    'total_allowance' => $totalAllowance,
                    'total_deduction' => $totalDeduction,
                    'total_overtime' => $totalOvertime,
                    'gross_salary' => $grossSalary,
                    'net_salary' => $netSalary,
                    'deduction_group_total' => $deductionGroupTotal,
                    'net_salary_after_deduction_group' => $netSalaryAfterDeductionGroup,
                ];
            });

            Log::info("Group totals calculated", ['totals' => json_encode($result, JSON_PRETTY_PRINT)]);

            //return response()->json($result);

            return view('pages.payroll_history.group', compact('result', 'payrollHistoryDetail'));
        } catch (\Exception $e) {
            Log::error("Error fetching group totals: " . $e->getMessage(), ['exception' => $e]);

            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    }

    public function showGroupDetails($payrollHistoryId, $groupId)
    {
        try {
            $group = Group::with([
                'leader.payrollHistoryDetails' => function ($query) use ($payrollHistoryId) {
                    $query->where('id_payroll_history', $payrollHistoryId);
                },
                'members.member',
                'members.member.payrollHistoryDetails' => function ($query) use ($payrollHistoryId) {
                    $query->where('id_payroll_history', $payrollHistoryId);
                }
            ])->findOrFail($groupId);

            //Log::info("Group data", ['data' => json_encode($group, JSON_PRETTY_PRINT)]);

            $result = [
                'group_name' => $group->name,
                'group_code' => $group->code,
                'leader' => [
                    'id' => $group->leader->id ?? null,
                    'name' => $group->leader->name ?? null,
                    'nip' => $group->leader->nip ?? null,
                    'payroll_details' => $group->leader->payrollHistoryDetails->map(function ($payroll) {
                        return [
                            'salary' => $payroll->salary,
                            'allowance' => json_decode($payroll->allowance, true) ?? [],
                            'deduction' => json_decode($payroll->deduction, true) ?? [],
                            'total_pendapatan' => $payroll->total_pendapatan,
                            'total_overtime' => $payroll->total_overtime,
                            'total_potongan' => $payroll->total_potongan,
                            'gaji_bruto' => $payroll->gaji_bruto,
                            'gaji_bersih' => $payroll->gaji_bersih,
                        ];
                    }),
                ],
                'members' => $group->members->map(function ($member) {
                    return [
                        'id' => $member->member->id,
                        'employee_name' => $member->member->name,
                        'employee_nip' => $member->member->nip,
                        'id_transaksi_payment' => $member->member->payrollHistoryDetails->first()?->id_transaksi_payment,
                        'payroll_details' => $member->member->payrollHistoryDetails->map(function ($payroll) {
                            return [
                                'salary' => $payroll->salary,
                                'allowance' => json_decode($payroll->allowance, true) ?? [],
                                'deduction' => json_decode($payroll->deduction, true) ?? [],
                                'total_pendapatan' => $payroll->total_pendapatan,
                                'total_overtime' => $payroll->total_overtime,
                                'total_potongan' => $payroll->total_potongan,
                                'gaji_bruto' => $payroll->gaji_bruto,
                                'gaji_bersih' => $payroll->gaji_bersih,
                            ];
                        }),
                    ];
                }),
            ];
            

            Log::info("Group details data", ['data' => json_encode($result, JSON_PRETTY_PRINT)]);

            return view('pages.payroll_history.detail', compact('result'));
        } catch (\Exception $e) {
            Log::error("Error fetching group details: " . $e->getMessage());
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    }

    public function showAttendanceDetails($idPayrollHistoryDetail)
    {
        $id = session('id_payroll_histories');
        Log::info("Payroll History ID from session: " . $id);
        Log::info("Attendance Details Data: ", ['data' => $idPayrollHistoryDetail]);
        try {
            // Ambil semua AttendanceDetail berdasarkan id_payroll_history_detail
            $payrollHistoryDetail = PayrollHistoryDetail::with('employee', 'payrollHistory')
                ->where('id_payroll_history', $id)
                ->where('employee_id', $idPayrollHistoryDetail)
                ->first();
            $getId = $payrollHistoryDetail->id;
            // Ambil semua AttendanceDetail terkait
            $attendanceDetails = AttendanceDetail::with('payrollHistoryDetail')
                ->where('id_payroll_history_detail', $getId)
                ->get();

            // Log data untuk debugging
            Log::info("Attendance Details Data: ", ['data' => $attendanceDetails]);

            // Kirim data ke view
            return view('pages.payroll_history.attendance-detail', compact('attendanceDetails', 'payrollHistoryDetail'));
        } catch (\Exception $e) {
            Log::error("Error fetching attendance details: " . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan.');
        }
    }

    public function locking(Request $request)
    {
        Log::info("Request", ['data' => json_encode($request->all(), JSON_PRETTY_PRINT)]);
        $validated = $request->validate([
            'id' => 'required|exists:payroll_histories,id',
            'locking' => 'required|boolean',
            'startPeriode' => 'required|date',
            'endPeriode' => 'required|date',
        ]);
        $existingPeriode = PayrollHistory::where('locking', true)
            ->where('id', '!=', $validated['id'])
            ->where(function ($query) use ($validated) {
                $query->where('start_periode', '=', $validated['startPeriode'])
                    ->where('end_periode', '=', $validated['endPeriode']);
            })
            ->first();
        Log::info("Existing Periode", ['data' => json_encode($existingPeriode, JSON_PRETTY_PRINT)]);

        if ($existingPeriode) {
            return response()->json([
                'success' => false,
                'error' => 'There is already a locked payroll history for the selected period.',
            ], 422);
        }

        $transaksi = PayrollHistory::find($validated['id']);
        $transaksi->locking = $validated['locking'];
        $transaksi->save();

        return response()->json([
            'success' => true,
            'message' => 'Locking status updated successfully.',
            'locking' => $transaksi->locking,
        ]);
    }

    public function createDeductionGroup($groupId, $payrollHistoryId)
    {
        return view('pages.payroll_history.create-deduction-group', compact('groupId', 'payrollHistoryId'));
    }

    public function storeDeductionGroup(Request $request)
    {
        DB::beginTransaction();
    
        try {
            $request->validate([
                'payroll_id' => 'required|exists:payroll_histories,id',
                'group_id' => 'required|exists:groups,id',
                'amount' => 'required|numeric',
            ]);

            $payrollHistory = PayrollHistory::findOrFail($request->payroll_id);
    
            $deductionGroup = DeductionGroup::where('id_payroll_history', $request->payroll_id)
                ->where('group_id', $request->group_id)
                ->first();
    
            if ($deductionGroup) {
                $deductionGroup->amount = $request->amount;
                $deductionGroup->save();
            } else {
                $deductionGroup = DeductionGroup::create([
                    'id_payroll_history' => $request->payroll_id,
                    'group_id' => $request->group_id,
                    'amount' => $request->amount,
                ]);
            }
    
            $deductionAmount = $deductionGroup->amount;
            $payrollDetails = PayrollHistoryDetail::where('id_payroll_history', $request->payroll_id)->get();
           
            $totalGajiBersih = $payrollDetails->sum('gaji_bersih');
            $totalDeductions = $deductionGroup->amount;
            $payrollHistory->amount_transaksi = $totalGajiBersih - $totalDeductions;
            $payrollHistory->save();
    
            DB::commit();
    
            return response()->json(['message' => 'Deduction Group created or updated successfully, and Payroll History updated!'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
