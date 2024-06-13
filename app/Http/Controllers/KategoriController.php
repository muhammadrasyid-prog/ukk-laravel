<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $rsetKategori = DB::table('kategori')
                            ->select('id', 'deskripsi', DB::raw('getKategori(kategori) as kat'))
                            ->where('id','like','%'.$request->search.'%')
                            // ->orWhere('deskripsi','like','%'.$request->search.'%')
                            ->orWhere('kategori','like','%'.$request->search.'%')
                            //  ->orWhere(DB::raw('ketKategori(kategori)'),'like','%'.$request->search.'%')
                            ->paginate(10);
        } else {
            $rsetKategori = DB::table('kategori')
                            ->select('id', 'deskripsi', DB::raw('getKategori(kategori) as kat'))
                            ->paginate(10);
        }
        
        return view('v_kategori.index', compact('rsetKategori'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('v_kategori.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'deskripsi' => 'required|string|max:100',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);

        // Kategori::create([
        //     'deskripsi' => $request->deskripsi,
        //     'kategori' => $request->kategori,
        // ]); 

        // return redirect()->route('kategori.index')->with(['success' => 'Data Kategori Berhasil Disimpan']);
        
        try {
            DB::beginTransaction(); // Mulai transaksi
    
            // Sisipkan data baru ke tabel kategori
            DB::table('kategori')->insert([
                'deskripsi' => $request->deskripsi,
                'kategori' => $request->kategori,
            ]);
    
            DB::commit(); // Commit perubahan jika berhasil

            // Kembali ke halaman index dengan pesan sukses
            return redirect()->route('kategori.index')->with([
                'success' => 'Data berhasil disimpan!'
            ]);

        } catch (\Exception $e) {
            // Laporkan kesalahan
            report($e);
    
            // Rollback perubahan jika terjadi kesalahan
            DB::rollBack();
    
            // Kembali ke halaman pembuatan kategori dengan pesan error
            return redirect()->route('kategori.index')->with([
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
        $rsetKategori = Kategori::find($id);

        return view('v_kategori.show', compact('rsetKategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
        $rsetKategori = Kategori::find($id);

        return view('v_kategori.edit', compact('rsetKategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $request->validate([
            'deskripsi' => 'required|string|max:100',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);

        $rsetKategori = Kategori::find($id);

        $rsetKategori->update([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]); 

        return redirect()->route('kategori.index')->with(['success' => 'Data Kategori Berhasil Diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        if (DB::table('kategori')->where('kategori', $id)->exists()){
            return redirect()->route('kategori.index')->with(['error' => 'Data Gagal Dihapus']);
        } else {
            $rsetKategori = Kategori::find($id);
            $rsetKategori->delete();
            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus']);
        }
    }
}
