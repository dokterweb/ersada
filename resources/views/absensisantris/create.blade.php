@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-cyan-lt">
                            <h3 class="card-title">Input Absensi</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('absensisantris.store') }}" method="POST">
                                @csrf
                                {{-- TANGGAL --}}
                                <div class="mb-3">
                                    <label class="form-label">Tanggal Absensi</label>
                                    <input type="date" name="tgl_absen" class="form-control" required>
                                </div>
                                {{-- KELOMPOK --}}
                                <div class="mb-3">
                                    <label class="form-label">Kelompok</label>
                        
                                    {{-- ADMIN --}}
                                    @if(auth()->user()->hasRole('admin'))
                                        <select name="kelompok_id" id="kelompok_id" class="form-select" required>
                                            <option value="">-- Pilih Kelompok --</option>
                                            @foreach($kelompoks as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama_kelompok }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                        
                                    {{-- USTADZ --}}
                                    @if(auth()->user()->hasRole('ustadz'))
                                        <input type="hidden" name="kelompok_id" value="{{ $kelompok->id }}">
                                        <input type="text" class="form-control"
                                                value="{{ $kelompok->nama_kelompok }}"
                                                readonly>
                                    @endif
                                </div>
                                {{-- TABEL SANTRI --}}
                                <div class="table-responsive mt-4">
                                    <table class="table table-bordered">
                                        <thead class="bg-light">
                                            <tr>
                                                <th width="35%">Nama Santri</th>
                                                <th>Status Kehadiran</th>
                                                <th>Keterangan</th> <!-- Kolom Keterangan -->
                                            </tr>
                                        </thead>
                                        <tbody id="santri-table">
                                            {{-- diisi via JS --}}
                                        </tbody>
                                    </table>
                                </div>
                                {{-- SUBMIT --}}
                                <div class="mt-3 text-end">
                                    <button type="submit" class="btn btn-primary">
                                        Simpan Absensi
                                    </button>
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

@section('scripts')

<script>
    $(document).ready(function () {
    
        // ADMIN: pilih kelompok
        $('#kelompok_id').on('change', function () {
            let kelompokId = $(this).val();
            if (kelompokId) {
                loadSantri(kelompokId);
            } else {
                $('#santri-table').html('');
            }
        });
    
        // USTADZ: auto load kelompok sendiri
        @if(auth()->user()->hasRole('ustadz'))
            loadSantri({{ $kelompok->id }});
        @endif
    
        function loadSantri(kelompokId) {
            $.ajax({
                url: `/absensi-santri/get-santri/${kelompokId}`,
                type: 'GET',
                success: function (res) {
                    let html = '';
                    $.each(res, function (i, s) {
                        html += `
                            <tr>
                                <td>${s.user.name}</td>
                                <td>
                                    ${radioButton(s.id)}
                                </td>
                                <td>
                                    <input type="text"
                                        name="absensi[${s.id}][keterangan]"
                                        class="form-control"
                                        placeholder="Keterangan"
                                        style="width: 100%;"
                                    />
                                </td>
                            </tr>
                        `;
                    });
                    $('#santri-table').html(html);
                },
                error: function () {
                    alert('Gagal mengambil data santri');
                }
            });
        }
    
        function radioButton(id) {
        let status = ['hadir', 'ghoib', 'sakit', 'izin'];
        let html = '';
        $.each(status, function (i, v) {
            html += `
                <label class="me-3">
                    <input type="radio"
                           name="absensi[${id}][status]"
                           value="${v}"
                           required> ${v}
                </label>
            `;
        });
        return html;
    }

    });
    </script>


@endsection