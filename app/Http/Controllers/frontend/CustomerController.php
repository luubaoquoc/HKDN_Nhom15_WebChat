<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Notifications\VerifyEmail;


class CustomerController extends Controller
{
    //
    public function login(){
        return view('frontend.login');
    }
    public function postLogin(Request $req){

        //validate
        $req->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ],
        [
            'email.required' => 'Email không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
        ]
    );

        if (Auth::attempt(['email' => $req->email, 'password' => $req->password])) {
            if (Auth::user()->email_verified_at === null) {
                Auth::logout();
                return redirect()->back()->with('msgError', 'Tài khoản của bạn chưa được kích hoạt. Vui lòng kiểm tra email.');
            }
            return redirect()->route('index');
        }
        return redirect()->back()->with('msgError', 'Đăng nhập không thành công!!!');
        
        
    }
    public function register(){
        return view('frontend.register');
    }
    public function postRegister(Request $req){

        //validate
        $req->validate([
            'name' => 'required|string|min:6|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm-password' => 'required|same:password'
        ]);
        // dd($req->all());
        $req->merge(['password' => Hash::make($req->password)]);
        try {
            $user = User::create($req->all());

            // Tạo URL xác minh
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $user->id]
            );

            // Gửi email xác minh
            $user->notify(new VerifyEmail($verificationUrl));

            return redirect()->route('login')->with('msgSuccess', 'Vui lòng kiểm tra email để kích hoạt tài khoản của bạn.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('msgError', 'Đăng ký không thành công.');
        }
    }
}
