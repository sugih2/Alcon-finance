<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        return view('pages.employee.employee', compact('employees'));
    }

    public function create()
    {
        return view('pages.employee.create');
    }
    
    public function store(Request $request)
    {
        // Log input request
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25',
            'nip' => 'required|max:10|unique:employees,nip',
            'position' => 'required|integer'
        ]);

        // Jika validasi gagal, kirim response error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Buat dan simpan data ke model
            $employees = Employee::create([
                'name' => $request->name,
                'nip' => $request->nip,
                'position_id' => $request->position,
            ]);

            // Log data yang berhasil disimpan
            Log::info("Berhasil Menyimpan: " . json_encode($employees));

            // Kirim response sukses
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data'    => $employees
            ], 201);

        } catch (\Exception $e) {
            // Log error jika terjadi masalah
            Log::error("Error: " . $e->getMessage());

            // Kirim response gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ], 500);
        }
    }

    public function list()
    {
        $employees = Employee::select('id', 'name', 'nip')->get();
        return response()->json($employees);
    }

    public function getEmployeName(Request $request)
    {
        $employees = Employee::find($request->leader_id);
        return response()->json(['name' => $employees->name]);
    }
}
