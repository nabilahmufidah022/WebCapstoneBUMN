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
{
    Schema::create('feedbacks', function (Blueprint $table) {
        $table->id();

        // Relasi ke tabel mitra_event_participation
        $table->foreignId('mitra_event_participation_id')
              ->constrained('mitra_event_participation')
              ->onDelete('cascade');

        // Kolom data feedback
        $table->text('isi_feedback')->nullable(); // Sesuaikan dengan nama kolom di form kamu
        $table->integer('rating')->nullable();    // Jika ada rating

        $table->timestamps();
    });
}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
