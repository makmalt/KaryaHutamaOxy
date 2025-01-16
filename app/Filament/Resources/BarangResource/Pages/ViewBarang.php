<?php

namespace App\Filament\Resources\BarangResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\BarangResource;
use App\Filament\Widgets\TambahStokWidget;

class ViewBarang extends ViewRecord
{
    protected static string $resource = BarangResource::class;
    protected ?string $heading = 'Detail Barang';
    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
