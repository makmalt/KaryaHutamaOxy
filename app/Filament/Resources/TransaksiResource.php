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
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\TransaksiExporter;
use Filament\Forms\Components\DateTimePicker;
use Illuminate\Validation\ValidationException;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\TransaksiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiResource\RelationManagers;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

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
                Repeater::make('barang_transaksis')
                    ->label('Transaksi') // nama field di database
                    ->relationship('barangTransaksi')
                    ->reactive()
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
                                $repeaterState = $get('barang_transaksis') ?? [];
                                $grandTotal = collect($repeaterState)->sum(fn($item) => $item['total_harga'] ?? 0);
                                $set('grand_total', number_format($grandTotal, 2, '.', ''));
                            }),
                        TextInput::make('total_harga')
                            ->label('Total Harga')
                            ->readOnly()
                            ->prefix('Rp. ')
                            ->numeric(),
                    ])
                    ->columnSpan('full') // Agar form-nya lebar
                    ->minItems(1) // Minimal 1 barang
                    ->addActionLabel('Tambah Barang')
                    ->afterStateUpdated(function (array $state, callable $set) {
                        // Hitung grand_total
                        $grandTotal = collect($state)->sum(fn($item) => $item['total_harga'] ?? 0);
                        $set('grand_total', number_format($grandTotal, 2, '.', ''));
                    }),

                // Total transaksi (menghitung total semua barang)
                TextInput::make('grand_total')
                    ->label('Grand Total')
                    ->numeric()
                    ->readOnly()
                    ->reactive()
                    ->default(0)
                    ->prefix('Rp. ')
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
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.'))
                    ->sortable(),
                TextColumn::make('tgl_transaksi')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('tgl_transaksi', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(TransaksiExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('delete')
                    ->action(fn(Barang $record) => $record->delete())
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Transaksi')
                    ->modalDescription('Anda yakin menghapus transaksi ini?')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->modalSubmitActionLabel('Ya, Hapus Transaksi')
                    ->modalCancelActionLabel('Batal'),
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

    public static function getNavigationLabel(): string
    {
        return 'Transaksi';
    }

    public static function getModelLabel(): string
    {
        return 'Transaksi';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Transaksi';
    }
}
