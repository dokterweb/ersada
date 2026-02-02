@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-12">
                    <form action="{{ route('hariliburs.store') }}" method="POST" class="card">
                    <div class="card">
                        <div class="card-header bg-cyan-lt">
                            <h3 class="card-title">Create Hari Libur</h3>
                        </div>
                            @csrf
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label >Tanggal Mulai</label>
                                        <input type="date" name="tanggal_mulai" class="form-control" >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label >Tanggal Selesai</label>
                                        <input type="date" name="tanggal_selesai" class="form-control" >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label >Nama Libur</label>
                                        <input type="text" name="nama_libur" class="form-control" >
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Tipe</label>
                                        <select name="tipe" class="form-select" style="width:100%">
                                            <option value="nasional" {{ old('tipe') == 'nasional' ? 'selected' : '' }}>Nasional</option>
                                            <option value="sekolah" {{ old('tipe') == 'sekolah' ? 'selected' : '' }}>sekolah</option>
                                            <option value="mingguan" {{ old('tipe') == 'mingguan' ? 'selected' : '' }}>mingguan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label>Berlaku Untuk</label>
                                        <select name="berlaku_untuk" class="form-select" style="width:100%">
                                            <option value="semua" {{ old('berlaku_untuk') == 'semua' ? 'selected' : '' }}>semua</option>
                                            <option value="siswa" {{ old('berlaku_untuk') == 'siswa' ? 'selected' : '' }}>siswa</option>
                                            <option value="ustadz" {{ old('berlaku_untuk') == 'ustadz' ? 'selected' : '' }}>ustadz</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label >Keterangan</label>
                                        <input type="text" name="keterangan" class="form-control" >
                                    </div>
                                </div>
                                <!-- /.card-body -->
                                <div class="card-footer text-end">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>


@endsection

@section('scripts')


@endsection