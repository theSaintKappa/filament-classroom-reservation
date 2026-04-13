<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        $weekStart = CarbonImmutable::now()->startOfWeek();

        return [
            CreateAction::make(),
            Action::make('calendar')
                ->label('Occupancy Calendar')
                ->url(fn (): string => route('filament.admin.pages.room-occupancy-calendar')),
            Action::make('exportWeeklyPdf')
                ->label('Export This Week PDF')
                ->url(fn (): string => route('weekly-schedule.export', [
                    'week_start' => $weekStart->toDateString(),
                ]))
                ->openUrlInNewTab(),
        ];
    }
}
