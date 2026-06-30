<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengajuanStep4Request extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'documents.*' =>'nullable|mimes:jpg,jpeg,png,pdf|max:2048'
        ];
    }
}
