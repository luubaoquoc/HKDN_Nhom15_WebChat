<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Member;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('broadcast-group-message.{room_id}', function ($user, $room_id) {
    Log::info('User: ' . $user->id . ' is trying to join room: ' . $room_id);
    // Kiểm tra nếu người dùng là thành viên của phòng
    return Member::where('room_id', $room_id)->where('user_id', $user->id)->exists();
});

Broadcast::channel('room-message-deleted', function ($user) {
    return $user;
});


