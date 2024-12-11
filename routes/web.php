<?php

use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\frontend\CustomerController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\MessagePinnedApi;
use App\Http\Controllers\frontend\UserProfileController;
use App\Http\Controllers\frontend\VerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\frontend\ProfileController;
use App\Http\Controllers\frontend\ForgotPasswordController;
use App\Http\Controllers\Admin\RoomController;

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
Route::get('/', [CustomerController::class, 'login'])->name('login');
Route::post('/', [CustomerController::class, 'postLogin']);
Route::get('/register', [CustomerController::class, 'register'])->name('register');
Route::post('/register', [CustomerController::class, 'postRegister']);
Route::middleware(['auth', 'verified'])->get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
Route::get('/email/verify/{id}', [VerificationController::class, 'verify'])->name('verification.verify');
Route::get('/logout', [CustomerController::class, 'getLogout']) ->name('logout');
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password/{token}', [ForgotPasswordController::class, 'reset'])->name('password.update');


// home
Route::get('/home', [HomeController::class, 'index'])->name('home');


// profile frontend
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');


    // room routes
   
   
});






//route Room
 Route::middleware(['auth', 'verified'])->get('/rooms', [HomeController::class, 'loadRooms'])->name('rooms');
Route::middleware(['auth', 'verified'])->post('/create-room', [HomeController::class, 'createRoom'])->name('createRoom');
Route::middleware(['auth', 'verified'])->post('/add-member', [HomeController::class, 'addMembers'])->name('addMembers');
Route::post('/save-room-chat', [HomeController::class,'saveRoomChat'])->name('saveRoomChat');
Route::post('/load-room-chats', [HomeController::class,'loadRoomChats'])->name('loadRoomChats');
Route::post('/delete-room-chats', [HomeController::class,'deleteRoomChats'])->name('deleteRoomChats');
Route::get('/room-members', [HomeController::class, 'showRoomMembers'])->name('showRoomMembers');
Route::post('/delete-room-chats', [HomeController::class,'deleteRoomChats'])->name('deleteRoomChats');
Route::post('/remove-member', [HomeController::class, 'removeMember'])->name('removeMember');
Route::post('/add-members', [HomeController::class, 'addMembers1'])->name('add.members1');

    
    /* Pin message routes */
    Route::group(['prefix' => 'api'], function () {
        Route::get('pin-message', [MessagePinnedApi::class, 'list'])->name('api.pin.message.list');
        Route::post('pin-message', [MessagePinnedApi::class, 'pinned'])->name('api.pin.message.pinned');
        Route::post('unpin-message', [MessagePinnedApi::class, 'unpin'])->name('api.pin.message.unpin');
        Route::get('detail-pin-message', [MessagePinnedApi::class, 'detail'])->name('api.pin.message.detail');
    });


//route Admin
Route::get('/admin/login', [UserController::class, 'getLogin'])->name('admin.login');
Route::post('/admin/login', [UserController::class, 'postLogin'])->name('admin.login.post');
Route::get('/admin/logout', [UserController::class, 'getLogout'])->name('admin.logout');

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
