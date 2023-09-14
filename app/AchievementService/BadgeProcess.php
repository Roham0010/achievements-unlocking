<?php

namespace App\AchievementService;


use App\Events\BadgeUnlockedEvent;

class BadgeProcess extends AchievementService
{
    protected const ACHIEVEMENT_TYPE = 'badge';

    /**
     * When we process the badges we already inserted an achievement to the user, so to get the
     * previous count of the unlocked achievements we need to decrease one achievement of it.
     *
     * @return int
     */
    protected function getPreviousCountOfAchievements(): int
    {
        return $this->user->unlockedAchievements()->count() - 1;
    }

    protected function fireTheEvent(): void
    {
        event(new BadgeUnlockedEvent($this->newUnlockedAchievement->label, $this->user));
    }
}
