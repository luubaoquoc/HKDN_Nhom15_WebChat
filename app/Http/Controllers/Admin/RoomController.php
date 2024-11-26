<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        
        $rooms = Room::with('creator')->paginate(10);
        return view('admin.rooms.list', ['rooms' => $rooms]);
    }

    /**
     * Hiển thị form tạo mới phòng.
     */
    public function create()
    {
        $users = User::all();
        return view('admin.rooms.create', ['users' => $users]);
    }

    /**
     * Lưu phòng mới vào database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'create_by' => 'required|exists:users,id',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index')->with('success', 'Phòng đã được tạo thành công!');
    }

    /**
     * Hiển thị chi tiết phòng.
     */
    public function show(Room $room)
    {
        return view('admin.rooms.show', ['room' => $room]);
    }

    /**
     * Hiển thị form chỉnh sửa phòng.
     */
    public function edit(Room $room)
    {
        $users = User::all();
        return view('admin.rooms.edit', [
            'room' => $room,
            'users' => $users
        ]);
    }
    

    /**
     * Cập nhật thông tin phòng.
     */
    public function update(Request $request, Room $room)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'create_by' => 'required|exists:users,id',
        ]);

        $room->update($request->all());
        return redirect()->route('rooms.index')->with('success', 'Phòng đã được cập nhật thành công!');
    }

    /**
     * Xóa phòng.
     */
    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('rooms.index')->with('success', 'Phòng đã được xóa thành công!');
    }
}
