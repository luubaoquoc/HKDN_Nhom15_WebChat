<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Mail\ForgotPassword;  // Import lớp Mailable
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\ResetPasswordToken;

class ForgotPasswordController extends Controller
{
    // Hiển thị form yêu cầu link reset password
    public function showLinkRequestForm()
    {
        return view('frontend.resetpassword.mail-resetpassword');
    }

    // Gửi email chứa link reset password
    public function sendResetLinkEmail(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
    ], [
        'email.required' => 'Vui lòng nhập email.',
        'email.email' => 'Định dạng email không hợp lệ.',
        'email.exists' => 'Email này không tồn tại trong hệ thống.',
    ]);

    try {
        // Bước 1: Lấy thông tin user
        Log::info('Bắt đầu lấy thông tin user.', ['email' => $request->email]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            Log::error('User không tồn tại.', ['email' => $request->email]);
            throw new \Exception('User không tồn tại.');
        }
        Log::info('Thông tin user:', ['user' => $user]);

        // Bước 2: Tạo token
        $token = Str::random(40);
        $tokenData = [
            'token' => $token,
            'email' => $request->email,
        ];
        Log::info('Token được tạo thành công.', ['token' => $token]);

        // Bước 3: Lưu token vào bảng password_resets
        $userToken = ResetPasswordToken::create($tokenData);
        Log::info('Token đã được lưu vào bảng password_resets.', ['tokenData' => $tokenData]);

        // Bước 4: Gửi email
        Mail::to($request->email)->send(new ForgotPassword($user, $token));
        Log::info('Email đã được gửi thành công.', ['email' => $request->email]);

        return redirect()->back()->with('msgSuccess', 'Vui lòng kiểm tra email để tiếp tục.');
    } catch (\Throwable $th) {
        // Log lỗi
        Log::error('Lỗi trong quá trình gửi email reset password.', [
            'message' => $th->getMessage(),
            'trace' => $th->getTraceAsString(),
        ]);

        return redirect()->back()->with('msgError', 'Gửi mail thất bại! Vui lòng kiểm tra lại email!');
    }
}


    // Hiển thị form đặt lại mật khẩu
    public function showResetForm($token)
    {
        //dd($token); //
        return view('frontend.resetpassword.resetpassword', ['token' => $token]);
    }

    // Xử lý đặt lại mật khẩu
    public function reset($token)
    {
        // Bước 1: Validate dữ liệu
        request()->validate([
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|same:password',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);
    
        // Bước 2: Kiểm tra token
        $tokenData = ResetPasswordToken::where('token', $token)->first();
    
        if (!$tokenData) {
            return redirect()->back()->with('msgError', 'Token không hợp lệ hoặc đã hết hạn.');
        }
    
        $user = $tokenData->customer;
    
        // Bước 3: Cập nhật mật khẩu
        $data = [
            'password' => bcrypt(request('password'))
        ];
    
        $check = $user->update($data);
    
        // Bước 4: Xóa token nếu cập nhật thành công
        if ($check) {
            ResetPasswordToken::where('token', $token)->delete(); // Xóa token dựa vào token
            return redirect()->route('login')->with('msgSuccess', 'Cập nhật mật khẩu thành công.');
        }
    
        return redirect()->back()->with('msgError', 'Cập nhật mật khẩu thất bại.');
    }
}
