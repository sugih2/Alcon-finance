<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Presence;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PresenceController extends Controller
{
    public function index()
    {
        $presences = Presence::with('employee')->get(); // Asumsikan ada relasi dengan tabel karyawan
        return view('pages.presence.presence', compact('presences'));
    }

    public function create()
    {
        return view('pages.presence.create');
    }

    public function edit($id)
    {
        $presence = Presence::find($id);
        $html = view('pages.presence.edit', compact('presence'))->render();

        return response()->json([
            'html' => $html,
            'presence_id' => $presence->id,
        ]);
    }

    public function store(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id', // Validasi karyawan
            'date' => 'required|date',
            'status' => 'required|string|in:Present,Absent,Sick,Leave', // Contoh status presensi
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            // Simpan data presensi
            $presence = Presence::create([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil disimpan',
                'data' => $presence,
            ], 201);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data presensi',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|integer|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|string|in:Present,Absent,Sick,Leave',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $presence = Presence::findOrFail($id);
            $presence->update([
                'employee_id' => $request->employee_id,
                'date' => $request->date,
                'status' => $request->status,
                'remarks' => $request->remarks,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil diupdate',
                'data' => $presence,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate data presensi',
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $presence = Presence::findOrFail($id);
            $presence->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil dihapus',
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data presensi',
            ], 500);
        }
    }

    public function list()
    {
        $presences = Presence::select('id', 'employee_id', 'date', 'status')->get();
        return response()->json($presences);
    }
}