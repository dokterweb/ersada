@extends('survey.layouts.wizard')

@section('survey-content')
<h3 class="mb-4">
    <i class="fa fa-folder-open text-primary"></i>
    Step 1 - Pemeriksaan Berkas

</h3>

@php
    $pasangan = $survey->pengajuan->referensis->firstWhere('jenis', 'pasangan');
    $penjamin = $survey->pengajuan->referensis->firstWhere('jenis', 'penjamin');
@endphp

<form action="{{ route('survey.storeBerkas',$survey) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            {{-- ================= IDENTITAS ================= --}}
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-primary-lt">
                    <strong>Dokumen Debitur</strong>
                    <span class="badge bg-yellow-lt" id="badgeDebitur">Belum Lengkap</span>
                </div>

                <div class="card-body">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th width="80%">Dokumen</th>
                                <th class="text-center">Ada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>KTP</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="ktp_debitur" id="ktp_debitur" value="1"
                                            {{ old('ktp_debitur', optional($survey->berkas)->ktp_debitur) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>Kartu Keluarga</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="kk_debitur" id="kk_debitur"  value="1"
                                            {{ old('kk_debitur', optional($survey->berkas)->kk_debitur) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                        </tbody>                    
                    </table>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">
                                Status Peminjam
                            </label>
                            <select class="form-select" name="status_peminjam" id="status_peminjam">
                                <option value="">-- Pilih --</option>
                                <option value="baru"
                                    {{ old('status_peminjam', optional($survey->berkas)->status_peminjam) == 'baru' ? 'selected' : '' }}>
                                    Baru
                                </option>
                                <option value="lama"
                                    {{ old('status_peminjam', optional($survey->berkas)->status_peminjam) == 'lama' ? 'selected' : '' }}>
                                    Lama
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6" id="plafond_lama_box">
                            <label class="form-label">
                                Plafond Pinjaman Lama
                            </label>
                            <input type="number" class="form-control" name="plafond_pinjaman_lama" id="plafond_pinjaman_lama"
                                value="{{ old('plafond_pinjaman_lama', optional($survey->berkas)->plafond_pinjaman_lama) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            @if($pasangan)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Dokumen Pasangan</strong>
                    <span id="badgePasangan" class="badge bg-yellow-lt">Belum Lengkap</span>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Nama Pasangan :</strong>
                        {{ $pasangan->nama }}
                    </div>
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th width="80%">Dokumen</th>
                                <th class="text-center">Ada</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>KTP Pasangan</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="ktp_pasangan" id="ktp_pasangan" value="1"
                                            {{ old('ktp_pasangan', optional($survey->berkas)->ktp_pasangan) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>Kartu Keluarga</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="kk_pasangan" id="kk_pasangan"  value="1"
                                            {{ old('kk_pasangan', optional($survey->berkas)->kk_pasangan) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="alert alert-secondary mb-4">
                <i class="ti ti-info-circle"></i>
                Debitur tidak memiliki data pasangan.
            </div>
            @endif
        </div>
      
    </div>

    <div class="row">
        <div class="col-md-6">
            
            {{-- ================= Dokumen Penjamin ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <strong>Dokumen Penjamin</strong>
                    <span id="badgePenjamin" class="badge bg-yellow-lt">Belum Lengkap</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <div><strong>Nama :</strong> {{ $penjamin->nama }}</div>
                        <div><strong>Hubungan :</strong> {{ $penjamin->hubungan }}</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th width="70%">Dokumen</th>
                                    <th class="text-center">Ada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>KTP Penjamin</td>
                                    <td class="text-center">
                                        <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" name="ktp_penjamin" id="ktp_penjamin" value="1"
                                        {{ old('ktp_penjamin', optional($survey->berkas)->ktp_penjamin) ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>KK Penjamin</td>
                                    <td class="text-center">
                                        <label class="form-check form-switch">
                                        <input type="checkbox" class="form-check-input" name="kk_penjamin" id="kk_penjamin" value="1"
                                                {{ old('kk_penjamin', optional($survey->berkas)->kk_penjamin) ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>Nama Pasangan Penjamin</strong>
                    <span id="badgePasangan" class="badge bg-yellow-lt">Belum Lengkap</span>
                </div>

                <div class="card-body">
                    <label class="form-label">
                        Nama Pasangan Penjamin
                    </label>
                    <input type="text" class="form-control" name="nama_pasangan_penjamin" id="nama_pasangan_penjamin"
                        value="{{ old('nama_pasangan_penjamin', optional($survey->berkas)->nama_pasangan_penjamin) }}">
                        
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered align-middle">
                            <tbody>
                                <tr>
                                    <td width="70%">KTP Pasangan Penjamin</td>
                                    <td class="text-center">
                                        <label class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" name="ktp_pasangan_penjamin" id="ktp_pasangan_penjamin" value="1"
                                                {{ old('ktp_pasangan_penjamin', optional($survey->berkas)->ktp_pasangan_penjamin) ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                </tr>
                                <tr>
                                    <td>KK Pasangan Penjamin</td>
                                    <td class="text-center">
                                        <label class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input" name="kk_pasangan_penjamin" id="kk_pasangan_penjamin" value="1"
                                                {{ old('kk_pasangan_penjamin', optional($survey->berkas)->kk_pasangan_penjamin) ? 'checked' : '' }}>
                                        </label>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>   
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-azure-lt">
                    <h3 class="card-title">
                        <i class="ti ti-briefcase"></i>
                        Verifikasi Data Pekerjaan
                    </h3>
                    <span id="badgePekerjaan" class="badge bg-yellow-lt">Belum Lengkap</span>
                </div>
            
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">
                                Slip Gaji
                            </label>
                            <div>
                                <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="slip_gaji" name="slip_gaji" value="1"
                                    {{ old('slip_gaji', optional($survey->berkas)->slip_gaji) ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="pekerjaan_box">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">
                                    Nomor ID Karyawan
                                </label>
                                <input type="text" class="form-control" id="no_id_karyawan" name="no_id_karyawan"
                                    value="{{ old('no_id_karyawan', optional($survey->berkas)->no_id_karyawan) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    Lama Bekerja
                                </label>
                                <input type="text" class="form-control" id="lama_bekerja"  name="lama_bekerja"
                                    value="{{ old('lama_bekerja', optional($survey->berkas)->lama_bekerja) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">
                                    No. Telepon Kantor
                                </label>
                                <input type="text" class="form-control" id="no_telp_karyawan" name="no_telp_karyawan"
                                    value="{{ old('no_telp_karyawan', optional($survey->berkas)->no_telp_karyawan) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-azure-lt">
                    <h3 class="card-title">
                       <i class="ti ti-heart-handshake"></i>
                        Verifikasi BPJS
                    </h3>
                    <span id="badgeBPJS" class="badge bg-yellow-lt">Belum Lengkap</span>
                </div>
            
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">
                                BPJS
                            </label>
                            <div>
                                <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="bpjs" name="bpjs" value="1"
                                    {{ old('bpjs', optional($survey->berkas)->bpjs) ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label">
                                Nomor BPJS
                            </label>
                            <input type="text" class="form-control" id="no_bpjs" name="no_bpjs"
                                value="{{ old('no_bpjs', optional($survey->berkas)->no_bpjs) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
           
   <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-indigo-lt">
                    <h3 class="card-title">
                        <i class="ti ti-building-bank"></i>
                        Verifikasi Rekening
                    </h3>
                    <span id="badgeRekening" class="badge bg-yellow-lt">Belum Lengkap</span>
                </div>
            
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">
                                Buku Tabungan
                            </label>
                            <div>
                                <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="buku_tabungan" name="buku_tabungan" value="1"
                                    {{ old('buku_tabungan', optional($survey->berkas)->buku_tabungan) ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">
                                Nama Bank
                            </label>
                            <input type="text" class="form-control" id="nama_bank" name="nama_bank"
                                value="{{ old('nama_bank', optional($survey->berkas)->nama_bank) }}">
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">
                                Kartu ATM
                            </label>
                            <div>
                                <label class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="kartu_atm" name="kartu_atm" value="1"
                                    {{ old('kartu_atm', optional($survey->berkas)->kartu_atm) ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">
                                PIN ATM
                            </label>
                            <input type="text" class="form-control" id="pin_atm" name="pin_atm"
                                value="{{ old('pin_atm', optional($survey->berkas)->pin_atm) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-red-lt">
                    <h3 class="card-title">
                        <i class="ti ti-notes"></i>
                        Catatan Kekurangan Berkas
                    </h3>
                </div>
                <div class="card-body">
                    <textarea class="form-control" rows="6" name="catatan_kekurangan" id="catatan_kekurangan">
                        {{ old('catatan_kekurangan', optional($survey->berkas)->catatan_kekurangan) }}
                    </textarea>
                </div>
            </div>
        </div>
   </div>


 {{--   <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('survey.index') }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left"></i>
            Kembali
        </a>
        <button type="submit" class="btn btn-primary">
            Simpan & Lanjut
            <i class="ti ti-arrow-right"></i>
        </button>
    </div> --}}

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('survey.berkas',$survey) }}" class="btn btn-secondary">
            <i class="ti ti-arrow-left"></i>
            Kembali
        </a>
        <button class="btn btn-primary">
            Simpan & Review
            <i class="ti ti-arrow-right"></i>
        </button>
    </div>

    
</form>

@endsection

@section('scripts')

<script>

    $(function(){
    
        //---------------------------------------------------
        // STATUS PEMINJAM
        //---------------------------------------------------
        function toggleStatusPeminjam(){
            if($('#status_peminjam').val()=='lama'){
                $('#plafond_lama_box').show();
            }else{
                $('#plafond_lama_box').hide();
                $('input[name=plafond_pinjaman_lama]').val('');
            }
        }
    
        //---------------------------------------------------
        // SLIP GAJI
        //---------------------------------------------------
    
        function togglePekerjaan(){
            let aktif=$('#slip_gaji').is(':checked');
            $('#no_id_karyawan').prop('disabled',!aktif);
            $('#lama_bekerja').prop('disabled',!aktif);
            $('#no_telp_karyawan').prop('disabled',!aktif);
        }
    
        //---------------------------------------------------
        // BPJS
        //---------------------------------------------------
    
        function toggleBPJS(){
            let aktif=$('#bpjs').is(':checked');
            $('#no_bpjs').prop('disabled',!aktif);
        }
    
        //---------------------------------------------------
        // TABUNGAN
        //---------------------------------------------------
    
        function toggleTabungan(){
            let aktif=$('#buku_tabungan').is(':checked');
            $('#nama_bank').prop('disabled',!aktif);
        }
    
        //---------------------------------------------------
        // ATM
        //---------------------------------------------------
    
        function toggleATM(){
            let aktif=$('#kartu_atm').is(':checked');
            $('#pin_atm').prop('disabled',!aktif);
        }

        function badgeDebitur(){
            let lengkap=true;
            $('input[name=ktp_debitur],input[name=kk_debitur]').each(function(){
                if(!$(this).is(':checked')){
                    lengkap=false;
                }
            });
            if(lengkap){
                $('#badgeDebitur')
                    .removeClass('bg-yellow-lt')
                    .addClass('bg-green-lt')
                    .text('Lengkap');
            }else{
                $('#badgeDebitur')
                    .removeClass('bg-green-lt')
                    .addClass('bg-yellow-lt')
                    .text('Belum Lengkap');
            }

        }

        function badgePasangan(){
            if($('#badgePasangan').length==0){
                return;
            }
            let lengkap=true;
            $('input[name=ktp_pasangan],input[name=kk_pasangan]').each(function(){
                if(!$(this).is(':checked')){
                    lengkap=false;
                }
            });
            if(lengkap){
                $('#badgePasangan')
                    .removeClass('bg-yellow-lt')
                    .addClass('bg-green-lt')
                    .text('Lengkap');
            }else{
                $('#badgePasangan')
                    .removeClass('bg-green-lt')
                    .addClass('bg-yellow-lt')
                    .text('Belum Lengkap');
            }

        }

        //---------------------------------------------------
        // EVENT
        //---------------------------------------------------

        $('#status_peminjam').change(toggleStatusPeminjam);
        $('#slip_gaji').change(function(){
            togglePekerjaan();
            badgePekerjaan();
        });

        $('#bpjs').change(function(){
            toggleBPJS();
            badgeBPJS();
        });

        $('#buku_tabungan').change(function(){
            toggleTabungan();
            badgeRekening();
        });

        $('#kartu_atm').change(function(){
            toggleATM();
            badgeRekening();
        });

        $('input[type=checkbox]').change(function(){
            badgeDebitur();
            badgePasangan();
            badgePenjamin();
        });

        $('input[type=text]').keyup(function(){
            badgePekerjaan();
            badgeBPJS();
            badgeRekening();
        });


        toggleStatusPeminjam();

        togglePekerjaan();

        toggleBPJS();

        toggleTabungan();

        toggleATM();

        badgeDebitur();

        badgePasangan();

        badgePenjamin();

        badgePekerjaan();

        badgeBPJS();

        badgeRekening();

    }); 
</script>
@endsection