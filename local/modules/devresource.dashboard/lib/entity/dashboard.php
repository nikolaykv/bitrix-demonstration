<?php

namespace Devresource\Dashboard\Entity;

use \Bitrix\Main\Entity\DateField;
use \Bitrix\Main\Entity\DataManager;
use \Bitrix\Main\Entity\IntegerField;
use \Bitrix\Main\Entity\StringField;

class DashboardTable extends DataManager
{
    public static function getTableName()
    {
        return 'devresource_dashboard';
    }

    public static function getMap()
    {
        return array(
            new IntegerField('ID', array('primary' => true, 'autocomplete' => true)),
            new StringField('DATA'),
            new DateField('DATE_CREATE'),
        );
    }
}