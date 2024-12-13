<?php

namespace App\Livewire;

use App\Models\Barang;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;

class BarangHabis extends Component implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;
    public function table(Table $table): Table
    {
        return $table
            ->query(Barang::query()->where('stok_tersedia', '<=', 5)->orderBy('stok_tersedia', 'desc'))
            ->columns([
                TextColumn::make('nama_barang')
                    ->label('Nama Barang'),
                TextColumn::make('stok_tersedia')
                    ->label('Jumlah'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    public function render()
    {
        return view('livewire.barang-habis');
    }
}
