<?php

namespace App\AchievementService;

use App\Models\Achievement;
use App\Models\AchievementLevel;
use App\Models\Badges;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AchievementService
{
    protected const ACHIEVEMENT_TYPE = '';

    protected User $user;
    protected Achievement $achievement;

    protected array $achievementLevels;
    protected AchievementLevel $newUnlockedAchievement;

    protected string $type;
    protected string $name;

    public function __construct(User $user, string $name)
    {
        $this->user = $user;
        $this->name = $name;

        $this->setAchievement();

        $this->setAchievementLevels();

        $this->handleAchievementsAndEvents();
    }

    private function setAchievement(): void
    {
        $this->achievement = Cache::remember('achievement', 10, function () {
            return Achievement::query()
                ->where('type', static::ACHIEVEMENT_TYPE)
                ->where('name', $this->name)
                ->get();
        });
    }

    private function setAchievementLevels(): void
    {
        $this->achievementLevels = Cache::remember('achievements', 60, function () {
            return $this->achievement->levels()
                ->orderBy('count')
                ->get();
        });
    }

    public function handleAchievementsAndEvents(): void
    {
        $previousUserCommentAchievementsCount = $this->getPreviousCountOfAchievements();
        $newCommentAchievementsCount = $previousUserCommentAchievementsCount + 1;

        $previousUnlockedAchievement = $this->getUnlockedAchievementsBasedOnCount(
            $previousUserCommentAchievementsCount
        );

        $this->newUnlockedAchievement = $this->getUnlockedAchievementsBasedOnCount(
            $newCommentAchievementsCount
        );

        if ($previousUnlockedAchievement->count !== $this->newUnlockedAchievement->count) {
            $this->user->unlockedAchievementsById($this->achievement->id)->create([
                'achievement_level_id' => $this->newUnlockedAchievement->id
            ]);

            $this->fireTheEvent();
        }
    }

    protected function getPreviousCountOfAchievements(): int
    {
        return 0;
    }

    private function getUnlockedAchievementsBasedOnCount($count)
    {
        $userUnlockedAchievement = null;

        foreach ($this->achievementLevels as $achievementLevel) {
            if ($achievementLevel->count > $count) {
                continue;
            }

            $userUnlockedAchievement = $achievementLevel;
        }

        return $userUnlockedAchievement;
    }

    protected function fireTheEvent(): void
    {
        return;
    }
}
