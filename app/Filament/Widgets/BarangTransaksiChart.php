<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\BarangTransaksi;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class BarangTransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Barang';
    protected static ?int $sort = 2;
    protected function getData(): array
    {
        $query = BarangTransaksi::whereNotNull('barang_id');
        $data = Trend::query($query)
            ->between(
                start: now()->subMonth()->startOfMonth(),
                end: now()->subMonth()->endOfMonth(),
            )
            ->perMonth()
            ->count();

        $barangLabels = BarangTransaksi::with('barang')
            ->whereNotNull('barang_id')
            // ->where('quantity', '>', 5) // barang yang terjual lebih dari 5
            ->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->get()
            ->map(fn($transaksi) => $transaksi->barang->nama_barang) // Ambil nama barang
            ->unique(); // Hilangkan duplikasi nama barang


        return [
            'datasets' => [
                [
                    'label' => 'Barang Terjual',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $barangLabels->values()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
