<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Regency;

class RegencyController extends Controller
{
    public function regency()
    {
        $regencies = Regency::all();
        return response()->json($regencies);
    }
}
