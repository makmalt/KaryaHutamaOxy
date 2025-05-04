<?php

use App\Models\Transaksi;
use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use App\Http\Controllers\TransaksiController;

Route::get('/', function () {
    return redirect('/admin');
});
Route::get('/invoice/{id}/print', function ($id) {
    $transaksi = Transaksi::with('barangTransaksi.barang')->findOrFail($id);

    $pdf = DomPDF::loadView('invoice', compact('transaksi'));
    return response()->streamDownload(
        fn() => print($pdf->stream()),
        "invoice-{$transaksi->no_transaksi}.pdf",
        ['Content-Type' => 'application/pdf']
    );
})->name('invoice.print');

Route::get('/transaksi/{transaksi}/struk', [TransaksiController::class, 'struk'])->name('struk');
