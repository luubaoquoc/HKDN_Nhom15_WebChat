<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\frontend\CustomerController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\VerificationController;
use App\Http\Controllers\frontend\UserProfileController;
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

//route home
Route::get('/', [CustomerController::class,'login'])->name('login');
Route::post('/', [CustomerController::class,'postLogin']);
Route::get('/register', [CustomerController::class,'register'])->name('register');
Route::post('/register', [CustomerController::class,'postRegister']);
Route::middleware(['auth', 'verified'])->get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');


//route Room
// Route::middleware(['auth', 'verified'])->get('/rooms', [HomeController::class, 'loadRooms'])->name('rooms');
Route::middleware(['auth', 'verified'])->post('/create-room', [HomeController::class, 'createRoom'])->name('createRoom');
Route::middleware(['auth', 'verified'])->post('/add-member', [HomeController::class, 'addMembers'])->name('addMembers');
Route::post('/save-room-chat', [HomeController::class,'saveRoomChat'])->name('saveRoomChat');
Route::post('/load-room-chats', [HomeController::class,'loadRoomChats'])->name('loadRoomChats');
Route::post('/delete-room-chats', [HomeController::class,'deleteRoomChats'])->name('deleteRoomChats');
Route::get('/room-members', [HomeController::class, 'showRoomMembers'])->name('showRoomMembers');
Route::post('/delete-room-chats', [HomeController::class,'deleteRoomChats'])->name('deleteRoomChats');
Route::post('/remove-member', [HomeController::class, 'removeMember'])->name('removeMember');
Route::post('/add-members', [HomeController::class, 'addMembers1'])->name('add.members1');


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

});

