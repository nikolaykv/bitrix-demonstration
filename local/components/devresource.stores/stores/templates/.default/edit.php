<?php
defined('B_PROLOG_INCLUDED') || die;

/** @var CBitrixComponentTemplate $this */

$urlTemplates = [
    'DETAIL' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['details'],
    'EDIT' => $arResult['SEF_FOLDER'] . $arResult['SEF_URL_TEMPLATES']['edit'],
];

$APPLICATION->IncludeComponent(
    'devresource.stores:store.edit',
    '',
    [
        'ID' => $arResult['VARIABLES']['ID'],
        'URL_TEMPLATES' => $urlTemplates,
        'SEF_FOLDER' => $arResult['SEF_FOLDER'],
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);