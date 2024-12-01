<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class MasterPayroll extends Model
{
    use HasFactory;
    protected $fillable = ['id_transaksi', 'type', 'efektif_date', 'end_date', 'description'];

    public static function generateIdTransaksi($prefix)
    {
        $today = Carbon::now()->format('Ymd');
        $randomString = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, 6);
        return $prefix . $today . $randomString;
    }

    public function detailPayroll()
    {
        return $this->hasMany(DetailPayroll::class, 'id_transaksi', 'id_transaksi');
    }
}
