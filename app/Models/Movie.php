<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    //
    use SoftDeletes;
    //mendaftarkan column yang akan diisi oleh pengguna (column migration selain id dan timestamps)
    protected $fillable = ['title', 'genre', 'duration', 'director', 'age_rating', 'poster', 'description', 'actived'];

    public function schedules(){
        return $this->hasMany(Schedule::class);
    }
}
