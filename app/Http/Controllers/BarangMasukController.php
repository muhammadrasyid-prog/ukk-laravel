<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        // $barangMasuk = BarangMasuk::all();
        // return view('v_barangmasuk.index', compact('barangMasuk'));
        // menggunakan eloquent
        $rsetBarang = Barang::all();

        // Menggunakan eloquent untuk pencarian
        if ($request->search) {
            $barangMasuk = BarangMasuk::select('barangmasuk.*', 'barang.seri as seri')
                            ->join('barang', 'barang_id', '=', 'barang.id')
                            ->where('barangmasuk.id','like','%'.$request->search.'%')
                            ->orWhere('barangmasuk.tgl_masuk','like','%'.$request->search.'%')
                            ->orWhere('barangmasuk.qty_masuk','like','%'.$request->search.'%')
                            ->orWhereHas('barang', function($query) use ($request) {
                                $query->where('seri','like','%'.$request->search.'%')
                                    ->orWhere('merk','like','%'.$request->search.'%');
                            })
                            ->paginate(10);
        } else {
            $barangMasuk = BarangMasuk::select('barangmasuk.*', 'barang.seri as seri')
                                    ->join('barang', 'barang_id', '=', 'barang.id')
                                    ->paginate(10);
        }

        // Kembalikan view dengan barangMasuk dan rsetBarang
        return view('v_barangmasuk.index', compact('barangMasuk', 'rsetBarang'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        $rsetBarang = Barang::all();
        return view('v_barangmasuk.create', compact('rsetBarang'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'tgl_masuk' => 'required|date',
            'qty_masuk' => 'required|integer',
            'barang_id' => 'required|exists:barang,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barangmasuk.create')
                ->withErrors($validator)
                ->withInput();
        }

        // BarangMasuk::create([
        //     'tgl_masuk' => $request->tgl_masuk,
        //     'qty_masuk' => $request->qty_masuk,
        //     'barang_id' => $request->barang_id,
        // ]);

        // return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Disimpan']);

        try {
            DB::beginTransaction(); // Mulai transaksi
    
            // Sisipkan data baru ke tabel kategori
            DB::table('barangmasuk')->insert([
                'tgl_masuk' => $request->tgl_masuk,
                'qty_masuk' => $request->qty_masuk,
                'barang_id' => $request->barang_id,
            ]);
    
            DB::commit(); // Commit perubahan jika berhasil

            // Kembali ke halaman index dengan pesan sukses
            return redirect()->route('barangmasuk.index')->with([
                'success' => 'Data berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            // Rollback perubahan jika terjadi kesalahan
            DB::rollBack();

            // Laporkan kesalahan
            report($e);

    
            // Kembali ke halaman pembuatan kategori dengan pesan error
            return redirect()->route('barangmasuk.index')->with([
                'error' => 'Terjadi kesalahan saat menyimpan data! Kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $barangMasuk = BarangMasuk::find($id);
        return view('v_barangmasuk.show', compact('barangMasuk'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $barangMasuk = BarangMasuk::find($id);
        $rsetBarang = Barang::all();
        return view('v_barangmasuk.edit', compact('barangMasuk', 'rsetBarang'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $validator = Validator::make($request->all(), [
            'tgl_masuk' => 'required|date',
            'qty_masuk' => 'required|integer',
            'barang_id' => 'required|exists:barang,id',
        ]);

        if ($validator->fails()) {
            return redirect()->route('barangmasuk.edit', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $barangMasuk = BarangMasuk::find($id);

        $barangMasuk->update([
            'tgl_masuk' => $request->tgl_masuk,
            'qty_masuk' => $request->qty_masuk,
            'barang_id' => $request->barang_id,
        ]);

        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $barangMasuk = BarangMasuk::find($id);
        // Menghitung stok baru setelah penghapusan data BarangMasuk
        $newStok = $barangMasuk->barang->stok - $barangMasuk->qty_masuk;

        // Jika stok baru negatif, tolak operasi penghapusan
        if ($newStok < 0) {
        return redirect()->route('barangmasuk.index')->with('error', 'Stok barang tidak mencukupi untuk menghapus data ini.');
    }
        $barangMasuk->delete();
        return redirect()->route('barangmasuk.index')->with(['success' => 'Data Barang Masuk Berhasil Dihapus']);
    }
}
