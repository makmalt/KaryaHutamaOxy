<?php

namespace App\Livewire;

use App\Models\Transaksi;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Filament\Widgets\ChartWidget;

class TransaksiPerDay extends ChartWidget
{
    protected static ?string $heading = 'Transaksi Minggu Ini';

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
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi Minggu ini',
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
