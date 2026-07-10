<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    protected $fillable = ['pengajuan_id','assigned_by','assigned_to','status','accepted_at','started_at','finished_at',];

    protected $casts = [
        'accepted_at' => 'datetime',
        'started_at'  => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function pengajuan()
    {
        return $this->belongsTo(Pengajuan::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
