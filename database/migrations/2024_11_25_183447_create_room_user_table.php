<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_user', function (Blueprint $table) {
            $table->foreignId('room_id')->constrained('rooms');
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('join_at');
            $table->primary(['room_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_user');
    }
};
