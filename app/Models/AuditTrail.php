<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    use HasFactory;

    protected $fillable = ['pembiayaan_id','user_id','modul','aktivitas','keterangan','ip_address','user_agent',];

    public function pembiayaan()
    {
        return $this->belongsTo(Pembiayaan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
