<?php

namespace Hubertusanton\Leaderboard\Task;

use SilverStripe\Dev\BuildTask;
use Hubertusanton\Leaderboard\Model\Badge;

class PopulateLeaderboardBadgesTask extends BuildTask
{
    protected $title = 'Populate Default Leaderboard Badges';

    protected $description = 'Creates the default set of badges for the leaderboard system';

    private static $segment = 'populate-leaderboard-badges';

    public function run($request)
    {
        $badges = [
            [
                'Title' => 'First Steps 👶',
                'Description' => 'Created your very first page!',
                'BadgeType' => 'page_creation',
                'RequiredValue' => 1,
                'Icon' => 'font-icon-page',
                'Color' => '#28a745'
            ],
            [
                'Title' => 'Page Creator 📄',
                'Description' => 'Created 10 pages - you\'re getting the hang of this!',
                'BadgeType' => 'page_creation',
                'RequiredValue' => 10,
                'Icon' => 'font-icon-page-multiple',
                'Color' => '#17a2b8'
            ],
            [
                'Title' => 'Content Machine 🏭',
                'Description' => 'Amazing! You\'ve created 50 pages!',
                'BadgeType' => 'page_creation',
                'RequiredValue' => 50,
                'Icon' => 'font-icon-block-banner',
                'Color' => '#ffc107'
            ],
            [
                'Title' => 'Editor in Chief 📝',
                'Description' => 'Made 25 edits - perfectionism at its finest!',
                'BadgeType' => 'page_edit',
                'RequiredValue' => 25,
                'Icon' => 'font-icon-edit-write',
                'Color' => '#6f42c1'
            ],
            [
                'Title' => 'Rapid Fire 🔥',
                'Description' => 'Made 5 or more actions within an hour!',
                'BadgeType' => 'rapid_fire',
                'RequiredValue' => 5,
                'Icon' => 'font-icon-rocket',
                'Color' => '#dc3545'
            ],
            [
                'Title' => 'The Destroyer 💀',
                'Description' => 'Created something... then immediately deleted it. Oops!',
                'BadgeType' => 'destroyer',
                'RequiredValue' => 1,
                'Icon' => 'font-icon-cancel-circled',
                'Color' => '#6c757d'
            ],
            [
                'Title' => 'Streak Master 🔥',
                'Description' => 'Worked for 7 consecutive days - dedication!',
                'BadgeType' => 'streak',
                'RequiredValue' => 7,
                'Icon' => 'font-icon-menu',
                'Color' => '#fd7e14'
            ],
            [
                'Title' => 'Night Owl 🦉',
                'Description' => 'Working the late shift (10pm - 6am)!',
                'BadgeType' => 'night_owl',
                'RequiredValue' => 1,
                'Icon' => 'font-icon-moon',
                'Color' => '#4C4C4C'
            ],
            [
                'Title' => 'Early Bird 🐦',
                'Description' => 'Up with the sun (5am - 8am) and ready to work!',
                'BadgeType' => 'early_bird',
                'RequiredValue' => 1,
                'Icon' => 'font-icon-light',
                'Color' => '#FFD700'
            ],
            [
                'Title' => 'Perfectionist 💎',
                'Description' => 'Made 10+ edits - attention to detail matters!',
                'BadgeType' => 'perfectionist',
                'RequiredValue' => 10,
                'Icon' => 'font-icon-cog',
                'Color' => '#20c997'
            ],
            [
                'Title' => 'Century Club 💯',
                'Description' => 'Reached 100 total points!',
                'BadgeType' => 'total_score',
                'RequiredValue' => 100,
                'Icon' => 'font-icon-trophy',
                'Color' => '#FFD700'
            ],
            [
                'Title' => 'High Achiever 🎯',
                'Description' => 'Reached 500 total points!',
                'BadgeType' => 'total_score',
                'RequiredValue' => 500,
                'Icon' => 'font-icon-medal',
                'Color' => '#FF6B35'
            ]
        ];

        $created = 0;
        $updated = 0;

        foreach ($badges as $badgeData) {
            $existingBadge = Badge::get()->filter([
                'Title' => $badgeData['Title'],
                'BadgeType' => $badgeData['BadgeType']
            ])->first();

            if ($existingBadge) {
                // Update existing badge
                foreach ($badgeData as $field => $value) {
                    $existingBadge->{$field} = $value;
                }
                $existingBadge->IsActive = true;
                $existingBadge->write();
                $updated++;
                echo "Updated: {$badgeData['Title']}\n";
            } else {
                // Create new badge
                $badge = Badge::create();
                foreach ($badgeData as $field => $value) {
                    $badge->{$field} = $value;
                }
                $badge->IsActive = true;
                $badge->write();
                $created++;
                echo "Created: {$badgeData['Title']}\n";
            }
        }

        echo "\nCompleted! Created {$created} new badges, updated {$updated} existing badges.\n";
    }
}