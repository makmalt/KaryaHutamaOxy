<?php

namespace App\Filament\Widgets;

use Flowframe\Trend\Trend;
use App\Models\BarangTransaksi;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class BarangTransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Top Barang Bulan ini';
    protected static ?int $sort = 2;
    protected function getData(): array
    {
        $query = BarangTransaksi::whereNotNull('barang_id');
        $data = Trend::query($query)
            ->between(
                start: now()->startOfMonth(),
                end: now()->endOfMonth(),
            )
            ->perMonth()
            ->sum('quantity');

        $transactions = BarangTransaksi::with('barang')
            ->whereNotNull('barang_id')
            ->where('quantity', '>', 0) // ubah batas 
            ->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->get();

        $barangLabels = $transactions->pluck('barang.nama_barang')->unique()->values();

        $data = $transactions->mapWithKeys(function ($transaction) {
            return [$transaction->barang->nama_barang => $transaction->quantity];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Barang Terjual',
                    'data' => $barangLabels->map(fn($label) => $data->get($label, 0)),
                ],
            ],
            'labels' => $barangLabels->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
