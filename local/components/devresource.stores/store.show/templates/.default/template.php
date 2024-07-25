<?php
defined('B_PROLOG_INCLUDED') || die;


/** @var CBitrixComponentTemplate $this */


$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.form',
    'show',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'FORM_ID' => $arResult['FORM_ID'],
        'TACTILE_FORM_ID' => $arResult['TACTILE_FORM_ID'],
        'ENABLE_TACTILE_INTERFACE' => 'Y',
        'SHOW_SETTINGS' => 'Y',
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
                        'id' => 'ID',
                        'name' => "ID",
                        'type' => 'label',
                        'value' => $arResult['STORE']['ID'],
                        'isTactile' => true,
                    ],
                    [
                        'id' => 'NAME',
                        'name' => "Название",
                        'type' => 'label',
                        'value' => $arResult['STORE']['NAME'],
                        'isTactile' => true,
                    ],
                    [
                        'id' => 'CREATED_BY',
                        'name' => "Автор",
                        'type' => 'label',
                        'value' => $arResult['STORE']['CREATED_BY'],
                        'isTactile' => true,
                    ],
                ]
            ],
        ],
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);