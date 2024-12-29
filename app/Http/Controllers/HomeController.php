<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Group;
use App\Models\Project;
use Illuminate\Http\Request;

class HomeController extends Controller
{
        /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $totalKaryawan = Employee::where('status', 'Aktif')->count();
        $totalGroup = Group::count();
        $totalProject = Project::count();
        return view('pages.dashboard', compact('totalKaryawan', 'totalGroup', 'totalProject'));
    }

}
