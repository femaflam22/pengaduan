<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;
use Excel;
use App\Exports\ReportsExport;

class ReportController extends Controller
{
    public function detailPDF($id)
    {
        $data = Report::where('id', $id)->firstOrFail()->toArray();
        view()->share('report', $data);
        $pdf = PDF::loadView('print_detail', $data);
        $nameFile = 'pengaduan-' . $id . '.pdf';
        return $pdf->download($nameFile);
    }

    public function exportPDF() { 
        // ambil data yg akan ditampilkan pada pdf, bisa juga dengan where atau eloquent lainnya
        $data = Report::all()->toArray(); 
        // kirim data yg diambil kepada view yg akan ditampilkan, kirim dengan inisial 
        view()->share('reports',$data); 
        // panggil view blade yg akan dicetak pdf serta data yg akan digunakan
        $pdf = PDF::loadView('print', $data); 
        // download PDF file dengan nama tertentu
        return $pdf->download('reports-data.pdf'); 
    } 

    public function exportExcel()
    {
        // nama file yang akan terdownload
        // selain .xlsx juga bisa .csv
        $file_name = 'data_keseluruhan_pengaduan.xlsx';
        // memanggil file ReportsExport dan mendownloadnya dengan nama seperti $file_name
        return Excel::download(new ReportsExport, $file_name);
    }

    public function index()
    {
        //  ASC : ascending -> terkecil terbesar 1-100 / a-z
        // DESC : descending -> terbesar terkecil 100-1 / z- a
        $reports = Report::orderBy('created_at', 'DESC')->simplePaginate(2);
        return view('index', compact('reports'));
    }

    //  Request $ request ditambahkan karna pada halaman data ada fitur search nya, dan akan mengambil teks yg diinput search
    public function data(Request $request)
    {
        // ambil data yg diinput ke input yg name nya search
        $search = $request->search;
        // where akan mencari data berdasarkan column nama
        // data yang diambil merupakan data yg 'LIKE' (terdapat) teks yg dimasukin ke input search
        // contoh : ngisi input search dengan 'fem'
        // bakal nyari ke db yg column nama nya ada isi 'fem' nya
        $reports = Report::where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data', compact('reports'));
    }

    public function dataPetugas(Request $request)
    {
        $search = $request->search;
        // with : ambil relasi (nama fungsi hasOne/hasMany/belongsTo di modelnya), ambil data dr relasi itu
        $reports = Report::with('response')->where('nama', 'LIKE', '%' . $search . '%')->orderBy('created_at', 'DESC')->get();
        return view('data_petugas', compact('reports'));
    }

    public function auth(Request $request)
    {
        // validasi
        $request->validate([
            'email' => 'required|email:dns',
            'password' => 'required',
        ]);
        // ambil data dan simpan di variable
        $user = $request->only('email', 'password');
        // simpen data ke auth dengan Auth::attempt
        // cek proses penyimpanan ke auth berhasil ato tidak lewat if else
        if (Auth::attempt($user)) {
            // nesting if, if bersarang, if didalam if
            // kalau data login uda masuk ke fitur Auth, dicek lagi pake if-else
            // kalau data Auth tersebut role nya admin maka masuk ke route data
            // kalau data Auth role nya petugas maka masuk ke route data.petugas
            if (Auth::user()->role == 'admin') {
                return redirect()->route('data');
            }elseif(Auth::user()->role == 'petugas') {
                return redirect()->route('data.petugas');
            }
        }else {
            return redirect()->back()->with('gagal', 'Gagal login, coba lagi!');
        }
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validasi
        $request->validate([
            'nik' => 'required',
            'nama' => 'required',
            'no_telp' => 'required|max:13',
            'pengaduan' => 'required|min:5',
            'foto' => 'required|image|mimes:jpg,jpeg,png,svg',
        ]);
        // pindah foto ke folder public
        $path = public_path('assets/image/');
        $image = $request->file('foto');
        $imgName = rand() . '.' . $image->extension(); // foto.jpg : 1234.jpg
        $image->move($path, $imgName);
        // tambah data ke db
        Report::create([
            'nik' => $request->nik,
            'nama' => $request->nama,
            'no_telp' => $request->no_telp,
            'pengaduan' => $request->pengaduan,
            'foto' => $imgName,
        ]);
        return redirect()->back()->with('success', 'Berhasil menambahkan pengaduan!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(Report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit(Report $report)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Report $report)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Report::where('id', $id)->firstOrFail();
        $image = public_path('assets/image/'.$data['foto']);
        unlink($image);
        $data->delete();
        return redirect()->back();
    }
}
