<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            //
            $table->dropUnique('barangs_sku_id_unique');

            // Ubah kolom sku_id menjadi nullable dan unique
            $table->string('sku_id')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            //
            $table->dropUnique('barangs_sku_id_unique');

            // Ubah kolom sku_id kembali ke tidak nullable dan tetap unique
            $table->string('sku_id')->nullable(false)->unique()->change();
        });
    }
};
