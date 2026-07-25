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


    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">
                        <i class="ti ti-home"></i>
                        Dokumentasi Rumah Tinggal
                    </h3>
                    <span class="badge bg-yellow-lt" id="badgeRumah">
                        Belum Lengkap
                    </span>
                </div>
            
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Foto Rumah Tampak Depan</span>
                            <span class="badge bg-success-lt d-none status-upload" id="status-rumah-depan">
                                ✓ Berhasil
                            </span>
                        </label>
                        <input type="file" class="upload-file" data-kategori="rumah" data-posisi="depan" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Foto Rumah Tampak Dalam</span>
                            <span class="badge bg-success-lt d-none status-upload" id="status-rumah-dalam">
                                ✓ Berhasil
                            </span>
                        </label>
                        <input type="file" class="upload-file" data-kategori="rumah" data-posisi="dalam" accept="image/*">
                    </div>
                    <div>
                        <label class="form-label d-flex justify-content-between">
                            <span>Foto Rumah Tampak Samping</span>
                            <span class="badge bg-success-lt d-none status-upload" id="status-rumah-samping">
                                ✓ Berhasil
                            </span>
                        </label>
                        <input type="file" class="upload-file" data-kategori="rumah" data-posisi="samping" accept="image/*">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
           <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h3 class="card-title">
                        <i class="ti ti-building-store"></i>
                        Dokumentasi Tempat Usaha
                    </h3>
                    <span class="badge bg-yellow-lt" id="badgeUsaha">
                        Belum Lengkap
                    </span>
                </div>
                <div class="card-body">
                    <label class="form-label d-flex justify-content-between">
                        <span>Foto Tempat Usaha</span>
                        <span class="badge bg-success-lt d-none" id="status-usaha">
                            ✓ Berhasil
                        </span>
                    </label>
                    <input type="file" multiple class="upload-file" data-kategori="usaha"  data-posisi="usaha" multiple accept="image/*">>
                    <small class="text-muted">
                        Upload sebanyak mungkin foto tempat usaha.
                    </small>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @foreach($survey->pengajuan->jaminans as $jaminan)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="ti ti-shield"></i>
                        Jaminan {{ $loop->iteration }} | {{ $jaminan->jenis_jaminan }} - {{ $jaminan->nama_jaminan }}
                    </h3>
                    <span class="badge bg-yellow-lt badge-jaminan" data-id="{{ $jaminan->id }}">
                        Belum Lengkap
                    </span>
            
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Foto Depan</span>
                                <span class="badge bg-success-lt d-none" id="status-jaminan-{{ $jaminan->id }}-depan">
                                    ✓ Berhasil
                                </span>
                            </label>
                            <input type="file" class="upload-file" data-kategori="jaminan" data-jaminan="{{ $jaminan->id }}" data-posisi="depan" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Foto Samping</span>
                                <span class="badge bg-success-lt d-none" id="status-jaminan-{{ $jaminan->id }}-samping">
                                    ✓ Berhasil
                                </span>
                            </label>
                            <input type="file" class="upload-file" data-kategori="jaminan" data-jaminan="{{ $jaminan->id }}" data-posisi="samping" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Foto Dalam</span>
                                <span class="badge bg-success-lt d-none" id="status-jaminan-{{ $jaminan->id }}-dalam">
                                    ✓ Berhasil
                                </span>
                            </label>
                            <input type="file" class="upload-file" data-kategori="jaminan" data-jaminan="{{ $jaminan->id }}" data-posisi="dalam" accept="image/*">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-flex justify-content-between">
                                <span>Foto Belakang</span>
                                <span class="badge bg-success-lt d-none" id="status-jaminan-{{ $jaminan->id }}-belakang">
                                    ✓ Berhasil
                                </span>
                            </label>
                            <input type="file" class="upload-file" data-kategori="jaminan" data-jaminan="{{ $jaminan->id }}" data-posisi="belakang">
                        </div>
                    </div>
                    <hr>
                    <label class="form-label d-flex justify-content-between">
                        <span>Video Jaminan</span>
                        <span class="badge bg-success-lt d-none" id="status-video-{{ $jaminan->id }}">
                            ✓ Berhasil
                        </span>
                    </label>
                    <input type="file" multiple class="upload-file" data-kategori="video" data-posisi="video" data-jaminan="{{ $jaminan->id }}">
                </div>
            </div>
            @endforeach
         </div>
    </div>
    <form action="{{ route('survey.reviewCheck',$survey) }}" method="POST">
        @csrf
        <div class="d-flex justify-content-between mt-4">
            <a href="{{ route('survey.berkas',$survey) }}"
            class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i>
                Kembali
            </a>
            <button class="btn btn-primary">
                Lanjut ke Review
                <i class="ti ti-arrow-right"></i>
            </button>
        </div>
    </form>

@endsection

@section('scripts')

<script>

$('.upload-file').change(function(){
    let input=$(this);
    let formData=new FormData();
    let files=input[0].files;
    $.each(files,function(i,file){
        formData.append('files[]',file);
    });
    formData.append('kategori',input.data('kategori'));
    formData.append('posisi',input.data('posisi') ?? '');
    formData.append('jaminan_pengajuan_id',input.data('jaminan') ?? '');
    $.ajax({
        url:"{{ route('survey.uploadDokumentasi',$survey) }}",
        type:"POST",
        headers:{
        'X-CSRF-TOKEN':
            $('meta[name="csrf-token"]').attr('content')
        },
        data:formData,
        processData:false,
        contentType:false,
        success:function(res){

            let kategori=input.data('kategori');

            let posisi=input.data('posisi') ?? '';

            let jaminan=input.data('jaminan');

            if(kategori==='rumah'){
                $('#status-rumah-'+posisi).removeClass('d-none');
            }

            else if(kategori==='usaha'){
                $('#status-usaha').removeClass('d-none');
            }

            else if(kategori==='video'){
                $('#status-video-'+jaminan).removeClass('d-none');
            }

            else{
                $('#status-jaminan-'+jaminan+'-'+posisi).removeClass('d-none');
            }
        },
        
        error:function(xhr){

        console.log(xhr);

        console.log(xhr.responseText);

        alert('Upload gagal.');

        }
    });
});
</script>
@endsection