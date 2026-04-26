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
    Schema::table('mitra_event_participation', function (Blueprint $table) {
        // Cek dulu, kalau kolom 'status' belum ada baru ditambah
        if (!Schema::hasColumn('mitra_event_participation', 'status')) {
            $table->string('status')->default('Terjadwal')->after('narasumber');
        }

        // Tambahkan kolom rating (ini yang paling penting buat integrasi)
        if (!Schema::hasColumn('mitra_event_participation', 'rating')) {
            $table->integer('rating')->nullable()->after('status');
        }

        // Tambahkan kolom pelaksanaan (Online/Offline)
        if (!Schema::hasColumn('mitra_event_participation', 'pelaksanaan')) {
            $table->string('pelaksanaan')->nullable()->after('tempat_pelatihan');
        }
    });
}

public function down(): void
{
    Schema::table('mitra_event_participation', function (Blueprint $table) {
        // Hanya hapus jika kolom-kolom ini memang baru dibuat
        $table->dropColumn(['rating', 'pelaksanaan']);
    });
}
};
