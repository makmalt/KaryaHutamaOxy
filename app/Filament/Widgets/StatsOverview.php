<?php

namespace App\Filament\Widgets;

use App\Models\Barang;
use App\Models\Transaksi;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Barang', Barang::query()->count()),
            Stat::make('Jumlah Transaksi Bulan Ini', Transaksi::query()
                ->whereBetween('created_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])->count()),
            Stat::make(
                'Grand Total Per Bulan ini',
                'Rp. ' . number_format(Transaksi::query()
                    ->whereBetween('created_at', [
                        now()->startOfMonth(),
                        now()->endOfMonth()
                    ])->sum('grand_total'), 2, ',', '.')
            ),
        ];
    }
}
