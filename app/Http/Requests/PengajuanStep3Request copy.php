<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanStep3Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // PASANGAN
            'pasangan.nama' => 'nullable|string|max:255',
            'pasangan.tempat_lahir' => 'nullable|string|max:255',
            'pasangan.tgl_lahir' => 'nullable|date',

            // PENJAMIN
            'penjamin.nama' => 'nullable|string|max:255',
            'penjamin.tempat_lahir' => 'nullable|string|max:255',
            'penjamin.tgl_lahir' => 'nullable|date',
            'penjamin.hubungan' => 'nullable|string|max:255',
            'penjamin.no_hp' => 'nullable|string|max:20',
            'penjamin.alamat' => 'nullable|string',

            // SAUDARA
            'saudara.*.nama' => 'nullable|string|max:255',
            'saudara.*.tempat_lahir' => 'nullable|string|max:255',
            'saudara.*.tgl_lahir' => 'nullable|date',
            'saudara.*.hubungan' => 'nullable|string|max:255',
            'saudara.*.no_hp' => 'nullable|string|max:20',
            'saudara.*.alamat' => 'nullable|string',
        ];
    }
}
