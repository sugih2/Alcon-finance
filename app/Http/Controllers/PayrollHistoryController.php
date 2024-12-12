<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollHistory;
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
            // Ambil data payroll history berdasarkan ID
            $payrollHistoryDetail = PayrollHistory::with(['detailPayroll.employee'])
                ->findOrFail($id);

            // Ubah allowance dan deduction menjadi array jika berbentuk JSON
            foreach ($payrollHistoryDetail->detailPayroll as $detail) {
                $detail->allowance = json_decode($detail->allowance, true) ?? [];
                $detail->deduction = json_decode($detail->deduction, true) ?? [];
            }

            // Log detail data untuk debugging
            Log::info("Payroll History Detail: " . json_encode($payrollHistoryDetail, JSON_PRETTY_PRINT));

            // Kirim data ke view
            return view('pages.payroll_history.detail', compact('payrollHistoryDetail'));
        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error("Error fetching payroll history detail: " . $e->getMessage());
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }
    }


}
