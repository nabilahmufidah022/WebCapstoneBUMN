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
            $table->string('kategori')->nullable()->after('narasumber');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mitra_event_participation', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
