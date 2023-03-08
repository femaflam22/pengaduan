<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Masyarakat</title>
    <link rel="stylesheet" href="{{asset('assets/css/style.css')}}">
</head>

<body>
    <h2 class="title-table">Laporan Keluhan</h2>
    <div style="display: flex; justify-content: center; margin-bottom: 30px">
        <a href="/logout" style="text-align: center">Logout</a>
        <div style="margin: 0 10px"> | </div>
        <a href="/" style="text-align: center">Home</a>
    </div>
    <div style="display: flex; justify-content: flex-end; align-items: center; padding: 0 30px">
        {{-- menggunakan method GET karna route untuk masuk ke halaman data ini menggunakan ::get --}}
        <form action="" method="GET">
            @csrf
            <input type="text" name="search" placeholder="Cari berdasarkan nama...">
            <button type="submit" class="btn-login" style="margin-top: -1px">Cari</button>
        </form>
        {{-- refresh balik lg ke route data karna nanti pas di klik refresh mau bersihin riwayat pencarian sebelumnya dan balikin lagi nya ke halaman data lagi --}}
        <a href="{{route('data')}}" style="margin-left: 10px; margin-top: -10px">Refresh</a>
        <a href="{{route('export-pdf')}}" style="margin-left: 10px; margin-top: -10px">Cetak PDF</a>
        <a href="{{route('export.excel')}}" style="margin-left: 10px; margin-top: -10px">Cetak Excel</a>
    </div>
    <div style="padding: 0 30px">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Telp</th>
                    <th>Pengaduan</th>
                    <th>Gambar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $no = 1;
                @endphp
                @foreach ($reports as $report)
                    <tr>
                        {{-- menambahkan angka 1 dr $no di php tiap baris nya --}}
                        <td>{{$no++}}</td>
                        <td>{{$report['nik']}}</td>
                        <td>{{$report['nama']}}</td>
                {{-- mengganti format no yg 08 jadi 628 --}}
                @php
                    // substr_replace : mengubah karakter string
                    // punya 3 argumen. argumen ke-1 : data yg mau dimasukin ke string
                    // argumen ke-2 : mulai dr index mana ubahnya
                    // argumen ke-3 : sampai index mana diubahnya
                    $telp = substr_replace($report->no_telp, "62", 0, 1);
                @endphp
                    {{-- yg ditampilkan tag a dengan $telp (data no_telp yg uda diubah jadi format 628) --}}
                    {{-- %20 fungsinya buat ngasi space --}}
                    {{-- target="_blank" untuk buka di tab baru --}}
                    <td><a href="https://wa.me/{{$telp}}?text=Hallo,%20{{$report->nama}}%20pengaduan%20anda%20akan%20kami%20cek" target="_blank">{{$telp}}</a></td>
                        <td>{{$report['pengaduan']}}</td>
                        <td>
                            {{-- menampilkan gambar full layar di tab baru --}}
                            <a href="../assets/image/{{$report->foto}}" target="_blank">
                                <img src="{{asset('assets/image/'.$report->foto)}}" width="120">
                            </a>
                        </td>
                        <td style="display: flex; justify-content: center;">
                            <form action="{{route('destroy', $report->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">Hapus</button>
                            </form>
                            <div>
                                <a href="{{route('export.pdf-detail',$report->id)}}" class="back-btn" style="margin-left: 8px">Print</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>

</html>
