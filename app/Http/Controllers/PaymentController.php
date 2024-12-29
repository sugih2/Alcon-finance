<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Models\PayrollHistory;
use App\Models\PayrollProcessDetail;

class PaymentController extends Controller
{
    public function index($id)
    {
        $payrollHistory = PayrollHistory::select('id')->find($id);
        return view('pages.payment.index', compact('payrollHistory'));
    }
    
    public function process(Request $request, $id)
    {
        Log::info("Request: " . json_encode($request->all()));

        $validator = Validator::make($request->all(), [
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:transfer,cash',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors(),
            ], 422);
        }

        $payrollHistory = PayrollHistory::find($id);
        if (!$payrollHistory) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll history tidak ditemukan.',
            ], 404);
        }

        if (!$payrollHistory->locking) {
            return response()->json([
                'success' => false,
                'message' => 'Payroll history belum terkunci, tidak dapat diproses.',
            ], 403); // 403 Forbidden
        }

        // Cek apakah sudah ada data di PayrollProcessDetail untuk payroll ini
        $existingDetail = PayrollProcessDetail::where('id_payroll_history', $id)->first();
        if ($existingDetail) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi untuk payroll ini sudah ada.',
            ], 409); // 409 Conflict
        }

        DB::beginTransaction();
        try {
            // Buat data di PayrollProcessDetail
            $payrollProcessDetail = PayrollProcessDetail::create([
                'id_payroll_history' => $id,
                'payment_date' => $request->payment_date,
                'payment_amount' => $request->payment_amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'success', // Status otomatis sukses
                'processed_by' => auth()->user()->id, // User yang sedang login
            ]);

            // Update status_payroll di PayrollHistory
            $payrollHistory->update([
                'status_payroll' => 'Payment',
            ]);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses.',
                'data' => $payrollProcessDetail,
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error processing payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses pembayaran.',
            ], 500);
        }
    }



}
