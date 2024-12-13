<?php

namespace App\Filament\Resources\TagihanResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TagihanResource;

class ListTagihans extends ListRecords
{
    protected static string $resource = TagihanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'Belum lunas' => Tab::make()->query(fn($query) => $query->where('status_lunas', 0)),
            'Lunas' => Tab::make()->query(fn($query) => $query->where('status_lunas', 1)),
        ];
    }
}
