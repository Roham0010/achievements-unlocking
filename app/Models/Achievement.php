<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property integer $id id of the achievement
 * @property string $type type of the achievement
 * @property string $name name of the achievement
 *
 * @property-read AchievementLevel $levels
 */
class Achievement extends Model
{
    use HasFactory;

    const ACHIEVEMENT_TYPE = 'achievement';
    const BADGE_TYPE = 'badge';

    public $fillable = [
        'type',
        'name',
    ];

    public function levels(): HasMany
    {
        return $this->hasMany(AchievementLevel::class);
    }
}
