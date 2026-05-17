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
        'status',           // Berisi: 'Akan Datang' (Silabus) atau 'Selesai' (Histori)
        'pelaksanaan',      // Online / Offline
        'kategori',         // Literasi Digital, Bisnis, Dasar, Tematik
        'rating',           // Nilai dari Admin (1-5) untuk monitoring internal
        'catatan_internal', // Evaluasi internal Admin
        'rating_mitra',     // Tambahkan ini: Rating kepuasan dari sisi Mitra
        'feedback_mitra'    // Tambahkan ini: Saran/Masukan dari sisi Mitra
    ];

    /**
     * Relasi ke Pusat Data Mitra
     * Menghubungkan partisipasi dengan profil perusahaan mitra
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

    /**
     * Relasi ke Feedback (Jika kamu menggunakan tabel feedback terpisah)
     */
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'mitra_event_participation_id');
    }

    /**
     * Helper untuk cek status di Blade
     */
    public function isFinished()
    {
        return $this->status === 'Selesai';
    }
}