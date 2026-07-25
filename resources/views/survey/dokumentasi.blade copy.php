@extends('survey.layouts.wizard')

@section('survey-content')
<h3 class="mb-4">
    <i class="fa fa-folder-open text-primary"></i>
    Step 1 - Pemeriksaan Berkas

</h3>

@php
    $pasangan = $survey->pengajuan->referensis->firstWhere('jenis', 'pasangan');
    $penjamin = $survey->pengajuan->referensis->firstWhere('jenis', 'penjamin');
@endphp

<form action="{{ route('survey.storeDokumentasi',$survey) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">
                        <i class="ti ti-home"></i>
                        Dokumentasi Rumah Tinggal
                    </h3>
                    <span class="badge bg-yellow-lt" id="badgeRumah">
                        Belum Lengkap
                    </span>
                </div>
            
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            Foto Rumah Tampak Depan
                        </label>
                        <input type="file" class="form-control" name="rumah[depan]" multipleaccept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            Foto Rumah Tampak Dalam
                        </label>
                        <input type="file" class="form-control" name="rumah[dalam]" multiple accept="image/*">
                    </div>
                    <div>
                        <label class="form-label">
                            Foto Rumah Tampak Samping
                        </label>
                        <input type="file" class="form-control" name="rumah[samping]" multiple accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
           <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">
                        <i class="ti ti-building-store"></i>
                        Dokumentasi Tempat Usaha
                    </h3>
                    <span class="badge bg-yellow-lt" id="badgeUsaha">
                        Belum Lengkap
                    </span>
                </div>
                <div class="card-body">
                    <input type="file" class="form-control" name="usaha[]" multiple accept="image/*">
                    <small class="text-muted">
                        Upload sebanyak mungkin foto tempat usaha.
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @foreach($survey->pengajuan->jaminans as $jaminan)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="ti ti-shield"></i>
                        Jaminan {{ $loop->iteration }} | {{ $jaminan->jenis_jaminan }} - {{ $jaminan->nama_jaminan }}
                    </h3>
                    <span class="badge bg-yellow-lt badge-jaminan" data-id="{{ $jaminan->id }}">
                        Belum Lengkap
                    </span>
            
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">
                                Foto Depan
                            </label>
                            <input type="file" class="form-control file-jaminan" data-id="{{ $jaminan->id }}" name="jaminan[{{ $jaminan->id }}][depan]" multiple accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                Foto Samping
                            </label>
                            <input type="file" class="form-control file-jaminan" data-id="{{ $jaminan->id }}" name="jaminan[{{ $jaminan->id }}][samping]" multiple accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                Foto Dalam
                            </label>
                            <input type="file" class="form-control file-jaminan" data-id="{{ $jaminan->id }}" name="jaminan[{{ $jaminan->id }}][dalam]" multiple accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">
                                Foto Belakang
                            </label>
                            <input type="file" class="form-control file-jaminan" data-id="{{ $jaminan->id }}" name="jaminan[{{ $jaminan->id }}][belakang]" multiple accept="image/*">
                        </div>
                    </div>
                    <hr>
                    <label class="form-label">
                        Video Jaminan
                    </label>
                    <input type="file" class="form-control video-jaminan" data-id="{{ $jaminan->id }}" name="video[{{ $jaminan->id }}][]" multiple accept="video/*">
                </div>
            </div>
            @endforeach
         </div>
    </div>
    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('survey.berkas',$survey) }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left"></i>
            Kembali
        </a>
        <button class="btn btn-primary">
            Simpan & Review
            <i class="ti ti-arrow-right"></i>
        </button>
    </div>
</form>

@endsection

@section('scripts')

<script>

</script>
@endsection