<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AchievementSeeder::class);

        $lessons = Lesson::factory()->count(20)->create();

        $comments = Comment::factory()->count(5)->create();
    }
}
