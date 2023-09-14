<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    private array $achievements = [
        'achievement' => [
            'lesson' => [
                [
                    'count' => 0,
                    'label' => 'First Lesson Watched',
                ],
                [
                    'count' => 5,
                    'label' => '5 Lesson Watched',
                ],
                [
                    'count' => 10,
                    'label' => '10 Lesson Watched',
                ],
                [
                    'count' => 25,
                    'label' => '25 Lesson Watched',
                ],
                [
                    'count' => 50,
                    'label' => '50 Lesson Watched',
                ],
            ],
            'comment' => [
                [
                    'count' => 0,
                    'label' => 'First Comment Written',
                ],
                [
                    'count' => 3,
                    'label' => '3 Comment Written',
                ],
                [
                    'count' => 5,
                    'label' => '5 Comment Written',
                ],
                [
                    'count' => 10,
                    'label' => '10 Comment Written',
                ],
                [
                    'count' => 20,
                    'label' => '20 Comment Written',
                ],
            ],
        ],
        'badge' => [
            'badge' => [
                [
                    'count' => 0,
                    'label' => 'Beginner: 0 Achievements',
                ],
                [
                    'count' => 4,
                    'label' => 'Intermediate: 4 Achievements',
                ],
                [
                    'count' => 8,
                    'label' => 'Advanced: 8 Achievements',
                ],
                [
                    'count' => 10,
                    'label' => 'Master: 10 Achievements',
                ],
            ]
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // We should seed the achievements only once
        if (Achievement::query()->exists()) {
            return;
        }

        foreach ($this->achievements as $type => $achievementsAndBadges) {
            foreach ($achievementsAndBadges as $key => $levels) {
                $achievement = Achievement::query()->create(['name' => $key, 'type' => $type]);
                foreach ($levels as $level) {
                    $achievement->levels()->create([
                        'count' => $level['count'],
                        'label' => $level['label']
                    ]);
                }
            }
        }
    }
}
