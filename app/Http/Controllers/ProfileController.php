<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Services\SurveyMediaService;

class ProfileController extends Controller
{

    protected SurveyMediaService $mediaService;

    public function __construct(SurveyMediaService $mediaService){
        $this->mediaService = $mediaService;
    }

    public function index()
    {
        $user = auth()->user();
        $user->load('karyawan.cabang');
        return view('profile.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
    
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => ['required','email',Rule::unique('users')->ignore($user->id),],
            'avatar'        => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'tempat_lahir'  => 'nullable|string|max:100',
            'tgl_lahir'     => 'nullable|date',
            'alamat'        => 'nullable|string|max:500',
            'no_hp'         => 'nullable|string|max:30',
            'password'      => 'nullable|confirmed|min:8',
        ]);
    
        DB::transaction(function () use ($request, $user) {
            if ($request->hasFile('avatar')) {
                // hapus avatar lama
                if ($user->avatar) {
                    $this->mediaService->deleteMedia($user->avatar);
                }
    
                $user->avatar = $this->mediaService->storeImage(
                    file: $request->file('avatar'),
                    folder: 'avatars',
                    width: 400,
                    height: 400,
                    quality: 80
                );
            }
    
            $user->name = $request->name;
            $user->email = $request->email;
    
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
    
            $user->save();
    
            if ($user->karyawan) {
                $user->karyawan->update([
                    'tempat_lahir' => $request->tempat_lahir,
                    'tgl_lahir'    => $request->tgl_lahir,
                    'alamat'       => $request->alamat,
                    'no_hp'        => $request->no_hp,
                ]);
            }
        });
    
        return redirect()->route('profile.index')->with('success', 'Profile berhasil diperbarui.');
    }
  

}