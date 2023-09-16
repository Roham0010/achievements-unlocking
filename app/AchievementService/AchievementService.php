<?php

namespace App\AchievementService;

use App\Models\Achievement;
use App\Models\AchievementLevel;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AchievementService
{
    protected const ACHIEVEMENT_TYPE = '';

    protected User $user;
    protected Achievement $achievement;

    protected Collection $achievementLevels;
    protected AchievementLevel $newUnlockedAchievement;

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
        $this->achievement =
            Cache::remember('achievement' . static::ACHIEVEMENT_TYPE . $this->name, 10, function () {
                return Achievement::query()
                    ->where('type', static::ACHIEVEMENT_TYPE)
                    ->where('name', $this->name)
                    ->first();
            });
    }

    private function setAchievementLevels(): void
    {
        $this->achievementLevels = Cache::remember('achievements' . $this->name, 60, function () {
            return $this->achievement->levels()
                ->orderBy('count')
                ->get();
        });
    }

    public function handleAchievementsAndEvents(): void
    {
        $previousUserAchievementsCount = $this->getPreviousCountOfAchievements();
        $newAchievementsCount = $previousUserAchievementsCount + 1;

        $previousUnlockedAchievement = $this->getUnlockedAchievementsBasedOnCount(
            $previousUserAchievementsCount
        );

        $this->newUnlockedAchievement = $this->getUnlockedAchievementsBasedOnCount(
            $newAchievementsCount
        );

        if (($previousUnlockedAchievement->count ?? -1) !== $this->newUnlockedAchievement->count) {
            $this->checkForAchievementUnlocking();
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
                break;
            }

            $userUnlockedAchievement = $achievementLevel;
        }

        return $userUnlockedAchievement;
    }

    protected function checkForAchievementUnlocking(): void
    {
        //
    }

    protected function fireTheEvent(): void
    {
    }

    protected function addThisAchievement(): void
    {
    }
}
