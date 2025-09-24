<?php

namespace Hubertusanton\Leaderboard\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Security\Permission;

class UserBadge extends DataObject
{
    private static $table_name = 'UserBadge';

    private static $db = [
        'EarnedDate' => 'Datetime',
        'Context' => 'Text' // Additional context about how the badge was earned
    ];

    private static $has_one = [
        'Member' => Member::class,
        'Badge' => Badge::class
    ];

    private static $default_sort = 'EarnedDate DESC';

    private static $summary_fields = [
        'Member.Name' => 'User',
        'Badge.Title' => 'Badge',
        'EarnedDate.Nice' => 'Earned Date',
        'Context' => 'Context'
    ];

    public function getCMSFields()
    {
        $fields = FieldList::create([
            DropdownField::create('MemberID', 'Member')
                ->setSource(Member::get()->map('ID', 'Name')),
            DropdownField::create('BadgeID', 'Badge')
                ->setSource(Badge::get()->filter('IsActive', true)->map('ID', 'Title')),
            ReadonlyField::create('EarnedDate', 'Earned Date')
        ]);

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->EarnedDate) {
            $this->EarnedDate = date('Y-m-d H:i:s');
        }
    }

    public function canView($member = null)
    {
        return true;
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }
}