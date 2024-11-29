<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;

class RunPayrollController extends Controller
{
    public function index()
    {
        return view('pages.run_payroll.index');
    }

    public function employee()
    {
        return view('pages.run_payroll.employee');
    }

    public function storeselectkar(Request $request)
    {
        $validatedData = $request->validate([
            'employees' => 'required|array',
            'employees.*.id' => 'required|exists:employees,id', 
            'employees.*.nama_lengkap' => 'required|string', 
            'employees.*.nomor_induk_karyawan' => 'required|string', 
        ]);

        $employeeIds = collect($validatedData['employees'])->pluck('id')->toArray();

        session(['selected_employee_ids' => $employeeIds]);

        return response()->json([
            'success' => true,
            'message' => 'ID karyawan berhasil disimpan ke dalam session.',
            'data' => session('selected_employee_ids'), 
        ]);
    }

    public function getSelectedEmployees()
    {
        $selectedEmployeeIds = session('selected_employee_ids', []);
        $employees = Employee::whereIn('id', $selectedEmployeeIds)->get();

        return response()->json([
            'success' => true,
            'data' => $employees,
        ]);
    }

}
