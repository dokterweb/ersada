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
            <form action="{{ route('pengajuan.storeJaminan',$pengajuan->id) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">REVIEW</li>
                            <li class="step-item">ANALISA</li>
                            <li class="step-item active">JAMINAN</li>
                        </ul>
        
                    </div>
                    <div class="card-header bg-success">
                        <h3 class="card-title">DATA JAMINAN</h3>
                    </div>
                    <div class="card-body">
                        <button type="button" id="addJaminan" class="btn btn-success mb-3">
                            Tambah Jaminan
                        </button>
                        <div id="jaminan_wrapper">
                            @foreach($jaminans as $index => $jaminan)
                                <div class="card p-3 mb-3">
                                    <input type="hidden" name="jaminan[{{ $index }}][id]" value="{{ $jaminan->id }}">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Jenis Jaminan</label>
                                            <input type="text" class="form-control" name="jaminan[{{ $index }}][jenis_jaminan]" value="{{ $jaminan->jenis_jaminan }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nama Jaminan</label>
                                            <input type="text" class="form-control" name="jaminan[{{ $index }}][nama_jaminan]" value="{{ $jaminan->nama_jaminan }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Nilai Taksiran</label>
                                            <input type="number" class="form-control" name="jaminan[{{ $index }}][nilai_taksiran]" value="{{ $jaminan->nilai_taksiran }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label>Detail</label>
                                            <input type="text" class="form-control" name="jaminan[{{ $index }}][detail_jaminan]" value="{{ $jaminan->detail_jaminan }}">
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-danger mt-3 removeJaminan">
                                        Hapus
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
    $('#addJaminan').click(function(){
        index++;
        let html = `
        <div class="card p-3 mb-3">
            <div class="row">
                <div class="col-md-3">
                    <label>Jenis Jaminan</label>
                    <input class="form-control" name="jaminan[${index}][jenis_jaminan]">
                </div>
                <div class="col-md-3">
                    <label>Nama Jaminan</label>
                    <input class="form-control" name="jaminan[${index}][nama_jaminan]">
                </div>
                <div class="col-md-3">
                    <label>Nilai Taksiran</label>
                    <input class="form-control" name="jaminan[${index}][nilai_taksiran]">
                </div>
                <div class="col-md-3">
                    <label>Detail</label>
                    <input class="form-control" name="jaminan[${index}][detail_jaminan]">
                </div>
            </div>
            <button type="button" class="btn btn-danger mt-3 removeJaminan">
                Hapus
            </button>
        </div>
        `;
        $('#jaminan_wrapper').append(html);
    
    });
    
    
    $(document).on('click','.removeJaminan',function()
        {
        $(this).closest('.card').remove();
        }
    );
    
    </script>
@endsection