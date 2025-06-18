<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kursus extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'kategori',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    public function getStatusAttribute()
    {
        if (is_null($this->tanggal_selesai)) {
            return 'Lifetime';
        }

        return Carbon::now()->lte(Carbon::parse($this->tanggal_selesai)) ? 'Aktif' : 'Kadaluarsa';
    }
}
