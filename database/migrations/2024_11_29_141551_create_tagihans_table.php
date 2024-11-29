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
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->string('tagihan');
            $table->decimal('nominal_tagihan', 15, 2);
            $table->date('jatuhTempo_tagihan');
            $table->boolean('status_lunas')->default(false);
            $table->string('keterangan')->nullable();
            $table->string('img_nota')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
