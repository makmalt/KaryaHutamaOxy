<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\TransaksiResource;
use App\Filament\Widgets\BarangTransaksiChart;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;
}
