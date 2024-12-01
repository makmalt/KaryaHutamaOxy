<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Filament\Widgets\StatsOverviewWidget;

class Laporan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?int $navigationSort = 6;
    protected static string $view = 'filament.pages.laporan';

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }
}
