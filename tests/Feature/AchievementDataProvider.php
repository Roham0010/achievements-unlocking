<?php

namespace Tests\Feature;

use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Comment;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class AchievementDataProvider extends TestCase
{
    public function setUserAchievement(string $type, int $count): void
    {
        $achievement = $this->getAchievement(Achievement::ACHIEVEMENT_TYPE, $type);
        $levels = $achievement->levels->sortBy('count');

        for ($i = 0; $i < $count; $i++) {
            $this->user->achievements()->create([
                'achievement_id' => $achievement->id,
            ]);

            $this->fireEvents($type);
        }
    }

    private function getAchievement(string $type, string $name): Model|Achievement
    {
        return Achievement::query()->where(compact('type', 'name'))->first();
    }

    private function fireEvents(string $type): void
    {
        if ($type === 'comment') {
            CommentWritten::dispatch(Comment::factory()->create(['user_id' => $this->user->id]));
        } elseif ($type === 'lesson') {
            LessonWatched::dispatch(Lesson::factory()->create(), $this->user);
        }
    }

    protected function getAchievementsEndpointResponse(bool $withUser = true): TestResponse
    {
        return $this->get("users/{$this->user->id}/achievements");
    }
}
