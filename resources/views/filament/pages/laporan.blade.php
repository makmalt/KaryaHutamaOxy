<x-filament-panels::page>
    @livewire(\App\Filament\Widgets\BarangTransaksiChart::class)
    <h2><strong>Barang Keluar</strong></h2>
    @livewire('list-barang-transaksi')
    <h2><strong>Stok Menipis</strong></h2>
    @livewire('barang-habis')
</x-filament-panels::page>