@extends('survey.layouts.wizard')

@section('survey-content')
<h3 class="mb-4">
    <i class="fa fa-folder-open text-primary"></i>
    Step 1 - Pemeriksaan Berkas

</h3>

<form action="{{ route('survey.storeBerkas',$survey) }}" method="POST">
    @csrf
    <div class="row">
        <div class="col-md-6">
            {{-- ================= IDENTITAS ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <strong>I. Dokumen Identitas</strong>
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
                                        <input type="checkbox" class="form-check-input" name="ktp" value="1"
                                            {{ old('ktp', optional($survey->berkas)->ktp) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>Kartu Keluarga</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="kk" value="1"
                                            {{ old('kk', optional($survey->berkas)->kk) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>Buku Nikah</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="buku_nikah" value="1"
                                            {{ old('buku_nikah', optional($survey->berkas)->buku_nikah) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                        </tbody>                    
                    </table>
                </div>
            </div>
        </div>
    
        <div class="col-md-6">
            {{-- ================= PENGHASILAN ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <strong>II. Dokumen Penghasilan</strong>
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
                                <td>Slip Gaji</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="slip_gaji" value="1"
                                            {{ old('slip_gaji', optional($survey->berkas)->slip_gaji) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>BPJS Ketenagakerjaan</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="bpjs" value="1"
                                            {{ old('bpjs', optional($survey->berkas)->bpjs) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            {{-- ================= REKENING ================= --}}
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <strong>III. Dokumen Rekening</strong>
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
                                <td>Buku Tabungan</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="buku_tabungan" value="1"
                                            {{ old('buku_tabungan', optional($survey->berkas)->buku_tabungan) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>Kartu ATM</td>
                                <td class="text-center">
                                    <label class="form-check form-switch d-inline-block">
                                        <input type="checkbox" class="form-check-input" name="kartu_atm" value="1"
                                            {{ old('kartu_atm', optional($survey->berkas)->kartu_atm) ? 'checked' : '' }}>
                                    </label>
                                </td>
                            </tr>
                        <tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ================= CATATAN ================= --}}

    <div class="card">

        <div class="card-header">

            <strong>Catatan Surveyor</strong>

        </div>

        <div class="card-body">

            <textarea
                class="form-control"
                rows="5"
                name="catatan">{{ old('catatan', optional($survey->berkas)->catatan) }}</textarea>

        </div>

    </div>



    <div class="mt-4 d-flex justify-content-between">

        <a href="{{ route('survey.index') }}"
           class="btn btn-secondary">

            Kembali

        </a>

        <button class="btn btn-primary">

            Simpan & Lanjut

            <i class="fa fa-arrow-right"></i>

        </button>

    </div>

</form>

@endsection

@section('scripts')
<script>
$(function(){

    function togglePenerbit(){

        if($('#skt_spgr').is(':checked')){

            $('#penerbitArea').show();

        }else{

            $('#penerbitArea').hide();

            $('select[name="skt_spgr_penerbit"]').val('');

        }

    }

    togglePenerbit();

    $('#skt_spgr').change(function(){

        togglePenerbit();

    });

});

</script>
@endsection