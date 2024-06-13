<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        // $barangKeluar = BarangKeluar::all();
        // return view('v_barangkeluar.index', compact('barangKeluar'));

        $rsetBarang = Barang::all();

        // Menggunakan eloquent untuk pencarian
        if ($request->search) {
            $barangKeluar = barangKeluar::select('barangkeluar.*', 'barang.seri as seri')
                            ->join('barang', 'barang_id', '=', 'barang.id')
                            ->where('barangkeluar.id','like','%'.$request->search.'%')
                            ->orWhere('barangkeluar.tgl_keluar','like','%'.$request->search.'%')
                            ->orWhere('barangkeluar.qty_keluar','like','%'.$request->search.'%')
                            ->orWhereHas('barang', function($query) use ($request) {
                                $query->where('seri','like','%'.$request->search.'%')
                                    ->orWhere('merk','like','%'.$request->search.'%');
                            })
                            ->paginate(10);
        } else {
            $barangKeluar = barangKeluar::select('barangkeluar.*', 'barang.seri as seri')
                                    ->join('barang', 'barang_id', '=', 'barang.id')
                                    ->paginate(10);
        }
        // Kembalikan view dengan barangMasuk dan rsetBarang
        return view('v_barangkeluar.index', compact('barangKeluar', 'rsetBarang'));
    }

    public function create()
    {
        $rsetBarang = Barang::all();
        return view('v_barangkeluar.create', compact('rsetBarang'));
    }

    public function store(Request $request)
    {
        $maxStok = Barang::max('stok');

        // // Ambil tgl_masuk berdasarkan barang_id dari request
        // $tgl_masuk = BarangMasuk::where('barang_id', $request->barang_id)->value('tgl_masuk');

        // // Jika tanggal masuk tidak ditemukan, tampilkan pesan kesalahan
        // if (!$tgl_masuk) {
        // return redirect()->route('barangkeluar.create')->with(['Gagal' => 'Tanggal masuk tidak ditemukan untuk barang ini.']);
        // }

        // // Jika tanggal keluar lebih awal dari tanggal masuk, tampilkan pesan kesalahan
        // if ($request->tgl_keluar < $tgl_masuk) {
        // return redirect()->route('barangkeluar.create')->with(['Gagal' => 'Tanggal keluar tidak boleh lebih awal dari tanggal masuk.']);
        // }

        // Ambil data barang berdasarkan barang_id
        $barang = Barang::find($request->barang_id);

        // Jika barang tidak ditemukan, buat validator gagal
        if (!$barang) {
            return redirect()->route('barangkeluar.create')
                             ->withErrors(['barang_id' => 'Barang tidak ditemukan.'])
                             ->withInput();
        }
        
        // Ambil tgl_masuk dari BarangMasuk berdasarkan barang_id
        $barangMasuk = BarangMasuk::where('barang_id', $request->barang_id)->first();
        
        // Jika tidak ada data masuk untuk barang tersebut
        if (!$barangMasuk) {
            return redirect()->route('barangkeluar.create')
                             ->withErrors(['barang_id' => 'Data masuk untuk barang ini tidak ditemukan.'])
                             ->withInput();
        }
        
        // Validasi input
        $validator = Validator::make($request->all(), [
            'tgl_keluar' => 'required|date|after_or_equal:' . $barangMasuk->tgl_masuk,
            'qty_keluar' => 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
            ], [
            'tgl_keluar.after_or_equal' => 'Tanggal keluar harus setelah atau sama dengan tanggal masuk.',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barangkeluar.create')
                ->withErrors($validator)
                ->withInput();
        }

        // BarangKeluar::create([
        //     'tgl_keluar' => $request->tgl_keluar,
        //     'qty_keluar' => $request->qty_keluar,
        //     'barang_id' => $request->barang_id,
        // ]);

        // return redirect()->route('barangkeluar.index')->with(['success' => 'Data Barang Keluar Berhasil Disimpan!']);
        
        try {
            DB::beginTransaction(); // Mulai transaksi
    
            // Sisipkan data baru ke tabel kategori
            DB::table('barangkeluar')->insert([
                'tgl_keluar' => $request->tgl_keluar,
                'qty_keluar' => $request->qty_keluar,
                'barang_id' => $request->barang_id,
            ]);
    
            DB::commit(); // Commit perubahan jika berhasil

            // Kembali ke halaman index dengan pesan sukses
            return redirect()->route('barangkeluar.index')->with([
                'success' => 'Data berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            // Laporkan kesalahan
            report($e);
    
            // Rollback perubahan jika terjadi kesalahan
            DB::rollBack();
    
            // Kembali ke halaman pembuatan kategori dengan pesan error
            return redirect()->route('barangkeluar.index')->with([
                'error' => 'Terjadi kesalahan saat menyimpan data! Kesalahan: ' . $e->getMessage()
            ]);
        }

    }

    public function show(string $id)
    {
        $barangKeluar = BarangKeluar::find($id);

        return view('v_barangkeluar.show', compact('barangKeluar'));
    }

    public function edit(string $id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $rsetBarang = Barang::all();
        return view('v_barangkeluar.edit', compact('barangKeluar', 'rsetBarang'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tgl_keluar' => 'required|date',
            'qty_keluar' => 'required|integer',
            'barang_id' => 'required|exists:barang,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barangkeluar.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $barangKeluar = BarangKeluar::find($id);

        $barangKeluar->update([
            'tgl_keluar' => $request->tgl_keluar,
            'qty_keluar' => $request->qty_keluar,
            'barang_id' => $request->barang_id,
        ]);

        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Barang Keluar Berhasil Diubah!']);
    }

    public function destroy($id)
    {
        $barangKeluar = BarangKeluar::find($id);
        $barangKeluar->delete();
        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Barang Keluar Berhasil Dihapus!']);
    }
}

