@extends('layouts.app')

@section('content')
<div class="page-wrapper">
    <!-- Page header -->
    <div class="page-header d-print-none">
      <div class="container-xl">
        <div class="row g-2 align-items-center">
          <div class="col">
            <h2 class="page-title">
              Data Karyawan
            </h2>
          </div>
          <!-- Page title actions -->
          <div class="col-auto ms-auto d-print-none">
            <div class="btn-list">
              <a href="{{route('karyawans.create')}}" class="btn btn-primary">
                Tambah Karyawan
            </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page body -->
    <div class="page-body">
      <div class="container-xl">
        <div class="row row-cards">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header bg-blue-lt">
                <h3 class="card-title">Data Pengajuan Pembiayaan</h3>
              </div>
              <div class="card-body">
                <form action="{{ route('survey.store',$pengajuan->id) }}" method="POST">
                @csrf
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="220">No Pengajuan</th>
                            <td>{{ $pengajuan->nomor_pengajuan }}</td>
                        </tr>
                        <tr>
                            <th>Nasabah</th>
                            <td>{{ optional($pengajuan->nasabah)->nama }}</td>
                        </tr>
                        <tr>
                            <th>Marketing</th>
                            <td>{{ optional($pengajuan->marketing->user)->name }}</td>
                        </tr>
                        <tr>
                            <th>Cabang</th>
                            <td>{{ optional($pengajuan->cabang)->nama_cabang }}</td>
                        </tr>
                    </table>
                    <hr>
    
                    <div class="mb-3">

                      <label class="form-label">Jenis Penugasan</label>
                  
                      <select name="jenis" id="jenis" class="form-select">
                          <option value="sendiri" {{ old('jenis')=='sendiri' ? 'selected':'' }}>
                              Survey Saya Sendiri
                          </option>
                          <option value="assign"
                              {{ old('jenis')=='assign' ? 'selected':'' }}>
                              Tugaskan Surveyor
                          </option>
                  
                      </select>
                  
                  </div>
                  
                  <div class="mb-3" id="surveyor-area">
                  
                      <label class="form-label">
                  
                          Pilih Surveyor
                  
                      </label>
                  
                      <select name="surveyor_id" id="surveyor_id" class="form-select">
                          <option value="">-- Pilih Surveyor --</option>
                          @foreach($surveyors as $surveyor)
                              <option value="{{ $surveyor->id }}"
                                  {{ old('surveyor_id')==$surveyor->id ? 'selected':'' }}>
                                  {{ $surveyor->name }}
                              </option>
                          @endforeach
                      </select>
                      @error('surveyor_id')
                          <small class="text-danger">
                              {{ $message }}
                          </small>
                      @enderror
                  </div>
                </div>
    
                <div class="card-footer">
                    <a href="{{ route('survey.index') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                    <button class="btn btn-success">Simpan Penugasan</button>
                </div>
            </form>
    
            </div>
    
           
            
            </div>
          </div>
        </div>
      </div>
    </div>
    
</div>
@endsection

@push('scripts')

<script>

$(function(){

    function toggleSurveyor(){

        let jenis = $('#jenis').val();

        if(jenis == 'assign'){

            $('#surveyor-area').slideDown(200);

            $('#surveyor_id').prop('required',true);

        }else{

            $('#surveyor-area').slideUp(200);

            $('#surveyor_id')
                .prop('required',false)
                .val('');

        }

    }

    toggleSurveyor();

    $('#jenis').change(function(){

        toggleSurveyor();

    });

});

</script>

@endpush