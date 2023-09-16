<?php

namespace Tests\Feature;

use App\Events\AchievementUnlockedEvent;
use App\Events\BadgeUnlockedEvent;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\UserUnlockedAchievement;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;

class AchievementsTest extends AchievementDataProvider
{
    use DatabaseTransactions;

    /**
     * A basic feature test example.
     */
    public function test_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_achievements_and_badges_exists(): void
    {
        $this->setUp();

        $this->assertDatabaseHas('achievements', [
            'type' => Achievement::ACHIEVEMENT_TYPE
        ]);
        $this->assertDatabaseHas('achievements', [
            'type' => Achievement::BADGE_TYPE
        ]);
    }

    public function test_user_with_no_achievement(): void
    {
        $this->setUp();

        $response = $this->getAchievementsEndpointResponse();

        $response->assertOk()->assertJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [
                'First Lesson Watched',
                'First Comment Written',
            ],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }

    public function test_user_with_one_lesson_watched(): void
    {
        $this->setUp();

        $this->setUserAchievement('lesson', 1);

        $response = $this->getAchievementsEndpointResponse();

        $response->assertOk()->assertJson([
            'unlocked_achievements' => ['First Lesson Watched'],
            'next_available_achievements' => [
                '5 Lessons Watched',
                'First Comment Written',
            ],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 3,
        ]);
    }

    public function test_user_with_one_comment_watched(): void
    {
        $this->setUp();

        $this->setUserAchievement('comment', 1);

        $response = $this->getAchievementsEndpointResponse();

        $response->assertOk()->assertJson([
            'unlocked_achievements' => ['First Comment Written'],
            'next_available_achievements' => [
                '3 Comments Written',
                'First Lesson Watched',
            ],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaining_to_unlock_next_badge' => 3,
        ]);
    }

    public function test_user_with_one_badge_given(): void
    {
        $this->setUp();

        $this->setUserAchievement('lesson', 10);
        $this->setUserAchievement('comment', 1);

        $response = $this->getAchievementsEndpointResponse();

        $response->assertOk()->assertJson([
            'unlocked_achievements' => [
                "First Lesson Watched",
                "5 Lessons Watched",
                "10 Lessons Watched",
                "First Comment Written"
            ],
            'next_available_achievements' => [
                "25 Lessons Watched",
                "3 Comments Written"
            ],
            'current_badge' => 'Intermediate',
            'next_badge' => 'Advanced',
            'remaining_to_unlock_next_badge' => 4,
        ]);
    }

    public function test_user_all_achievements_acquired(): void
    {
        $this->setUp();
        Event::fake([
            BadgeUnlockedEvent::class
        ]);

        $this->setUserAchievement('lesson', 50);
        $this->setUserAchievement('comment', 20);

        $response = $this->getAchievementsEndpointResponse();

        $response->assertOk()->assertJson([
            'unlocked_achievements' => [
                "First Lesson Watched",
                "5 Lessons Watched",
                "10 Lessons Watched",
                "25 Lessons Watched",
                "50 Lessons Watched",
                "First Comment Written",
                "3 Comments Written",
                "5 Comments Written",
                "10 Comments Written",
                "20 Comments Written"
            ],
            'next_available_achievements' => [
                "",
                ""
            ],
            'current_badge' => 'Master',
            'next_badge' => '',
            'remaining_to_unlock_next_badge' => 0,
        ]);

        // Note: !Important, This event will never fire because the Beginner badge
        //  is not achievable it's already assigned to each user on the endpoint
        // and the event will never get fired.
        // Event::assertDispatched(BadgeUnlockedEvent::class, function (BadgeUnlockedEvent $event) {
        //     return $event->badgeName === 'Beginner';
        // });

        Event::assertDispatched(BadgeUnlockedEvent::class, function (BadgeUnlockedEvent $event) {
            return $event->badgeName === 'Intermediate';
        });

        Event::assertDispatched(BadgeUnlockedEvent::class, function (BadgeUnlockedEvent $event) {
            return $event->badgeName === 'Advanced';
        });

        Event::assertDispatched(BadgeUnlockedEvent::class, function (BadgeUnlockedEvent $event) {
            return $event->badgeName === 'Master';
        });
    }

    public function test_user_unlocked_achievement_event(): void
    {
        $this->setUp();

        Event::fake([
            AchievementUnlockedEvent::class
        ]);

        $this->setUserAchievement('lesson', 5);

        Event::assertDispatched(AchievementUnlockedEvent::class);
    }

    public function test_user_badge_given_event(): void
    {
        $this->setUp();

        Event::fake([
            BadgeUnlockedEvent::class
        ]);

        $this->setUserAchievement('lesson', 25);

        Event::assertDispatched(BadgeUnlockedEvent::class);
    }

    public function test_user_achievements_on_database(): void
    {
        $this->setUp();

        $this->setUserAchievement('lesson', 5);
        $this->setUserAchievement('comment', 5);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $this->user->id
        ]);
        $this->assertDatabaseHas('user_unlocked_achievements', [
            'user_id' => $this->user->id
        ]);

        $this->assertDatabaseCount(UserAchievement::where('user_id', $this->user->id), 10);
        $this->assertDatabaseCount(UserUnlockedAchievement::where('user_id', $this->user->id), 5);
    }
}
