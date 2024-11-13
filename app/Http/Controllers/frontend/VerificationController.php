<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VerificationController extends Controller
{
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            abort(401); // Link không hợp lệ hoặc đã hết hạn
        }

        $user = User::findOrFail($id);
        if ($user->email_verified_at) {
            return redirect('')->with('message', 'Tài khoản đã được kích hoạt.');
        }

        $user->email_verified_at = now();
        $user->status = 1;
        $user->save();

        return redirect('')->with('message', 'Tài khoản đã được kích hoạt thành công!');
    }
}
