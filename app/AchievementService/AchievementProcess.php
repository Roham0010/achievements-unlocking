<?php

namespace App\AchievementService;

use App\Events\AchievementUnlockedEvent;

class AchievementProcess extends AchievementService
{
    protected const ACHIEVEMENT_TYPE = 'achievement';

    protected function getPreviousCountOfAchievements(): int
    {
        return $this->user->achievements($this->achievement->id)->count();
    }

    protected function fireTheEvent(): void
    {
        event(new AchievementUnlockedEvent($this->newUnlockedAchievement->label, $this->user));
    }
}
