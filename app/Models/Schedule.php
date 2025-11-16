<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['cinema_id', 'movie_id', 'hours', 'price'];

    // casts : memastikan format data
    protected function casts() : array
    {
        return [
            // mengubah format json migration hours jadi array
            'hours' => 'array'
        ];
    }

    // schedule pegang posisi kedua, panggil relasi dengan belongsTo
    // cinema pegang posisi pertama dan jenis (one) jadi gunakan tunggal
    // jika foreign key tidak sesuai berarti harus didaftarkan di dalam belongsTo contoh : return $this->belongsTo(Cinema::class, 'bioskop_id', 'id');
    public function cinema(){
        return $this->belongsTo(Cinema::class);
    }

    public function movie(){
        return $this->belongsTo(Movie::class);
    }
}
