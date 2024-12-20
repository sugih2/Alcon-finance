<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollHistory;
use App\Models\PayrollHistoryDetail;
use App\Models\AttendanceDetail;
use Illuminate\Support\Facades\Log;

class PayrollHistoryController extends Controller
{
    public function index()
    {
        $payrollHistories = PayrollHistory::all();
        return view('pages.payroll_history.index', compact('payrollHistories'));
    }

    public function showDetails($id)
    {
        try {
            // Ambil data payroll history dengan relasi
            $payrollHistoryDetail = PayrollHistory::with(['detailPayroll.employee'])
                ->findOrFail($id);

            // Ubah allowance dan deduction menjadi array jika berbentuk JSON
            foreach ($payrollHistoryDetail->detailPayroll as $detail) {
                // Pastikan allowance dalam bentuk array
                $allowanceData = is_string($detail->allowance)
                    ? json_decode($detail->allowance, true)
                    : $detail->allowance;
            
                // Bersihkan nilai allowance jika ada
                if (is_array($allowanceData)) {
                    foreach ($allowanceData as $key => $allowance) {
                        $allowanceData[$key]['nilai'] = (float) str_replace('.', '', $allowance['nilai']);
                    }
                }
            
                // Tetapkan allowance yang sudah diperbarui kembali ke properti
                $detail->allowance = $allowanceData;
            
                // Lakukan hal yang sama untuk deduction jika diperlukan
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
            
            

            // Log detail data untuk debugging
            Log::info("Payroll History Detail: ", ['data' => $payrollHistoryDetail]);

            // Kirim data ke view
            return view('pages.payroll_history.detail', compact('payrollHistoryDetail'));
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
    




}
