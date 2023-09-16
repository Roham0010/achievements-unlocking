<?php

namespace App\Models;

use Illuminate\Support\Collection;

/**
 * Here is a helper class for retrieving user achievements and badges.
 */
trait HasAchievementAndBadge
{
    public function getUnlockedAchievements(): Collection
    {
        // Get achievements
        $unlockedAchievements = $this->unlockedAchievementsWithLevels()->get();
        return $unlockedAchievements->pluck('achievementLevel');
    }

    public function nextAchievementsToUnlock(): Collection
    {
        // Get achievements
        $unlockedAchievements = $this->unlockedAchievementsWithLevels()->get();
        $levels = $unlockedAchievements->pluck('achievementLevel');
        $unlockedAchievements = $levels
            ->pluck('label');

        if (!$unlockedAchievements->isEmpty()) {
            // Get next achievements
            $nextAchievements = $levels->groupBy('achievement_id')->map(function ($level) {
                /** @var AchievementLevel $maxLevel */
                $maxLevel = $level->sortByDesc('count')->first();
                $nextAchievement = $maxLevel->nextAchievementOf();
                return $nextAchievement->label ?? '';
            });
            // In case one of the achievements is not presented
            if (count($nextAchievements) === 1) {
                $allLevels = AchievementLevel::query()
                    ->whereHas('achievement', function ($q) {
                        return $q->where('type', Achievement::ACHIEVEMENT_TYPE);
                    })
                    ->get()
                    ->groupBy('achievement_id');
                foreach ($allLevels as $eachAchievementLevels) {
                    if (in_array($nextAchievements->first(), $eachAchievementLevels->pluck('label')->all())) {
                        continue;
                    }

                    $nextAchievements[] = $eachAchievementLevels->sortBy('count')->first()->label;
                }
            }
        } else {
            $nextAchievements = collect(AchievementLevel::firstOfEachAchievement());
        }

        return $nextAchievements;
    }

    /**
     * This can be a database column if needed in future.
     */
    public function getBadgeAttribute(): AchievementLevel
    {
        $achievementUnlockedCount = $this->unlockedAchievements()->count();
        /** @var Achievement $badgeAchievement */
        $badgeAchievement = Achievement::query()
            ->where('type', Achievement::BADGE_TYPE)
            ->first();
        /** @var Collection $badgeLevels */
        $badgeLevels = $badgeAchievement->levels;
        $badgeLevels = $badgeLevels->sortBy('count');

        // Finding the current badge
        foreach ($badgeLevels as $level) {
            if ($level->count > $achievementUnlockedCount) {
                break;
            }

            $currentLevel = $level;
        }
        return $currentLevel ?? $badgeLevels->first();
    }

    public function nextBadge(AchievementLevel $currentBadge): ?AchievementLevel
    {
        /** @var Achievement $badgeAchievement */
        $badgeAchievement = Achievement::query()
            ->where('type', Achievement::BADGE_TYPE)
            ->first();
        /** @var Collection $badgeLevels */
        $badgeLevels = $badgeAchievement->levels;

        $nextBadgeLevel = null;
        foreach ($badgeLevels as $level) {
            if ($level->count > $currentBadge->count) {
                $nextBadgeLevel = $level;
                break;
            }
        }

        return $nextBadgeLevel ?? null;
    }
}
