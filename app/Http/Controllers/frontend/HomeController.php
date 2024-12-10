<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
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
                    return $chat;
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

    public function saveRoomChat(Request $request)
    {
        try {
            DB::beginTransaction();

            // Khởi tạo dữ liệu cơ bản
            $data = [
                'user_id' => auth()->id(),
                'room_id' => $request->room_id,
                'pinned' => false
            ];

            // Xử lý nội dung tin nhắn nếu có
            if ($request->filled('message')) {
                $data['content'] = $request->message;
            }

            // Lưu tin nhắn vào database trước
            $chat = RoomChat::create($data);

            // Kiểm tra và xử lý file sau khi đã có chat record
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                if (!$file->isValid()) {
                    DB::rollBack();
                    return response()->json(['error' => 'File không hợp lệ'], 400);
                }

                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $fileType = $file->getMimeType();
                $isImage = strpos($fileType, 'image') === 0;

                $uploadPath = $isImage ? 'chat_files/images' : 'chat_files/documents';
                $path = $file->storeAs($uploadPath, $fileName, 'public');

                if (!$path) {
                    DB::rollBack();
                    return response()->json(['error' => 'Không thể lưu file'], 500);
                }

                // Cập nhật thông tin file vào chat record
                $chat->update([
                    'file_url' => asset('storage/' . $path),
                    'content' => $fileName
                ]);
            }

            // Xử lý tin nhắn được ghim
            if ($request->boolean('pinned')) {
                $chat->update([
                    'pinned' => true,
                    'pin_expires_at' => $request->pin_expires ? Carbon::parse($request->pin_expires) : now()->addMinutes(120)
                ]);
            }

            // Refresh chat data với user info
            $chat->load('user');

            DB::commit();

            // Gửi sự kiện đến các client
            event(new RoomMessageEvent($chat));

            return response()->json([
                'success' => true,
                'data' => $chat,
                'file_type' => isset($isImage) ? ($isImage ? 'image' : 'document') : null
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi lưu tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function pinMessage(Request $request)
    {
        try {
            if (!$request->has('message_id')) {
                return response()->json(['error' => 'Thiếu message_id'], 400);
            }

            $message = RoomChat::findOrFail($request->message_id);
            $room = Room::findOrFail($message->room_id);
            if ($room->create_by !== auth()->id()) {
                return response()->json(['error' => 'Bạn không có quyền ghim tin nhắn'], 403);
            }

            DB::beginTransaction();

            $message->pinned = !$message->pinned;
            $message->pin_expires_at = $message->pinned ? now()->addMinutes(120) : null;
            $message->save();

            event(new MessagePinnedEvent($message, $message->pinned));

            DB::commit();

            $pinnedMessages = RoomChat::where('room_id', $message->room_id)
                ->where('pinned', true)
                ->where(function($query) {
                    $query->where('pin_expires_at', '>', now())
                          ->orWhereNull('pin_expires_at');
                })
                ->with('user')
                ->get();

            return response()->json([
                'success' => true,
                'message' => $message->pinned ? 'Tin nhắn đã được ghim' : 'Tin nhắn đã được bỏ ghim',
                'pinned_messages' => $pinnedMessages
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi ghim tin nhắn: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi ghim tin nhắn'], 500);
        }
    }

    public function getPinnedMessages(Request $request)
    {
        try {
            if (!$request->has('room_id')) {
                return response()->json(['error' => 'Thiếu room_id'], 400);
            }

            $pinnedMessages = RoomChat::where('room_id', $request->room_id)
                ->where('pinned', true)
                ->where(function($query) {
                    $query->where('pin_expires_at', '>', now())
                          ->orWhereNull('pin_expires_at');
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'pinned_messages' => $pinnedMessages
            ]);

        } catch (\Exception $e) {
            Log::error('Lỗi lấy tin nhắn ghim: ' . $e->getMessage());
            return response()->json(['error' => 'Có lỗi xảy ra khi lấy tin nhắn ghim'], 500);
        }
    }
}
