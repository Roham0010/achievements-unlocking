## Achievements Unlocking Task Roadmap (estimation 5h total)

- Create Achievements and Badge model and table (30m)
    - Adding achievements
    - Create achievements levels
    - Seed the achievements and levels
- Create UserAchievements model and table (10m)
- Create UserUnlockedAchievements model and table (10m)
- Achievements service (3h)
    - Comment Written event
    - Lesson Watched event
    - Handle Badge on both comment and Badge events
    - Unlock Comment achievement
    - Unlock Lesson achievement
    - Update User Badge
    - Tests
- Events (20m)
    - Achievement unlocked
    - Badge unlocked
- Achievements endpoint (1h)
    - Tests

#### Reports

- It took about a full day of work to do all the possible features and tests
- I was about 3 hours off my estimation

### For Reviewers

Because of an issue that I had in the past couple of days couldn't finish the task on the given time on 3 days, I asked
for more time in an Email from Jennifer, and fortunately she agreed. That is why I am sending the project to you now.
And also sorry about the last comments are not as you expected in the doc, I have done the latest commits in different
times of the day and that is why it took much time than expected and I couldn't commit each part separately during that
times.

I hope it suits your requirements for the BE developer and accept my offer. thank you for your time and consideration.

**How to:**

- Run `php artisan db:seed`
- Run `./vendor/bin/phpunit`
- To create data for checking the endpoint in the browser, remove the DatabaseTransactions trait from the tests and run
  the test once to generate all the data for a user.
