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

    public function achievement(): BelongsTo
    {
        return $this->belongsTo(Achievement::class);
    }
}
