<?php
defined('B_PROLOG_INCLUDED') || die;


use \Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\Elements\ElementOutletsTable;

class CDevresourceStoresStoreShowComponent extends \CBitrixComponent
{
    const FORM_ID = 'STORES_SHOW';

    public function __construct(\CBitrixComponent $component = null)
    {
        parent::__construct($component);

        \CBitrixComponent::includeComponentClass('devresource.stores:stores.list');
        \CBitrixComponent::includeComponentClass('devresource.stores:store.edit');
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $APPLICATION->SetTitle(Loc::getMessage('STORES_SHOW_TITLE_DEFAULT'));

        $dbStore = ElementOutletsTable::getList([
            'select' => ['*'],
            'filter' => ['=ID' => $this->arParams['ID']]
        ]);
        $store = $dbStore->fetch();

        if (empty($store)) {
            ShowError(Loc::getMessage('STORES_STORE_NOT_FOUND'));
            return;
        }

        $APPLICATION->SetTitle(
            Loc::getMessage(
                'STORES_SHOW_TITLE',
                [
                    '#ID#' => $store['ID'],
                    '#NAME#' => $store['NAME']
                ]
            )
        );

        $this->arResult = [
            'FORM_ID' => self::FORM_ID,
            'TACTILE_FORM_ID' => CDevresourceStoresStoreEditComponent::FORM_ID,
            'GRID_ID' => CDevresourceStoresStoresListComponent::GRID_ID,
            'STORE' => $store
        ];

        $this->includeComponentTemplate();
    }
}