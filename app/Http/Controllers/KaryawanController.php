<?php

namespace App\Http\Controllers;

use App\Http\Requests\KaryawanCreateRequest;
use App\Http\Requests\KaryawanEditRequest;
use App\Models\Cabang;
use App\Models\Karyawan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::with(['user','cabang'])->get();
        return view('karyawans.index', compact('karyawans'));
    }

    public function create()
    {
        $cabangs = Cabang::orderBy('nama_cabang')->get();
    
        $roles = Role::whereNotIn('name', ['superadmin'])
                    ->orderBy('name')
                    ->get();
    
        return view('karyawans.create', compact('cabangs','roles'));
    }

    public function store(KaryawanCreateRequest $request)
    {
        // dd($request->all());
       /*  dd(
            $request->hasFile('avatar'),
            $request->file('avatar')
        ); */

        DB::beginTransaction();

        try {

            $avatarPath = null;

            if ($request->hasFile('avatar')) {
                $avatarPath = $request ->file('avatar') ->store('avatars', 'public');
            }

            // Simpan user
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'avatar'    => $avatarPath,
            ]);

            $user->assignRole($request->role);
            
            Karyawan::create([
                'user_id'       => $user->id,
                'cabang_id'     => $request->cabang_id,
                'nik'           => $request->nik,
                'kelamin'       => $request->kelamin,
                'status'        => $request->status,
                'tempat_lahir'  => $request->tempat_lahir,
                'tgl_lahir'     => $request->tgl_lahir,
                'alamat'        => $request->alamat,
                'tgl_masuk'     => $request->tgl_masuk,
                'no_hp'         => $request->no_hp,
            ]);

            DB::commit();

            return redirect()->route('karyawans')->with('success', 'Data siswa berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(Karyawan $karyawan)
    {
        $karyawan->load('user');

        $cabangs = Cabang::orderBy('nama_cabang')->get();

        $roles = Role::whereNotIn('name', ['superadmin'])
            ->orderBy('name')
            ->get();

        return view('karyawans.edit', compact('karyawan','cabangs','roles'));
    }

    public function update(KaryawanEditRequest $request, Karyawan $karyawan)
    {
        DB::beginTransaction();

        try {
            $user = $karyawan->user;
            // Upload avatar baru
            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = $request
                    ->file('avatar')
                    ->store('avatars', 'public');
            }
            
            // Update user
            
            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Update role
            
            $user->syncRoles([$request->role]);

            //  Update karyawan
            
            $karyawan->update([
                'cabang_id'     => $request->cabang_id,
                'nik'           => $request->nik,
                'kelamin'       => $request->kelamin,
                'status'        => $request->status,
                'tempat_lahir'  => $request->tempat_lahir,
                'tgl_lahir'     => $request->tgl_lahir,
                'alamat'        => $request->alamat,
                'tgl_masuk'     => $request->tgl_masuk,
                'no_hp'         => $request->no_hp,
            ]);

            DB::commit();

            return redirect()
                ->route('karyawans')
                ->with('success', 'Data karyawan berhasil diperbarui.');

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Karyawan $karyawan)
    {
        $karyawan->load(['user','cabang',]);
        return view('karyawans.show', compact('karyawan'));
    }

    public function destroy(Karyawan $karyawan)
    {
        $user = $karyawan->user;
    
        if ($user && $user->hasRole('superadmin')) {
            return back()->with(
                'error',
                'Akun Superadmin tidak dapat dihapus.'
            );
        }
    
        DB::beginTransaction();
    
        try {
    
            if (
                $user &&
                $user->avatar &&
                Storage::disk('public')->exists($user->avatar)
            ) {
                Storage::disk('public')->delete($user->avatar);
            }
    
            if ($user) {
                $user->syncRoles([]);
            }
    
            $karyawan->delete();
    
            if ($user) {
                $user->delete();
            }
    
            DB::commit();
    
            return redirect()->route('karyawans')->with('success', 'Data karyawan berhasil dihapus.');
    
        } catch (\Throwable $e) {
    
            DB::rollBack();
    
            return back()->with('error', $e->getMessage());
        }
    }
}
