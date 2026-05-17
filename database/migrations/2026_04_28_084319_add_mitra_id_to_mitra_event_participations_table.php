<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('mitra_event_participation', function (Blueprint $table) {
        // Menambahkan kolom mitra_id yang relasi ke tabel mitra
        $table->unsignedBigInteger('mitra_id')->nullable()->after('id');
        $table->foreign('mitra_id')->references('id')->on('mitra')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_event_participation', function (Blueprint $table) {
            //
        });
    }
};
