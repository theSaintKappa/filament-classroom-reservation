<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Reservation;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
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
                    ->required()
                    ->live(),
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
                    ->live(onBlur: true)
                    ->seconds(false)
                    ->required()
                    ->rules([self::conflictRule()]),
                DateTimePicker::make('ends_at')
                    ->live(onBlur: true)
                    ->seconds(false)
                    ->required()
                    ->rule('after:starts_at')
                    ->rules([self::conflictRule()]),
            ]);
    }

    private static function conflictRule(): Closure
    {
        return function (Get $get, ?Reservation $record): Closure {
            return function (string $attribute, mixed $value, Closure $fail) use ($get, $record): void {
                $roomId = $get('room_id');
                $startsAt = $get('starts_at');
                $endsAt = $get('ends_at');

                if (blank($roomId) || blank($startsAt) || blank($endsAt)) {
                    return;
                }

                if (Reservation::roomHasConflict(
                    (int) $roomId,
                    (string) $startsAt,
                    (string) $endsAt,
                    $record?->getKey(),
                )) {
                    $fail(Reservation::conflictMessage());
                }
            };
        };
    }
}
