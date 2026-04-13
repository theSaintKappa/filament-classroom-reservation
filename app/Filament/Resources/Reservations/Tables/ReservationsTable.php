<?php

namespace App\Filament\Resources\Reservations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.building.name')
                    ->label('Building')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('room.name')
                    ->label('Room')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teacher.name')
                    ->label('Teacher')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('starts_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ends_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('room')
                    ->relationship('room', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('teacher')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => Auth::user()?->isAdmin() ?? false),
                Filter::make('week')
                    ->schema([
                        DatePicker::make('week_start')
                            ->label('Week starts on'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (blank($data['week_start'] ?? null)) {
                            return $query;
                        }

                        $weekStart = \Carbon\CarbonImmutable::parse($data['week_start'])->startOfWeek();
                        $weekEnd = $weekStart->endOfWeek();

                        return $query
                            ->where('starts_at', '<=', $weekEnd)
                            ->where('ends_at', '>=', $weekStart);
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
