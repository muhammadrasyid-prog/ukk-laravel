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
        //
        // $rsetKategori = Kategori::all();

        // return view('v_kategori.index', compact('rsetKategori'));

        if ($request->search){
            $rsetKategori = DB::table('kategori')->select('id','deskripsi',DB::raw('ketKategorik(kategori) as kat'))
                                                 ->where('id','like','%'.$request->search.'%')
                                                 ->orWhere('deskripsi','like','%'.$request->search.'%')
                                                 ->orWhere('kategori', '=', '%'.$request->search.'%')
                                                 ->paginate(10);
        }else {
            $rsetKategori = DB::table('kategori')->select('id','deskripsi',DB::raw('ketKategorik(kategori) as kat'))
                                                //  ->where('kategori', '=', 'A') // Batasi hanya untuk kategori 'A'
                                                 ->paginate(10);
        }
        // return $rsetKategori;
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

        Kategori::create([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]); 

        return redirect()->route('kategori.index')->with(['Success' => 'Data Kategori Berhasil Disimpan']);
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

        return redirect()->route('kategori.index')->with(['Success' => 'Data Kategori Berhasil Diubah']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        if (DB::table('kategori')->where('kategori', $id)->exists()){
            return redirect()->route('kategori.index')->with(['Gagal' => 'Data Gagal Dihapus']);
        } else {
            $rsetKategori = Kategori::find($id);
            $rsetKategori->delete();
            return redirect()->route('kategori.index')->with(['Success' => 'Data Berhasil Dihapus']);
        }
    }
}
