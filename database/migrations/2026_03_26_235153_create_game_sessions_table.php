<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('player_name');
            $table->string('conversation_id')->nullable();
            $table->unsignedTinyInteger('health')->default(100);
            $table->unsignedTinyInteger('energy')->default(100);
            $table->json('inventory')->nullable();
            $table->unsignedTinyInteger('current_encounter')->default(1);
            $table->boolean('game_over')->default(false);
            $table->boolean('victory')->default(false);
            $table->timestamps();
        });
    }
};
