<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class RoomController extends Controller
{
    // Phương thức hiển thị trang chủ với danh sách phòng
    public function index()
    {
        // Lấy tất cả các phòng
        $rooms = Room::with('users')->get();

        // Trả về view home và truyền biến $rooms vào
        return view('home', compact('rooms')); // 'home' là view chính
    }
    // Hiển thị form tạo room
    public function create()
    {
        // Lấy tất cả các phòng
        $rooms = Room::with('users')->get();

        // Truyền biến $rooms vào view
        return view('frontend.rooms.index', compact('rooms'));
    }

    // Xử lý tạo room
    public function store(Request $request)
    {
        // Validate dữ liệu nhập
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'members' => 'required|string',
        ]);

        // Tạo phòng mới
        $room = Room::create(['name' => $validated['room_name']]);

        // Thêm thành viên vào phòng
        $members = explode(',', $validated['members']); // Giả sử các thành viên được nhập dưới dạng chuỗi phân cách bằng dấu phẩy
        foreach ($members as $member) {
            $user = User::where('username', trim($member))->first();
            if ($user) {
                // Giả sử bạn có bảng pivot để lưu thành viên trong phòng
                $room->members()->attach($user->id);
            }
        }

        // Redirect về trang chủ hoặc đến trang các phòng chat
        return redirect()->route('home'); // Hoặc một route khác
    }
}
