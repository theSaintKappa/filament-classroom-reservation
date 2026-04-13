<?php

namespace App\Filament\Resources\Reservations\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        $isAdmin = Auth::user()?->isAdmin() ?? false;

        return $schema
            ->components([
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                $isAdmin
                    ? Select::make('teacher_id')
                        ->relationship('teacher', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                    : Hidden::make('teacher_id')
                        ->default((string) Auth::id()),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('notes')
                    ->rows(3)
                    ->columnSpanFull(),
                DateTimePicker::make('starts_at')
                    ->seconds(false)
                    ->required(),
                DateTimePicker::make('ends_at')
                    ->seconds(false)
                    ->required()
                    ->rule('after:starts_at'),
            ]);
    }
}
