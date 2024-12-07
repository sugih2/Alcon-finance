<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LastTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'last_transaksi_name',
        'last_transaction_id',
    ];
}
