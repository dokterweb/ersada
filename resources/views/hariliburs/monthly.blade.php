@extends('layouts.app')

@section('content')
<div class="page-wrapper">
   
    <!-- Page body -->
    <div class="page-body">
        <div class="container-xl">
            <div class="row row-cards">
                <div class="col-lg-12">
                    <form action="{{ route('hariliburs.monthly.store') }}" method="POST" class="card">
                    <div class="card">
                        <div class="card-header bg-cyan-lt">
                            <h3 class="card-title">Create Hari Libur</h3>
                        </div>
                            @csrf
                            <div class="card-body">
                                <div class="mb-3">
                                    <label>Tipe</label>
                                    <input type="month" id="bulan" class="form-control"
                                    value="{{ $currentMonth }}">
                                </div>
                                <!-- /.card-body -->
                                <form method="POST" action="{{ route('hariliburs.monthly.store') }}" id="formLiburBulanan">
                                    @csrf
                    
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="tableLibur">
                                            <thead>
                                                <tr>
                                                    <th>Tgl</th>
                                                    <th>Hari</th>
                                                    <th>Libur?</th>
                                                    <th>Nama Libur</th>
                                                    <th>Tipe</th>
                                                    <th>Berlaku Untuk</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- akan diisi oleh JS --}}
                                            </tbody>
                                        </table>
                                    </div>
                    
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            Simpan Hari Libur
                                        </button>
                                    </div>
                                </form>
                                
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

<script>
      function generateDays(monthValue) {
        // monthValue format: YYYY-MM (dari input type="month")
        if (!monthValue) return;

        const [year, month] = monthValue.split('-');
        const lastDay = new Date(year, month, 0).getDate(); // hari terakhir bulan

        const tbody = document.querySelector('#tableLibur tbody');
        tbody.innerHTML = '';

        const dayNames = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];

        for (let d = 1; d <= lastDay; d++) {
            const dateObj  = new Date(year, month - 1, d);
            const dayName  = dayNames[dateObj.getDay()];
            const dateStr  = dateObj.toISOString().slice(0,10); // YYYY-MM-DD
            const index    = d; // index unik per hari

            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td>${dateStr}</td>
                <td>${dayName}</td>
                <td>
                    <input type="checkbox" name="rows[${index}][is_libur]" value="1">
                    <input type="hidden" name="rows[${index}][tanggal]" value="${dateStr}">
                </td>
                <td>
                    <input type="text" class="form-control"
                           name="rows[${index}][nama_libur]"
                           placeholder="Nama libur">
                </td>
                <td>
                    <select class="form-control" name="rows[${index}][tipe]">
                        <option value="sekolah">Sekolah</option>
                        <option value="nasional">Nasional</option>
                        <option value="mingguan">Mingguan</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" name="rows[${index}][berlaku_untuk]">
                        <option value="semua">Semua</option>
                        <option value="siswa">Siswa</option>
                        <option value="ustadz">Ustadz</option>
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control"
                           name="rows[${index}][keterangan]"
                           placeholder="Keterangan">
                </td>
            `;

            tbody.appendChild(tr);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const bulanInput = document.getElementById('bulan');

        // generate awal (bulan default)
        generateDays(bulanInput.value);

        // kalau bulan diganti
        bulanInput.addEventListener('change', function() {
            generateDays(this.value);
        });
    });
</script>
@endsection