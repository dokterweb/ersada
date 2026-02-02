<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Santri extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id','kelas_id', 'kelompok_id', 'kelamin'];

    public function kelasnya()
    {
        return $this->belongsTo(Kelasnya::class, 'kelas_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function kelompok()
    {
        return $this->belongsTo(Kelompok::class, 'kelompok_id', 'id');
    }

    public function ustadz()
    {
        return $this->hasOneThrough(
            Ustadz::class,   // model tujuan
            Kelompok::class,// model perantara
            'id',           // FK di Kelompok (primary key)
            'kelompok_id',  // FK di Ustadz
            'kelompok_id',  // FK di Santri
            'id'            // PK di Kelompok
        );
    }
}
