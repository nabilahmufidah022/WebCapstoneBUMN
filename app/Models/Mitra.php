<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    protected $table = 'mitra';

    protected $fillable = [
        'user_id',
        'nama_lengkap',
        'no_telepon',
        'nama_perusahaan',
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
}
