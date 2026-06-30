<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karyawan extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'karyawans';
    protected $fillable = ['user_id', 'nik', 'tempat_lahir', 'tgl_lahir', 'alamat', 'no_hp', 'tgl_masuk', 'kelamin', 'status', 'cabang_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }
}
