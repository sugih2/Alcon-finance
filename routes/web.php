<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

use App\Http\Controllers\HomeController;
use App\Http\Controllers\SettingShiftController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ParamPositionController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RegencyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PresenceController;
use App\Http\Controllers\ParamComponenController;
use App\Http\Controllers\PraPayrollController;
use App\Http\Controllers\RunPayrollController;
use App\Http\Controllers\PayrollHistoryController;
use App\Models\SettingShift;

Route::get('/data', [ExampleController::class, 'getData'])->middleware('auth')->name('data.index');

Route::group(['prefix' => 'user-management', 'middleware' => 'auth'], function () {
	Route::get('/', [UserManagementController::class, 'index'])->middleware('auth')->name('user-management.index');
	Route::get('/users', [UserManagementController::class, 'getDataUser'])->name('users.data');
	Route::post('/users', [UserManagementController::class, 'storeUser'])->name('users.store');
	Route::put('/users/{user}', [UserManagementController::class, 'updateUser'])->name('users.update');
	Route::delete('/users/{user}', [UserManagementController::class, 'destroyUser'])->name('users.destroy');
	Route::post('/roles', [UserManagementController::class, 'storeRole'])->name('roles.store');
	Route::put('/roles/{role}', [UserManagementController::class, 'updateRole'])->name('roles.update');
	Route::delete('/roles/{role}', [UserManagementController::class, 'destroyRole'])->name('roles.destroy');
	Route::get('/roles/{role}/permissions', [UserManagementController::class, 'getPermissions'])->name('roles.permissions');
	Route::post('/roles/{role}/permissions/save', [UserManagementController::class, 'savePermissions'])->name('roles.permissions.save');
	Route::get('/menus', [UserManagementController::class, 'getDataMenu'])->name('menus.data');
	Route::post('/menus', [UserManagementController::class, 'storeDataMenu'])->name('menus.store');
	Route::get('/menus/{menu}', [UserManagementController::class, 'showDataMenu'])->name('menus.show');
	Route::put('/menus/{menu}', [UserManagementController::class, 'updateMenu'])->middleware('auth')->name('menus.update');
	Route::delete('/menus/{menu}', [UserManagementController::class, 'destroyMenu'])->name('menus.destroy');
});
Route::group(['prefix' => 'paramposition', 'middleware' => 'auth'], function () {
	Route::get('/', [ParamPositionController::class, 'index'])->middleware('auth')->name('paramposition.index');
	Route::get('/create', [ParamPositionController::class, 'create'])->name('paramposition.create');
	Route::post('/store', [ParamPositionController::class, 'store'])->name('paramposition.store');
	Route::get('/edit/{id}', [ParamPositionController::class, 'edit'])->name('paramposition.edit');
	Route::post('/storeedit/{id}', [ParamPositionController::class, 'storeEdit'])->name('paramposition.storeedit');
	Route::get('/list', [ParamPositionController::class, 'list'])->name('paramposition.list');
});

Route::group(['prefix' => 'position', 'middleware' => 'auth'], function () {
	Route::get('/', [PositionController::class, 'index'])->middleware('auth')->name('position.index');
	Route::post('/store', [PositionController::class, 'store'])->name('position.store');
	Route::get('/create', [PositionController::class, 'create'])->name('position.create');
	Route::get('/edit/{id}', [PositionController::class, 'edit'])->name('position.edit');
	Route::post('/storeedit/{id}', [PositionController::class, 'storeEdit'])->name('position.storeedit');
	Route::get('/list', [PositionController::class, 'list'])->name('position.list');
	Route::get('/get-position-name', [PositionController::class, 'getPositionName'])->name('position.name');
});

Route::group(['prefix' => 'project', 'middleware' => 'auth'], function () {
	Route::get('/', [ProjectController::class, 'index'])->middleware('auth')->name('project.index');
	Route::post('/store', [ProjectController::class, 'store'])->name('project.store');
	Route::get('/create', [ProjectController::class, 'create'])->name('project.create');
	Route::get('/edit/{id}', [ProjectController::class, 'edit'])->name('project.edit');
	Route::post('/storeedit/{id}', [ProjectController::class, 'storeEdit'])->name('project.storeedit');
	Route::get('/list', [ProjectController::class, 'list'])->name('project.list');
	Route::get('/get-project-name', [ProjectController::class, 'getProjectName'])->name('project.name');
});
Route::group(['prefix' => 'group', 'middleware' => 'auth'], function () {
	Route::get('/', [GroupController::class, 'index'])->middleware('auth')->name('group.index');
	Route::post('/store', [GroupController::class, 'store'])->name('group.store');
	Route::get('/create', [GroupController::class, 'create'])->name('group.create');
	Route::get('/edit/{id}', [GroupController::class, 'edit'])->name('group.edit');
});
Route::group(['prefix' => 'employee', 'middleware' => 'auth'], function () {
	Route::get('/', [EmployeeController::class, 'index'])->middleware('auth')->name('employee.index');
	Route::post('/store', [EmployeeController::class, 'store'])->name('employee.store');
	Route::get('/create', [EmployeeController::class, 'create'])->name('employee.create');
	Route::get('/edit/{id}', [EmployeeController::class, 'edit'])->name('employee.edit');
	Route::post('/storeedit/{id}', [EmployeeController::class, 'edit'])->name('employee.storeedit');
	Route::get('/list', [EmployeeController::class, 'list'])->name('employee.list');
	Route::get('/list/pekerja', [EmployeeController::class, 'list_pekerja'])->name('employee.list-member');
	Route::get('/list/kepala-pekerja', [EmployeeController::class, 'list_kepala_pekerja'])->name('employee.list-kepala-pekerja');
	Route::get('/get-employee-name', [EmployeeController::class, 'getEmployeeName'])->name('employee.name');
	Route::get('/employee-list', [EmployeeController::class, 'getEmployeeList'])->name('employee.getlist');
});
Route::group(['prefix' => 'regency', 'middleware' => 'auth'], function () {
	Route::get('/', [RegencyController::class, 'regency'])->name('regency.list');
	Route::get('/get-regency-name', [RegencyController::class, 'getRegencyName'])->name('regency.name');
});
Route::group(['prefix' => 'presence', 'middleware' => 'auth'], function () {
	Route::get('/', [PresenceController::class, 'index'])->middleware('auth')->name('presence.index');
	Route::post('/store', [PresenceController::class, 'store'])->name('presence.store');
	Route::get('/create', [PresenceController::class, 'create'])->name('presence.create');
	Route::get('/list', [PresenceController::class, 'list'])->name('presence.list');
	Route::get('/edit/{id}', [PresenceController::class, 'edit'])->name('presence.edit');
	Route::post('/update/{id}', [PresenceController::class, 'update'])->name('presence.update');
	Route::delete('/delete/{id}', [PresenceController::class, 'destroy'])->name('presence.delete');
	Route::post('/process-import', [PresenceController::class, 'processImport'])->name('presence.processImport');
	Route::post('/store-import', [PresenceController::class, 'storeImport'])->name('presence.storeImport');
});
Route::group(['prefix' => 'shift', 'middleware' => 'auth'], function () {
	Route::get('/', [SettingShiftController::class, 'index'])->middleware('auth')->name('shift.index');
	Route::get('/edit/{id}', [SettingShiftController::class, 'edit'])->name('shift.edit');
	Route::post('/update/{id}', [SettingShiftController::class, 'update'])->name('shift.update');
});
Route::group(['prefix' => 'componen', 'middleware' => 'auth'], function () {
	Route::get('/', [ParamComponenController::class, 'index'])->middleware('auth')->name('componen.index');
	Route::post('/store', [ParamComponenController::class, 'store'])->name('componen.store');
	Route::get('/create', [ParamComponenController::class, 'create'])->name('componen.create');
	Route::get('/edit/{id}', [ParamComponenController::class, 'edit'])->name('componen.edit');
	Route::post('/storeedit/{id}', [ParamComponenController::class, 'edit'])->name('componen.storeedit');
	Route::get('/list', [ParamComponenController::class, 'list'])->name('componen.list');
	Route::get('/get-componen-name', [ParamComponenController::class, 'getComponenName'])->name('componen.name');
	Route::get('/componen-list', [ParamComponenController::class, 'getComponentList'])->name('componen.getlist');
	Route::get('/getform/{componentType}', [ParamComponenController::class, 'getform'])->name('componen.getform');
});
Route::group(['prefix' => 'pra-payroll', 'middleware' => 'auth'], function () {
	Route::get('/', [PraPayrollController::class, 'index'])->middleware('auth')->name('prapayroll.index');
	Route::get('/index/detail', [PraPayrollController::class, 'indexDetail'])->name('prapayroll.index-detail');
	Route::post('/store', [PraPayrollController::class, 'store'])->name('prapayroll.store');
	Route::get('/create', [PraPayrollController::class, 'create'])->name('prapayroll.create');
	Route::get('/edit/{id}', [PraPayrollController::class, 'edit'])->name('prapayroll.edit');
	Route::get('/edit/detail/{id}', [PraPayrollController::class, 'editDetail'])->name('prapayroll.editDetail');
	Route::post('/storeedit/{id}', [PraPayrollController::class, 'edit'])->name('prapayroll.storeedit');
	Route::post('/storeedit/detail/{id}', [PraPayrollController::class, 'edit'])->name('prapayroll.storeedit');
	Route::get('/list', [PraPayrollController::class, 'list'])->name('prapayroll.list');
	Route::get('/get-componen-name', [PraPayrollController::class, 'getComponenName'])->name('prapayroll.name');
});
Route::group(['prefix' => 'adjusment', 'middleware' => 'auth'], function () {
	Route::get('/', [PraPayrollController::class, 'adjusment'])->middleware('auth')->name('adjusment.index');
	Route::post('/store', [PraPayrollController::class, 'store'])->name('adjusment.store');
	Route::get('/create', [PraPayrollController::class, 'create'])->name('adjusment.create');
	Route::get('/edit/{id}', [PraPayrollController::class, 'edit'])->name('adjusment.edit');
	Route::post('/storeedit/{id}', [PraPayrollController::class, 'edit'])->name('adjusment.storeedit');
	Route::get('/list', [PraPayrollController::class, 'list'])->name('adjusment.list');
	Route::get('/get-componen-name', [PraPayrollController::class, 'list-name'])->name('adjusment.name');
	Route::get('/employee', [PraPayrollController::class, 'employee'])->name('adjusment.employee');
	Route::get('/component', [PraPayrollController::class, 'component'])->name('adjusment.component');
	Route::post('/store-employee', [PraPayrollController::class, 'storeselectkar'])->name('adjusment.storeselectkar');
	Route::post('/store-component', [PraPayrollController::class, 'storeselectcom'])->name('adjusment.storeselectcom');
	Route::post('/store-adjusment', [PraPayrollController::class, 'storeadjusment'])->name('adjusment.storeadjusment');
});
Route::group(['prefix' => 'run-payroll', 'middleware' => 'auth'], function () {
	Route::get('/', [RunPayrollController::class, 'index'])->middleware('auth')->name('runpayroll.index');
	Route::post('/store', [RunPayrollController::class, 'store'])->name('runpayroll.store');
	Route::get('/employee', [RunPayrollController::class, 'employee'])->name('runpayroll.employee');
	Route::get('/get-selected-employees', [RunPayrollController::class, 'getSelectedEmployees'])->name('runpayroll.employee');
	Route::post('/store-employee', [RunPayrollController::class, 'storeselectkar'])->name('runpayroll.storeselectkar');
});
Route::group(['prefix' => 'history-payroll', 'middleware' => 'auth'], function () {
	Route::get('/', [PayrollHistoryController::class, 'index'])->middleware('auth')->name('historypayroll.index');
	Route::post('/store', [PayrollHistoryController::class, 'store'])->name('historypayroll.store');
	Route::get('/employee', [PayrollHistoryController::class, 'employee'])->name('historypayroll.');
	Route::get('/detail/{id}', [PayrollHistoryController::class, 'showDetails'])->middleware('auth')->name('historypayroll.detail');
});
Route::group(['prefix' => 'history-payroll-detail', 'middleware' => 'auth'], function () {
	Route::get('/{id}', [PayrollHistoryController::class, 'showDetails'])->middleware('auth')->name('historypayrollDetail.index');
});











Route::get('/', function () {
	return redirect('/dashboard');
})->middleware('auth');
Route::get('/register', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('/register', [RegisterController::class, 'store'])->middleware('guest')->name('register.perform');
Route::get('/login', [LoginController::class, 'show'])->middleware('guest')->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest')->name('login.perform');
Route::get('/reset-password', [ResetPassword::class, 'show'])->middleware('guest')->name('reset-password');
Route::post('/reset-password', [ResetPassword::class, 'send'])->middleware('guest')->name('reset.perform');
Route::get('/change-password', [ChangePassword::class, 'show'])->middleware('guest')->name('change-password');
Route::post('/change-password', [ChangePassword::class, 'update'])->middleware('guest')->name('change.perform');
Route::get('/dashboard', [HomeController::class, 'index'])->name('home')->middleware('auth');
Route::group(['middleware' => 'auth'], function () {
	Route::get('/example', [PageController::class, 'example'])->name('example');
	Route::get('/virtual-reality', [PageController::class, 'vr'])->name('virtual-reality');
	Route::get('/rtl', [PageController::class, 'rtl'])->name('rtl');
	Route::get('/profile', [UserProfileController::class, 'show'])->name('profile');
	Route::post('/profile', [UserProfileController::class, 'update'])->name('profile.update');
	Route::get('/profile-static', [PageController::class, 'profile'])->name('profile-static');
	Route::get('/sign-in-static', [PageController::class, 'signin'])->name('sign-in-static');
	Route::get('/sign-up-static', [PageController::class, 'signup'])->name('sign-up-static');
	Route::get('/{page}', [PageController::class, 'index'])->name('page');
	Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});
