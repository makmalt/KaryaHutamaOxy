<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use Filament\Forms\Form;
use Nette\Utils\DateTime;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\ImageEntry;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use App\Filament\Resources\TagihanResource\Pages;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\Split as ComponentsSplit;
use App\Filament\Resources\TagihanResource\RelationManagers;
use App\Models\Supplier;
use Filament\Infolists\Components\Section as ComponentsSection;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Data';

    protected static ?int $navigationSort = 5;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Tagihan')
                    ->description('')
                    ->schema([
                        TextInput::make('tagihan')
                            ->label('Tagihan')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('nominal_tagihan')
                            ->label('Nominal')
                            ->prefix('Rp. ')
                            ->required()
                            ->numeric(),
                        Select::make('supplier_id')
                            ->label('Supplier')
                            ->options(
                                Supplier::all()->pluck('nama_supplier', 'id')
                            )
                            ->required()
                            ->searchable()
                            ->reactive(),
                        DatePicker::make('jatuhTempo_tagihan')
                            ->label('Jatuh Tempo')
                            ->required()
                            ->default(now()),
                        Textarea::make('keterangan')
                            ->label('Keterangan')
                            ->autosize(),
                        FileUpload::make('img_nota')
                            ->label('Nota'),
                        Checkbox::make('status_lunas')
                            ->label('Lunas')
                            ->default(false)
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('tagihan')
                    ->searchable()
                    ->label('Tagihan'),
                TextColumn::make('nominal_tagihan')
                    ->label('Nominal')
                    ->prefix('Rp. ')
                    ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                TextColumn::make('jatuhTempo_tagihan')
                    ->date('d M Y')
                    ->label('Jatuh Tempo'),
                TextColumn::make('supplier.nama_supplier')
                    ->searchable()
                    ->label('Supplier'),
                TextColumn::make('status_lunas')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn(int $state) => $state === 1 ? 'Lunas' : 'Belum Lunas')
                    ->color(fn(int $state): string => match ($state) {
                        1 => 'success',
                        0 => 'danger',
                    }),
            ])
            ->defaultSort('status_lunas', 0)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('delete')
                    ->action(fn(Tagihan $record) => $record->delete())
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Tagihan')
                    ->modalDescription('Anda yakin menghapus tagihan ini?')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->modalSubmitActionLabel('Ya, Hapus Tagihan')
                    ->modalCancelActionLabel('Batal'),
                Tables\Actions\Action::make('update')
                    ->color('success')
                    ->form([
                        Checkbox::make('status_lunas')
                            ->label('Status Lunas'),
                    ])
                    ->action(function (array $data, Tagihan $record): void {
                        $record->status_lunas = ($data['status_lunas']);
                        $record->save();
                    })
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
                                        TextEntry::make('tagihan'),
                                        TextEntry::make('nominal_tagihan')
                                            ->prefix('Rp. ')
                                            ->formatStateUsing(fn($state) => number_format($state, 2, ',', '.')),
                                        TextEntry::make('keterangan'),
                                    ]),
                                    ComponentsGroup::make([
                                        TextEntry::make('jatuhTempo_tagihan')
                                            ->date('d M Y'),
                                        TextEntry::make('status_lunas')
                                            ->badge()
                                            ->color(fn($state): string => match ($state) {
                                                1 => 'success',
                                                0 => 'danger',
                                            })
                                            ->formatStateUsing(fn($state) => $state == 1 ? 'Lunas' : 'Belum Lunas'),
                                    ])
                                ]),
                            ImageEntry::make('img_nota')
                                ->hiddenLabel()
                                ->grow(false)
                        ])->from('lg')
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTagihans::route('/'),
            'create' => Pages\CreateTagihan::route('/create'),
            'view' => Pages\ViewTagihan::route('/{record}'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
        ];
    }
    public static function getModelLabel(): string
    {
        return 'Tagihan';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Tagihan';
    }
}
