<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryMitra extends Model
{
    use HasFactory;

    protected $table = 'history_mitra';

    protected $fillable = [
        'mitra_id',
        'action',
        'description',
        'user_id',
    ];

    public function mitra()
    {
        return $this->belongsTo(Mitra::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
