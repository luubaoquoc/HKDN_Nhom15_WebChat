<?php

namespace App\Http\Controllers\frontend;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\Room;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    // Hiển thị danh sách phòng chat
    public function index()
    {
        $rooms = Room::all();
        return view('frontend.rooms.index', compact('rooms'));
    }

    // Hiển thị form tạo phòng chat
    public function create()
    {
        return view('frontend.rooms.create-room');
    }

    // Xử lý tạo phòng chat
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $room = Room::create([
            'name' => $validated['name']
        ]);

        return redirect()->route('rooms.index')->with('success', 'Room created successfully!');
    }

    // Hiển thị tin nhắn trong phòng
    public function show(Room $room)
{
    $messages = Message::where('room_id', $room->id)->with('user')->latest()->get();
    return view('frontend.rooms.show-room', compact('room', 'messages'));
}

    // Thêm user vào phòng
    public function addUserToRoom(Request $request, Room $room)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $room->users()->syncWithoutDetaching([$validated['user_id']]);

        return redirect()->route('rooms.show', $room)->with('success', 'User added to room!');
    }

    // Gửi tin nhắn
    public function sendMessage(Request $request, Room $room)
{
    $validated = $request->validate([
        'message' => 'required|string',
    ]);

    $message = Message::create([
        'room_id' => $room->id,
        'user_id' => auth()->id(),
        'message' => $validated['message'],
    ]);

    broadcast(new \App\Events\MessageSent($message)); // Broadcast message

    return back()->with('success', 'Message sent successfully!');
}
}

