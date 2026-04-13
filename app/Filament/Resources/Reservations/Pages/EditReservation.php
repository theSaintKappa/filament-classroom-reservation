<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EditReservation extends EditRecord
{
    protected static string $resource = ReservationResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordUpdate($record, array $data): Reservation
    {
        if (! (Auth::user()?->isAdmin() ?? false)) {
            $data['teacher_id'] = Auth::id();
        }

        if (Reservation::roomHasConflict(
            (int) $data['room_id'],
            (string) $data['starts_at'],
            (string) $data['ends_at'],
            (int) $record->getKey(),
        )) {
            throw ValidationException::withMessages([
                'starts_at' => 'This room is already reserved during the selected time range.',
            ]);
        }

        $record->update($data);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
