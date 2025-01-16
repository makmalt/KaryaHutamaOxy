<?php

namespace App\Filament\Resources\BarangResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Get;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

use Filament\Resources\RelationManagers\RelationManager;

class TambahStokRelationManager extends RelationManager
{
    protected static string $relationship = 'tambahStok';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('supplier_id')
                    ->label('Supplier')
                    ->options(Supplier::all()->pluck('nama_supplier', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\DatePicker::make('tgl_masuk')
                    ->label('Tanggal Masuk')
                    ->default(now())
                    ->required(),
                Forms\Components\TextInput::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $kuantitas = $get('kuantitas');
                        $totalHarga = $kuantitas * $state;
                        $set('total_harga', $totalHarga);
                    })
                    ->required(),
                Forms\Components\TextInput::make('kuantitas')
                    ->numeric()
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $harga_satuan = $get('harga_satuan');
                        $totalHarga = $harga_satuan * $state;
                        $set('total_harga', $totalHarga);
                    })
                    ->required(),
                Forms\Components\TextInput::make('total_harga')
                    ->label('Total Harga')
                    ->numeric()
                    ->readOnly()
                    ->required()
                    ->reactive(),
                Forms\Components\Textarea::make('keterangan')
                    ->label('Keterangan'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier_id')
                    ->label('Supplier')
                    ->formatStateUsing(fn($state) => Supplier::find($state)?->nama_supplier ?? 'Tidak Diketahui'),
                TextColumn::make('tgl_masuk')
                    ->label('Tanggal Masuk')
                    ->dateTime('d M Y'),
                TextColumn::make('kuantitas'),
                TextColumn::make('harga_satuan')
                    ->label('Harga Satuan')
                    ->prefix('Rp. ')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                TextColumn::make('total_harga')
                    ->label('Total Harga')
                    ->prefix('Rp. ')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.'))

            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function () {
                        // Dispatch ke EditBarang page
                        $this->dispatch('refresh')
                            ->to('app.filament.resources.barang-resource.pages.edit-barang');
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(function () {
                        // Dispatch ke EditBarang page
                        $this->dispatch('refresh')
                            ->to('app.filament.resources.barang-resource.pages.edit-barang');
                    }),
                Tables\Actions\DeleteAction::make()
                    ->after(function () {
                        // Dispatch ke EditBarang page
                        $this->dispatch('refresh')
                            ->to('app.filament.resources.barang-resource.pages.edit-barang');
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
