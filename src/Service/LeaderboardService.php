<?php

namespace Hubertusanton\Leaderboard\Service;

use SilverStripe\Security\Member;
use SilverStripe\Security\Security;
use Hubertusanton\Leaderboard\Model\Badge;
use Hubertusanton\Leaderboard\Model\UserBadge;
use SilverStripe\Core\Injector\Injectable;

class LeaderboardService
{
    use Injectable;

    // Score values for different actions
    const SCORES = [
        'page_creation' => 10,
        'page_edit' => 3,
        'page_publication' => 5,
        'page_deletion' => 2
    ];

    public function awardPoints($activity, $member = null)
    {
        if (!$member) {
            $member = Security::getCurrentUser();
        }

        if (!$member || !isset(self::SCORES[$activity])) {
            return false;
        }

        $points = self::SCORES[$activity];
        $member->addScore($points, $activity);

        // Check for badge achievements
        $this->checkBadges($member, $activity);

        return true;
    }

    public function checkBadges($member, $activity = null)
    {
        $badges = Badge::get()->filter('IsActive', true);

        foreach ($badges as $badge) {
            if (!$this->hasBadge($member, $badge) && $this->meetsRequirements($member, $badge, $activity)) {
                $this->awardBadge($member, $badge, $activity);
            }
        }
    }

    private function hasBadge($member, $badge)
    {
        return UserBadge::get()
            ->filter([
                'MemberID' => $member->ID,
                'BadgeID' => $badge->ID
            ])
            ->exists();
    }

    private function meetsRequirements($member, $badge, $activity = null)
    {
        switch ($badge->BadgeType) {
            case 'page_creation':
                return $member->PageCreations >= $badge->RequiredValue;

            case 'page_edit':
                return $member->PageEdits >= $badge->RequiredValue;

            case 'rapid_fire':
                // Check if user made 5+ actions in last hour
                return $this->checkRapidFire($member);

            case 'destroyer':
                // Award when someone creates and deletes a page quickly
                return $activity === 'page_deletion' && $this->checkDestroyer($member);

            case 'streak':
                return $member->CurrentStreak >= $badge->RequiredValue;

            case 'total_score':
                return $member->TotalScore >= $badge->RequiredValue;

            case 'night_owl':
                // Working between 10pm and 6am
                $hour = date('H');
                return ($hour >= 22 || $hour <= 6);

            case 'early_bird':
                // Working between 5am and 8am
                $hour = date('H');
                return ($hour >= 5 && $hour <= 8);

            case 'perfectionist':
                // Edit a page 10+ times in a day
                return $this->checkPerfectionist($member);

            default:
                return false;
        }
    }

    private function checkRapidFire($member)
    {
        // Count actions in last hour by checking last activity timestamp
        // This is a simplified check - you might want to track individual actions
        $oneHourAgo = date('Y-m-d H:i:s', strtotime('-1 hour'));
        return $member->LastActivity >= $oneHourAgo &&
               ($member->PageCreations + $member->PageEdits + $member->PagePublications) >= 5;
    }

    private function checkDestroyer($member)
    {
        // Simple check - if they have deletions and recent activity
        return $member->PageDeletions > 0 && $member->PageCreations > 0;
    }

    private function checkPerfectionist($member)
    {
        // Simplified check - high edit count
        return $member->PageEdits >= 10;
    }

    private function awardBadge($member, $badge, $activity = null)
    {
        $userBadge = UserBadge::create();
        $userBadge->MemberID = $member->ID;
        $userBadge->BadgeID = $badge->ID;
        $userBadge->Context = "Earned through: " . ($activity ?: 'automatic check');
        $userBadge->write();
    }

    public function getLeaderboard($limit = 10)
    {
        return Member::get()
            ->filter('TotalScore:GreaterThan', 0)
            ->sort('TotalScore DESC')
            ->limit($limit);
    }

    public function getMemberRank($member)
    {
        $higherScores = Member::get()
            ->filter('TotalScore:GreaterThan', $member->TotalScore)
            ->count();

        return $higherScores + 1;
    }
}