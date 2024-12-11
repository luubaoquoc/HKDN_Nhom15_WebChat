<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\MessagePinned;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessagePinnedApi extends Controller
{
    public function list(Request $request)
    {
        try {
            $room_id = $request->input('room_id');

            $pins = MessagePinned::where('room_id', $room_id)
                ->orderByDesc('id')
                ->cursor()
                ->map(function (MessagePinned $messagePinned) {
                    $item = $messagePinned->toArray();
                    $message = DB::table('messages')->where('id', $messagePinned->message_id)->first();

                    $user = User::find($message->user_id);

                    $item['message'] = $message;
                    $item['message_user'] = $user;
                    return $item;
                });

            $data = returnMessage(1, $pins, 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function detail(Request $request)
    {
        try {
            $pin_id = $request->input('pin_id');
            $pin = MessagePinned::find($pin_id);

            if (!$pin) {
                $data = returnMessage(-1, '', 'Pin not found');
                return response($data, 400);
            }

            $message = DB::table('messages')->where('id', $pin->message_id)->first();
            $res = $pin->toArray();
            $res['message'] = $message;

            $data = returnMessage(1, $res, 'Success');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function pinned(Request $request)
    {
        try {
            $message_id = $request->input('message_id');
            $room_id = $request->input('room_id');
            $user_id = Auth::user()->id;

            $pin = MessagePinned::where('room_id', $room_id)
                ->where('message_id', $message_id)
                ->first();

            if ($pin) {
                $pin->delete();
                $data = returnMessage(1, 'Unpin message successfully!', 'Unpin message successfully!');
            } else {
                $pin = new MessagePinned();
                $pin->message_id = $message_id;
                $pin->room_id = $room_id;
                $pin->user_id = $user_id;
                $pin->save();
                $data = returnMessage(1, $pin, 'Pin message successfully!');
            }
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }

    public function unpin(Request $request)
    {
        try {
            $pin_id = $request->input('pin_id');
            $pin = MessagePinned::find($pin_id);
            $pin->delete();

            $data = returnMessage(1, 'Unpin message successfully!', 'Unpin message successfully!');
            return response($data, 200);
        } catch (\Exception $exception) {
            $data = returnMessage(-1, '', $exception->getMessage());
            return response($data, 400);
        }
    }
}
