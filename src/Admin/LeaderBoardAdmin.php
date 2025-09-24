<?php

namespace Hubertusanton\Leaderboard\Admin;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Security\Member;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
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

            $leaderboardHTML = $this->generateLeaderboardHTML($topMembers);

            $fields = FieldList::create([
                LiteralField::create('LeaderboardView', $leaderboardHTML)
            ]);

            $form->setFields($fields);
        }

        return $form;
    }

    private function generateLeaderboardHTML($members)
    {
        $html = '<div class="leaderboard-container" style="padding: 20px;">';
        $html .= '<h2 style="margin-bottom: 30px; color: #3276b1;">🏆 CMS Leaderboard</h2>';

        if ($members->count() == 0) {
            $html .= '<p style="color: #666; font-style: italic;">No active users yet. Start creating pages to appear on the leaderboard!</p>';
        } else {
            $html .= '<div class="leaderboard-table" style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">';
            $html .= '<table style="width: 100%; border-collapse: collapse;">';
            $html .= '<thead style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">';
            $html .= '<tr>';
            $html .= '<th style="padding: 15px; text-align: left; font-weight: 600;">Rank</th>';
            $html .= '<th style="padding: 15px; text-align: left; font-weight: 600;">User</th>';
            $html .= '<th style="padding: 15px; text-align: left; font-weight: 600;">Level</th>';
            $html .= '<th style="padding: 15px; text-align: center; font-weight: 600;">Score</th>';
            $html .= '<th style="padding: 15px; text-align: center; font-weight: 600;">Pages Created</th>';
            $html .= '<th style="padding: 15px; text-align: center; font-weight: 600;">Edits</th>';
            $html .= '<th style="padding: 15px; text-align: center; font-weight: 600;">Badges</th>';
            $html .= '<th style="padding: 15px; text-align: center; font-weight: 600;">Streak</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';

            $rank = 1;
            foreach ($members as $member) {
                $rankColor = '';
                $rankIcon = '';

                if ($rank == 1) {
                    $rankColor = 'color: #FFD700; font-weight: bold;';
                    $rankIcon = '🥇 ';
                } elseif ($rank == 2) {
                    $rankColor = 'color: #C0C0C0; font-weight: bold;';
                    $rankIcon = '🥈 ';
                } elseif ($rank == 3) {
                    $rankColor = 'color: #CD7F32; font-weight: bold;';
                    $rankIcon = '🥉 ';
                }

                $rowStyle = $rank <= 3 ? 'background: linear-gradient(45deg, rgba(255,215,0,0.05), rgba(255,255,255,1));' : '';

                $html .= '<tr style="border-bottom: 1px solid #dee2e6; ' . $rowStyle . '">';
                $html .= '<td style="padding: 12px; ' . $rankColor . '">' . $rankIcon . '#' . $rank . '</td>';
                $html .= '<td style="padding: 12px; font-weight: 500;">' . $member->getName() . '</td>';
                $html .= '<td style="padding: 12px; font-size: 14px;">' . $member->getLevel() . '</td>';
                $html .= '<td style="padding: 12px; text-align: center; font-weight: bold; color: #28a745;">' . number_format($member->TotalScore) . '</td>';
                $html .= '<td style="padding: 12px; text-align: center;">' . $member->PageCreations . '</td>';
                $html .= '<td style="padding: 12px; text-align: center;">' . $member->PageEdits . '</td>';
                $html .= '<td style="padding: 12px; text-align: center;">' . $member->getBadgeCount() . '</td>';
                $html .= '<td style="padding: 12px; text-align: center;">';
                if ($member->CurrentStreak > 0) {
                    $html .= '🔥 ' . $member->CurrentStreak . ' days';
                } else {
                    $html .= '-';
                }
                $html .= '</td>';
                $html .= '</tr>';
                $rank++;
            }

            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
        }

        // Add some stats
        $totalMembers = Member::get()->filter('TotalScore:GreaterThan', 0)->count();
        $totalScore = Member::get()->sum('TotalScore');
        $totalPages = Member::get()->sum('PageCreations');

        $html .= '<div style="margin-top: 30px; display: flex; gap: 20px;">';
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center;">';
        $html .= '<h3 style="margin: 0 0 10px 0; color: #3276b1;">👥 Active Users</h3>';
        $html .= '<p style="margin: 0; font-size: 24px; font-weight: bold; color: #28a745;">' . $totalMembers . '</p>';
        $html .= '</div>';
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center;">';
        $html .= '<h3 style="margin: 0 0 10px 0; color: #3276b1;">🏆 Total Points</h3>';
        $html .= '<p style="margin: 0; font-size: 24px; font-weight: bold; color: #ff6b35;">' . number_format($totalScore) . '</p>';
        $html .= '</div>';
        $html .= '<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center;">';
        $html .= '<h3 style="margin: 0 0 10px 0; color: #3276b1;">📄 Pages Created</h3>';
        $html .= '<p style="margin: 0; font-size: 24px; font-weight: bold; color: #6f42c1;">' . number_format($totalPages) . '</p>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}
