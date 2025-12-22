<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mitra_event_participation_id')->constrained('mitra_event_participation')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('tujuan_manfaat');
            $table->text('materi_narasumber');
            $table->text('susunan_waktu');
            $table->text('teknis_fasilitas');
            $table->text('panitia_pelayanan');
            $table->text('informasi_publikasi');
            $table->text('kepuasan_peserta');
            $table->text('saran_masukan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
