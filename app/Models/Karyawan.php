<?php

namespace App\Models;

use App\Models\User;
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

    public function scopeMarketing($query)
    {
        return $query
            ->select('karyawans.*')
            ->join('users', 'users.id', '=', 'karyawans.user_id')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->whereIn('roles.name', [
                'marketing',
                'spvmarketing'
            ])
            ->with('user')
            ->orderBy('users.name');
    }
}
