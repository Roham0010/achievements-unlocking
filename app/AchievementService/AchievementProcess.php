<?php

namespace App\AchievementService;

use App\Events\AchievementUnlockedEvent;

class AchievementProcess extends AchievementService
{
    protected const ACHIEVEMENT_TYPE = 'achievement';

    protected function getPreviousCountOfAchievements(): int
    {
        // When we are here it means that a new achievement already is acquired
        // because of that to get the previous count of achievements we should
        // decrease the count by one.
        return $this->user->achievementsById($this->achievement->id)->count() - 1;
    }

    protected function storeUnlockedAchievementAndFireTheEvents(): void
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
