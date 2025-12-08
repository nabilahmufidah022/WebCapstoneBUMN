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
        Schema::create('history_mitra', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->string('action'); // 'registered', 'approved', 'rejected'
            $table->text('description')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // admin who performed action
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_mitra');
    }
};
