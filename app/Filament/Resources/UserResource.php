<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use DateTime;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\TextColumn;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'User';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Tambah User')
                    ->description('')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->required(),
                        DateTimePicker::make('email_verified_at')
                            ->default(now())
                            ->label('Email Verified At'),
                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->revealable()
                            ->confirmed()
                            ->required(fn() => request()->routeIs('filament.user.create')),
                        TextInput::make('password_confirmation')
                            ->label('Password Confirmation')
                            ->password()
                            ->revealable()
                            ->required(fn() => request()->routeIs('filament.user.create')),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_verified_at')
                    ->label('Email Verified At')
                    ->searchable()
                    ->sortable()
                    ->dateTime('d/m/Y H:i:s'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
