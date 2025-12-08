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
        Schema::create('mitra_event_participation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mitra_id');
            $table->string('judul_pelatihan');
            $table->date('tanggal_pelatihan');
            $table->time('waktu_pelatihan');
            $table->string('tempat_pelatihan');
            $table->string('narasumber');
            $table->string('status');
            $table->timestamps();

            $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mitra_event_participation');
    }
};
