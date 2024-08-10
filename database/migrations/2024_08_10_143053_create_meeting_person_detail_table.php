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
        Schema::create('meeting_personal_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personal_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('meeting_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['accepted', 'rejected', 'maybe', 'pending']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meeting_personal_detail');
    }
};
