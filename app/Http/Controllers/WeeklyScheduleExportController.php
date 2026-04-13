<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Reservation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WeeklyScheduleExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $weekStart = CarbonImmutable::parse($request->query('week_start', now()->startOfWeek()->toDateString()))
            ->startOfWeek();
        $weekEnd = $weekStart->endOfWeek();
        $buildingId = $request->integer('building_id') ?: null;

        $reservations = Reservation::query()
            ->with(['room.building', 'teacher'])
            ->where('starts_at', '<=', $weekEnd)
            ->where('ends_at', '>=', $weekStart)
            ->when($buildingId, function ($query) use ($buildingId): void {
                $query->whereHas('room', fn ($roomQuery) => $roomQuery->where('building_id', $buildingId));
            })
            ->when(
                ! ($request->user()?->isAdmin() ?? false),
                fn ($query) => $query->where('teacher_id', $request->user()->id),
            )
            ->orderBy('starts_at')
            ->get();

        $buildingName = $buildingId
            ? (Building::query()->whereKey($buildingId)->value('name') ?? 'All buildings')
            : 'All buildings';

        $pdf = Pdf::loadView('pdf.weekly-schedule', [
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'buildingName' => $buildingName,
            'reservations' => $reservations,
        ]);

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            sprintf('weekly-schedule-%s.pdf', $weekStart->format('Y-m-d')),
        );
    }
}
