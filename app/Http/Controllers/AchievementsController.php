<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AchievementLevel;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AchievementsController extends Controller
{
    public function index(User $user): JsonResponse
    {
        [$currentAchievements, $nextAchievements] = $this->getAchievements($user);
        [$currentBadge, $nextBadge, $remainingToUnlockNext] = $this->getBadges($user);

        return response()->json([
            'unlocked_achievements' => $currentAchievements,
            'next_available_achievements' => $nextAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaing_to_unlock_next_badge' => $remainingToUnlockNext,
        ]);
    }

    private function getAchievements(User $user): array
    {
        // Get achievements
        $unlocked = $user->unlockedAchievementsByType(Achievement::ACHIEVEMENT_TYPE)->get();
        $levels = $unlocked->pluck('achievementLevel');
        $unlockedAchievements = $levels
            ->pluck('label');

        if (!$unlocked->isEmpty()) {
            // Get next achievements
            $nextAchievements = $levels->groupBy('achievement_id')->map(function ($level) {
                /** @var AchievementLevel $maxLevel */
                $maxLevel = $level->sortByDesc('count')->last();
                $nextAchievement = $maxLevel->nextAchievementOf();
                return $nextAchievement->label ?? $maxLevel->label;
            });
        } else {
            $nextAchievements = AchievementLevel::firstOfEachAchievement();
        }

        return [$unlockedAchievements, $nextAchievements];
    }

    private function getBadges(User $user): array
    {
        // Get achievements
        $unlocked = $user->unlockedAchievementsByType(Achievement::BADGE_TYPE)->get();
        if (!$unlocked->isEmpty()) {
            $latestBadge = $unlocked->pluck('achievementLevel')->sortBy('count')->last();
        } else {
            $latestBadge = AchievementLevel::defaultBadge();
        }

        $latestBadgeLabel = $latestBadge->label;

        // Get next achievements
        $nextBadge = $latestBadge->nextAchievementOf();
        $nextBadgeLabel = $nextBadge->label ?? $latestBadgeLabel;

        $badgeAchievement = Achievement::query()
            ->where('type', Achievement::BADGE_TYPE)
            ->first();

        /** @var AchievementLevel $lastPossibleBadge */
        $lastPossibleBadge = $badgeAchievement->levels()
            ->orderBy('count', 'desc')
            ->first();

        return [$latestBadgeLabel, $nextBadgeLabel, $nextBadge->count - $latestBadge->count];
    }
}
