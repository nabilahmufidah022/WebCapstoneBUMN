<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $table = 'feedbacks';

    protected $fillable = [
        'mitra_event_participation_id',
        'user_id',
        'tujuan_manfaat',
        'materi_narasumber',
        'susunan_waktu',
        'teknis_fasilitas',
        'panitia_pelayanan',
        'informasi_publikasi',
        'kepuasan_peserta',
        'saran_masukan',
    ];

    public function participation()
    {
        return $this->belongsTo(MitraEventParticipation::class, 'mitra_event_participation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(FeedbackReply::class);
    }
}
