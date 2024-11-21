<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BarangResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BarangResource\RelationManagers;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\Split as ComponentsSplit;
use Filament\Infolists\Components\TextEntry;
use Filament\Support\Enums\FontWeight;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tambah Barang')
                    ->description('')
                    ->schema([
                        TextInput::make('nama_barang')->required(),
                        TextInput::make('deskripsi')->nullable(),
                        TextInput::make('harga')->required(),
                        TextInput::make('stok_tersedia')->label('Stok Awal')->required(),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(
                                Supplier::pluck('nama_supplier', 'id')->toArray()
                            )->required(),
                        TextInput::make('barcode'),
                        FileUpload::make('image')
                            ->label('Gambar(Optional)')
                            ->uploadingMessage('Uploading...'), //logika belom jalan
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nama_barang')
                    ->label('Nama Barang'),
                TextColumn::make('harga')
                    ->label('Harga'),
                TextColumn::make('stok_tersedia')

                    ->label('Stok'),
                TextColumn::make('supplier.nama_supplier')
                    ->label('Supplier'),
                ImageColumn::make('image')
                    ->circular()
            ])
            ->defaultSort('stok_tersedia', 'ascending')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make()
                    ->schema([
                        ComponentsSplit::make([
                            ComponentsGrid::make(2)
                                ->schema([
                                    ComponentsGroup::make([
                                        TextEntry::make('nama_barang'),
                                        TextEntry::make('deskripsi'),
                                        TextEntry::make('supplier.nama_supplier'),
                                    ]),
                                    ComponentsGroup::make([
                                        TextEntry::make('harga')
                                            ->prefix('Rp.'),
                                        TextEntry::make('stok_tersedia'),
                                        TextEntry::make('barcode'),
                                    ])
                                ]),
                            ImageEntry::make('image')
                                ->hiddenLabel()
                                ->grow(false)
                        ])->from('lg')
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'view' => Pages\ViewBarang::route('/{record}'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string
    {
        return "Barang";
    }

    public static function getPluralModelLabel(): string
    {
        return "Daftar Barang";
    }

    public static function getModelLabel(): string
    {
        return "Barang";
    }
}
