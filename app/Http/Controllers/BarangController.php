<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\Kategori; // Pastikan untuk mengimpor model Kategori
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        // $rsetBarang = Barang::all();
        // $rsetBarang = Barang::with('kategori')->get();
        // return view('v_barang.index', compact('rsetBarang'));

        // menggunakan eloquent
        if ($request->search) {
            $rsetBarang = Barang::with('kategori')
                            ->where('id','like','%'.$request->search.'%')
                            ->orWhere('merk', 'like','%'.$request->search.'%')
                            ->orWhere('seri','like','%'.$request->search.'%')
                            ->orWhere('spesifikasi','like','%'.$request->search.'%')
                            ->orWhere('stok','like','%'.$request->search.'%')
                            ->orWhereHas('kategori', function($query) use ($request) {
                                $query->where('deskripsi','like','%'.$request->search.'%');
                            })
                            ->paginate(10);
        } else {
            $rsetBarang = Barang::with('kategori')->paginate(10);
        }
        
        return view('v_barang.index', compact('rsetBarang'));
    }

    public function create()
    {
        $rsetKategori = Kategori::all();
        return view('v_barang.create', compact('rsetKategori'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'merk' => 'required|string|max:50|unique:barang,merk',
            'seri' => 'nullable|string|max:50',
            'spesifikasi' => 'nullable|string',
            'stok' => 'nullable|integer',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barang.create')
                ->withErrors($validator)
                ->withInput();
        }

        // Barang::create([
        //     'merk' => $request->merk,
        //     'seri' => $request->seri,
        //     'spesifikasi' => $request->spesifikasi,
        //     'stok' => $request->stok,
        //     'kategori_id' => $request->kategori_id,
        // ]);

        // return redirect()->route('barang.index')->with(['success' => 'Data Barang Berhasil Disimpan!']);
        
        try {
            DB::beginTransaction(); // Mulai transaksi
    
            // Sisipkan data baru ke tabel kategori
            DB::table('barang')->insert([
                'merk' => $request->merk,
                'seri' => $request->seri,
                'spesifikasi' => $request->spesifikasi,
                'kategori_id' => $request->kategori_id,
            ]);
            DB::commit(); // Commit perubahan jika berhasil

            // Kembali ke halaman index dengan pesan sukses
            return redirect()->route('barang.index')->with([
                'success' => 'Data berhasil disimpan!'
            ]);
            
        } catch (\Exception $e) {
            // Rollback perubahan jika terjadi kesalahan
            DB::rollBack();
            // Laporkan kesalahan
            report($e);
                
            // Kembali ke halaman pembuatan kategori dengan pesan error
            return redirect()->route('barang.index')->with([
                'error' => 'Terjadi kesalahan saat menyimpan data! Kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function show(string $id)
    {
        $rsetBarang = Barang::find($id);

        return view('v_barang.show', compact('rsetBarang'));
    }

    public function edit(string $id)
    {
        $rsetBarang = Barang::find($id);
        $rsetKategori = Kategori::all(); // Anda mungkin perlu menyesuaikan ini sesuai dengan model dan tabel kategori Anda
        return view('v_barang.edit', compact('rsetBarang', 'rsetKategori'));
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'merk' => 'required|string|max:50',
            'seri' => 'nullable|string|max:50',
            'spesifikasi' => 'nullable|string',
            'stok' => 'nullable|integer',
            'kategori_id' => 'required|exists:kategori,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barang.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $barang = Barang::find($id);

        $barang->update([
            'merk' => $request->merk,
            'seri' => $request->seri,
            'spesifikasi' => $request->spesifikasi,
            'stok' => $request->stok,
            'kategori_id' => $request->kategori_id,
        ]);

        return redirect()->route('barang.index')->with(['success' => 'Data Barang Berhasil Diubah!']);
    }

    public function destroy($id)
    {
        if (DB::table('barangmasuk')->where('barang_id', $id)->exists() || DB::table('barangkeluar')->where('barang_id', $id)->exists()){ 
            return redirect()->route('barang.index')->with(['error' => 'Data Gagal dihapus']);
        } else {
            $rseBarang = Barang::find($id);
            $rseBarang->delete();
            return redirect()->route('barang.index')->with(['success' => 'Data Berhasil dihapus']);
        }
    }
}
