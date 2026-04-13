<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    /** @use HasFactory<\Database\Factories\ReservationFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'room_id',
        'teacher_id',
        'title',
        'notes',
        'starts_at',
        'ends_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeOverlapping(Builder $query, string $startsAt, string $endsAt): Builder
    {
        return $query
            ->where('starts_at', '<', $endsAt)
            ->where('ends_at', '>', $startsAt);
    }

    public static function conflictMessage(): string
    {
        return 'This room is already reserved during the selected time range.';
    }

    public static function roomHasConflict(
        int $roomId,
        string $startsAt,
        string $endsAt,
        ?int $ignoreReservationId = null
    ): bool {
        $query = static::query()
            ->where('room_id', $roomId)
            ->overlapping($startsAt, $endsAt);

        if ($ignoreReservationId !== null) {
            $query->whereKeyNot($ignoreReservationId);
        }

        return $query->exists();
    }
}
