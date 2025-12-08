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
        'status'
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }
}
