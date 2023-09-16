<?php

namespace App\Http\Controllers;

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
            'remaining_to_unlock_next_badge' => $remainingToUnlockNext,
        ]);
    }

    private function getAchievements(User $user): array
    {
        // Get achievements
        $unlockedAchievements = $user->getUnlockedAchievements()->pluck('label');

        $nextAchievements = $user->nextAchievementsToUnlock();

        return [$unlockedAchievements, $nextAchievements->values()];
    }

    private function getBadges(User $user): array
    {
        $currentBadge = $user->badge;

        $nextBadge = $user->nextBadge($currentBadge);

        $countAchievements = $user->unlockedAchievements()->count();
        $countAchievementsForRemainingBadge = min($countAchievements, 10);
        return [
            $currentBadge->label,
            $nextBadge->label ?? '',
            ($nextBadge->count ?? $currentBadge->count) - $countAchievementsForRemainingBadge
        ];
    }
}
