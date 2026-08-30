@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('pengajuan.storeJaminan',$pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">ANALISA</li>
                            <li class="step-item active">JAMINAN</li>
                            <li class="step-item">ANALISA KAPITAL</li>
                            <li class="step-item">REVIEW FINAL</li>
                        </ul>
        
                    </div>
                    <div class="card-header bg-success">
                        <h3 class="card-title">DATA JAMINAN</h3>
                    </div>
                    <div class="card-body">

                        <button
                            type="button"
                            id="addJaminan"
                            class="btn btn-success mb-3"
                        >
                            Tambah Jaminan
                        </button>


                        <div id="jaminan_wrapper">

                            @foreach($jaminans as $index => $jaminan)

                                <div class="card p-3 mb-3 jaminan-item">

                                    {{-- ID JAMINAN --}}
                                    <input
                                        type="hidden"
                                        name="jaminan[{{ $index }}][id]"
                                        value="{{ $jaminan->id }}"
                                    >

                                    <div class="row">

                                        {{-- JENIS JAMINAN --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Jenis Jaminan
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                name="jaminan[{{ $index }}][jenis_jaminan]"
                                                value="{{ old("jaminan.$index.jenis_jaminan", $jaminan->jenis_jaminan) }}"
                                                required
                                            >

                                        </div>


                                        {{-- NAMA JAMINAN --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Nama Jaminan
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                name="jaminan[{{ $index }}][nama_jaminan]"
                                                value="{{ old("jaminan.$index.nama_jaminan", $jaminan->nama_jaminan) }}"
                                                required
                                            >

                                        </div>


                                        {{-- NILAI TAKSIRAN --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Nilai Taksiran
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input
                                                type="number"
                                                class="form-control"
                                                name="jaminan[{{ $index }}][nilai_taksiran]"
                                                value="{{ old("jaminan.$index.nilai_taksiran", $jaminan->nilai_taksiran) }}"
                                                min="0"
                                                required
                                            >

                                        </div>


                                        {{-- DETAIL --}}
                                        <div class="col-md-3 mb-3">

                                            <label class="form-label">
                                                Detail
                                            </label>

                                            <input
                                                type="text"
                                                class="form-control"
                                                name="jaminan[{{ $index }}][detail_jaminan]"
                                                value="{{ old("jaminan.$index.detail_jaminan", $jaminan->detail_jaminan) }}"
                                            >

                                        </div>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- UPLOAD DOKUMEN --}}
                                    {{-- ================================================= --}}

                                    <div class="row">

                                        <div class="col-md-8 mb-3">

                                            <label class="form-label">
                                                Upload Dokumen Jaminan
                                            </label>

                                            <input
                                                type="file"
                                                class="form-control"
                                                name="jaminan[{{ $index }}][files][]"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                multiple
                                            >

                                            <div class="form-text">
                                                Bisa memilih beberapa file sekaligus.
                                                Format PDF, JPG, JPEG, PNG.
                                                Maksimal 50 MB per file.
                                            </div>

                                            @error("jaminan.$index.files")
                                                <div class="text-danger small">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                            @error("jaminan.$index.files.*")
                                                <div class="text-danger small">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>

                                    </div>


                                    {{-- ================================================= --}}
                                    {{-- DOKUMEN YANG SUDAH ADA --}}
                                    {{-- ================================================= --}}

                                    @if($jaminan->dokumenJaminans->count() > 0)

                                        <div class="mt-2">

                                            <label class="form-label fw-bold">
                                                Dokumen yang Sudah Diupload
                                            </label>

                                            <div class="list-group">

                                                @foreach($jaminan->dokumenJaminans as $dokumen)

                                                    <div class="list-group-item d-flex justify-content-between align-items-center">

                                                        <div>

                                                            @if(
                                                                str_starts_with(
                                                                    $dokumen->mime_type ?? '',
                                                                    'image/'
                                                                )
                                                            )
                                                                🖼️
                                                            @else
                                                                📄
                                                            @endif

                                                            {{ $dokumen->nama_file }}

                                                            @if($dokumen->file_size)

                                                                <small class="text-muted">
                                                                    (
                                                                    {{ number_format($dokumen->file_size / 1024, 1) }}
                                                                    KB
                                                                    )
                                                                </small>

                                                            @endif

                                                        </div>


                                                        <div>

                                                            <a
                                                                href="{{ asset('storage/' . $dokumen->file_path) }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-primary"
                                                            >
                                                                Lihat
                                                            </a>

                                                        </div>

                                                    </div>

                                                @endforeach

                                            </div>

                                        </div>

                                    @else

                                        <div class="alert alert-warning mt-2">
                                            Belum ada dokumen jaminan.
                                        </div>

                                    @endif


                                    {{-- HAPUS JAMINAN --}}

                                    <button
                                        type="button"
                                        class="btn btn-danger mt-3 removeJaminan"
                                    >
                                        Hapus Jaminan
                                    </button>

                                </div>

                            @endforeach

                        </div>

                    </div>
                    <div class="card-footer text-end">
                        <a href="{{ route('pengajuan.analisa',$pengajuan->id) }}"class="btn btn-warning">
                            Previous
                        </a>
                        <button class="btn btn-primary">
                            Simpan & Lanjut Kapital
                        </button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@section('scripts')
<script>
let index = {{ $jaminans->count() }};


$('#addJaminan').click(function () {

    let html = `
        <div class="card p-3 mb-3 jaminan-item">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">
                        Jenis Jaminan
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="jaminan[${index}][jenis_jaminan]" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Nama Jaminan
                        <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" name="jaminan[${index}][nama_jaminan]" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Nilai Taksiran
                        <span class="text-danger">*</span>
                    </label>
                    <input type="number" class="form-control" name="jaminan[${index}][nilai_taksiran]" min="0" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Detail</label>
                    <input type="text" class="form-control" name="jaminan[${index}][detail_jaminan]">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">Upload Dokumen Jaminan</label>
                    <input type="file" class="form-control" name="jaminan[${index}][files][]"
                        accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <div class="form-text">
                        Bisa memilih beberapa file sekaligus.
                        PDF, JPG, JPEG, PNG.
                        Maksimal 50 MB per file.
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-danger mt-2 removeJaminan">
                Hapus Jaminan
            </button>
        </div>
    `;

    $('#jaminan_wrapper').append(html);
    index++;
});


/*
|--------------------------------------------------------------------------
| HAPUS JAMINAN DARI FORM
|--------------------------------------------------------------------------
*/

$(document).on('click','.removeJaminan',function () {
        $(this).closest('.jaminan-item').remove();
    }
);
</script>
@endsection