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
                    'count' => 1,
                    'label' => 'First Lesson Watched',
                ],
                [
                    'count' => 5,
                    'label' => '5 Lessons Watched',
                ],
                [
                    'count' => 10,
                    'label' => '10 Lessons Watched',
                ],
                [
                    'count' => 25,
                    'label' => '25 Lessons Watched',
                ],
                [
                    'count' => 50,
                    'label' => '50 Lessons Watched',
                ],
            ],
            'comment' => [
                [
                    'count' => 1,
                    'label' => 'First Comment Written',
                ],
                [
                    'count' => 3,
                    'label' => '3 Comments Written',
                ],
                [
                    'count' => 5,
                    'label' => '5 Comments Written',
                ],
                [
                    'count' => 10,
                    'label' => '10 Comments Written',
                ],
                [
                    'count' => 20,
                    'label' => '20 Comments Written',
                ],
            ],
        ],
        'badge' => [
            'badge' => [
                [
                    'count' => 0,
                    'label' => 'Beginner',
                ],
                [
                    'count' => 4,
                    'label' => 'Intermediate',
                ],
                [
                    'count' => 8,
                    'label' => 'Advanced',
                ],
                [
                    'count' => 10,
                    'label' => 'Master',
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
