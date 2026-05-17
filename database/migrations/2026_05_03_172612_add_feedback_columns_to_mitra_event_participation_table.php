<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
{
    Schema::table('mitra_event_participation', function (Blueprint $table) {
        // Menambahkan kolom untuk menyimpan feedback dari sisi mitra
        $table->integer('rating_mitra')->nullable(); 
        $table->text('feedback_mitra')->nullable();
    });
}

public function down()
{
    Schema::table('mitra_event_participation', function (Blueprint $table) {
        $table->dropColumn(['rating_mitra', 'feedback_mitra']);
    });
}
};