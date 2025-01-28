<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Get;
use App\Models\Kategori;
use App\Models\Supplier;
use Filament\Forms\Form;
use App\Models\TambahStok;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Layout\Split;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use RelationManagers\TambahStokRelationManager;
use App\Filament\Resources\BarangResource\Pages;
use AnourValar\EloquentSerialize\Tests\Models\Post;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use App\Filament\Resources\BarangResource\RelationManagers;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Split as ComponentsSplit;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Notifications\Notification;

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
                            ->numeric(),
                        TextInput::make('harga')
                            ->label('Harga Jual')
                            ->prefix('Rp. ')
                            ->required()
                            ->numeric(),
                        TextInput::make('stok_tersedia')
                            ->label('Stok Awal')
                            ->required()
                            ->reactive()
                            ->numeric(),
                        Select::make('kategori_id')
                            ->label('Kategori')
                            ->options(
                                Kategori::all()->pluck('nama_kategori', 'id')
                            )
                            ->required()
                            ->searchable()
                            ->reactive(),
                        TextInput::make('sku_id')
                            ->label('SKU')
                            ->unique(table: Barang::class, column: 'sku_id', ignoreRecord: true)
                            ->reactive()
                            ->helperText('Format: Brand ditulis, diikuti tanda "-" nama barang'),
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
                // TextColumn::make('sku_id')
                //     ->searchable()
                //     ->label('SKU ID'),
                // TextColumn::make('nama_barang')
                //     ->searchable()
                //     ->label('Nama Barang'),
                // TextColumn::make('harga')
                //     ->label('Harga')
                //     ->prefix('Rp. ')
                //     ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                // TextColumn::make('stok_tersedia')
                //     ->label('Stok'),
                // ImageColumn::make('image')
                //     ->label('Gambar')
                //     ->circular(),
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\ImageColumn::make('image')
                        ->height('100%')
                        ->width('100%'),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('sku_id')
                            ->searchable(),
                        Tables\Columns\TextColumn::make('nama_barang')
                            ->weight(FontWeight::Bold)
                            ->searchable(),
                        Tables\Columns\TextColumn::make('harga')
                            ->prefix('Rp. ')
                            ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.'))
                            ->color('gray')
                            ->limit(30),
                    ]),
                ])
            ])
            ->defaultSort('stok_tersedia', 'ascending')
            ->filters([
                //
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 3,
            ])
            ->paginated([
                18,
                36,
                72,
                'all',
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('Add')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(Supplier::all()->pluck('nama_supplier', 'id')) // Ambil daftar supplier
                            ->required()
                            ->searchable()
                            ->placeholder('Pilih supplier'),

                        TextInput::make('harga_satuan')
                            ->label('Harga Satuan')
                            ->required()
                            ->numeric()
                            ->live()
                            ->minValue(0)
                            ->placeholder('Masukkan harga satuan (per unit)')
                            ->prefix('Rp. '),

                        TextInput::make('stok_tambahan')
                            ->label('Stok Tambahan')
                            ->required()
                            ->numeric()
                            ->live()
                            ->minValue(1)
                            ->afterStateUpdated(function ($state, callable $set, Get $get) {
                                $harga_satuan = $get('harga_satuan');
                                $totalHarga = $harga_satuan * $state;
                                $set('total_harga', $totalHarga);
                            })
                            ->placeholder('Masukkan jumlah stok tambahan'),

                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->numeric()
                            ->readOnly()
                            ->prefix('Rp. '),

                        DatePicker::make('tgl_masuk')
                            ->displayFormat('d/m/Y')
                            ->default(now()),
                        Textarea::make('keterangan'),
                    ])
                    ->action(function (array $data, Barang $record) {
                        // Tambahkan stok_tambahan ke stok_tersedia
                        // $record->stok_tersedia += $data['stok_tambahan'];
                        // $record->save();

                        // Simpan ke dalam riwayat stok (jika tabel riwayat stok ada)
                        TambahStok::create([
                            'barang_id' => $record->id,
                            'supplier_id' => $data['supplier_id'],
                            'kuantitas' => $data['stok_tambahan'],
                            'harga_satuan' => $data['harga_satuan'],
                            'total_harga' => $data['stok_tambahan'] * $data['harga_satuan'], // Menghitung total harga
                            'tgl_masuk' => $data['tgl_masuk'],
                            'keterangan' => $data['keterangan'],
                        ]);

                        Notification::make()
                            ->title('Stok berhasil ditambahkan!')
                            ->body("Stok tambahan sebanyak {$data['stok_tambahan']} unit berhasil ditambahkan.")
                            ->success() // Notifikasi tipe sukses
                            ->send();
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
            RelationManagers\TambahStokRelationManager::class,
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
                                        TextEntry::make('barcode'),
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
                                        TextEntry::make('kategori_id')
                                            ->label('Kategori')
                                            ->formatStateUsing(fn($state) => Kategori::find($state)->nama_kategori)
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
