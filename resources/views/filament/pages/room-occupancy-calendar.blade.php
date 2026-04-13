<x-filament-panels::page>
    <div class="space-y-6">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
            <div class="grid gap-4 md:grid-cols-3">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Week start</label>
                    <input
                        type="date"
                        wire:model.live="weekStart"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Building</label>
                    <select
                        wire:model.live="buildingId"
                        class="block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-amber-500 focus:ring-amber-500"
                    >
                        <option value="">All buildings</option>
                        @foreach ($this->buildings() as $building)
                            <option value="{{ $building->id }}">{{ $building->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end text-sm text-gray-600">
                    {{ $this->weekStartDate()->format('M d') }} - {{ $this->weekStartDate()->endOfWeek()->format('M d, Y') }}
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700">Room</th>
                        @foreach ($this->weekDays() as $day)
                            <th class="px-4 py-3 text-left font-semibold text-gray-700">
                                {{ $day->format('D') }}<br>
                                <span class="text-xs font-normal text-gray-500">{{ $day->format('M d') }}</span>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php($matrix = $this->calendarMatrix())
                    @forelse ($this->rooms() as $room)
                        <tr>
                            <td class="whitespace-nowrap px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">{{ $room->name }}</div>
                                <div class="text-xs text-gray-500">{{ $room->building->name }}</div>
                            </td>
                            @foreach ($this->weekDays() as $day)
                                @php($entries = $matrix[$room->id][$day->toDateString()] ?? collect())
                                <td class="px-4 py-3 align-top">
                                    <div class="space-y-2">
                                        @forelse ($entries as $entry)
                                            <div class="rounded-md border border-amber-200 bg-amber-50 p-2">
                                                <div class="text-xs font-semibold text-amber-900">{{ $entry->title }}</div>
                                                <div class="text-xs text-amber-700">
                                                    {{ $entry->starts_at->format('H:i') }} - {{ $entry->ends_at->format('H:i') }}
                                                </div>
                                                <div class="text-xs text-amber-700">{{ $entry->teacher->name }}</div>
                                            </div>
                                        @empty
                                            <span class="text-xs text-gray-400">Free</span>
                                        @endforelse
                                    </div>
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-sm text-gray-500">
                                No rooms available for selected building.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
