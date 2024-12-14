<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Models\Transaksi;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use App\Models\BarangTransaksi;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Filters\Filter;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Exports\TransaksiExporter;
use Filament\Forms\Components\DateTimePicker;
use Filament\Actions\Exports\Enums\ExportFormat;
use App\Filament\Resources\TransaksiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TransaksiResource\RelationManagers;

class TransaksiResource extends Resource
{
    protected static ?string $model = Transaksi::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Data';


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
                    ->label('Transaksi')
                    ->relationship('barangTransaksi')
                    ->live()
                    ->schema([
                        Select::make('barang_id')
                            ->label('Barang')
                            ->options(Barang::all()->pluck('nama_barang', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
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
                            ->live()
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
                    ->live()
                    ->default(0.00)
                    ->placeholder(function (Get $get, callable $set) {
                        $fields = $get('barang_transaksis');
                        $sum = 0;
                        foreach ($fields as $field) {
                            $sum += $field['total_harga'];
                        }
                        $set('grand_total', number_format($sum, 2, '.', ''));
                        return $sum;
                    })
                    ->prefix('Rp. '),

                Section::make('Pembayaran')
                    ->schema([
                        TextInput::make('cash_input')
                            ->label('Uang Diterima')
                            ->numeric()
                            ->prefix('Rp. ')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                $grandTotal = floatval(str_replace(['Rp. ', ','], '', $get('grand_total')));
                                $cash = floatval($state);

                                if ($cash < $grandTotal) {
                                    Notification::make()
                                        ->title('Error')
                                        ->body('Uang yang diterima kurang dari total transaksi.')
                                        ->danger()
                                        ->send();
                                } else {
                                    $change = $cash - $grandTotal;
                                    $set('change_amount', number_format($change, 2, '.', ''));
                                }
                            }),

                        TextInput::make('change_amount')
                            ->label('Kembalian')
                            ->numeric()
                            ->prefix('Rp. ')
                            ->readOnly()
                    ])
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
    public function create(array $data): Model
    {
        $transactionData = Arr::except($data, ['cash_input', 'change_amount']);

        return $this->model::create($transactionData);
    }
}
