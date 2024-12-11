<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\MessagePinned;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Room;
use App\Models\Member;
use App\Models\RoomChat;
use App\Events\RoomMessageEvent;
use App\Events\RoomMessageDeletedEvent;
use App\Events\MessagePinnedEvent;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function dashboard() {
        $users = User::whereNotIn('id', [auth()->user()->id])->get();
        $rooms = Room::where('create_by', auth()->user()->id)
            ->orWhereHas('users', function($query) {
                $query->where('user_id', auth()->user()->id);
            })
            ->get();
        return view('frontend.home', compact('users', 'rooms'));
    }

    public function createRoom(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|',
        ]);

        try {
            $room = Room::create([
                'name' => $request->input('name'),
                'create_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => 'Phòng đã được tạo thành công',
                'room_id' => $room->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    public function addMembers(Request $request)
    {
        try {
            Member::where('room_id', $request->room_id)->delete();

            Member::create([
                'room_id' => $request->room_id,
                'user_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($request->members as $user) {
                Member::create([
                    'room_id' => $request->room_id,
                    'user_id' => $user,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            return response()->json(['msg' => 'Thêm thành viên thành công'], 200);

        } catch (\Exception $e) {
            Log::error('Lỗi thêm thành viên: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }
    public function addMembers1(Request $request)
{
    try {
        // Kiểm tra nếu không có thành viên được chọn
        if (!isset($request->members) || count($request->members) == 0) {
            return response()->json(['error' => 'Vui lòng chọn ít nhất một thành viên.'], 400);
        }

        // Thêm người tạo phòng vào danh sách thành viên (nếu chưa có)
        $creatorMember = Member::firstOrCreate([
            'room_id' => $request->room_id,
            'user_id' => auth()->id(),  // Người tạo phòng
        ], [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("Thêm người tạo phòng vào room_id: {$request->room_id}");

        // Thêm các thành viên mới vào phòng, tránh trùng lặp
        $addedMembers = 0;  // Biến đếm số thành viên được thêm
        foreach ($request->members as $userId) {
            // Kiểm tra nếu người dùng đã là thành viên trong phòng
            $existingMember = Member::where('room_id', $request->room_id)
                                    ->where('user_id', $userId)
                                    ->first();

            // Nếu chưa có, mới thêm vào phòng
            if (!$existingMember) {
                Member::create([
                    'room_id' => $request->room_id,
                    'user_id' => $userId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $addedMembers++;  // Tăng số thành viên đã thêm
            }
        }

        // Kiểm tra nếu có thành viên mới được thêm
        if ($addedMembers > 0) {
            Log::info("Đã thêm {$addedMembers} thành viên vào phòng room_id: {$request->room_id}");
            return response()->json(['msg' => 'Thêm thành viên thành công', 'success' => true], 200);
        } else {
            return response()->json(['msg' => 'Không có thành viên mới nào được thêm (tất cả đã tồn tại)', 'success' => true], 200);
        }

    } catch (\Exception $e) {
        // Ghi log lỗi nếu có
        Log::error('Lỗi thêm thành viên: ' . $e->getMessage());
        return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
    }
}




    public function loadRoomChats(Request $request){
        try {
            $chats = RoomChat::with('user')
                ->where('room_id', $request->room_id)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($chat) {
                    if ($chat->file_path) {
                        try {
                            if (Storage::disk('public')->exists($chat->file_path)) {
                                $chat->file_url = Storage::disk('public')->url($chat->file_path);
                                $chat->content = $chat->file_url;
                            } else {
                                $chat->file_url = asset('images/error-image.jpg'); // Ảnh mặc định khi không tìm thấy file
                                $chat->content = 'File không tồn tại';
                            }
                        } catch (\Exception $e) {
                            Log::error('Lỗi khi lấy file: ' . $e->getMessage());
                            $chat->file_url = asset('images/error-image.jpg');
                            $chat->content = 'Không thể tải file';
                        }
                    }

                    $pin = MessagePinned::where('room_id', $chat->room_id)
                        ->where('message_id', $chat->id)
                        ->first();

                    $new_chat = $chat->toArray();
                    $new_chat['is_pinned'] = (bool)$pin;

                    return $new_chat;
                });

            $isRoomCreator = Room::where('id', $request->room_id)
                                ->where('create_by', auth()->id())
                                ->exists();

            return response()->json([
                'chats' => $chats,
                'can_pin' => $isRoomCreator
            ]);
        } catch (\Exception $e) {
            Log::error('Lỗi tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    public function deleteRoomChats(Request $request){
        try {
            $chat = RoomChat::find($request->id);

            if ($chat && $chat->file_path) {
                Storage::disk('public')->delete($chat->file_path);
            }

            $chat->delete();
            event(new RoomMessageDeletedEvent($request->id));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Lỗi tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    public function showRoomMembers(Request $request)
    {
        try {
            $room = Room::with('users')->find($request->room_id);
            return response()->json(['roomsMember' => $room]);

        } catch (\Exception $e) {
            Log::error('Lỗi tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    public function removeMember(Request $request) {
        try {
            $room = Room::findOrFail($request->room_id);
            $user = User::findOrFail($request->user_id);

            $room->users()->detach($user->id);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Lỗi xóa thành viên: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Có lỗi xảy ra.'], 500);
        }
    }

 




}
