<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\UserRequest;
use App\Http\Requests\Admin\Users\UserUpdateProfileRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(){
        $active = "active";
        view()->share('activeUser', $active);
    }
    //Danh sách tài khoản
    public function index()
    {
        //
        $data = User::orderBy('id', 'DESC')->paginate(5);

        return view('admin.users.list', ['data' => $data]);
    }

    //Form tạo tài khoản
    public function create()
    {
        return view('admin.users.add');
    }
    //Thêm tài khoản
    public function store(UserRequest $request)
    {
        $data = new User();
        
        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);//bcrypt mã hóa mật khẩu trước 
        $data->role = $request->role;
        $data->email_verified_at = now();
        $data->status = 1;

        if($data->save()){
            return redirect('admin/users/create')->with('msgSuccess', 'Đăng kí thành công');
        }
        else{
            return redirect('admin/users/create')->with('msgError', 'Đăng kí thất bại');
        }

    }
    //Form sửa vai trò
    public function edit($id)
    {
        $data = User::find($id);
        return view('admin.users.update', ['data' => $data]);

    }
    //Sửa vai trò
    public function update(Request $request, $id)
    {
        $data = User::find($id);

        $data->role = $request->role;

        if($data->save()){
            return redirect()->back()->with('msgSuccess', 'Cập nhật thông tin thành công');
        }
        else{
            return redirect()->back()->with('msgError', 'Cập nhật thông tin thất bại');
        }
    }
    //Xóa tài khoản
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('msgError', 'Người dùng không tồn tại.');
        }
        $user->delete();    
        return redirect()->back()->with('msgSuccess', 'Xóa người dùng thành công.');
    }
    //Form login
    public function getLogin(){
        return view('admin.users.login');
    }
    //Xửa lý đăng nhập
    public function postLogin(Request $request){       
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role' => 2])){
            return redirect()->route('user.index')->with('msgSuccess', 'Đăng nhập thành công');
        }
        else{
            return redirect()->back()->with('msgError', 'Đăng nhập thất bại </br> Tài khoản hoặc mật khẩu không đúng');
        };
    }
    //Xử lý đăng xuất 
    public function getLogout(){
        Auth::logout();

        return redirect()->route('admin.login')->with('msgSuccess', 'Đăng xuất thành công');
    }
    //Hiển thị thông tin admin đang đăng nhập
    public function showProfileAdmin(){
        if(Auth::check()){
            $data = Auth::user();
            return view('admin.profile.show', ['data' => $data]);
        }
        else{
            return redirect()->route('admin.login');
        }
    }
    //Form chỉnh sửa thoogn tin admin
    public function showFormUpdateAdmin(){
        $dataUser = Auth::user();

        $data = User::find($dataUser->id);

        return view('admin.profile.update', ['data' => $data]);
    }
    //Cập nhật thông tin admin
    public function updateProfileAdmin(UserUpdateProfileRequest $request){
        $dataUser = Auth::user();

        $data = User::find($dataUser->id);

        $data->name = $request->name;
        $data->password = bcrypt($request->password);
        
        if($data->save()){
            return redirect('admin/profile/show')->with('msgSuccess', 'Cập nhật thông tin thành công');
        }
        else{
            return redirect()->back()->with('msgError', 'Cập nhật thông tin thất bại');
        }
    }
}
