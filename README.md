# SilverStripe Leaderboard 🏆

A gamification module for SilverStripe CMS that adds scoring, levels, and badges for CMS users. Transform content creation into an engaging experience!

## Features

### 🎯 Scoring System
- **Page Creation**: 10 points
- **Page Edit**: 3 points
- **Page Publication**: 5 points
- **Page Deletion**: 2 points

### 🎮 Level System
Users progress through funny levels based on their total score:
- 🥺 **CMS Newbie** (0+ points)
- 🤓 **Page Padawan** (50+ points)
- 🪄 **Content Conjurer** (150+ points)
- ✨ **Edit Enthusiast** (300+ points)
- 🚀 **Publishing Pro** (500+ points)
- 👑 **CMS Champion** (750+ points)
- 🌟 **Digital Deity** (1000+ points)
- 🧙‍♂️ **Website Wizard** (1500+ points)
- 👹 **Content Overlord** (2000+ points)
- ⚡ **The Chosen One** (3000+ points)

### 🏅 Badge System
Extensible badge system with fun achievements:
- **First Steps** 👶 - Create your first page
- **Page Creator** 📄 - Create 10 pages
- **Content Machine** 🏭 - Create 50 pages
- **Editor in Chief** 📝 - Make 25 edits
- **Rapid Fire** 🔥 - 5+ actions within an hour
- **The Destroyer** 💀 - Create then immediately delete
- **Streak Master** 🔥 - Work 7 consecutive days
- **Night Owl** 🦉 - Work between 10pm-6am
- **Early Bird** 🐦 - Work between 5am-8am
- **Perfectionist** 💎 - Make 10+ edits
- **Century Club** 💯 - Reach 100 points
- **High Achiever** 🎯 - Reach 500 points

### 📊 Leaderboard Interface
- View top performers with rankings
- Display user levels, scores, and badges
- Show activity streaks
- Overall statistics dashboard

### 👤 Member Profile Integration
- Leaderboard stats added to member profiles in CMS
- View individual user progress and achievements

## Installation

```bash
composer require hubertusanton/silverstripe-leaderboard
```

## Setup

1. Run database migration:
```bash
vendor/bin/sake dev/build flush=1
```

2. Populate default badges:
```bash
vendor/bin/sake dev/tasks/populate-leaderboard-badges
```

3. Access the leaderboard in the CMS admin menu (🏆 Leaderboard)

## Usage

The module automatically tracks user activity and awards points/badges. Users can view:
- Their progress in their member profile (Leaderboard tab)
- The global leaderboard in the admin interface
- Badge collection and achievements

## Extending

### Custom Badges

Create custom badges by adding records to the Badge model or extend the badge checking logic in `LeaderboardService::meetsRequirements()`.

### Custom Scoring

Modify point values in `LeaderboardService::SCORES` constant or extend the service to add new activities.

## Requirements

- PHP 7.4+ or 8.0+
- SilverStripe Framework ^4.0 or ^5.0
- SilverStripe CMS ^4.0 or ^5.0
