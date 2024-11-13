<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleLoginAdmin
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
        if(Auth::check()){
            if(Auth::user()->role != 3){

                return $next($request);

            }
            else{
                return redirect()->route('admin.login')->with('msgError', 'Bạn không được cấp quyền Quản lý');
            }
        }
        else{
            return redirect()->route('admin.login')->with('msgError', 'Bạn không được cấp quyền Quản lý');
        }
    }
}
