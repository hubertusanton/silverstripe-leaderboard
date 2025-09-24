<% require css("hubertusanton/silverstripe-leaderboard: client/css/leaderboard.css") %>

<div class="leaderboard-container">
    <h2 class="leaderboard-title">🏆 CMS Leaderboard</h2>

    <% if $Members.Count == 0 %>
        <p class="leaderboard-empty">No active users yet. Start creating pages to appear on the leaderboard!</p>
    <% else %>
        <div class="leaderboard-table">
            <table>
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>User</th>
                        <th>Level</th>
                        <th class="center">Score</th>
                        <th class="center">Pages Created</th>
                        <th class="center">Edits</th>
                        <th class="center">Badges</th>
                        <th class="center">Streak</th>
                    </tr>
                </thead>
                <tbody>
                    <% loop $Members %>
                    <tr<% if $Pos <= 3 %> class="top-three"<% end_if %>>
                        <td class="<% if $Pos == 1 %>rank-gold<% else_if $Pos == 2 %>rank-silver<% else_if $Pos == 3 %>rank-bronze<% end_if %>">
                            <% if $Pos == 1 %>🥇 <% else_if $Pos == 2 %>🥈 <% else_if $Pos == 3 %>🥉 <% end_if %>#$Pos
                        </td>
                        <td class="user-name">$Name</td>
                        <td class="level">$Level</td>
                        <td class="score">$TotalScore.Nice</td>
                        <td class="center">$PageCreations</td>
                        <td class="center">$PageEdits</td>
                        <td class="center">$BadgeCount</td>
                        <td class="center">
                            <% if $CurrentStreak > 0 %>
                                🔥 $CurrentStreak days
                            <% else %>
                                -
                            <% end_if %>
                        </td>
                    </tr>
                    <% end_loop %>
                </tbody>
            </table>
        </div>
    <% end_if %>

    <!-- Stats Dashboard -->
    <div class="leaderboard-stats">
        <div class="stat-card users">
            <h3>👥 Active Users</h3>
            <p>$Stats.TotalMembers</p>
        </div>
        <div class="stat-card score">
            <h3>🏆 Total Points</h3>
            <p>$Stats.TotalScore.Nice</p>
        </div>
        <div class="stat-card pages">
            <h3>📄 Pages Created</h3>
            <p>$Stats.TotalPages.Nice</p>
        </div>
    </div>
</div>