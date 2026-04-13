<?php

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;

it('detects room reservation conflict for overlapping range', function () {
    $room = Room::factory()->create();

    Reservation::factory()
        ->for($room)
        ->create([
            'starts_at' => CarbonImmutable::parse('2026-04-20 09:00:00'),
            'ends_at' => CarbonImmutable::parse('2026-04-20 10:00:00'),
        ]);

    $hasConflict = Reservation::roomHasConflict(
        roomId: $room->id,
        startsAt: '2026-04-20 09:30:00',
        endsAt: '2026-04-20 10:30:00',
    );

    expect($hasConflict)->toBeTrue();
});

it('allows adjacent reservations with no overlap', function () {
    $room = Room::factory()->create();

    Reservation::factory()
        ->for($room)
        ->create([
            'starts_at' => CarbonImmutable::parse('2026-04-20 09:00:00'),
            'ends_at' => CarbonImmutable::parse('2026-04-20 10:00:00'),
        ]);

    $hasConflict = Reservation::roomHasConflict(
        roomId: $room->id,
        startsAt: '2026-04-20 10:00:00',
        endsAt: '2026-04-20 11:00:00',
    );

    expect($hasConflict)->toBeFalse();
});

it('scopes reservations to current teacher in reservation resource query', function () {
    $teacherA = User::factory()->teacher()->create();
    $teacherB = User::factory()->teacher()->create();
    $room = Room::factory()->create();

    $ownReservation = Reservation::factory()->for($room)->for($teacherA, 'teacher')->create();
    $otherReservation = Reservation::factory()->for($room)->for($teacherB, 'teacher')->create();

    $this->actingAs($teacherA);

    $visibleIds = ReservationResource::getEloquentQuery()->pluck('id')->all();

    expect($visibleIds)
        ->toContain($ownReservation->id)
        ->not->toContain($otherReservation->id);
});

it('exports weekly schedule PDF for authenticated user', function () {
    $teacher = User::factory()->teacher()->create();

    $response = $this->actingAs($teacher)
        ->get(route('weekly-schedule.export', ['week_start' => '2026-04-13']));

    $response->assertSuccessful();
    $response->assertHeader('content-disposition');
    expect($response->headers->get('content-disposition'))->toContain('weekly-schedule-2026-04-13.pdf');
});
