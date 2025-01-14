<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\ParamComponen;
use App\Models\MasterPayroll;
use App\Models\DetailPayroll;
use App\Models\Group;
use App\Models\GroupMember;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PraPayrollController extends Controller
{
    public function index()
    {
        $masterPayrolls = MasterPayroll::all();
        $detailPayrolls = DetailPayroll::all();
        return view('pages.pra_payroll.index', compact('masterPayrolls', 'detailPayrolls'));
    }

    public function indexDetail()
    {
        $detailPayrolls = DetailPayroll::with(['employee', 'component'])
            ->get()
            ->groupBy('id_employee');

        $detailPayrolls->each(function ($payrollGroup, $employeeId) {
            $totalAmount = $payrollGroup->sum('amount');
            $leaderGroups = Group::where('leader_id', $employeeId)->pluck('name');
            $memberGroups = Group::whereHas('members', function ($query) use ($employeeId) {
                $query->where('member_id', $employeeId);
            })->pluck('name');
            $groups = $leaderGroups->merge($memberGroups)->unique();

            $payrollGroup->each(function ($payroll) use ($groups, $totalAmount) {
                $payroll->groups = $groups;
                $payroll->total_amount = $totalAmount;
            });
        });

        Log::info("Data :" . json_encode($detailPayrolls, JSON_PRETTY_PRINT));
        return view('pages.pra_payroll.detail', compact('detailPayrolls'));
    }
    
    public function adjusment()
    {
        return view('pages.adjusment.index');
    }

    public function employee()
    {
        return view('pages.adjusment.employee');
    }
    public function component()
    {
        return view('pages.adjusment.component');
    }
    public function list()
    {
        $presences = ParamComponen::select('id', 'name', 'category')->get();
        return response()->json($presences);
    }

    public function storeselectkar(Request $request)
    {
        $employeeData = $request->all();
        $finalEmployeeData = [];
        foreach ($employeeData as $employee) {
            $employeeId = $employee['id'];

            $karyawan = Employee::findOrFail($employeeId);

            $finalEmployeeData[] = [
                'id' => $karyawan->id,
                'nama_lengkap' => $karyawan->name,
                'nomor_induk_karyawan' => $karyawan->nip,
                'jabatan' => $karyawan->position->name,
            ];
        }
        session()->put('selectedEmployees', $finalEmployeeData);

        return response()->json($finalEmployeeData);
    }

    public function storeselectcom(Request $request)
    {
        $components = $request->all();
        $employeeData = session()->get('selectedEmployees');

        if (!$employeeData) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'No employees selected.',
                ],
                400,
            );
        }

        foreach ($components as $componentReq) {
            if (isset($componentReq['id_com'])) {
                $componentId = $componentReq['id_com'];

                $componentData = ParamComponen::select('name', 'type', 'amount', 'componen')->findOrFail($componentId);

                foreach ($employeeData as &$employee) {
                    $employee['componentData'][] = [
                        'id_com' => $componentId,
                        'nama' => $componentData->name,
                        'komponen' => $componentData->type,
                        'nilai' => $componentData->amount,
                    ];
                }
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Missing id_com in request data',
                        'errorData' => $componentReq,
                    ],
                    400,
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Components successfully stored.',
            'employees' => $employeeData,
        ]);
    }

    public function storeadjusment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'efektif_date' => 'required|date',
            'description' => 'nullable|string',
            'employeedata' => 'required|array',
            'employeedata.*.id' => 'required|integer|exists:employees,id',
            'employeedata.*.nomor_induk_karyawan' => 'required|string',
            'employeedata.*.nama_lengkap' => 'required|string',
            'employeedata.*.jabatan' => 'required|string',
            'employeedata.*.components' => 'required|array',
            'employeedata.*.components.*.id_com' => 'required|integer|exists:param_componens,id',
            'employeedata.*.components.*.component_name' => 'required|string',
            'employeedata.*.components.*.last_amount' => 'required|numeric',
            'employeedata.*.components.*.new_amount' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $errorMessages = [];

            foreach ($errors as $field => $errorMessagesForField) {
                $errorMessages[$field] = $errorMessagesForField[0];
            }

            return response()->json(['error' => $errorMessages], 400);
        }
        // Log::info("Request Data Input: " . json_encode($request->all(), JSON_PRETTY_PRINT));
        try {
            DB::beginTransaction();

            foreach ($request->employeedata as $employee) {
                $employeeModel = Employee::find($employee['id']);
                $validationErrors = [];

                if (!$employeeModel) {
                    $validationErrors[] = "Data karyawan dengan ID {$employee['id']} tidak ditemukan.";
                } else {
                    foreach ($employee['components'] as $component) {
                        $componentModel = ParamComponen::find($component['id_com']);
                        if (!$componentModel) {
                            $validationErrors[] = "Komponen dengan ID {$component['id_com']} tidak ditemukan.";
                            continue;
                        }

                        // Validasi kecocokan posisi untuk Salary
                        if ($componentModel->componen === 'Salary' && $employeeModel->position_id !== $componentModel->id_position) {
                            $validationErrors[] = "Komponen Salary dengan ID {$component['component_name']} tidak sesuai dengan posisi karyawan {$employee['nama_lengkap']}.";
                        }

                        // Validasi apakah Salary sudah ada
                        $existingSalary = DetailPayroll::where('id_employee', $employee['id'])
                            ->whereHas('component', function ($query) {
                                $query->where('componen', 'Salary');
                            })
                            ->exists();

                        if ($existingSalary && $componentModel->componen === 'Salary') {
                            $validationErrors[] = "Komponen Salary sudah ada untuk karyawan ID {$employee['nama_lengkap']}.";
                        }

                        // Validasi kategori Allowance
                        if ($componentModel->type === 'Allowance') {
                            $existingAllowance = DetailPayroll::where('id_employee', $employee['id'])
                                ->whereHas('component', function ($query) use ($componentModel) {
                                    $query->where('type', 'Allowance')->where('category', $componentModel->category);
                                })
                                ->exists();

                            if ($existingAllowance) {
                                $validationErrors[] = "Allowance dengan kategori {$componentModel->category} sudah ada untuk karyawan ID {$employee['id']}.";
                            }
                        }
                    }
                }

                // Jika ada error validasi, kembalikan respons error 400
                if (!empty($validationErrors)) {
                    return response()->json(['error' => $validationErrors], 400);
                }
            }

            $prefix = 'PP';
            $id_transaksi = MasterPayroll::generateIdTransaksi($prefix);

            $settingTunjangan = MasterPayroll::create([
                'id_transaksi' => $id_transaksi,
                'type' => $request->type,
                'efektif_date' => $request->efektif_date,
                'description' => $request->description,
                'end_date' => $request->end_date,
            ]);

            foreach ($request->employeedata as $employee) {
                foreach ($employee['components'] as $component) {
                    $amount = isset($component['new_amount']) && !empty($component['new_amount']) ? $component['new_amount'] : $component['last_amount'];

                    DetailPayroll::create([
                        'id_employee' => $employee['id'],
                        'id_component' => $component['id_com'],
                        'id_transaksi' => $settingTunjangan->id_transaksi,
                        'amount' => $amount,
                    ]);
                }
            }

            DB::commit();
            session()->forget('selectedEmployees');
            return response()->json(['message' => 'Data Param Payroll berhasil ditambahkan'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function editDetail($id)
    {
        $details = DetailPayroll::find($id);
        $paramComponent = ParamComponen::where('category', $details->component->category)->first();
        $paramComponents = ParamComponen::where('id', $details->id_component)->first();
        // log::info("CEK AMOUNT : ", ['CEk' => $paramComponents]);
        $html = view('pages.pra_payroll.editDetail', compact('details', 'paramComponents'))->render();

        return response()->json([
            'html' => $html,
            'detail_id' => $details->id,
            'param_name' => $paramComponent->name,
            'category' => $paramComponent->category,
            'amount' => $paramComponent->amount,
        ]);
    }

    public function updateDetail(Request $request, $id)
    {
        log::info('Cek Request update: ' . json_encode($request, JSON_PRETTY_PRINT));
        $validator = Validator::make($request->all(), []);
        $idEmployee = Employee::where('name', $request->employee_name)->first();
        $getIdEmployee = $idEmployee->id;
        log::info('cek nama : ', ['cek' => $request->component]);
        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }
        try {
            $updateDetail = DetailPayroll::findOrFail($id);
            $newAmount = $request->new_amount ? $request->new_amount : $request->amount;
            $updateDetail->update([
                'id_transaksi' => $request->id_transaksi,
                'id_employee' => $getIdEmployee,
                'id_component' => $request->component,
                'amount' => $newAmount,
            ]);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Data detailpayroll berhasil disimpan',
                    'data' => $updateDetail,
                ],
                201,
            );
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menyimpan data detail prapayroll',
                ],
                500,
            );
        }
    }
    public function destroy($id)
    {
        try {
            $deleteDetail = DetailPayroll::findOrFail($id);
            $deleteDetail->delete();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Data Detail Payroll berhasil dihapus',
                ],
                200,
            );
        } catch (\Exception $e) {
            Log::error('Error: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Gagal menghapus data Detail Payroll',
                ],
                500,
            );
        }
    }

    public function getEmployeeGroups($employeeId)
    {
        $leaderGroups = Group::where('leader_id', $employeeId)->get();

        $memberGroups = Group::whereHas('members', function ($query) use ($employeeId) {
            $query->where('member_id', $employeeId);
        })->get();

        $employeeGroups = $leaderGroups->merge($memberGroups);

        return $employeeGroups;
    }

}
