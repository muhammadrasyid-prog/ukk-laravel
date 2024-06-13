<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barang;
use App\Models\BarangMasuk;
use Illuminate\Support\Facades\Validator;


class BarangMasukController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $barangMasuk = BarangMasuk::all();
        return view('v_barangmasuk.index', compact('barangMasuk'));
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

        BarangMasuk::create([
            'tgl_masuk' => $request->tgl_masuk,
            'qty_masuk' => $request->qty_masuk,
            'barang_id' => $request->barang_id,
        ]);

        return redirect()->route('barangmasuk.index')->with(['Success' => 'Data Barang Masuk Berhasil Disimpan']);
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

        return redirect()->route('barangmasuk.index')->with(['Success' => 'Data Barang Masuk Berhasil Diubah']);
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
        return redirect()->route('barangmasuk.index')->with(['Success' => 'Data Barang Masuk Berhasil Dihapus']);
    }
}
