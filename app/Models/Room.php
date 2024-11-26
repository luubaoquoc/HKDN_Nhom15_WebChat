<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'create_by',
    ];

    // Quan hệ với User
    public function creator()
    {
        return $this->belongsTo(User::class, 'create_by');
    }
}
