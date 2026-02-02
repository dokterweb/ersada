<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Madina extends Model
{
    protected $table = 'madina';

    public function murojaahs()
    {
        return $this->hasMany(Murojaah::class, 'surat_id');
    }
}
