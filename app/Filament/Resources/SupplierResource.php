<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Supplier;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use App\Filament\Resources\SupplierResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Filament\Resources\SupplierResource\Pages\EditSupplier;
use App\Filament\Resources\SupplierResource\Pages\ViewSupplier;
use App\Filament\Resources\SupplierResource\Pages\ListSuppliers;
use App\Filament\Resources\SupplierResource\Pages\CreateSupplier;
use Filament\Infolists\Components\Component;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section as ComponentsSection;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationGroup = 'Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Section::make('Tambah Supplier')
                    ->description('')
                    ->schema([
                        TextInput::make('nama_supplier')
                            ->label('Nama Supplier')
                            ->unique(table: Supplier::class, column: 'nama_supplier', ignoreRecord: true)
                            ->required(),
                        TextInput::make('alamat')
                            ->label('Alamat')
                            ->required(),
                        TextInput::make('kontak')
                            ->label('Kontak')
                            ->required(),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('nama_supplier')
                    ->label('Nama Supplier'),
                TextColumn::make('alamat')
                    ->label('Alamat'),
                TextColumn::make('kontak')
                    ->label('Kontak'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('delete')
                    ->action(fn(Supplier $record) => $record->delete())
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Supplier')
                    ->modalDescription('Anda yakin menghapus supplier ini?')
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->modalSubmitActionLabel('Ya, Hapus Supplier')
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ComponentsSection::make()
                    ->schema([
                        TextEntry::make('nama_supplier'),
                        TextEntry::make('alamat'),
                        TextEntry::make('kontak')
                    ])
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'view' => Pages\ViewSupplier::route('/{record}'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
    public static function getNavigationLabel(): string
    {
        return 'Supplier';
    }

    public static function getModelLabel(): string
    {
        return 'Supplier';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Daftar Supplier';
    }
}
