<?php

namespace App\Filament\Exports;

use App\Models\Transaksi;
use Filament\Actions\Exports\Exporter;
use Illuminate\Support\Facades\Storage;
use Filament\Notifications\Notification;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;

class TransaksiExporter extends Exporter
{
    protected static ?string $model = Transaksi::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('no_transaksi')
                ->label('NO TRANSAKI'),
            ExportColumn::make('tgl_transaksi')
                ->label('TGL TRANSAKI'),
            ExportColumn::make('grand_total')
                ->label('GRAND TOTAL')
                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 2, ',', '.')),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your transaksi export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }
        return $body;
    }
    public function getFileName(Export $export): string
    {
        return "Laporan Transaksi-{$export->getKey()}.xlsx";
    }
}
