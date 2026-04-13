<?php

namespace App\Filament\Pages;

use App\Models\Building;
use App\Models\Reservation;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use UnitEnum;

class RoomOccupancyCalendar extends Page
{
    protected static ?string $navigationLabel = 'Room Occupancy Calendar';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Reservations';

    protected string $view = 'filament.pages.room-occupancy-calendar';

    public string $weekStart;

    public ?int $buildingId = null;

    public function mount(): void
    {
        $this->weekStart = now()->startOfWeek()->toDateString();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('previousWeek')
                ->label('Previous Week')
                ->action(function (): void {
                    $this->weekStart = $this->weekStartDate()->subWeek()->toDateString();
                }),
            Action::make('nextWeek')
                ->label('Next Week')
                ->action(function (): void {
                    $this->weekStart = $this->weekStartDate()->addWeek()->toDateString();
                }),
            Action::make('exportWeeklyPdf')
                ->label('Export Weekly PDF')
                ->url(fn (): string => route('weekly-schedule.export', [
                    'week_start' => $this->weekStartDate()->toDateString(),
                    'building_id' => $this->buildingId,
                ]))
                ->openUrlInNewTab(),
        ];
    }

    public function weekStartDate(): CarbonImmutable
    {
        return CarbonImmutable::parse($this->weekStart)->startOfWeek();
    }

    public function weekDays(): array
    {
        $start = $this->weekStartDate();

        return collect(range(0, 6))
            ->map(fn (int $offset): CarbonImmutable => $start->addDays($offset))
            ->all();
    }

    /**
     * @return Collection<int, Building>
     */
    public function buildings(): Collection
    {
        return Building::query()->orderBy('name')->get();
    }

    /**
     * @return Collection<int, Room>
     */
    public function rooms(): Collection
    {
        return Room::query()
            ->with('building')
            ->when($this->buildingId, fn ($query) => $query->where('building_id', $this->buildingId))
            ->orderBy('building_id')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function weeklyReservations(): Collection
    {
        $weekStart = $this->weekStartDate();
        $weekEnd = $weekStart->endOfWeek();

        return Reservation::query()
            ->with(['room.building', 'teacher'])
            ->where('starts_at', '<=', $weekEnd)
            ->where('ends_at', '>=', $weekStart)
            ->when($this->buildingId, function ($query): void {
                $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('building_id', $this->buildingId));
            })
            ->orderBy('starts_at')
            ->get();
    }

    /**
     * @return array<int, array<string, Collection<int, Reservation>>>
     */
    public function calendarMatrix(): array
    {
        $matrix = [];
        $rooms = $this->rooms();
        $reservations = $this->weeklyReservations();

        foreach ($rooms as $room) {
            foreach ($this->weekDays() as $day) {
                $dayStart = $day->startOfDay();
                $dayEnd = $day->endOfDay();
                $dayKey = $day->toDateString();

                $matrix[$room->id][$dayKey] = $reservations
                    ->where('room_id', $room->id)
                    ->filter(fn (Reservation $reservation): bool => $reservation->starts_at->lte($dayEnd) && $reservation->ends_at->gte($dayStart)
                    )
                    ->values();
            }
        }

        return $matrix;
    }
}
