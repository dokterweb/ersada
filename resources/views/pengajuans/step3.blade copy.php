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
            <form action="{{ route('pengajuan.storeStep3',$pengajuan->id) }}" method="POST" class="card">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <ul class="steps steps-green steps-counter my-4">
                            <li class="step-item">
                                STEP 1
                            </li>
        
                            <li class="step-item">
                                STEP 2
                            </li>
        
                            <li class="step-item active">
                                STEP 3
                            </li>
        
                            <li class="step-item">
                                STEP 4
                            </li>
        
                            <li class="step-item">
                                REVIEW
                            </li>
        
                        </ul>
        
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA REFERENSI DAN PEKERJAAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" id="has_pasangan" class="form-check-input">
                                <span class="form-check-label">Memiliki Pasangan</span>
                            </label>
                            <div id="pasangan_form" style="display:none;">
                                <h4>DATA PASANGAN</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nama Sesuai KTP</label>
                                        <input type="text" class="form-control" name="pasangan[nama]" placeholder="Nama pasangan">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" name="pasangan[tempat_lahir]" placeholder="Tempat lahir">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tgl Lahir</label>
                                        <input type="date" class="form-control" name="pasangan[tgl_lahir]">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" id="pasangan_pekerjaan_check"class="form-check-input">
                                <span class="form-check-label">Pasangan memiliki pekerjaan</span>
                            </label>
                            <div id="pasangan_job" style="display:none;">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Jumlah Penghasilan</label>
                                        <input type="number" class="form-control" name="pasangan[pekerjaan][penghasilan]"placeholder="Penghasilan">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nama Usaha</label>
                                        <input type="text" class="form-control" name="pasangan[pekerjaan][nama_usaha]" placeholder="Nama usaha">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="checkbox" id="has_penjamin" class="form-check-input">
                                <span class="form-check-label">Memiliki Penjamin</span>
                            </label>
                            <div id="penjamin_form" style="display:none;">
                                <h4>PENJAMIN</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nama Sesuai KTP</label>
                                        <input type="text" class="form-control" name="penjamin[nama]" placeholder="Nama">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Hubungan</label>
                                        <input type="text" class="form-control" name="penjamin[hubungan]" placeholder="Hubungan">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Alamat</label>
                                        <input type="text" class="form-control" name="penjamin[alamat]" placeholder="Alamat">
                                    </div>
                                </div>

                                <label class="form-check">
                                    <input type="checkbox" id="penjamin_job_check" class="form-check-input">
                                    <span class="form-check-label">Penjamin memiliki pekerjaan</span>
                                </label>
                                <div id="penjamin_job"style="display:none;">
                                <input type="number" class="form-control mt-2" name="penjamin[pekerjaan][penghasilan]" placeholder="Penghasilan">
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <h4>SAUDARA TERDEKAT</h4>
                            <button type="button" id="addSaudara" class="btn btn-success">
                                Tambah Saudara
                            </button>
                            <div id="saudara_wrapper"></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary"> Simpan & Lanjut Step 3</button>
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



document.getElementById('has_pasangan').addEventListener('change', function(){

    document.getElementById('pasangan_form').style.display =
    this.checked ? 'block' : 'none';

});


document.getElementById('has_penjamin').addEventListener('change', function(){

    document.getElementById('penjamin_form').style.display =
    this.checked ? 'block' : 'none';

});


document.getElementById('pasangan_pekerjaan_check').addEventListener('change', function(){
    document.getElementById('pasangan_job').style.display =
    this.checked ? 'block' : 'none';
});


document.getElementById('penjamin_job_check').addEventListener('change', function(){
    document.getElementById('penjamin_job').style.display =
this.checked ? 'block' : 'none';
});


/*
saudara dynamic
*/

let saudaraIndex = 0;

document.getElementById('addSaudara').addEventListener('click', function(){

saudaraIndex++;

let html = `

<div class="border p-3 mt-3">
    <h5>Saudara ${saudaraIndex}</h5>
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label">Nama Sesuai KTP</label>
            <input class="form-control" name="saudara[${saudaraIndex}][nama]" placeholder="Nama">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Hubungan</label>
            <input class="form-control" name="saudara[${saudaraIndex}][hubungan]" placeholder="Hubungan">
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label">Hubungan</label>
            <input class="form-control" name="saudara[${saudaraIndex}][alamat]" placeholder="alamat">
        </div>
    <label>
        <input type="checkbox" onclick="toggleJob(${saudaraIndex})"> Memiliki pekerjaan
    </label>
    <div id="job_${saudaraIndex}" style="display:none;">
    <input class="form-control mt-2" name="saudara[${saudaraIndex}][pekerjaan][penghasilan]" placeholder="Penghasilan">

</div>

`;

document.getElementById('saudara_wrapper')
    .insertAdjacentHTML('beforeend',html);
});


function toggleJob(id){

    let el = document.getElementById('job_'+id);
    el.style.display =
    el.style.display === 'none'? 'block': 'none';

}

</script>


@endsection