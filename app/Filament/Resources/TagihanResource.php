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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Split;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Forms\Components\DateTimePicker;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\TagihanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TagihanResource\RelationManagers;
use Filament\Forms\Components\Component;
use Filament\Infolists\Components\Section as ComponentsSection;
use Filament\Infolists\Components\Split as ComponentsSplit;
use Filament\Infolists\Components\Grid as ComponentsGrid;
use Filament\Infolists\Components\Group as ComponentsGroup;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
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
                            ->required()
                            ->numeric(),
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
