<?php

namespace Hubertusanton\Leaderboard\Extension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\HeaderField;
use Hubertusanton\Leaderboard\Model\UserBadge;

class MemberLeaderboardExtension extends DataExtension
{
    private static $db = [
        'TotalScore'       => 'Int',
        'PageCreations'    => 'Int',
        'PageEdits'        => 'Int',
        'PagePublications' => 'Int',
        'PageDeletions'    => 'Int',
        'LastActivity'     => 'Datetime',
        'CurrentStreak'    => 'Int', // Days of consecutive activity
        'LongestStreak'    => 'Int'
    ];

    private static $has_many = [
        'UserBadges' => UserBadge::class
    ];

    private static $level_thresholds = [
        0    => 'CMS Newbie 🥺',
        50   => 'Page Padawan 🤓',
        150  => 'Content Conjurer 🪄',
        300  => 'Edit Enthusiast ✨',
        500  => 'Publishing Pro 🚀',
        750  => 'CMS Champion 👑',
        1000 => 'Digital Deity 🌟',
        1500 => 'Website Wizard 🧙‍♂️',
        2000 => 'Content Overlord 👹',
        3000 => 'The Chosen One ⚡'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->addFieldsToTab('Root.Leaderboard', [
            HeaderField::create('LeaderboardHeader', 'Leaderboard Stats'),
            ReadonlyField::create('Level', 'Current Level', $this->getLevel()),
            ReadonlyField::create('TotalScore', 'Total Score'),
            ReadonlyField::create('PageCreations', 'Pages Created'),
            ReadonlyField::create('PageEdits', 'Page Edits'),
            ReadonlyField::create('PagePublications', 'Publications'),
            ReadonlyField::create('PageDeletions', 'Page Deletions'),
            ReadonlyField::create('CurrentStreak', 'Current Streak (days)'),
            ReadonlyField::create('LongestStreak', 'Longest Streak (days)'),
            ReadonlyField::create('LastActivity', 'Last Activity')
        ]);
    }

    public function getLevel()
    {
        $levels = $this->owner->config()->get('level_thresholds');
        $currentLevel = 'CMS Newbie 🥺';

        foreach ($levels as $threshold => $level) {
            if ($this->owner->TotalScore >= $threshold) {
                $currentLevel = $level;
            } else {
                break;
            }
        }

        return $currentLevel;
    }

    public function getNextLevel()
    {
        $levels = $this->owner->config()->get('level_thresholds');

        foreach ($levels as $threshold => $level) {
            if ($this->owner->TotalScore < $threshold) {
                return [
                    'level'     => $level,
                    'threshold' => $threshold,
                    'needed'    => $threshold - $this->owner->TotalScore
                ];
            }
        }

        return null; // Max level reached
    }

    public function addScore($points, $activity = null)
    {
        $this->owner->TotalScore += $points;
        $this->owner->LastActivity = date('Y-m-d H:i:s');

        switch ($activity) {
            case 'page_creation':
                $this->owner->PageCreations++;
                break;
            case 'page_edit':
                $this->owner->PageEdits++;
                break;
            case 'page_publication':
                $this->owner->PagePublications++;
                break;
            case 'page_deletion':
                $this->owner->PageDeletions++;
                break;
        }

        $this->updateStreak();
        $this->owner->write();
    }

    private function updateStreak()
    {
        $lastActivity = $this->owner->LastActivity ? strtotime($this->owner->LastActivity) : 0;
        $today = strtotime(date('Y-m-d'));
        $yesterday = strtotime('-1 day', $today);

        if (date('Y-m-d', $lastActivity) == date('Y-m-d')) {
            // Activity today, streak continues
            return;
        } elseif (date('Y-m-d', $lastActivity) == date('Y-m-d', $yesterday)) {
            // Activity yesterday, increment streak
            $this->owner->CurrentStreak++;
            if ($this->owner->CurrentStreak > $this->owner->LongestStreak) {
                $this->owner->LongestStreak = $this->owner->CurrentStreak;
            }
        } else {
            // Streak broken
            $this->owner->CurrentStreak = 1;
        }
    }

    public function getBadgeCount()
    {
        return $this->owner->UserBadges()->count();
    }

    public function getRecentBadges($limit = 5)
    {
        return $this->owner->UserBadges()
            ->sort('EarnedDate DESC')
            ->limit($limit);
    }
}
