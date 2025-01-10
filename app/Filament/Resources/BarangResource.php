<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\BarangResource\Pages;
use AnourValar\EloquentSerialize\Tests\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use App\Filament\Resources\BarangResource\RelationManagers;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Split as ComponentsSplit;
use Filament\Infolists\Components\Section as ComponentsSection;

class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tambah Barang')
                    ->description('')
                    ->schema([
                        TextInput::make('nama_barang')
                            ->required()
                            ->autocapitalize('words'),
                        TextInput::make('deskripsi')->nullable(),
                        TextInput::make('hargaBeli')
                            ->label('Harga Beli')
                            ->prefix('Rp. ')
                            ->required()
                            ->numeric(),
                        TextInput::make('harga')
                            ->label('Harga Jual')
                            ->prefix('Rp. ')
                            ->required()
                            ->numeric(),
                        TextInput::make('stok_tersedia')
                            ->label('Stok Awal')
                            ->required()
                            ->numeric(),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(
                                Supplier::all()->pluck('nama_supplier', 'id')
                            )
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set, $get) {
                                $namaBarang = $get('nama_barang');
                                $supplierNama = Supplier::find($state)->nama_supplier; // Ambil nama supplier
                                $set('sku_id', strtoupper(substr($supplierNama, 0, 3)) . '-' . str_replace(' ', '', $namaBarang));
                            }),
                        TextInput::make('sku_id')
                            ->label('SKU')
                            ->required()
                            ->unique(table: Barang::class, column: 'sku_id', ignoreRecord: true)
                            ->readOnly()
                            ->reactive()
                            ->helperText('Format: 3 huruf awal supplier diikuti tanda "-" nama barang'),
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
                TextColumn::make('sku_id')
                    ->searchable()
                    ->label('SKU ID'),
                TextColumn::make('nama_barang')
                    ->searchable()
                    ->label('Nama Barang'),
                TextColumn::make('harga')
                    ->label('Harga')
                    ->prefix('Rp. ')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                TextColumn::make('stok_tersedia')
                    ->label('Stok'),
                TextColumn::make('supplier.nama_supplier')
                    ->searchable()
                    ->label('Supplier'),
                ImageColumn::make('image')
                    ->label('Gambar')
                    ->circular(),
            ])
            ->defaultSort('stok_tersedia', 'ascending')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Add')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('stok_tambahan')
                            ->label('Jumlah Stok Tambahan')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ])
                    ->action(function (array $data, Barang $record) {
                        // Tambahkan stok_tambahan ke stok_tersedia
                        $record->stok_tersedia += $data['stok_tambahan'];
                        $record->save();
                    })
                    ->modalHeading('Tambah Stok')
                    ->modalSubmitActionLabel('Simpan')
                    ->color('success'),
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
                                        TextEntry::make('sku_id'),
                                        TextEntry::make('deskripsi'),
                                        TextEntry::make('supplier.nama_supplier'),
                                    ]),
                                    ComponentsGroup::make([
                                        TextEntry::make('hargaBeli')
                                            ->label('Harga Beli')
                                            ->prefix('Rp. ')
                                            ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                                        TextEntry::make('harga')
                                            ->label('Harga Jual')
                                            ->prefix('Rp. ')
                                            ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
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
