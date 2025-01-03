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
            'position_id' => $employee->id,
        ]);
    }

    public function update(Request $request, $id)
    {
        log::info('Cik Nempo Data:', $request->all());
        $validator = Validator::make($request->all(), [
            'nip' => "max:10|unique:employees,nip,$id",
            'nik' => "max:16|unique:employees,nik,$id",
            'name' => 'string|max:25',
            'birth_date' => 'date',
            'address' => 'string',
            'email' => "email|unique:employees,email,$id",
            'phone' => 'max:13',
            'position' => 'integer',
            // 'status' => 'in:Aktif,NonAktif',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $employee = Employee::findOrFail($id);
            log::info('Cik Nempo Data euy:', ['cek' => $employee]);
            $employee->update([
                'nip' => $request->nip,
                'nik' => $request->nik,
                'name' => $request->name,
                'birth_date' => $request->birth_date,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'position_id' => $request->position,
                // 'status' => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'employee Berhasil',
                'data' => $employee,
            ], 200);
        } catch (\Exception $e) {
            Log::error("Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal Edit Setting Shift',
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Aktif,NonAktif',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $employee = Employee::findOrFail($id);
            $employee->status = $request->status;
            $employee->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diubah',
                'data' => $employee
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function storeEdit($id)
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
            'position' => 'required|integer',
            'status' => 'in:Aktif,NonAktif'
        ]);

        // Jika validasi gagal, kirim response error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors'  => $validator->errors()
            ], 422);
        }
        log::info("data : ", $request->all());
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
                'position_id' => $request->position
                // 'status' => $request->status
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
        $employees = Employee::select('id', 'name', 'nip', 'status')->get();
        return response()->json($employees);
    }

    public function list_pekerja()
    {
        $employees = Employee::whereHas('position.paramPosition', function ($query) {
            $query->where('name', 'PEKERJA');
        })
            ->orderBy('name', 'asc')  // Mengurutkan berdasarkan nama karyawan dari A-Z
            ->get();

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

    public function getEmployeeList(Request $request)
    {
        //Log::info("Request: " . json_encode($request->all()));

        $employees = Employee::with(['position:id,name'])
            ->select('id', 'name', 'nip', 'position_id', 'status')
            ->orderBy('name', 'asc')
            ->distinct()
            ->get();


        $employees = $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'nama_lengkap' => $employee->name,
                'nomor_induk_karyawan' => $employee->nip,
                'jabatan_nama' => $employee->position->name,
                'status' => $employee->status
            ];
        });
        return response()->json($employees);
    }
}
