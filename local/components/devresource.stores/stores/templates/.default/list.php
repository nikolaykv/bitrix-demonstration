<?php
defined('B_PROLOG_INCLUDED') || die;

use \Bitrix\Main\Localization\Loc;


/** @var CBitrixComponentTemplate $this */

$APPLICATION->SetTitle(Loc::getMessage('STORES_LIST_TITLE'));


$urlTemplates = [
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['details'],
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
];

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.toolbar',
    'title',
    [
        'TOOLBAR_ID' => 'STORES_TOOLBAR',
        'BUTTONS' => [
            [
                'TEXT' => Loc::getMessage('STORES_ADD'),
                'TITLE' => Loc::getMessage('STORES_ADD'),
                'LINK' => CComponentEngine::makePathFromTemplate($urlTemplates['EDIT'], ['ID' => 0]),
                'ICON' => 'btn-add',
            ]
        ]
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);

$APPLICATION->IncludeComponent(
    'devresource.stores:stores.list',
    '',
    [
        'URL_TEMPLATES' => $urlTemplates,
        'SEF_FOLDER' => $arResult['SEF_FOLDER'],
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);