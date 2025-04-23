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
        Schema::table('tagihans', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('supplier_id')->after('id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihans', function (Blueprint $table) {
            //
            $table->dropForeign(['supplier_id']); // Hapus foreign key dulu
            $table->dropColumn('supplier_id');    // Hapus kolom supplier_id
        });
    }
};
