<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->integer('event_id')->unique();
            $table->string('title');
            $table->date('start');
            $table->date('end');
            $table->timestamps();
        });

        Schema::create('meeting_user', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['accepted', 'rejected', 'maybe', 'pending']);
            $table->timestamps();

            $table->primary(['user_id', 'meeting_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_user');
        Schema::dropIfExists('meetings');
    }
};
