@extends('layouts.app')

@section('title','My Profile')

@section('content')

<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h2 class="page-title">
                    My Profile
                </h2>

                <div class="text-secondary">
                    Kelola informasi akun dan data pribadi Anda.
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-body">
    <div class="container-xl">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible">
                {{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            @php
                                $avatar = $user->avatar? asset('storage/'.$user->avatar): asset('images/default-avatar.png');
                             @endphp
                        <img id="preview-avatar" src="{{ $avatar }}" class="avatar avatar-xl rounded-circle mb-3"
                            style="width:160px;height:160px;object-fit:cover;">
                
                        <div class="mb-3">
                            <label class="btn btn-primary btn-sm">
                                <i class="ti ti-camera"></i>
                                Pilih Avatar
                                <input type="file" name="avatar" id="avatar" accept="image/*" hidden>
                            </label>
                        </div>
                        <small class="text-muted">
                            JPG / PNG
                            Maksimal 2 MB
                        </small>
                            <h3 class="mb-1">{{ $user->name }}</h3>
                            <div class="text-secondary">
                                {{ $user->email }}
                            </div>
                            <div class="text-secondary">
                                {{$user->getRoleNames()->first()}}
                            </div>
                            <hr>
                            <div class="mb-2">
                                <span class="badge bg-blue">
                                    {{ optional($user->karyawan?->cabang)->nama_cabang }}
                                </span>
                            </div>
                            <div>
                                @if(optional($user->karyawan)->status)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Non Aktif</span>
                                @endif
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    Upload avatar akan tersedia
                                    pada Sprint 10.4
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ========================= --}}
                {{-- CONTENT --}}
                {{-- ========================= --}}
                <div class="col-md-9">

                    {{-- INFORMASI LOGIN --}}

                    <div class="card mb-4">

                        <div class="card-header">

                            <h3 class="card-title">

                                Informasi Login

                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Nama
                                    </label>

                                    <input
                                        type="text"
                                        name="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        value="{{ old('name',$user->name) }}">

                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Email
                                    </label>

                                    <input
                                        type="email"
                                        name="email"
                                        class="form-control @error('email') is-invalid @enderror"
                                        value="{{ old('email',$user->email) }}">

                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            <hr>

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Password Baru
                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Konfirmasi Password
                                    </label>

                                    <input
                                        type="password"
                                        name="password_confirmation"
                                        class="form-control">

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- DATA KARYAWAN --}}

                    <div class="card">

                        <div class="card-header">

                            <h3 class="card-title">

                                Data Karyawan

                            </h3>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        NIK
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        readonly
                                        value="{{ optional($user->karyawan)->nik }}">

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Cabang
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        readonly
                                        value="{{ optional(optional($user->karyawan)->cabang)->nama_cabang }}">

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">
                                        Tanggal Masuk
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        readonly
                                        value="{{ optional($user->karyawan)->tgl_masuk }}">

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Tempat Lahir
                                    </label>

                                    <input
                                        type="text"
                                        name="tempat_lahir"
                                        class="form-control"
                                        value="{{ old('tempat_lahir',optional($user->karyawan)->tempat_lahir) }}">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Tanggal Lahir
                                    </label>

                                    <input
                                        type="date"
                                        name="tgl_lahir"
                                        class="form-control"
                                        value="{{ old('tgl_lahir',optional($user->karyawan)->tgl_lahir) }}">

                                </div>

                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Jenis Kelamin
                                    </label>

                                    <input
                                        type="text"
                                        readonly
                                        class="form-control"
                                        value="{{ optional($user->karyawan)->kelamin }}">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        No HP
                                    </label>

                                    <input
                                        type="text"
                                        name="no_hp"
                                        class="form-control"
                                        value="{{ old('no_hp',optional($user->karyawan)->no_hp) }}">

                                </div>

                            </div>

                            <div class="mb-3">

                                <label class="form-label">
                                    Alamat
                                </label>

                                <textarea
                                    name="alamat"
                                    rows="4"
                                    class="form-control">{{ old('alamat',optional($user->karyawan)->alamat) }}</textarea>

                            </div>

                        </div>

                        <div class="card-footer text-end">

                            <button
                                class="btn btn-primary">

                                <i class="ti ti-device-floppy"></i>

                                Simpan Perubahan

                            </button>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>
</div>

@endsection


@section('scripts')

<script>
$('#avatar').change(function(){
    const file=this.files[0];
    if(!file) return;
    let reader=new FileReader();
    reader.onload=function(e){
        $('#preview-avatar')
            .attr('src',e.target.result);
    }
    reader.readAsDataURL(file);
});

</script>
@endsection