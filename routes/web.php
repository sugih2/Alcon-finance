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
use App\Http\Controllers\PageController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\ResetPassword;
use App\Http\Controllers\ChangePassword;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;           
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ParamPositionController;
use App\Http\Controllers\PositionController;

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
	Route::delete('/roles/{role}', [UserManagementController::class, 'destroyRole'])->name('roles.destroy');
	Route::get('/menus', [UserManagementController::class, 'getDataMenu'])->name('menus.data');
	Route::post('/menus', [UserManagementController::class, 'storeDataMenu'])->name('menus.store');
	Route::get('/menus/{menu}', [UserManagementController::class, 'showDataMenu'])->name('menus.show');
	Route::put('/menus/{menu}', [UserManagementController::class, 'updateMenu'])->middleware('auth')->name('menus.update');
	Route::delete('/menus/{menu}', [UserManagementController::class, 'destroyMenu'])->name('menus.destroy');
});
Route::group(['prefix' => 'paramposition', 'middleware' => 'auth'], function () {
	Route::get('/', [ParamPositionController::class, 'index'])->middleware('auth')->name('paramposition.index');
	Route::post('/store', [ParamPositionController::class, 'store'])->name('paramposition.store');
	Route::get('/position/create', [PositionController::class, 'create'])->name('position.create');
});

Route::group(['prefix' => 'position', 'middleware' => 'auth'], function () {
	Route::get('/', [PositionController::class, 'index'])->middleware('auth')->name('position.index');
	Route::post('/store', [PositionController::class, 'store'])->name('position.store');
	Route::get('/create', [PositionController::class, 'create'])->name('position.create');
});






Route::get('/', function () {return redirect('/dashboard');})->middleware('auth');
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