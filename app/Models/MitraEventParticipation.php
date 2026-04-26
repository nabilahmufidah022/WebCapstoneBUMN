<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MitraEventParticipation extends Model
{
    use HasFactory;

    protected $table = 'mitra_event_participation';

    protected $fillable = [
        'mitra_id',
        'judul_pelatihan',
        'tanggal_pelatihan',
        'waktu_pelatihan',
        'tempat_pelatihan',
        'narasumber',
        'status',          // Akan berisi: 'Akan Datang' (Silabus) atau 'Selesai' (Histori)
        'pelaksanaan',     // Untuk Online / Offline
        'kategori',        // Literasi Digital, Bisnis, dll
        'rating',          // Nilai dari Admin (1-5)
        'catatan_internal' // Catatan evaluasi rahasia untuk Admin
    ];

    /**
     * Relasi ke Pusat Data Mitra
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Relasi ke Feedback dari Mitra
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'mitra_event_participation_id');
    }

    /**
     * Helper untuk cek apakah sudah selesai (Opsional, mempermudah di Blade)
     */
    public function isFinished()
    {
        return $this->status === 'Selesai';
    }
}
