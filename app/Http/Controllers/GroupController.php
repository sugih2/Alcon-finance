<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Position;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class GroupController extends Controller
{
    public function index()
    {
        $groups = Group::all();
        return view('pages.group.group', compact('groups'));
    }

    public function create()
    {
        return view('pages.group.create');
    }

    public function edit($id)
    {
        $groups = Group::find($id);
        $html = view('pages.group.edit', compact('groups'))->render();

        return response()->json([
            'html' => $html,
            'project_id' => $groups->project_id,
            'leader_id' => $groups->leader_id,
        ]);
    }


    public function store(Request $request)
    {
        // Log input request
        Log::info("Requestss: " . json_encode($request->all()));

        // Validasi input dengan Validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25',
            'code' => 'required|max:10|unique:groups,code',
            'project' => 'required|integer',
            'leader' => 'required|integer',
            'members' => 'required|array',
            'members.*' => 'integer|exists:employees,id'
        ]);

        // Jika validasi gagal, kirim response error
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors()
            ], 422);
        }

        DB::beginTransaction(); // Mulai transaksi

        try {
            // Cek apakah leader_id sudah ada di grup lain
            $groupExist = Group::where('leader_id', $request->leader)->exists();

            if ($groupExist) {
                // Jika leader_id sudah ada, kembalikan response error
                return response()->json([
                    'error' => 'Leader Grup Sudah ada.'
                ], 400);
            }

            // Buat dan simpan data grup
            $group = Group::create([
                'name' => $request->name,
                'code' => $request->code,
                'project_id' => $request->project,
                'leader_id' => $request->leader,
            ]);

            // Log data grup yang berhasil disimpan
            Log::info("Berhasil Menyimpan Group: " . json_encode($group));

            // Cek apakah anggota sudah ada di grup lain
            foreach ($request->members as $member_id) {
                // Periksa apakah anggota sudah terdaftar di grup lain
                $memberInOtherGroup = GroupMember::where('member_id', $member_id)
                    ->whereHas('group', function ($query) use ($request) {
                        // Memastikan grup yang memiliki member_id bukan grup yang baru saja dibuat
                        $query->where('leader_id', '!=', $request->leader);
                    })
                    ->exists();

                if ($memberInOtherGroup) {
                    // Jika anggota sudah ada di grup lain
                    return response()->json([
                        'error' => 'Anggota sudah terdaftar di grup lain.'
                    ], 400);
                }
            }

            // Simpan anggota ke dalam tabel GroupMember
            foreach ($request->members as $member_id) {
                GroupMember::create([
                    'group_id' => $group->id,
                    'member_id' => $member_id,
                ]);
            }

            DB::commit(); // Selesaikan transaksi jika semua berhasil

            // Kirim response sukses
            return response()->json([
                'success' => true,
                'message' => 'Data grup dan anggota berhasil disimpan',
                'data'    => $group
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan transaksi jika terjadi kesalahan
            Log::error("Error: " . $e->getMessage());

            // Kirim response gagal
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function ListGroup($id)
    {
        $groupMember = GroupMember::where('group_id', $id)->get();
        $getMemberId = $groupMember->pluck('member_id');
        // $cekId = $groupMember->member_id;
        // log::info("EKhm", ['cek' => $cekId]);
        $seleectEmployee = Employee::wherein('id', $getMemberId)
            ->get();
        $selectPositionId = $seleectEmployee->pluck('position_id');
        $selectPosition = Position::whereIn('id', $selectPositionId)->get();
        log::info("CEK" . json_encode($selectPosition, JSON_PRETTY_PRINT));
        return response()->json([
            'dataEmployes' => $seleectEmployee,
            'dataPosition' => $selectPosition
        ]);
    }
}
