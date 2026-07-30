<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

  
    public function karyawan()
    {
        return $this->hasOne(Karyawan::class);
    }

    public function pembayaranAngsurans()
    {
        return $this->hasMany(Pembayaran_angsuran::class,'created_by');
    }

    public function auditTrails()
    {
        return $this->hasMany(AuditTrail::class);
    }

    public function getCabangId(): ?int
    {
        return $this->karyawan?->cabang_id;
    }

    public function getMarketingId(): ?int
    {
        return $this->karyawan?->id;
    }
}
