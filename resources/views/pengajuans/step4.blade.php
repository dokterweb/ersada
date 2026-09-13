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
            <form action="{{ route('pengajuan.storeStep4',$pengajuan->id) }}" method="POST" enctype="multipart/form-data" class="card">
                @csrf
                <div class="card-body">
                    {{-- STEP INDICATOR --}}
                    <ul class="steps steps-green steps-counter my-4">
                        <li class="step-item">STEP 1</li>
                        <li class="step-item">STEP 2</li>
                        <li class="step-item">STEP 3</li>
                        <li class="step-item active">STEP 4</li>
                        <li class="step-item">REVIEW</li>
                        <li class="step-item">ANALISA</li>
                        <li class="step-item">JAMINAN</li>
                        <li class="step-item">ANALISA KAPITAL</li>
                        <li class="step-item">REVIEW FINAL</li>
                    </ul>
                </div>
                <div class="alert alert-info">Jenis Pengajuan :
                    <strong>                
                        {{ $pengajuan->kategori_nasabah == 'payroll'
                            ? 'Dinas / Potong Gaji ATM'
                            : 'Umum'
                        }}
                    </strong>
                </div>
          
              {{-- ========================================================= --}}
              {{-- DOKUMEN WAJIB --}}
              {{-- ========================================================= --}}
              <div class="card-header bg-success text-white">
                  <h3 class="card-title">DOKUMEN WAJIB</h3>
              </div>
          
             <div class="card-body">

                {{-- ERROR --}}
                @if($errors->has('error'))

                    <div class="alert alert-danger">
                        {!! nl2br(e($errors->first('error'))) !!}
                    </div>

                @endif


                {{-- SUCCESS --}}
                @if(session('success'))

                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>

                @endif


                {{-- ========================================================= --}}
                {{-- DOKUMEN WAJIB --}}
                {{-- ========================================================= --}}

                <h4 class="mb-3">
                    Dokumen Wajib
                </h4>


                @foreach($documents['required'] as $doc)

                    @php
                        $uploadedDoc = $uploaded->get($doc['code']);
                    @endphp


                    <div class="card mb-3">

                        <div class="card-body">

                            <div class="row align-items-center">

                                {{-- NAMA DOKUMEN --}}

                                <div class="col-md-4">

                                    <label class="form-label fw-bold">

                                        {{ $doc['label'] }}

                                        <span class="text-danger">
                                            *
                                        </span>

                                    </label>

                                </div>


                                {{-- FILE --}}

                                <div class="col-md-5">

                                    <input
                                        type="file"
                                        name="documents[{{ $doc['code'] }}]"
                                        class="form-control"
                                        accept=".jpg,.jpeg,.png,.pdf"
                                    >

                                    <div class="form-text">
                                        PDF, JPG, JPEG atau PNG.
                                        Maksimal 50 MB.
                                    </div>

                                </div>


                                {{-- STATUS --}}

                                <div class="col-md-3">

                                    @if($uploadedDoc)

                                        <div class="mb-2">

                                            <span class="badge bg-success">
                                                Sudah Upload
                                            </span>

                                        </div>

                                        <a
                                            href="{{ asset('storage/' . $uploadedDoc->file_path) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-primary"
                                        >
                                            Lihat Dokumen
                                        </a>

                                    @else

                                        <span class="badge bg-danger">
                                            Belum Upload
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach


                {{-- ========================================================= --}}
                {{-- DOKUMEN OPTIONAL --}}
                {{-- ========================================================= --}}

                @if(!empty($documents['optional']))

                    <hr class="my-4">

                    <h4 class="mb-3">
                        Dokumen Optional
                    </h4>


                    @foreach($documents['optional'] as $doc)

                        @php
                            $uploadedDoc = $uploaded->get($doc['code']);
                        @endphp


                        <div class="card mb-3">

                            <div class="card-body">

                                <div class="row align-items-center">

                                    {{-- NAMA --}}

                                    <div class="col-md-4">

                                        <label class="form-label fw-bold">

                                            {{ $doc['label'] }}

                                            <span class="text-muted">
                                                (Optional)
                                            </span>

                                        </label>

                                    </div>

                                {{-- KHUSUS BI CHECKING --}}
                                @if($doc['code'] === 'bi_checking')

                                    <div class="small text-muted mt-1">
                                        Dokumen BI Checking dapat diupload
                                        untuk semua jenis nasabah.
                                    </div>

                                @endif
                                
                                    {{-- FILE --}}

                                    <div class="col-md-5">

                                        <input
                                            type="file"
                                            name="documents[{{ $doc['code'] }}]"
                                            class="form-control"
                                            accept=".jpg,.jpeg,.png,.pdf"
                                        >

                                        <div class="form-text">
                                            PDF, JPG, JPEG atau PNG.
                                            Maksimal 50 MB.
                                        </div>

                                    </div>


                                    {{-- STATUS --}}

                                    <div class="col-md-3">

                                        @if($uploadedDoc)

                                            <div class="mb-2">

                                                <span class="badge bg-success">
                                                    Sudah Upload
                                                </span>

                                            </div>

                                            <a
                                                href="{{ asset('storage/' . $uploadedDoc->file_path) }}"
                                                target="_blank"
                                                class="btn btn-sm btn-primary"
                                            >
                                                Lihat Dokumen
                                            </a>

                                        @else

                                            <span class="badge bg-secondary">
                                                Optional
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    @endforeach

                @endif

            </div>
        
              {{-- BUTTON --}}
              <div class="card-footer text-end">
                <a href="{{ route('pengajuan.step3',$pengajuan->id) }}"class="btn btn-warning">Previous</a>
                  <button type="submit" class="btn btn-primary">Simpan & Lanjut Review</button>
          
              </div>  
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection