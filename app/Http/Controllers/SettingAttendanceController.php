<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SettingAttendance;

class SettingAttendanceController extends Controller
{
    public function index()
    {
        $setattendance = SettingAttendance::all();
        return view('pages.setting_attendance.index', compact('setattendance'));
    }

    public function edit($id)
    {
        $shift = SettingAttendance::find($id);

        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        return response()->json(['data' => $shift], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'min_minutes' => 'required|integer',
            'max_minutes' => 'required|integer',
            'deduction_type' => 'required|string',
            'deduction_value' => 'required|numeric',
        ]);

        $shift = SettingAttendance::find($id);

        if (!$shift) {
            return response()->json(['message' => 'Shift not found'], 404);
        }

        $shift->update($request->all());

        return response()->json(['message' => 'Shift updated successfully'], 200);
    }
}
