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
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item">STEP 3</li>
                            <li class="step-item active">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                        </ul>
        
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA PENGAJUAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                    @foreach($documents as $doc)
                        <div class="col-md-4 mb-3">
                            <label class="form-label">{{ ucwords(str_replace('_',' ',$doc)) }}</label>
                            <input type="file" name="documents[{{ $doc }}]" class="form-control">
                            @if(isset($uploaded[$doc]))
                                <small class="text-success">Uploaded :{{ $uploaded[$doc]->nama_file }}</small>
                                <br>
                                <small>Status :{{ $uploaded[$doc]->status }}</small>
                            @endif
                        </div>
                    @endforeach
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"> Simpan & Lanjut Step 5</button>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection