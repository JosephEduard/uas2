<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GameResource\Pages;
use App\Filament\Resources\GameResource\RelationManagers;
use App\Models\Game;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class GameResource extends Resource
{
    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                TextInput::make('title')
                    ->label('Game Title')
                    ->required()
                    ->maxLength(255),

                FileUpload::make('thumbnail')
                    ->label('Game Thumbnail')
                    ->image()
                    ->directory('game-thumbnails')
                    ->nullable()
                    ->helperText('Upload thumbnail untuk game ini. Format: jpg, png.'),

                // Input untuk Platform
                Select::make('platform')
                    ->label('Platform')
                    ->options([
                        'PS4' => 'PS4',
                        'PS5' => 'PS5',
                        'Xbox' => 'Xbox',
                        'PC' => 'PC',
                        'Nintendo' => 'Nintendo',
                        'Atari' => 'Atari',
                        'PS2' => 'PS2',
                        'PS3' => 'PS3',
                    ])
                    ->required()
                    ->default('PS4'),

                // Input untuk Genre
                TextInput::make('genre')
                    ->label('Genre')
                    ->maxLength(100),

                // Input untuk Price
                TextInput::make('price')
                    ->label('Price')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->helperText('Harga dalam format Rupiah.'),

                // Input untuk Stock
                TextInput::make('stock')
                    ->label('Stock')
                    ->required()
                    ->numeric()
                    ->default(0),

                // Input untuk Description
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(1000)
                    ->placeholder('Deskripsi singkat tentang game.'),

                // Input untuk Upload Gambar dengan Relasi ke GameImage
                Repeater::make('images') // Menyesuaikan dengan relasi
                    ->label('Game Images')
                    ->relationship('images') // Relasi hasMany
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Image')
                            ->directory('game-images')
                            ->image()
                            ->required(),
                    ])
                    ->columns(1)
                    ->helperText('Tambahkan satu atau lebih gambar untuk game ini.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Menampilkan kolom Title
                TextColumn::make('title')
                    ->label('Game Title')
                    ->sortable()
                    ->searchable(),

                // Menampilkan kolom Platform
                TextColumn::make('platform')
                    ->label('Platform')
                    ->sortable(),

                TextColumn::make('is_available')
                    ->label('Availability')
                    ->sortable()
                    ->getStateUsing(fn($record) => $record->is_available === 'Available'
                        ? '✔️'
                        : '❌'),


                // Menampilkan kolom Genre
                TextColumn::make('genre')
                    ->label('Genre')
                    ->sortable()
                    ->limit(30),

                // Menampilkan kolom Harga
                TextColumn::make('formatted_price')
                    ->label('Price')
                    ->sortable(),

                // Menampilkan kolom Stok
                TextColumn::make('stock')
                    ->label('Stock')
                    ->sortable(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}