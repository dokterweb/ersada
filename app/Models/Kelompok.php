<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelompok extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable=['nama_kelompok','jenis'];

    public function ustadz()
    {
        // 1 kelompok punya 1 ustadz
        return $this->hasOne(Ustadz::class, 'kelompok_id', 'id');
    }

    public function santris()
    {
        return $this->hasMany(Santri::class);
    }
}
