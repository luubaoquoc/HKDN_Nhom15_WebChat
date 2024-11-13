<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleRoleAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->role === 2) {
                return $next($request);
            } else {
                return redirect()->route('admin.login')->with('msgError', 'Bạn không được cấp quyền Quản lý người dùng');
            }
        } else {
            return redirect()->route('admin.login')->with('msgError', 'Bạn cần đăng nhập để truy cập trang quản lý');
        }
    }
}
