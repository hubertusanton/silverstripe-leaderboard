<?php

namespace Hubertusanton\Leaderboard\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Security\Member;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\View\ArrayData;
use SilverStripe\ORM\ArrayList;
use Hubertusanton\Leaderboard\Model\Badge;
use Hubertusanton\Leaderboard\Model\UserBadge;
use Hubertusanton\Leaderboard\Service\LeaderboardService;

class LeaderBoardAdmin extends ModelAdmin
{
    private static $managed_models = [
        Badge::class,
        UserBadge::class
    ];

    private static $url_segment = 'leaderboard';

    private static $menu_icon_class = 'font-icon-trophy';

    private static $menu_title = 'Leaderboard';

    private static $model_importers = [];

    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm($id, $fields);

        // If no model is selected, show the leaderboard
        if ($this->modelClass == Badge::class && !$id) {
            $service = LeaderboardService::singleton();
            $topMembers = $service->getLeaderboard(20);

            $leaderboardHTML = $this->renderLeaderboard($topMembers);

            $fields = FieldList::create([
                LiteralField::create('LeaderboardView', $leaderboardHTML)
            ]);

            $form->setFields($fields);
        }

        return $form;
    }

    private function renderLeaderboard($members)
    {
        // Prepare members data with position
        $membersWithRank = ArrayList::create();
        $rank = 1;
        foreach ($members as $member) {
            $membersWithRank->push(ArrayData::create([
                'Pos' => $rank,
                'Name' => $member->getName(),
                'Level' => $member->getLevel(),
                'TotalScore' => $member->TotalScore,
                'PageCreations' => $member->PageCreations,
                'PageEdits' => $member->PageEdits,
                'BadgeCount' => $member->getBadgeCount(),
                'CurrentStreak' => $member->CurrentStreak
            ]));
            $rank++;
        }

        // Gather statistics
        $totalMembers = Member::get()->filter('TotalScore:GreaterThan', 0)->count();
        $totalScore = Member::get()->sum('TotalScore');
        $totalPages = Member::get()->sum('PageCreations');

        $stats = ArrayData::create([
            'TotalMembers' => $totalMembers,
            'TotalScore' => $totalScore,
            'TotalPages' => $totalPages
        ]);

        // Prepare template data
        $data = ArrayData::create([
            'Members' => $membersWithRank,
            'Stats' => $stats
        ]);

        return $data->renderWith('Hubertusanton/Leaderboard/Admin/Leaderboard');
    }
}
