<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra'; // Pastikan nama tabel di database 'mitra' atau 'mitras'

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'no_telepon',
        'nama_perusahaan',
        'bidang_perusahaan', // <--- WAJIB DITAMBAHKAN DI SINI
        'lokasi_perusahaan',
        'deskripsi_perusahaan',
        'company_profile',
        'surat_permohonan_audiensi',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function mitraEventParticipations()
    {
        return $this->hasMany(MitraEventParticipation::class);
    }

    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'mitra_id');
    }

}