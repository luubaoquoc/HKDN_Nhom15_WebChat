<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'create_by'
    ];

    public function user()
    {
         return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id');
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'room_user', 'room_id', 'user_id');
    }
    
    public function rooms() {
        return $this->belongsToMany(Room::class, 'members', 'user_id', 'room_id');
    }
    

}
