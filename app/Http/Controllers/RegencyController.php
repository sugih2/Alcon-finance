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
    
    public function getRegencyName(Request $request)
    {
        $regency = Regency::find($request->regency_id);
        return response()->json(['name' => $regency->name]);
    }
}
