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
    Schema::table('mitra', function (Blueprint $table) {
        // Hapus ->after('email') agar tidak error lagi
        $table->decimal('average_rating', 3, 2)->default(0);
        $table->string('status_aktif')->default('Non-Aktif');
        $table->text('alasan_penolakan')->nullable();
    });
}

public function down(): void
{
    Schema::table('mitra', function (Blueprint $table) {
        $table->dropColumn(['average_rating', 'status_aktif', 'alasan_penolakan']);
    });
}
};
