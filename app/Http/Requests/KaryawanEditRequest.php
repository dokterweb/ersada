<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class KaryawanEditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check()
        && auth()->user()->hasRole('superadmin');
    }

    public function rules(): array
    {
        $userId = $this->route('karyawan')->user_id;
        $karyawanId = $this->route('karyawan')->id;

        return [
            // users
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $userId,
            'password'      => 'nullable|min:6',
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // karyawan
            'cabang_id'     => 'required|exists:cabangs,id',
            'nik'           => 'required|string|max:255|unique:karyawans,nik,' . $karyawanId,
            'tempat_lahir'  => 'required|string|max:255',
            'tgl_lahir'     => 'required|date',
            'alamat'        => 'required|string',
            'no_hp'         => 'required|string|max:20',
            'tgl_masuk'     => 'required|date',
            'kelamin'       => 'required|in:laki-laki,perempuan',
            'status'        => 'required|in:0,1',
            'role'          => 'required|exists:roles,name',
        ];
    }
}
