<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $password
 *
 * @property-read Achievement $achievement
 * @property-read AchievementLevel $badge  {@see User::getBadgeAttribute()}
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasAchievementAndBadge;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function achievement(): HasMany
    {
        return $this->hasMany(UserAchievement::class);
    }

    public function achievements($achievementId): HasMany
    {
        return $this->hasMany(UserAchievement::class)->where('achievement_id', $achievementId);
    }

    public function unlockedAchievementsById($achievementId): HasMany
    {
        return $this->hasMany(UserUnlockedAchievement::class)->where('achievement_id', $achievementId);
    }

    public function unlockedAchievementsWithLevels(): HasMany
    {
        return $this->hasMany(UserUnlockedAchievement::class)
            ->with('achievementLevel');
    }

    public function unlockedAchievements(): HasMany
    {
        return $this->hasMany(UserUnlockedAchievement::class);
    }
}
