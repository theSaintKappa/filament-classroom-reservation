<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Reservation
    {
        if (! (Auth::user()?->isAdmin() ?? false)) {
            $data['teacher_id'] = Auth::id();
        }

        if (Reservation::roomHasConflict(
            (int) $data['room_id'],
            (string) $data['starts_at'],
            (string) $data['ends_at'],
        )) {
            throw ValidationException::withMessages([
                'starts_at' => Reservation::conflictMessage(),
            ]);
        }

        return Reservation::create($data);
    }
}
