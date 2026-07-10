<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalPengajuan extends Model
{
    protected $fillable = ['pengajuan_id','user_id','role_name','aksi','status_sebelumnya','status_sesudahnya','catatan',];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
