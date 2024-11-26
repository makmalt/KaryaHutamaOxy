<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Form;
use App\Models\Transaksi;
use Filament\Tables\Table;
use App\Models\BarangTransaksi;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Validation\ValidationException;
use App\Filament\Resources\TransaksiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiResource\RelationManagers;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('no_transaksi')
                    ->default(fn() => 'TRX-' . now()->format('dmY') . '-' . strtoupper(uniqid()))
                    ->readOnly(),
                DateTimePicker::make('tgl_transaksi')
                    ->default(now())
                    ->required(),

                //repeater untuk menambahkan barang secara dinamis
                Repeater::make('barang_transaksis')
                    ->label('Transaksi') // nama field di database
                    ->relationship('barangTransaksi')
                    ->reactive() // Jika Anda menggunakan relasi
                    ->schema([
                        Select::make('barang_id')
                            ->label('Barang')
                            ->options(Barang::all()->pluck('nama_barang', 'id'))
                            ->searchable()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                $hargaBarang = Barang::find($state)?->harga ?? 0;
                                $set('harga_barang', $hargaBarang);
                            }),
                        TextInput::make('harga_barang')
                            ->readOnly()
                            ->numeric()
                            ->prefix('Rp. ')
                            ->required(),
                        TextInput::make('quantity')
                            ->label('Kuantitas')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->reactive()
                            ->minValue(0)
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                //logic for stok
                                $barang = Barang::find($get('barang_id'));
                                if ($barang && $state > $barang->stok_tersedia) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Jumlah barang melebihi stok tersedia.')
                                        ->danger()
                                        ->send();

                                    $set('quantity', 0);
                                    $set('total_harga', 0);
                                } else {
                                    // Hitung ulang total harga jika stok mencukupi
                                    $hargaBarang = $get('harga_barang') ?? 0;
                                    $totalHarga = $hargaBarang * $state;
                                    $set('total_harga', number_format($totalHarga, 2, '.', ''));
                                }
                            }),
                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->readOnly()
                            ->prefix('Rp. ')
                            ->numeric(),
                    ])
                    ->columnSpan('full') // Agar form-nya lebar
                    ->minItems(1) // Minimal 1 barang
                    ->addActionLabel('Tambah Barang'),

                // Total transaksi (menghitung total semua barang)
                TextInput::make('grand_total')
                    ->label('Grand Total')
                    ->numeric()
                    ->readOnly()
                    ->reactive()
                    ->default(0)
                    ->prefix('Rp. ')
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $barangTransaksis = $get('barang_transaksis');
                        $totalHarga = collect($barangTransaksis)->sum('total_harga');

                        $set('grand_total', $totalHarga);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('no_transaksi')
                    ->label('No Transaksi')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('grand_total')
                    ->label('Grand Total')
                    ->prefix('Rp. ')
                    ->sortable(),
                TextColumn::make('tgl_transaksi')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransaksis::route('/'),
            'create' => Pages\CreateTransaksi::route('/create'),
            'view' => Pages\ViewTransaksi::route('/{record}'),
            'edit' => Pages\EditTransaksi::route('/{record}/edit'),
        ];
    }
    //Logic transaksi
}
