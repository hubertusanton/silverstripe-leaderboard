<?php

namespace Hubertusanton\Leaderboard\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Security\Permission;

class Badge extends DataObject
{
    private static $table_name = 'Badge';

    private static $db = [
        'Title' => 'Varchar(100)',
        'Description' => 'Text',
        'BadgeType' => 'Varchar(50)',
        'Icon' => 'Varchar(100)',
        'Color' => 'Varchar(7)', // Hex color
        'IsActive' => 'Boolean',
        'RequiredValue' => 'Int', // For counting-based badges
        'Sort' => 'Int'
    ];

    private static $has_one = [
        'BadgeImage' => Image::class
    ];

    private static $has_many = [
        'UserBadges' => UserBadge::class
    ];

    private static $default_sort = 'Sort ASC, Title ASC';

    private static $summary_fields = [
        'Title',
        'BadgeType',
        'IsActive.Nice' => 'Active',
        'UserBadges.Count' => 'Times Earned'
    ];

    private static $searchable_fields = [
        'Title',
        'BadgeType',
        'IsActive'
    ];

    public function getCMSFields()
    {
        $fields = FieldList::create([
            TextField::create('Title', 'Badge Title'),
            TextareaField::create('Description', 'Description'),
            TextField::create('BadgeType', 'Badge Type')
                ->setDescription('e.g., page_creation, rapid_fire, destroyer'),
            TextField::create('Icon', 'Icon Class')
                ->setDescription('Font Awesome or SilverStripe icon class'),
            TextField::create('Color', 'Badge Color')
                ->setDescription('Hex color code (e.g., #ff6b35)'),
            NumericField::create('RequiredValue', 'Required Value')
                ->setDescription('For counting-based badges (e.g., number of pages created)'),
            NumericField::create('Sort', 'Sort Order'),
            CheckboxField::create('IsActive', 'Is Active')
        ]);

        $uploadField = UploadField::create('BadgeImage', 'Badge Image');
        $uploadField->setAllowedExtensions(['png', 'jpg', 'jpeg', 'gif', 'svg']);
        $uploadField->setFolderName('leaderboard/badges');
        $fields->push($uploadField);

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->Sort) {
            $this->Sort = Badge::get()->max('Sort') + 1;
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