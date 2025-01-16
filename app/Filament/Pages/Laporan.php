<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Tables\Actions\EditAction;
use App\Models\BarangTransaksi;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.laporan';
    protected static ?int $navigationSort = 6;

    protected static ?string $navigationGroup = 'Lainnya';
}
