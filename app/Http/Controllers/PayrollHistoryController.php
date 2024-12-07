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
        $payrollHistoryDetail = PayrollHistory::with(['detailPayroll.employee'])
            ->findOrFail($id);

        foreach ($payrollHistoryDetail->detailPayroll as $detail) {
            $detail->allowance = json_decode($detail->allowance, true) ?? [];
            $detail->deduction = json_decode($detail->deduction, true) ?? [];
        }

        Log::info("Payroll History Detail: " . json_encode($payrollHistoryDetail, JSON_PRETTY_PRINT));

        return view('pages.payroll_history.detail', compact('payrollHistoryDetail'));
    }

}
