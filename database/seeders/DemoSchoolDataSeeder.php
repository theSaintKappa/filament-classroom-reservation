<?php

namespace Database\Seeders;

use App\Models\Building;
use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSchoolDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = collect([
            ['name' => 'Alice Kowalska', 'email' => 'alice.teacher@example.com'],
            ['name' => 'Bob Nowak', 'email' => 'bob.teacher@example.com'],
            ['name' => 'Celia Wisniewska', 'email' => 'celia.teacher@example.com'],
        ])->map(function (array $teacherData): User {
            return User::query()->updateOrCreate(
                ['email' => $teacherData['email']],
                [
                    'name' => $teacherData['name'],
                    'password' => Hash::make('password'),
                    'role' => 'teacher',
                    'email_verified_at' => now(),
                ],
            );
        })->values();

        $buildingRooms = [
            [
                'name' => 'Main Building',
                'code' => 'B-100',
                'rooms' => [
                    ['name' => 'Room 101', 'code' => 'R-101', 'capacity' => 30],
                    ['name' => 'Room 102', 'code' => 'R-102', 'capacity' => 28],
                    ['name' => 'Room 103', 'code' => 'R-103', 'capacity' => 24],
                    ['name' => 'Lab 104', 'code' => 'R-104', 'capacity' => 18],
                ],
            ],
            [
                'name' => 'Science Building',
                'code' => 'B-200',
                'rooms' => [
                    ['name' => 'Physics Lab', 'code' => 'R-201', 'capacity' => 20],
                    ['name' => 'Chemistry Lab', 'code' => 'R-202', 'capacity' => 20],
                    ['name' => 'Room 203', 'code' => 'R-203', 'capacity' => 26],
                    ['name' => 'Room 204', 'code' => 'R-204', 'capacity' => 22],
                ],
            ],
        ];

        $weekStart = CarbonImmutable::now()->startOfWeek()->addWeek();

        foreach ($buildingRooms as $buildingData) {
            $building = Building::query()->updateOrCreate(
                ['code' => $buildingData['code']],
                [
                    'name' => $buildingData['name'],
                    'code' => $buildingData['code'],
                ],
            );

            foreach ($buildingData['rooms'] as $index => $roomData) {
                $room = Room::query()->updateOrCreate(
                    ['code' => $roomData['code']],
                    [
                        'building_id' => $building->id,
                        'name' => $roomData['name'],
                        'code' => $roomData['code'],
                        'capacity' => $roomData['capacity'],
                    ],
                );

                $teacher = $teachers[$index % $teachers->count()];

                $morningStart = $weekStart->addDays($index % 5)->setHour(8)->setMinute(0);
                $middayStart = $weekStart->addDays(($index + 1) % 5)->setHour(11)->setMinute(0);

                Reservation::query()->updateOrCreate(
                    [
                        'room_id' => $room->id,
                        'title' => $room->name.' Morning Session',
                        'starts_at' => $morningStart,
                        'ends_at' => $morningStart->addHour(),
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'notes' => 'Seeded sample reservation.',
                    ],
                );

                Reservation::query()->updateOrCreate(
                    [
                        'room_id' => $room->id,
                        'title' => $room->name.' Midday Session',
                        'starts_at' => $middayStart,
                        'ends_at' => $middayStart->addHours(2),
                    ],
                    [
                        'teacher_id' => $teacher->id,
                        'notes' => 'Seeded sample reservation.',
                    ],
                );
            }
        }
    }
}
