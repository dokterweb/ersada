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
          
                  <div class="row">
          
                      @foreach($docs['required'] as $doc)
          
                          <div class="col-md-4 mb-4">
          
                              <label class="form-label">
          
                                  {{ $doc['label'] }}
          
                                  <span class="text-danger">*</span>
          
                              </label>
          
                              <input type="file"
                                     name="documents[{{ $doc['code'] }}]"
                                     class="form-control">
          
                              {{-- sudah upload --}}
                              @if(isset($uploaded[$doc['code']]))
          
                                  <small class="text-success">
          
                                      Uploaded :
                                         <a href="{{ asset('storage/'.$uploaded[$doc['code']]->file_path) }}" target="_blank">
                                            {{ $uploaded[$doc['code']]->nama_file }}
                                        </a>
          
                                  </small>
          
                                  <br>
          
                                  <small>
          
                                      Status :
                                      {{ $uploaded[$doc['code']]->status }}
          
                                  </small>
          
                              @endif
          
                          </div>
          
                      @endforeach
          
                  </div>
          
              </div>
          
          
              {{-- ========================================================= --}}
              {{-- SALAH SATU WAJIB --}}
              {{-- ========================================================= --}}
              <div class="card-header bg-warning">
                  <h3 class="card-title">
          
                      JAMINAN (UPLOAD MINIMAL SALAH SATU)
          
                  </h3>
              </div>
          
              <div class="card-body">
          
                  <div class="alert alert-warning">
          
                      Pilih minimal salah satu:
                      <strong>BPKB</strong>
                      atau
                      <strong>Surat Tanah</strong>.
                      Bisa upload keduanya.
          
                  </div>
          
                  <div class="row">
          
                      @foreach($docs['one_of'] as $doc)
          
                          <div class="col-md-6 mb-4">
          
                              <label class="form-label">
          
                                  {{ $doc['label'] }}
          
                              </label>
          
                              <input type="file"
                                     name="documents[{{ $doc['code'] }}]"
                                     class="form-control">
          
          
                              @if(isset($uploaded[$doc['code']]))
          
                                  <small class="text-success">
          
                                      Uploaded :
                                         <a href="{{ asset('storage/'.$uploaded[$doc['code']]->file_path) }}" target="_blank">
                                            {{ $uploaded[$doc['code']]->nama_file }}
                                        </a>
          
                                  </small>
          
                                  <br>
          
                                  <small>
          
                                      Status :
                                      {{ $uploaded[$doc['code']]->status }}
          
                                  </small>
          
                              @endif
          
                          </div>
          
                      @endforeach
          
                  </div>
          
              </div>
          
          
              {{-- ========================================================= --}}
              {{-- OPTIONAL --}}
              {{-- ========================================================= --}}
              <div class="card-header bg-secondary text-white">
                  <h3 class="card-title">
          
                      DOKUMEN TAMBAHAN (OPTIONAL)
          
                  </h3>
              </div>
          
              <div class="card-body">
          
                  <div class="row">
          
                      @foreach($optionalDocs as $doc)
          
                          <div class="col-md-6 mb-4">
          
                              <label class="form-label">
          
                                  {{ $doc['label'] }}
          
                              </label>
          
                              <input type="file"
                                     name="documents[{{ $doc['code'] }}]"
                                     class="form-control">
          
          
                              @if(isset($uploaded[$doc['code']]))
          
                                  <small class="text-success">
          
                                      Uploaded :
                                         <a href="{{ asset('storage/'.$uploaded[$doc['code']]->file_path) }}"target="_blank">

                                            {{ $uploaded[$doc['code']]->nama_file }}

                                        </a>
          
                                  </small>
          
                                  <br>
          
                                  <small>
          
                                      Status :
                                      {{ $uploaded[$doc['code']]->status }}
          
                                  </small>
          
                              @endif
          
                          </div>
          
                      @endforeach
          
                  </div>
          
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