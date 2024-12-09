<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashBoardController;
use App\Http\Controllers\frontend\CustomerController;
use App\Http\Controllers\frontend\HomeController;
use App\Http\Controllers\frontend\ChatController;
use App\Http\Controllers\frontend\VerificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\HandleRoleAdmin;
use App\Http\Controllers\frontend\ProfileController;
use App\Http\Controllers\frontend\ForgotPasswordController;
use App\Http\Controllers\frontend\RoomController;


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
    Route::post('/create-room', [RoomController::class, 'store'])->name('create.room');
    // Chat Routes (Requires Auth)
    // Route::get('/rooms', [ChatController::class, 'index'])->name('rooms.index'); // View all rooms
    // Route::get('/rooms/create', [ChatController::class, 'create'])->name('rooms.create'); // Create room form
    // Route::post('/rooms', [ChatController::class, 'store'])->name('rooms.store'); // Store new room
    // Route::get('/rooms/{room}', [ChatController::class, 'show'])->name('rooms.show'); // Show room messages (Note the model binding)
    // Route::post('/rooms/{room}/add-user', [ChatController::class, 'addUserToRoom'])->name('rooms.addUser'); // Add user to room
    // Route::post('/rooms/{room}/messages', [ChatController::class, 'sendMessage'])->name('rooms.sendMessage'); // Send message

});



Route::middleware('auth')->post('/send-message', [ChatController::class, 'sendMessage']);



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

