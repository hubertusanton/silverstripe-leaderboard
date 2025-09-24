<div class="leaderboard-container" style="padding: 20px;">
    <h2 style="margin-bottom: 30px; color: #3276b1;">🏆 CMS Leaderboard</h2>

    <% if $Members.Count == 0 %>
        <p style="color: #666; font-style: italic;">No active users yet. Start creating pages to appear on the leaderboard!</p>
    <% else %>
        <div class="leaderboard-table" style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Rank</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">User</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600;">Level</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Score</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Pages Created</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Edits</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Badges</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600;">Streak</th>
                    </tr>
                </thead>
                <tbody>
                    <% loop $Members %>
                    <tr style="border-bottom: 1px solid #dee2e6;<% if $Pos <= 3 %> background: linear-gradient(45deg, rgba(255,215,0,0.05), rgba(255,255,255,1));<% end_if %>">
                        <td style="padding: 12px;<% if $Pos == 1 %> color: #FFD700; font-weight: bold;<% else_if $Pos == 2 %> color: #C0C0C0; font-weight: bold;<% else_if $Pos == 3 %> color: #CD7F32; font-weight: bold;<% end_if %>">
                            <% if $Pos == 1 %>🥇 <% else_if $Pos == 2 %>🥈 <% else_if $Pos == 3 %>🥉 <% end_if %>#$Pos
                        </td>
                        <td style="padding: 12px; font-weight: 500;">$Name</td>
                        <td style="padding: 12px; font-size: 14px;">$Level</td>
                        <td style="padding: 12px; text-align: center; font-weight: bold; color: #28a745;">$TotalScore.Nice</td>
                        <td style="padding: 12px; text-align: center;">$PageCreations</td>
                        <td style="padding: 12px; text-align: center;">$PageEdits</td>
                        <td style="padding: 12px; text-align: center;">$BadgeCount</td>
                        <td style="padding: 12px; text-align: center;">
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
    <div style="margin-top: 30px; display: flex; gap: 20px;">
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center;">
            <h3 style="margin: 0 0 10px 0; color: #3276b1;">👥 Active Users</h3>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #28a745;">$Stats.TotalMembers</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center;">
            <h3 style="margin: 0 0 10px 0; color: #3276b1;">🏆 Total Points</h3>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #ff6b35;">$Stats.TotalScore.Nice</p>
        </div>
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); flex: 1; text-align: center;">
            <h3 style="margin: 0 0 10px 0; color: #3276b1;">📄 Pages Created</h3>
            <p style="margin: 0; font-size: 24px; font-weight: bold; color: #6f42c1;">$Stats.TotalPages.Nice</p>
        </div>
    </div>
</div>