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
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('supplier_id');
            // Menambahkan kembali foreign key constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }
};
