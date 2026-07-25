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

<form action="{{ route('approvalSurvey.store',$pengajuan) }}" method="POST">
    @csrf
    <div class="card mt-4">
        <div class="card-header bg-warning">
            <strong>KEPUTUSAN PIMPINAN</strong>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Keputusan</label>
                <div>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="keputusan" value="setuju" required>
                        <span class="form-check-label">
                            SETUJU
                        </span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="keputusan" value="revisi">
                        <span class="form-check-label">
                            REVISI
                        </span>
                    </label>
                    <label class="form-check">
                        <input class="form-check-input" type="radio" name="keputusan" value="tolak">
                        <span class="form-check-label">
                            TOLAK
                        </span>
                    </label>
                </div>
            </div>
            {{-- hanya muncul jika SETUJU --}}

            <div id="form-setuju" style="display:none;">

                <div class="row">

                    <div class="col-md-6">

                        <div class="mb-3">

                            <label>Plafond Disetujui</label>

                            <input type="number"
                                   class="form-control"
                                   name="plafond_disetujui"
                                   id="plafond_disetujui">

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="mb-3">

                            <label>Tenor Disetujui</label>

                            <select class="form-select"
                                    name="tenor_disetujui"
                                    id="tenor_disetujui">

                                <option value="">-- Pilih --</option>

                                <option value="1">1 Bulan</option>
                                <option value="2">2 Bulan</option>
                                <option value="3">3 Bulan</option>
                                <option value="4">4 Bulan</option>
                                <option value="5">5 Bulan</option>

                                <option value="12">12 Bulan</option>
                                <option value="18">18 Bulan</option>
                                <option value="24">24 Bulan</option>
                                <option value="30">30 Bulan</option>
                                <option value="36">36 Bulan</option>
                                <option value="42">42 Bulan</option>
                                <option value="48">48 Bulan</option>
                                <option value="60">60 Bulan</option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>

            <div class="mb-3">
                <label class="form-label">
                    Catatan
                </label>
                <textarea class="form-control" rows="5" name="catatan"></textarea>
            </div>
        </div>
    </div>
    <div class="mt-4 d-flex justify-content-between">
        <a href="{{ route('approvalSurvey.index') }}" class="btn btn-secondary">
            Kembali
        </a>
        <button class="btn btn-success">
            Simpan Keputusan
        </button>
    </div>
</form>
@endsection


@section('scripts')

<script>
$(function(){

$('input[name="keputusan"]').on('change', function(){

    if($(this).val() === 'setuju'){

        $('#form-setuju').stop(true,true).slideDown();

    }else{

        $('#form-setuju').stop(true,true).slideUp();

    }

});

});
</script>
@endsection