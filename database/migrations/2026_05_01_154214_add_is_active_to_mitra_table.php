<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasColumn('mitra', 'is_active')) {
        Schema::table('mitra', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('status');
        });
    }
}

    public function down(): void
    {
        Schema::table('mitra', function (Blueprint $blueprint) {
            $blueprint->dropColumn('is_active');
        });
    }
};
