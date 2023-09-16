<?php

namespace App\AchievementService;

use App\Events\AchievementUnlockedEvent;

class AchievementProcess extends AchievementService
{
    protected const ACHIEVEMENT_TYPE = 'achievement';

    protected function getPreviousCountOfAchievements(): int
    {
        return $this->user->achievements($this->achievement->id)->count() - 1;
    }

    protected function checkForAchievementUnlocking(): void
    {
        $this->user->unlockedAchievementsById($this->achievement->id)->create([
            'achievement_id' => $this->achievement->id,
            'achievement_level_id' => $this->newUnlockedAchievement->id,
        ]);

        $this->fireTheEvent();
    }

    protected function fireTheEvent(): void
    {
        event(new AchievementUnlockedEvent($this->newUnlockedAchievement->label, $this->user));
    }
}
