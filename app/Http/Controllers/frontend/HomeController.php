<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Room;
use App\Models\Member;
use App\Models\RoomChat;
use App\Events\RoomMessageEvent;
use App\Events\RoomMessageDeletedEvent;

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
                'user_id' => auth()->id(),  // Người tạo phòng
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Tạo mảng dữ liệu để thêm thành viên mới
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
            // Ghi log lỗi nếu có
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




    public function saveRoomChat(Request $request){
        try {

            Log::info('Saving chat for room: ' . $request->room_id . ', message: ' . $request->message);
            $chat = RoomChat::create([
                'user_id' => auth()->id(),
                'room_id' => $request->room_id,
                'content' => $request->message
            ]);

            $chat= RoomChat::with('user')->where('id',$chat->id)->first();

            Log::info('Chat saved with ID: ' . $chat->id);
            event(new RoomMessageEvent($chat));
            Log::info('RoomMessageEvent fired for room: ' . $chat->room_id);

            return response()->json(['data' => $chat]);


        } catch (\Exception $e) {
            // Ghi log lỗi nếu có
            Log::error('Lỗi tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }
    }

    public function loadRoomChats(Request $request){
        try {
            $chats = RoomChat::with('user')->where('room_id', $request->room_id)->get();
            return response()->json(['chats' => $chats]);

        } catch (\Exception $e) {
            // Ghi log lỗi nếu có
            Log::error('Lỗi tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra, vui lòng thử lại!'], 500);
        }       
    }

    public function deleteRoomChats(Request $request){
        try {

            RoomChat::where('id', $request->id)->delete();
            event(new RoomMessageDeletedEvent($request->id));
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            // Ghi log lỗi nếu có
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
            // Ghi log lỗi nếu có
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
