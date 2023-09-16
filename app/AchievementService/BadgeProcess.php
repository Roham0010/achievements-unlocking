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
        // When we are here it means that a new achievement already is acquired
        // because of that to get the previous count of achievements we should
        // decrease the count by one.
        return $this->user->achievements()->count() - 1;
    }

    protected function storeUnlockedAchievementAndFireTheEvents(): void
    {
        $this->fireTheEvent();
    }

    protected function fireTheEvent(): void
    {
        event(new BadgeUnlockedEvent($this->newUnlockedAchievement->label, $this->user));
    }
}
