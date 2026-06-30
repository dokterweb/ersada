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
                            <li class="step-item">STEP 1</li>
                            <li class="step-item">STEP 2</li>
                            <li class="step-item active">STEP 3</li>
                            <li class="step-item">STEP 4</li>
                            <li class="step-item">REVIEW</li>
                        </ul>
                    </div>
                <div class="card-header bg-success">
                    <h3 class="card-title">DATA REFERENSI DAN PEKERJAAN</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="hidden" name="has_pasangan" value="0">
                                <input type="checkbox" id="has_pasangan" name="has_pasangan" value="1"class="form-check-input" {{ old('has_pasangan', isset($pasangan) ? 1 : 0) ? 'checked' : '' }}>
                                <span class="form-check-label">Memiliki Pasangan</span>
                            </label>
                            <div id="pasangan_form" style="display:none;">
                                <h4>DATA PASANGAN</h4>
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nama Sesuai KTP</label>
                                        <input type="text" class="form-control" name="pasangan[nama]" value="{{ old('pasangan.nama', $pasangan->nama ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input type="text" class="form-control" name="pasangan[tempat_lahir]" value="{{ old('pasangan.tempat_lahir', $pasangan->tempat_lahir ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Tgl Lahir</label>
                                        <input type="date" class="form-control" name="pasangan[tgl_lahir]" value="{{ old('pasangan.tgl_lahir', $pasangan->tgl_lahir ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="hidden" name="has_pekerjaan_pasangan" value="0">
                                <input type="checkbox" id="pasangan_pekerjaan_check" name="has_pekerjaan_pasangan"value="1" class="form-check-input" {{ old('has_pekerjaan_pasangan',
                                isset($pasangan?->pekerjaan) ? 1 : 0) ? 'checked' : '' }}>
                                <span class="form-check-label">Pasangan memiliki pekerjaan</span>
                            </label>
                            <div id="pasangan_job" style="display:none;">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Jumlah Penghasilan</label>
                                        <input type="number" class="form-control" name="pasangan[pekerjaan][penghasilan]" value="{{ old('pasangan.pekerjaan.penghasilan', $pasangan?->pekerjaan?->penghasilan ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Nama Usaha</label>
                                        <input type="text" class="form-control" name="pasangan[pekerjaan][nama_usaha]" value="{{ old('pasangan.pekerjaan.nama_usaha', $pasangan?->pekerjaan?->nama_usaha ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Jenis Usaha</label>
                                        <input type="text" name="pasangan[pekerjaan][jenis_usaha]" class="form-control" value="{{ old('pasangan.pekerjaan.jenis_usaha', $pasangan?->pekerjaan?->jenis_usaha ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Lama Usaha</label>
                                        <input type="text" name="pasangan[pekerjaan][lama_usaha]" class="form-control" value="{{ old('pasangan.pekerjaan.lama_usaha', $pasangan?->pekerjaan?->lama_usaha ?? '') }}">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Jlh Pegawai</label>
                                        <input type="text" name="pasangan[pekerjaan][jumlah_pegawai]" class="form-control" value="{{ old('pasangan.pekerjaan.jumlah_pegawai', $pasangan?->pekerjaan?->jumlah_pegawai ?? '') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Alamat Usaha</label>
                                        <input type="text" name="pasangan[pekerjaan][alamat_usaha]" class="form-control" value="{{ old('pasangan.pekerjaan.alamat_usaha', $pasangan?->pekerjaan?->alamat_usaha ?? '') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-check">
                                <input type="hidden" name="has_penjamin" value="0">
                                <input type="checkbox" id="has_penjamin" name="has_penjamin" value="1"class="form-check-input" value="1"class="form-check-input" {{ old('has_penjamin', isset($penjamin) ? 1 : 0) ? 'checked' : '' }}>
                                <span class="form-check-label">Memiliki Penjamin</span>
                            </label>
                            <div id="penjamin_form" style="display:none;">
                                <h4>PENJAMIN</h4>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nama Sesuai KTP</label>
                                        <input type="text" class="form-control" name="penjamin[nama]" value="{{ old('penjamin.nama', $penjamin->nama ?? '') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Tempat Lahir</label>
                                        <input class="form-control" name="penjamin[tempat_lahir]" value="{{ old('penjamin.tempat_lahir', $penjamin->tempat_lahir ?? '') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Tgl Lahir</label>
                                        <input class="form-control" type="date" name="penjamin[tgl_lahir]" value="{{ old('penjamin.tgl_lahir', $penjamin->tgl_lahir ?? '') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">No. HP</label>
                                        <input class="form-control" name="penjamin[no_hp]" value="{{ old('penjamin.no_hp', $penjamin->no_hp ?? '') }}">
                             
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Hubungan</label>
                                        <input type="text" class="form-control" name="penjamin[hubungan]" value="{{ old('penjamin.hubungan', $penjamin->hubungan ?? '') }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Alamat</label>
                                        <input type="text" class="form-control" name="penjamin[alamat]" value="{{ old('penjamin.alamat', $penjamin->alamat ?? '') }}">
                                    </div>
                                </div>

                                <label class="form-check">
                                    <input type="hidden" name="has_pekerjaan_penjamin" value="0">
                                    <input type="checkbox" id="penjamin_job_check" name="has_pekerjaan_penjamin" value="1" class="form-check-input" 
                                    {{ old('has_pekerjaan_penjamin',isset($penjamin?->pekerjaan) ? 1 : 0 ) ? 'checked' : '' }}>
                                    <span class="form-check-label">Penjamin memiliki pekerjaan</span>
                                </label>
                                <div id="penjamin_job"style="display:none;">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Jumlah Penghasilan</label>
                                            <input type="number" class="form-control" name="penjamin[pekerjaan][penghasilan]" value="{{ old('penjamin.pekerjaan.penghasilan', $penjamin?->pekerjaan?->penghasilan ?? '') }}">
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Nama Usaha</label>
                                            <input type="text" class="form-control" name="penjamin[pekerjaan][nama_usaha]" value="{{ old('penjamin.pekerjaan.nama_usaha', $penjamin?->pekerjaan?->nama_usaha ?? '') }}" >
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Jenis Usaha</label>
                                            <input type="text" name="penjamin[pekerjaan][jenis_usaha]" class="form-control" value="{{ old('penjamin.pekerjaan.jenis_usaha', $penjamin?->pekerjaan?->jenis_usaha ?? '') }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Lama Usaha</label>
                                            <input type="text" name="penjamin[pekerjaan][lama_usaha]" class="form-control" value="{{ old('penjamin.pekerjaan.lama_usaha', $penjamin?->pekerjaan?->lama_usaha ?? '') }}">
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label class="form-label">Jlh Pegawai</label>
                                            <input type="text" name="penjamin[pekerjaan][jumlah_pegawai]" class="form-control" value="{{ old('penjamin.pekerjaan.jumlah_pegawai', $penjamin?->pekerjaan?->jumlah_pegawai ?? '') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Alamat Usaha</label>
                                            <input type="text" name="penjamin[pekerjaan][alamat_usaha]" class="form-control" value="{{ old('penjamin.pekerjaan.alamat_usaha', $penjamin?->pekerjaan?->alamat_usaha ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <h4>SAUDARA TERDEKAT</h4>
                            <button type="button" id="addSaudara" class="btn btn-success">
                                Tambah Saudara
                            </button>
                            <div id="saudara_wrapper">

                                {{-- tampilkan data lama jika edit --}}
                            
                                @foreach($saudaras as $index => $saudara)
                            
                                    <div class="card mt-3 p-3 saudara-card">
                            
                                        <h5>Data Saudara</h5>
                            
                                        <div class="row">
                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Nama KTP</label>
                                                <input type="hidden" name="saudara[{{ $index }}][id]" value="{{ $saudara->id }}">
                                                <input class="form-control" name="saudara[{{ $index }}][nama]" value="{{ old("saudara.$index.nama", $saudara->nama) }}">
                                            </div>
                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tempat Lahir</label>
                                                <input class="form-control"
                                                       name="saudara[{{ $index }}][tempat_lahir]"
                                                       value="{{ old("saudara.$index.tempat_lahir", $saudara->tempat_lahir) }}">
                                            </div>
                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Tgl Lahir</label>
                                                <input type="date"
                                                       class="form-control"
                                                       name="saudara[{{ $index }}][tgl_lahir]"
                                                       value="{{ old("saudara.$index.tgl_lahir", $saudara->tgl_lahir) }}">
                                            </div>
                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Hubungan</label>
                                                <input class="form-control"
                                                       name="saudara[{{ $index }}][hubungan]"
                                                       value="{{ old("saudara.$index.hubungan", $saudara->hubungan) }}">
                                            </div>
                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">No HP</label>
                                                <input class="form-control"
                                                       name="saudara[{{ $index }}][no_hp]"
                                                       value="{{ old("saudara.$index.no_hp", $saudara->no_hp) }}">
                                            </div>
                            
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Alamat</label>
                                                <input class="form-control"
                                                       name="saudara[{{ $index }}][alamat]"
                                                       value="{{ old("saudara.$index.alamat", $saudara->alamat) }}">
                                            </div>
                            
                                            <button type="button"
                                                    class="btn btn-danger removeSaudara">
                                                Hapus Saudara
                                            </button>
                            
                                        </div>
                            
                                    </div>
                            
                                @endforeach
                            
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('pengajuan.step2',$pengajuan->id) }}"class="btn btn-warning">Previous</a>
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

    $(document).ready(function(){
        // PASANGAN
        $('#has_pasangan').on('change', function(){

            if($('#has_pasangan').is(':checked')){
                $('#pasangan_form').show();
            }

            if($('#pasangan_pekerjaan_check').is(':checked')){
                $('#pasangan_job').show();
            }

            if($('#has_penjamin').is(':checked')){
                $('#penjamin_form').show();
            }

            if($('#penjamin_job_check').is(':checked')){
                $('#penjamin_job').show();
            }

            
            if($(this).is(':checked')){
                $('#pasangan_form').slideDown();
            }else{
                $('#pasangan_form').slideUp();
                $('#pasangan_job').hide();
                $('#pasangan_pekerjaan_check').prop('checked', false);
            }
        });
    
        //pekerjaan pasangan
        
        $('#pasangan_pekerjaan_check').on('change', function(){
            if($(this).is(':checked')){
                $('#pasangan_job').slideDown();
            }else{
                $('#pasangan_job').slideUp();
            }
        });
    
        // PENJAMIN
        
        $('#has_penjamin').on('change', function(){
            if($(this).is(':checked')){
                $('#penjamin_form').slideDown();
            }else{
                $('#penjamin_form').slideUp();
                $('#penjamin_job').hide();
                $('#penjamin_job_check').prop('checked', false);
            }
        });
    
        //pekerjaan penjamin
        
        $('#penjamin_job_check').on('change', function(){
            if($(this).is(':checked')){
                $('#penjamin_job').slideDown();
            }else{
                $('#penjamin_job').slideUp();
            }
        });
    
        // SAUDARA DYNAMIC
        let index = {{ $saudaras->count() }};
        $('#addSaudara').click(function(){
            let html = `
            <div class="card mt-3 p-3">
                <h5>Saudara ${index}</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Nama KTP</label>
                        <input class="form-control" name="saudara[${index}][nama]" placeholder="Nama">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tempat Lahir</label>
                        <input class="form-control" name="saudara[${index}][tempat_lahir]" placeholder="Tempat lahir">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tgl Lahir</label>
                        <input class="form-control" type="date" name="saudara[${index}][tgl_lahir]">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hubungan</label>
                        <input class="form-control" name="saudara[${index}][hubungan]" placeholder="Hubungan">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">No. HP</label>
                        <input class="form-control" name="saudara[${index}][no_hp]" placeholder="No HP">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Alamat</label>
                        <input class="form-control" name="saudara[${index}][alamat]" placeholder="alamat">
                    </div>
                    <button type="button" class="btn btn-danger removeSaudara">Hapus Saudara</button>
                </div>
            </div>
            `;
            index++;
            $('#saudara_wrapper').append(html);
        });
    
        // hapus saudara
    
        $(document).on('click','.removeSaudara',function(){
            $(this).closest('.card').remove();
            }
        );
    
    });
    
</script>

@endsection