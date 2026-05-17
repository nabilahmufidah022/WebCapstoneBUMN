<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    /**
     * Atribut yang dapat diisi (Mass Assignable)
     */
    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'no_telepon',
        'nama_perusahaan',
        'bidang_perusahaan',
        'lokasi_perusahaan',
        'deskripsi_perusahaan',
        'company_profile',
        'surat_permohonan_audiensi',
        'status',
        // Kolom Integrasi untuk Skripsi / Sistem Informasi
        'average_rating',    // Menyimpan reputasi berdasarkan rating pelatihan
        'status_aktif',      // Status otomatis: Aktif / Non-Aktif
        'alasan_penolakan',  // Catatan jika pendaftaran ditolak
    ];

    /**
     * Relasi ke User (Account Mitra)
     * Menghubungkan identitas mitra dengan akun login sistem.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Partisipasi Kegiatan / Silabus (Alias Pendek)
     * Nama fungsi 'participations' untuk kemudahan pemanggilan data.
     */
    public function participations()
    {
        // Pastikan nama model target adalah MitraEventParticipation
        return $this->hasMany(MitraEventParticipation::class, 'mitra_id');
    }

    /**
     * Relasi Utama ke Partisipasi Kegiatan (Silabus Pelatihan)
     * Digunakan oleh Controller untuk menghitung Total Keterlibatan secara Realtime.
     * Fitur ini menghubungkan ID Mitra dengan kolom mitra_id di tabel partisipasi.
     */
    public function mitraEventParticipations()
    {
        // Ini adalah kunci utama untuk integrasi data realtime di halaman Pusat Data Mitra
        return $this->hasMany(MitraEventParticipation::class, 'mitra_id', 'id');
    }
}
