<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\frontend\CustomerController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\VerificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\HandleRoleAdmin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//route frontend
Route::get('/', [CustomerController::class,'login'])->name('login');
Route::post('/', [CustomerController::class,'postLogin']);
Route::get('/register', [CustomerController::class,'register'])->name('register');
Route::post('/register', [CustomerController::class,'postRegister']);
Route::middleware(['auth', 'verified'])->get('/home', [HomeController::class, 'index'])->name('index');
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');


//route Admin
Route::get('/admin/login', [UserController::class, 'getLogin']) ->name('admin.login');
Route::post('/admin/login', [UserController::class, 'postLogin']) ->name('admin.login.post');
Route::get('/admin/logout', [UserController::class, 'getLogout']) ->name('admin.logout');

//Route prefix admin, middleware login admin
Route::prefix('admin')->middleware('handleLoginAdmin')->group(function () {
    Route::get('/dashboard', [DashBoardController::class, 'index'])->name('user.index');

    //Route Users
    Route::resource('/users', UserController::class)->middleware('handleRoleAdmin');

    //Route profile admin
    Route::get('/profile/show', [UserController::class, 'showProfileAdmin'])->name('admin.profile.show');
    Route::get('/profile/update', [UserController::class, 'showFormUpdateAdmin'])->name('admin.profile.update');
    Route::patch('/profile/update', [UserController::class, 'updateProfileAdmin']);

    //Route CRUD Room
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.index');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms/store', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

});

