<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use App\Models\PayrollHistory;
use App\Models\PayrollHistoryDetail;
use App\Models\AttendanceDetail;
use App\Models\Group;
use Illuminate\Support\Facades\Log;

class PayrollHistoryController extends Controller
{
    public function index()
    {
        $payrollHistories = PayrollHistory::where('locking', false)
            ->orderBy('start_periode', 'desc')
            ->get();
        return view('pages.payroll_history.index', compact('payrollHistories'));
    }

    public function showListGroup()
    {
        $listGroup = Group::get();

        return view('pages.payroll_history.MasterDetail', compact('listGroup'));
    }

    public function showDetails($id)
    {
        try {
            // Ambil data payroll history dengan relasi
            $payrollHistoryDetail = PayrollHistory::with(['detailPayroll.employee'])
                ->findOrFail($id);

            Log::info('cek history' . json_encode($payrollHistoryDetail, JSON_PRETTY_PRINT));

            // Ubah allowance dan deduction menjadi array jika berbentuk JSON
            foreach ($payrollHistoryDetail->detailPayroll as $detail) {
                // Allowance
                $allowanceData = is_string($detail->allowance)
                    ? json_decode($detail->allowance, true)
                    : $detail->allowance;

                if (is_array($allowanceData)) {
                    foreach ($allowanceData as $key => $allowance) {
                        $allowanceData[$key]['nilai'] = (float) str_replace('.', '', $allowance['nilai']);
                    }
                }
                $detail->allowance = $allowanceData;

                // Deduction
                $deductionData = is_string($detail->deduction)
                    ? json_decode($detail->deduction, true)
                    : $detail->deduction;

                if (is_array($deductionData)) {
                    foreach ($deductionData as $key => $deduction) {
                        $deductionData[$key]['nilai'] = (float) str_replace('.', '', $deduction['nilai']);
                    }
                }
                $detail->deduction = $deductionData;
            }

            // Ambil semua employee_id dari detail payroll
            $employeeIds = $payrollHistoryDetail->detailPayroll->pluck('employee_id');

            // Ambil data grup berdasarkan employee_id
            $groups = DB::table('groups')
                ->join('group_members', 'groups.id', '=', 'group_members.group_id')
                ->whereIn('group_members.member_id', $employeeIds)
                ->select('groups.id as group_id', 'groups.name as group_name', 'group_members.member_id as employee_id')
                ->get()
                ->groupBy('group_id');

            // Tambahkan karyawan yang tidak memiliki grup ke dalam "no_group"
            $noGroup = $payrollHistoryDetail->detailPayroll
                ->filter(function ($detail) use ($groups) {
                    return !$groups->pluck('employee_id')->flatten()->contains($detail->employee_id);
                });

            $groups['no_group'] = $noGroup;

            // Log untuk debugging
            Log::info("Grouped Payroll Details" . json_encode($groups, JSON_PRETTY_PRINT));

            // Kirim data ke view
            return view('pages.payroll_history.detail', [
                'payrollHistoryDetail' => $payrollHistoryDetail,
                'groups' => $groups,
            ]);
        } catch (\Exception $e) {
            Log::error("Error fetching payroll history detail: " . $e->getMessage());
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    }


    public function showAttendanceDetails($idPayrollHistoryDetail)
    {
        try {
            // Ambil semua AttendanceDetail berdasarkan id_payroll_history_detail
            $payrollHistoryDetail = PayrollHistoryDetail::with('employee', 'payrollHistory')
                ->findOrFail($idPayrollHistoryDetail);

            // Ambil semua AttendanceDetail terkait
            $attendanceDetails = AttendanceDetail::with('payrollHistoryDetail')
                ->where('id_payroll_history_detail', $idPayrollHistoryDetail)
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
}
