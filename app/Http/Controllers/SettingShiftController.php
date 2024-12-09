<?php

namespace App\Http\Controllers;

use App\Models\SettingShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class SettingShiftController extends Controller
{
    public function index()
    {
        $shifts = SettingShift::get(); // Asumsikan ada relasi dengan tabel karyawan
        return view('pages.shift.index', compact('shifts'));
    }
    public function edit($id)
    {
        $shifts = SettingShift::find($id);
        $html = view('pages.shift.edit', compact('shifts'))->render();
        return response()->json([
            'html' => $html,
            'shift_id' => $shifts->id,
        ]);
    }
    public function update(Request $request, $id)
    {
        log::info('Cik Nempo Data:', $request->all());
        $validator = Validator::make($request->all(), [
            // 'employed_id' => 'required|integer|exists:employees,id',
            'jam_masuk' => 'nullable|date_format:H:i:s',
            'jam_pulang' => 'nullable|date_format:H:i:s',
            'awal_masuk' => 'nullable|date_format:H:i:s',
            'maks_late' => 'nullable|date_format:H:i:s',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $setting = SettingShift::findOrFail($id);
            log::info('Cik Nempo Data euy:', ['cek' => $setting]);
            $setting->update([
                // 'employed_id' => $request->employed_id,
                'jam_masuk' => $request->jam_masuk,
                'jam_pulang' => $request->jam_pulang,
                'awal_masuk' => $request->awal_masuk,
                'maks_late' => $request->maks_late,
                // 'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Setting Shift Berhasil',
                'data' => $setting,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal Edit Setting Shift',
            ], 500);
        }
    }
}
