<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PayrollHistory extends Model
{
    use HasFactory;
    protected $fillable = ['id_transaksi_payment', 'start_periode', 'end_periode', 'amount_transaksi', 'total_karyawan', 'status_payroll', 'description'];

    public function detailPayroll()
    {
        return $this->hasMany(PayrollHistoryDetail::class, 'id_payroll_history', 'id');
    }

    public static function generateIdTransaksiPayment($prefix)
    {
        return DB::transaction(function () use ($prefix) {
            $today = Carbon::now()->format('dmY');
            $prefixWithDate = $prefix . $today;

            $lastTransaction = LastTransaction::firstOrCreate(
                ['last_transaksi_name' => 'PayrollHistory'],
                ['last_transaction_id' => 0]
            );

            if ($lastTransaction->wasRecentlyCreated) {
                $lastGlobalId = 1;
            } else {
                $lastGlobalId = intval($lastTransaction->last_transaction_id) + 1;

                if ($lastGlobalId > 9999) {
                    $lastGlobalId = 1;
                }
            }

            $lastTransaction->last_transaction_id = $lastGlobalId;
            $lastTransaction->save();

            $finalId = $prefixWithDate . str_pad($lastGlobalId, 4, '0', STR_PAD_LEFT);

            return $finalId;
        });
    }
}
