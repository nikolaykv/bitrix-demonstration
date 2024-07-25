<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Error;
use Bitrix\Main\ErrorCollection;


/** @var CBitrixComponentTemplate $this */

/** @var ErrorCollection $errors */
$errors = $arResult['ERRORS'];

foreach ($errors as $error) {
    /** @var Error $error */
    ShowError($error->getMessage());
}

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.form',
    'edit',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'FORM_ID' => $arResult['FORM_ID'],
        'ENABLE_TACTILE_INTERFACE' => 'Y',
        'SHOW_SETTINGS' => 'Y',
        'TITLE' => $arResult['TITLE'],
        'IS_NEW' => $arResult['IS_NEW'],
        'DATA' => $arResult['STORE'],
        'TABS' => [
            [
                'id' => 'tab_1',
                'name' => "Торговая точка",
                'title' => "Свойства торговой точки",
                'display' => false,
                'fields' => [
                    [
                        'id' => 'section_store',
                        'name' => "Торговая точка",
                        'type' => 'section',
                        'isTactile' => true,
                    ],
                    [
                        'id' => 'NAME',
                        'name' => "Название",
                        'type' => 'text',
                        'value' => $arResult['STORE']['NAME'],
                        'isTactile' => true,
                    ],
                    [
                        'id' => 'CREATED_BY',
                        'name' => "Автор",
                        'type' => 'label',
                        'value' => $arResult['STORE']['CREATED_BY'],
                        'isTactile' => true,
                    ]
                ]
            ]
        ],
        'BUTTONS' => [
            'back_url' => $arResult['BACK_URL'],
            'standard_buttons' => true,
        ],
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);