<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

    <style>

    @page{
        margin:20px;
    }

    body{
        font-family: DejaVu Sans;
        font-size:12px;
        color:#000;
    }

    table{
    width:100%;
    border-collapse:collapse;
    }

    table.border{
        width:100%;
        border-collapse:collapse;
    }

    table.border,
    table.border th,
    table.border td{
        border:1px solid #000;
    }

    table.border th,
    table.border td{
        padding:6px;
    }

    table.border th{
        font-weight:bold;
        text-align:center;
    }

    .header-table{
        width:100%;
    }

    .header-table td,
    .header-table th{
        border:none;
        padding:5px;
    }

    .header-table{

    width:100%;

    border-collapse:collapse;

    }

    .header-table td{

    border:none;

    }

    td{
        padding:5px;
        vertical-align:top;
    }

    .center{
        text-align:center;
    }

    .right{
        text-align:right;
    }

    .bold{
        font-weight:bold;
    }

    .logo{
        width:120px;
    }

    .header-title{
        font-size:20px;
        font-weight:bold;
    }

    .small{
        font-size:11px;
    }

    .spacer{
        height:12px;
    }

    .ttd{
        height:90px;
    }

    </style>
</head>

<body>
    {{-- ================= HEADER ================= --}}
    <table class="header-table">
        <tr>
            <td width="50%">LAPORAN NON PERFORMING LOAN (NPL)</b></td>
            <td>
                Periode :
                @if($request->filled('tanggal_awal') && $request->filled('tanggal_akhir'))
                    {{ \Carbon\Carbon::parse($request->tanggal_awal)->format('d-m-Y') }}
                    s/d
                    {{ \Carbon\Carbon::parse($request->tanggal_akhir)->format('d-m-Y') }}
                @else
                    Semua Periode
                @endif
            </td>
        </tr>
    </table>
    <div class="spacer"></div>
    <table class="border">
        <thead>
            <tr>
                <th>No</th>
                <th>No Pembiayaan</th>
                <th>Nasabah</th>
                <th>Cabang</th>
                <th>Marketing</th>
                <th>Jatuh Tempo</th>
                <th>Total Angsuran</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($angsurans as $row)

            @php
                $pengajuan = $row->pembiayaan?->pengajuan;
            @endphp

            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row->pembiayaan?->nomor_pembiayaan }}</td>
                <td>{{ $pengajuan?->nasabah?->nama }}</td>
                <td>{{ $pengajuan?->cabang?->nama_cabang }}</td>
                <td>{{ $pengajuan?->marketing?->user?->name }}</td>
                <td>{{ $row->angsuran_ke }}</td>
                <td>{{ $row->tanggal_jatuh_tempo?->format('d-m-Y') }}</td>
                <td class="right">Rp {{ number_format($row->total_angsuran,0,',','.') }}</td>
                <td class="right">Rp {{ number_format($row->sisa_tagihan,0,',','.') }}</td>
                <td>{{ strtoupper($row->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
</body>

</html>