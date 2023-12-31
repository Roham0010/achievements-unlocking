<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property integer $id
 * @property integer $user_id
 * @property integer $achievement_id
 * @property integer $achievement_level_id
 */
class UserUnlockedAchievement extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'achievement_id',
        'achievement_level_id',
    ];

    public function achievementLevel(): BelongsTo
    {
        return $this->belongsTo(AchievementLevel::class);
    }
}
