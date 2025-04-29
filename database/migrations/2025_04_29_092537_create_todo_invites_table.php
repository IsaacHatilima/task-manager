<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('todo_invites', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('todo_id')->constrained('todos')->cascadeOnDelete();
            $table->string('email');
            $table->string('token');
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('todo_invites');
    }
};
