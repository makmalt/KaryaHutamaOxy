<x-filament-panels::page>
    <div style="display: flex; gap: 20px; justify-content: center;">
        <div style="flex: 1; max-width: 50%;">
            @livewire(\App\Filament\Widgets\BarangTransaksiChart::class)
        </div>

        <div style="flex: 1; max-width: 50%;">
            @livewire(\App\Livewire\TransaksiPerDay::class)
        </div>
    </div>

    <h2><strong>Barang Keluar</strong></h2>
    @livewire('list-barang-transaksi')

    <h2><strong>Stok Menipis</strong></h2>
    @livewire('barang-habis')
</x-filament-panels::page>