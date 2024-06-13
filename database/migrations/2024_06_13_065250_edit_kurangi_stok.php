<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        DB::unprepared('
        CREATE TRIGGER after_barang_keluar_update
        AFTER UPDATE ON barangkeluar FOR EACH ROW
        BEGIN
            -- Cek apakah kolom qty_keluar diupdate
            IF NEW.qty_keluar <> OLD.qty_keluar THEN
                -- Perbarui stok di tabel barang
                UPDATE barang
                SET stok = stok + OLD.qty_keluar - NEW.qty_keluar
                WHERE id = NEW.barang_id;
            END IF;
        END;
    ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        DB::unprepared('DROP TRIGGER IF EXISTS after_barang_keluar_update');
    }
};