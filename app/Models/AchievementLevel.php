<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property string $achievement_id The achievement that it's assigned to
 * @property integer $count Count of the achievements to unlock new one
 * @property string $label Name of the achievement Ex: First comment written
 */
class AchievementLevel extends Model
{
    use HasFactory;

    public $fillable = [
        'achievement_id',
        'count',
        'label',
    ];

    public static function defaultBadge(): AchievementLevel
    {
        return AchievementLevel::query()
            ->whereHas('achievement', function ($q) {
                $q->where('type', Achievement::BADGE_TYPE);
            })
            ->orderBy('count')
            ->limit(1)
            ->get()[0];
    }

    public static function firstOfEachAchievement(): array
    {
        $achievementLevels = AchievementLevel::query()
            ->whereHas('achievement', function ($q) {
                $q->where('type', Achievement::ACHIEVEMENT_TYPE);
            })
            ->orderBy('count')
            ->get()
            ->groupBy('achievement_id');

        $results = [];
        /** @var AchievementLevel $level */
        foreach ($achievementLevels as $level) {
            $results[] = $level->first()->label;
        }

        return $results;
    }

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }

    public function nextAchievementOf(): ?AchievementLevel
    {
        return AchievementLevel::query()
            ->where('achievement_id', $this->achievement_id)
            ->where('count', '>', $this->count)
            ->orderBy('count')
            ->limit(1)
            ->get()[0];
//        if (!$al) {
//            $al = AchievementLevel::query()
//                ->where('achievement_id', $this->achievement_id)
//                ->orderBy('count')
//                ->first();
//        }
    }
}
