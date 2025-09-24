<?php

namespace Hubertusanton\Leaderboard\Extension;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Security;
use Hubertusanton\Leaderboard\Service\LeaderboardService;

class SiteTreeLeaderboardExtension extends DataExtension
{
    public function onAfterWrite()
    {
        $member = Security::getCurrentUser();
        if (!$member) {
            return;
        }

        $service = LeaderboardService::singleton();

        // Check if this is a new record (page creation)
        if (!$this->owner->isInDB()) {
            $service->awardPoints('page_creation', $member);
        } else {
            // This is an edit
            $service->awardPoints('page_edit', $member);
        }
    }

    public function onAfterPublish()
    {
        $member = Security::getCurrentUser();
        if (!$member) {
            return;
        }

        $service = LeaderboardService::singleton();
        $service->awardPoints('page_publication', $member);
    }

    public function onAfterDelete()
    {
        $member = Security::getCurrentUser();
        if (!$member) {
            return;
        }

        $service = LeaderboardService::singleton();
        $service->awardPoints('page_deletion', $member);
    }
}