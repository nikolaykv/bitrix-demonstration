<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Uri;


/** @var CBitrixComponentTemplate $this */


$urlTemplates = [
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['details'],
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
];

$editUrl = CComponentEngine::makePathFromTemplate(
    $urlTemplates['EDIT'],
    ['ID' => $arResult['VARIABLES']['ID']]
);

$viewUrl = CComponentEngine::makePathFromTemplate(
    $urlTemplates['DETAIL'],
    ['ID' => $arResult['VARIABLES']['ID']]
);

$editUrl = new Uri($editUrl);
$editUrl->addParams(['backurl' => $viewUrl]);

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.toolbar',
    'type2',
    [
        'TOOLBAR_ID' => 'STORES_TOOLBAR',
        'BUTTONS' => [
            [
                'TEXT' => 'Редактировать',
                'TITLE' => 'Редактировать',
                'LINK' => $editUrl->getUri(),
                'ICON' => 'btn-edit',
            ],
        ]
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);


$APPLICATION->IncludeComponent(
    'devresource.stores:store.show',
    '',
    ['ID' => $arResult['VARIABLES']['ID']],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);
