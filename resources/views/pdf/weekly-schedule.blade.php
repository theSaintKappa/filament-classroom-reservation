<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Schedule</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        .meta { margin-bottom: 16px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-weight: bold; }
        .small { color: #6b7280; font-size: 11px; }
    </style>
</head>
<body>
    <h1>Weekly Room Schedule</h1>
    <div class="meta">
        Building: {{ $buildingName }}<br>
        Week: {{ $weekStart->format('Y-m-d') }} to {{ $weekEnd->format('Y-m-d') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Building</th>
                <th>Room</th>
                <th>Teacher</th>
                <th>Start</th>
                <th>End</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $reservation)
                <tr>
                    <td>{{ $reservation->title }}</td>
                    <td>{{ $reservation->room->building->name }}</td>
                    <td>{{ $reservation->room->name }}</td>
                    <td>{{ $reservation->teacher->name }}</td>
                    <td>{{ $reservation->starts_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $reservation->ends_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $reservation->notes ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="small">No reservations found for selected week.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
