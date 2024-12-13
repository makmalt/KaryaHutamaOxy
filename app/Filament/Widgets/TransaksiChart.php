<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class TransaksiChart extends ChartWidget
{
    protected static ?string $heading = 'Pembelian ';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $query = Transaksi::where('grand_total', '>', 0.00);
        $data = Trend::query($query)
            ->between(
                start: now()->subWeek(), // perweek
                end: now(),
                // start: now()->subMonth()->startOfMonth(), // Awal bulan sebelumnya (November 2024)
                // end: now()->subMonth()->endOfMonth(),
            )
            ->perDay()
            ->sum('grand_total');

        return [
            'datasets' => [
                [
                    'label' => 'Grand Total',
                    'data' => $data->map(fn(TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn(TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
