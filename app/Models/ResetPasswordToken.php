<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
class ResetPasswordToken extends Model
{
    use HasFactory;
    protected $table = 'password_resets';
    protected $fillable = ['email', 'token'];


    public function customer(){
        return $this->hasOne(User ::class, 'email', 'email');
    }

    public function scopeCheckToken($request, $token){
        return $request->where('token', $token)->firstOrFail();
    }
}
