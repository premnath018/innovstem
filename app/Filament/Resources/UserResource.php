<?php

namespace App\Filament\Resources;

use App\Filament\Actions\GeneratePasswordAction;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Builder;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Settings';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                        ->label('Phone Number')
                        ->required()
                        ->prefix('+91')
                        ->numeric()
                        ->minLength(10)
                        ->maxLength(10)
                        ->rules(['digits:10']) // Ensures exactly 10 digits
                        ->inputMode('tel') // Sets keyboard to phone pad on mobile devices
                        ->placeholder('Enter 10-digit phone number')
                        ->helperText('Enter a 10-digit Indian phone number (without country code)')
                        ->validationMessages([
                            'digits' => 'Phone number must be exactly 10 digits.',
                            'numeric' => 'Phone number must contain only numbers.',
                        ]),
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        TextInput::make('password')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password.label'))
                            ->password()
                            ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                            ->revealable(filament()->arePasswordsRevealable())
                            ->rule(Password::default())
                            ->autocomplete('new-password')
                            ->dehydrated(fn ($state): bool => filled($state))
                            ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                            ->live(debounce: 500)
                            ->same('passwordConfirmation')
                            ->suffixActions([
                                GeneratePasswordAction::make(),
                            ]),
                        TextInput::make('passwordConfirmation')
                            ->label(__('filament-panels::pages/auth/edit-profile.form.password_confirmation.label'))
                            ->password()
                            ->revealable(filament()->arePasswordsRevealable())
                            ->required()
                            ->visible(fn (Get $get): bool => filled($get('password')))
                            ->dehydrated(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone Number')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? '+91 ' . chunk_split($state, 5, ' ') : '-')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Role')
                    ->formatStateUsing(function (User $record) : string {
                        if ($record->student()->exists())  {
                            return 'Student';
                        }
                
                        return $record->roles->pluck('name')->join(', ');
                    })
                    ->badge()
                    ->sortable()
                    ->searchable(),                
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['roles']);
        return $query;
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
