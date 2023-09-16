<?php

namespace App\Listeners;

use App\AchievementService\AchievementProcess;
use App\AchievementService\BadgeProcess;
use App\Events\CommentWritten;
use App\Events\LessonWatched;

class UserAchievementListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CommentWritten|LessonWatched $event): void
    {
        new AchievementProcess($event->user, $event::TYPE);

        new BadgeProcess($event->user, 'badge');
    }
}
