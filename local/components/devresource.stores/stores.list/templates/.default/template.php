<?php
defined('B_PROLOG_INCLUDED') || die;


use \Bitrix\Main\Grid\Panel\Snippet;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\UserTable;
use \Bitrix\Main\Web\Json;

/** @var CBitrixComponentTemplate $this */


$asset = Asset::getInstance();
$asset->addJs('/bitrix/js/crm/interface_grid.js');

$gridManagerId = $arResult['GRID_ID'] . '_MANAGER';

$rows = [];
foreach ($arResult['STORES'] as $store) {

    $viewUrl = \CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['DETAIL'],
        [
            'ID' => $store['ID']
        ]
    );
    $editUrl = \CComponentEngine::makePathFromTemplate(
        $arParams['URL_TEMPLATES']['EDIT'],
        [
            'ID' => $store['ID']
        ]
    );

    $deleteUrlParams = http_build_query([
        'action_button_' . $arResult['GRID_ID'] => 'delete',
        'ID' => [$store['ID']],
        'sessid' => bitrix_sessid()
    ]);

    $deleteUrl = $arParams['SEF_FOLDER'] . '?' . $deleteUrlParams;


    $user = UserTable::getByPrimary($store['CREATED_BY'])->fetchObject();

    $rows[] = [
        'id' => $store['ID'],
        'actions' => [
            [
                'TITLE' => 'Просмотр',
                'TEXT' => 'Просмотр',
                'ONCLICK' => 'BX.Crm.Page.open(' . Json::encode($viewUrl) . ')',
                'DEFAULT' => true
            ],
            [
                'TITLE' => 'Редактирование',
                'TEXT' => 'Редактирование',
                'ONCLICK' => 'BX.Crm.Page.open(' . Json::encode($editUrl) . ')',
            ],
            [
                'TITLE' => 'Удаление',
                'TEXT' => 'Удаление',
                'ONCLICK' => 'BX.CrmUIGridExtension.processMenuCommand(' . Json::encode($gridManagerId) . ', BX.CrmUIGridMenuCommand.remove, { pathToRemove: ' . Json::encode($deleteUrl) . ' })',
            ]
        ],
        'data' => $store,
        'columns' => [
            'ID' => $store['ID'],
            'DATE_CREATE' => $store['DATE_CREATE']->format('d.m.Y'),
            'TIMESTAMP_X' => $store['TIMESTAMP_X']->format('d.m.Y'),
            'NAME' => '<a href="' . $viewUrl . '" target="_self">' . $store['NAME'] . '</a>',
            'ACTIVE' => $store['ACTIVE'] == 'Y' ? 'Активна' : 'Отключена',
            'CREATED_BY' => empty($store['CREATED_BY']) ? '' : CCrmViewHelper::PrepareUserBaloonHtml([
                'PREFIX' => "STORE_{$store['ID']}_RESPONSIBLE",
                'USER_ID' => $user->getId(),
                'USER_NAME' => $user->getLastName() . ' ' . $user->getName(),
                'USER_PROFILE_URL' => '/company/personal/user/' . $user->getId() . '/'
            ]),
        ]
    ];
}


$snippet = new Snippet();

$APPLICATION->IncludeComponent(
    'bitrix:crm.interface.grid',
    'titleflex',
    [
        'GRID_ID' => $arResult['GRID_ID'],
        'HEADERS' => $arResult['HEADERS'],
        'ROWS' => $rows,
        'PAGINATION' => $arResult['PAGINATION'],
        'SORT' => $arResult['SORT'],
        'FILTER' => $arResult['FILTER'],
        'FILTER_PRESETS' => $arResult['FILTER_PRESETS'],
        'IS_EXTERNAL_FILTER' => false,
        'ENABLE_LIVE_SEARCH' => $arResult['ENABLE_LIVE_SEARCH'],
        'DISABLE_SEARCH' => $arResult['DISABLE_SEARCH'],
        'ENABLE_ROW_COUNT_LOADER' => true,
        'AJAX_ID' => '',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_HISTORY' => 'N',
        'AJAX_LOADER' => null,
        'ACTION_PANEL' => [
            'GROUPS' => [
                [
                    'ITEMS' => [
                        $snippet->getRemoveButton(),
                        $snippet->getForAllCheckbox(),
                    ]
                ]
            ]
        ],
        'EXTENSION' => [
            'ID' => $gridManagerId,
            'CONFIG' => [
                'ownerTypeName' => 'STORE',
                'gridId' => $arResult['GRID_ID'],
                'serviceUrl' => $arResult['SERVICE_URL'],
            ],
            'MESSAGES' => [
                'deletionDialogTitle' => Loc::getMessage('STORES_DELETE_DIALOG_TITLE'),
                'deletionDialogMessage' => Loc::getMessage('STORES_DELETE_DIALOG_MESSAGE'),
                'deletionDialogButtonTitle' => Loc::getMessage('STORES_DELETE_DIALOG_BUTTON'),
            ]
        ],
    ],
    $this->getComponent(),
    ['HIDE_ICONS' => 'Y']
);