<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th,td{
            border:1px solid #000;
            padding:6px;
        }

        .title{
            text-align:center;
            font-size:20px;
            font-weight:bold;
            margin-bottom:20px;
        }

        .text-right{
            text-align:right;
        }

    </style>

</head>

<body>

    <div class="title">BUKTI PELUNASAN PEMBIAYAAN</div>
    <table>
        <tr>
            <th width="30%">Nomor Pelunasan</th>
            <td>{{ $pelunasan->nomor_pelunasan }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>
            <td>{{ $pelunasan->tanggal_pelunasan->format('d-m-Y') }}</td>
        </tr>
        <tr>
            <th>Nama Nasabah</th>
            <td>{{ $pelunasan->pembiayaan->pengajuan->nasabah->nama }}</td>
        </tr>
        <tr>
            <th>No Pembiayaan</th>
            <td>{{ $pelunasan->pembiayaan->nomor_pembiayaan }}</td>
        </tr>
        <tr>
            <th>Plafond</th>
            <td>Rp {{ number_format($pelunasan->pembiayaan->plafond,0,',','.') }}</td>
        </tr>
    </table>
    <br>
    <table>
        <tr>
            <th>Sisa Pokok</th>
            <td class="text-right">Rp {{ number_format($pelunasan->sisa_pokok,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Sisa Bunga</th>
            <td class="text-right">Rp {{ number_format($pelunasan->sisa_bunga,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Denda</th>
            <td class="text-right">Rp {{ number_format($pelunasan->denda,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Diskon</th>
            <td class="text-right">Rp {{ number_format($pelunasan->diskon,0,',','.') }}</td>
        </tr>
        <tr>
            <th>Total Pelunasan</th>
            <td class="text-right"><strong>Rp {{ number_format($pelunasan->total_pelunasan,0,',','.') }}</strong></td>
        </tr>
    </table>
        <br><br>
        <table style="border:none">
        <tr style="border:none">
            <td style="border:none;text-align:center">
            Petugas
            <br><br><br><br>
            <b>{{ $pelunasan->creator->name }}</b>
            </td>
            <td style="border:none;text-align:center">
            Nasabah
            <br><br><br><br>
            _____________________
            </td>
        </tr>
    </table>
</body>
</html>