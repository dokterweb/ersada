<?php

namespace App\Services;

use App\Models\AuditTrail;
use App\Models\Pembiayaan;

class AuditTrailService
{
    /**
     * Simpan audit trail
     */
    public function log(?Pembiayaan $pembiayaan,string $modul,string $aktivitas,?string $keterangan = null): AuditTrail {
        return AuditTrail::create([
            'pembiayaan_id' => $pembiayaan?->id,
            'user_id' => auth()->id(),
            'modul' => $modul,
            'aktivitas' => $aktivitas,
            'keterangan' => $keterangan,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}