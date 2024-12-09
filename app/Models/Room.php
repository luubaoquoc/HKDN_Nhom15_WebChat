<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Liên kết với bảng room_users
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_users');
    }

    // Liên kết với bảng messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Kiểm tra xem người dùng có quyền truy cập vào phòng này hay không
    public function isUserInRoom($userId)
    {
        // Kiểm tra nếu người dùng là creator hoặc người đã được thêm vào phòng
        return $this->users->contains('id', $userId);
    }
}
