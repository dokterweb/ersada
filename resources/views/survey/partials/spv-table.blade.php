<div class="row row-cards">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header bg-blue-lt">
                <strong>Pengajuan Menunggu Survey</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="60">No</th>
                                <th>No Pengajuan</th>
                                <th>Nasabah</th>
                                <th>Marketing</th>
                                <th>Cabang</th>
                                <th>Tanggal</th>
                                <th width="220">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuans as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nomor_pengajuan }}</td>
                                <td>{{ optional($item->nasabah)->nama }}</td>
                                <td>{{ $item->marketing->user->name }}</td>
                                <td>{{ optional($item->cabang)->nama_cabang }}</td>
                                <td>{{ $item->created_at->format('d-m-Y') }}</td>
                                <td>
                                    <a href="{{ route('survey.create',$item->id) }}" class="btn btn-success btn-sm"><i class="fa fa-play"></i>
                                        Proses Survey
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada pengajuan yang menunggu survey.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>