<?php defined('B_PROLOG_INCLUDED') || die;

use \Bitrix\Main\Localization\Loc;

$arComponentParameters = [
    'PARAMETERS' => [
        'SEF_MODE' => [
            'details' => [
                'NAME' => Loc::getMessage('STORES_DETAILS_URL_TEMPLATE'),
                'DEFAULT' => '#ID#/',
                'VARIABLES' => ['ID']
            ],
            'edit' => [
                'NAME' => Loc::getMessage('STORES_EDIT_URL_TEMPLATE'),
                'DEFAULT' => '#ID#/edit/',
                'VARIABLES' => ['ID']
            ]
        ]
    ]
];
