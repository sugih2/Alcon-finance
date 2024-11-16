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

    public function edit($id)
    {
        $employee = Employee::with('position')->find($id);
        $html = view('pages.employee.edit', compact('employee'))->render();

        return response()->json([
            'html' => $html,
            'position_id' => $employee->position_id,
        ]);
    }
    
    public function store(Request $request)
    {
        // Log input request
        Log::info("Request: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'nip' => 'required|max:10|unique:employees,nip',
            'nik' => 'required|max:16|unique:employees,nik',
            'name' => 'required|string|max:25',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|max:13',
            'position' => 'required|integer'
        ]);

        // Jika validasi gagal, kirim response error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }

        try {
            // Buat dan simpan data ke model
            $employees = Employee::create([
                'nip' => $request->nip,
                'nik' => $request->nik,
                'name' => $request->name,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
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

    public function list_pekerja()
    {
        $employees = Employee::whereHas('position.paramPosition', function ($query) {
            $query->where('name', 'PEKERJA');
        })->get();

        return response()->json($employees);
    }

    public function list_kepala_pekerja()
    {
        $employees = Employee::whereHas('position.paramPosition', function ($query) {
            $query->where('name', 'KEPALA PEKERJA');
        })->get();

        return response()->json($employees);
    }

    public function getEmployeeName(Request $request)
    {
        $employees = Employee::find($request->leader_id);
        return response()->json(['name' => $employees->name]);
    }


}
