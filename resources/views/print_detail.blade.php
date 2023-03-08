<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Detail Pengaduan PDF</title>
</head>
<body>
    <h2 style="text-align: center; margin-bottom: 30px">Detail Pengaduan dengan ID-{{$report['id']}}</h2>
    <ol>
        <li style="margin-bottom: 10px">NIK Pelapor : {{$report['nik']}}</li>
        <li style="margin-bottom: 10px">Nama Pelapor : {{$report['nama']}}</li>
        <li style="margin-bottom: 10px">No Telp Pelapor : {{$report['no_telp']}}</li>
        <li style="margin-bottom: 10px">Tanggal Laporan : {{\Carbon\Carbon::parse($report['created_at'])->format('j F, Y')}}</li>
        <li style="margin-bottom: 10px">Isi Pengaduan : {{$report['pengaduan']}}</li>
        <li style="margin-bottom: 10px">Gambar Terkait : <br> <img src="assets/image/{{$report['foto']}}" width="80"></li>
    </ol>
</body>
</html>