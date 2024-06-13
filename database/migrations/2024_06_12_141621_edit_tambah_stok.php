<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared('

            CREATE TRIGGER edit_tambah_stok AFTER UPDATE ON barangmasuk
            FOR EACH ROW
            BEGIN
                UPDATE barang SET barang.stok = barang.stok + (NEW.qty_masuk -OLD.qty_masuk) WHERE barang.id = NEW.barang_id; 
            END;

        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS edit_tambah_stok');
    }
};
