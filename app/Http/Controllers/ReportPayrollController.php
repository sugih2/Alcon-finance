<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PayrollHistory;
use App\Models\PayrollHistoryDetail;
use Illuminate\Support\Facades\Log;

class ReportPayrollController extends Controller
{
    public function index()
    {
        $datatransaksis = PayrollHistory::where('locking', true)->orderBy('start_periode', 'desc')
            ->get();
        //Log::info('data Loock:' . json_encode($datatransaksis, JSON_PRETTY_PRINT));
        return view('pages.report_payroll.index', compact('datatransaksis'));
    }
}
