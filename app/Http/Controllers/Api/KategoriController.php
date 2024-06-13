<?php

namespace App\Http\Controllers\Api;

//import model Kategori
use App\Models\Kategori;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
//import facade Validator
use Illuminate\Support\Facades\Validator;

//import facade Storage
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{    
    /**
     * index
     *
     * @return void
     */
    public function index()
    {
        //get all Kategoris
        $Kategori = Kategori::all();

        //return collection of Kategoris as a resource
        return response()->json($Kategori);
    }

    public function store(Request $request)
    {
         // Validate the request data
         $request->validate([
            'deskripsi' => 'required|string|max:100',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);        

        // Create a new Kategori record
        Kategori::create([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]);

        //return response
        return response()->json([
            'message' => 'Data Kategori Berhasil Ditambahkan',
            'data' => $request->all()
        ]);
        
    }

    public function show($id)
    {
        // Cari kategori berdasarkan ID
        $kategori = Kategori::find($id);

        // Periksa apakah kategori ditemukan
        if (!$kategori) {
            return response()->json([
                'message' => 'Kategori not found'
            ], 404);
        }

        // Kembalikan detail kategori sebagai respons JSON
        return response()->json([
            'message' => 'Detail Data Kategori',
            'data' => $kategori
        ]);
    }

    public function update(Request $request, String $id)
    {
        $request->validate([
            'deskripsi' => 'required|string|max:100',
            'kategori' => 'required|in:M,A,BHP,BTHP',
        ]);

        $rsetKategori = Kategori::find($id);

        $rsetKategori->update([
            'deskripsi' => $request->deskripsi,
            'kategori' => $request->kategori,
        ]); 

        // Return success response
        return response()->json([
            'message' => 'Kategori updated successfully',
            'data' => $rsetKategori,
        ]);
    }
    

    public function destroy(Kategori $kategori)
    {
        // Hapus Kategori berdasarkan model yang di-binding
        $kategori->delete();

        // Kembalikan respons JSON
        return response()->json([
            'message' => 'Kategori deleted successfully'
        ]);
    }

}