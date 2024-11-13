<?php

namespace App\Http\Requests\Admin\Users;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|min:10|max:50',
            'email' => 'required|unique:users,email|max:30|email:rfc,dns',
            'password' => 'required|min:5|max:20',
            'confirm-password' => 'required|same:password',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Họ tên không được để trống',
            'name.min' => 'Họ tên phải lớn hơn 5 kí tự',
            'name.max' => 'Họ tên phải bé hơn 50 kí tự',
            'email.required' => 'Email không được để trống',
            'email.unique' => 'Email này đã được sử dụng',
            'email.email' => 'Email chưa đúng định dạng',
            'email.max' => 'Email phải bé hơn 30 kí tự',
            'password.required' => 'Mật khẩu không được để trống',
            'password.min' => 'Mật khẩu phải dài hơn 5 kí tự',
            'password.max' => 'Mật khẩu không được dài quá 20 kí tự',
            'confirm-password.required' => 'Mật khẩu xác nhận không được để trống',
            'confirm-password.same' => 'Mật khẩu xác nhận không giống',
        ];
    }
}
