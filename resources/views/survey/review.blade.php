@extends('survey.layouts.wizard')

@section('survey-content')
<h3 class="mb-4">
    <i class="fa fa-folder-open text-primary"></i>
    Dokumentasi
</h3>

<div class="row">
    <h3>Rumah</h3>
    @foreach($rumah as $foto)
    <div class="col-md-3 mb-3">
        <div class="card">
            <a href="{{ asset('storage/'.$foto->file) }}" target="_blank">
                <img
                    src="{{ asset('storage/'.$foto->file) }}"
                    class="img-fluid rounded"
                    style="height:200px;width:100%;object-fit:cover;"
                    alt="{{ ucfirst($foto->posisi) }}">
            </a>
    
            <div class="card-footer text-center">
                <strong>{{ ucfirst($foto->posisi) }}</strong>
                <br>
                <small class="text-muted">
                    Klik gambar untuk memperbesar
                </small>
            </div>
        </div>
    </div>
    @endforeach
    <hr>
    <h3>Usaha</h3>
    @foreach($usaha as $foto)
    <div class="col-md-3 mb-3">
        <div class="card">
            <a href="{{ asset('storage/'.$foto->file) }}" target="_blank">
                <img
                    src="{{ asset('storage/'.$foto->file) }}"
                    class="img-fluid rounded"
                    style="height:200px;width:100%;object-fit:cover;"
                    alt="{{ ucfirst($foto->posisi) }}">
            </a>
    
            <div class="card-footer text-center">
                <strong>{{ ucfirst($foto->posisi) }}</strong>
                <br>
                <small class="text-muted">
                    Klik gambar untuk memperbesar
                </small>
            </div>
        </div>
    </div>
    @endforeach
    <hr>
    <h3>Usaha</h3>
    @foreach($jaminan as $foto)
    <div class="col-md-3 mb-3">
        <div class="card">
            <a href="{{ asset('storage/'.$foto->file) }}" target="_blank">
                <img
                    src="{{ asset('storage/'.$foto->file) }}"
                    class="img-fluid rounded"
                    style="height:200px;width:100%;object-fit:cover;"
                    alt="{{ ucfirst($foto->posisi) }}">
            </a>
    
            <div class="card-footer text-center">
                <strong>{{ ucfirst($foto->posisi) }}</strong>
                <br>
                <small class="text-muted">
                    Klik gambar untuk memperbesar
                </small>
            </div>
        </div>
    </div>
    @endforeach
    <hr>
    <h3>Video</h3>
    @foreach($videos as $video)
        <div class="col-md-4 mb-4">
            <div class="card">
                <a href="{{ asset('storage/'.$video->file) }}" target="_blank" class="btn btn-sm btn-primary">
                     Full Screen
                 </a>
                <div class="card-footer text-center">
                    <strong>Video Jaminan</strong>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($mode == 'surveyor')
<form action="{{ route('survey.submit',$survey) }}" method="POST">
    @csrf
    <div class="card mt-4">
        <div class="card-header bg-warning-lt">
            <strong>
                KONFIRMASI HASIL SURVEY
            </strong>
        </div>
        <div class="card-body">
            <div class="form-check">
                <input class="form-check-input @error('confirm_submit') is-invalid @enderror"
                    type="checkbox" id="confirm_submit" name="confirm_submit" value="1">
                <label class="form-check-label" for="confirm_submit">
                    Saya menyatakan seluruh hasil survey telah sesuai
                    dengan kondisi lapangan dan siap dikirim ke
                    SPV Surveyor untuk direview.
                </label>
            </div>
            @error('confirm_submit')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>
    </div>
    <div class="mt-4 d-flex justify-content-between">
        <a href="{{ route('survey.dokumentasi',$survey) }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left"></i>
            Kembali
        </a>
        <button class="btn btn-success"><i class="ti ti-send"></i>
            Kirim ke SPV Survey
        </button>
    </div>
</form>
@endif

@if($mode=='spv_self')
<form action="{{ route('survey.submit',$survey) }}" method="POST">
    @csrf
    <div class="card mt-4">
        <div class="card-header bg-success-lt">
            <strong>
                KONFIRMASI HASIL SURVEY
            </strong>
        </div>
        <div class="card-body">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="confirm_submit" name="confirm_submit" value="1">
                <label class="form-check-label" for="confirm_submit">
                    Saya menyatakan hasil survey telah sesuai
                    dan siap dikirim kepada Pimpinan.
                </label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-between">
        <a href="{{ route('survey.dokumentasi',$survey) }}" class="btn btn-secondary">
            Kembali
        </a>
        <button class="btn btn-success">Kirim ke Pimpinan</button>
    </div>
</form>
@endif

@endsection
