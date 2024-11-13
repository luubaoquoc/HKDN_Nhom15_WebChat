<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|min:5|max:50',
            'password_old' => 
            ['required',
                function ($attribute, $password_old, $fail) {
                    if (!Hash::check($password_old, Auth::user()->password)) {
                        $fail('Mật khẩu chưa đúng');
                    }
                },
            ],
            'password' => 'required|min:5|max:20|different:user_password_old',
            'confirm-password' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Họ tên không được để trống',
            'name.min' => 'Họ tên phải lớn hơn 5 kí tự',
            'name.max' => 'Họ tên phải bé hơn 50 kí tự',
            'password_old.required' => 'Mật khẩu cũ không được để trống',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải dài hơn 5 kí tự',
            'password.max' => 'Mật khẩu không được dài quá 20 kí tự',
            'password.different' => 'Mật khẩu không được trùng mật khẩu cũ',
            'confirm-password.required' => 'Mật khẩu nhập lại không được để trống',
            'confirm-password.same' => 'Mật khẩu nhập lại không Khớp',
        ];
    }
}
