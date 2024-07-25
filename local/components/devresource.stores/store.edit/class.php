<?php
defined('B_PROLOG_INCLUDED') || die;

use Bitrix\Main\Error;
use Bitrix\Main\Context;
use Bitrix\Main\UserTable;
use Bitrix\Main\ErrorCollection;
use Bitrix\Main\Localization\Loc;
use \Bitrix\Iblock\Elements\ElementOutletsTable;

class CDevresourceStoresStoreEditComponent extends \CBitrixComponent
{
    const FORM_ID = 'STORES_EDIT';

    private $errors;

    public function __construct(\CBitrixComponent $component = null)
    {
        parent::__construct($component);

        $this->errors = new ErrorCollection();

        \CBitrixComponent::includeComponentClass('devresource.stores:stores.list');
    }

    public function executeComponent()
    {
        global $APPLICATION;

        $title = "Торговая точка";


        $store = [
            'NAME' => '',
            'CREATED_BY' => 0
        ];

        if (intval($this->arParams['STORE_ID']) > 0) {
            $dbStore = ElementOutletsTable::getList([
                'select' => ['*'],
                'filter' => ['=ID' => $this->arParams['ID']]
            ]);
            $store = $dbStore->fetch();

            if (empty($store)) {
                ShowError("Торговая точка не существует.");
                return;
            }
        }

        if (!empty($store['ID'])) {
            $title = Loc::getMessage(
                'Торговая точка №#ID# &mdash; #NAME#',
                [
                    '#ID#' => $store['ID'],
                    '#NAME#' => $store['NAME']
                ]
            );
        }

        $APPLICATION->SetTitle($title);

        if (self::isFormSubmitted()) {
            $savedStoreId = $this->processSave($store);
            if ($savedStoreId > 0) {
                LocalRedirect($this->getRedirectUrl($savedStoreId));
            }

            $submittedStore = $this->getSubmittedStore();
            $store = array_merge($store, $submittedStore);
        }

        $this->arResult = [
            'FORM_ID' => self::FORM_ID,
            'GRID_ID' => CDevresourceStoresStoresListComponent::GRID_ID,
            'IS_NEW' => empty($store['ID']),
            'TITLE' => $title,
            'STORE' => $store,
            'BACK_URL' => $this->getRedirectUrl(),
            'ERRORS' => $this->errors,
        ];

        $this->includeComponentTemplate();
    }

    private function processSave($initialStore)
    {
        $submittedStore = $this->getSubmittedStore();

        $store = array_merge($initialStore, $submittedStore);

        $this->errors = self::validate($store);

        if (!$this->errors->isEmpty()) {
            return false;
        }

        if (!empty($store['ID'])) {
            $result = ElementOutletsTable::update($store['ID'], $store);
        } else {
            $result = ElementOutletsTable::add($store);
        }

        if (!$result->isSuccess()) {
            $this->errors->add($result->getErrors());
        }

        return $result->isSuccess() ? $result->getId() : false;
    }

    private function getSubmittedStore()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $submittedStore = [
            'NAME' => $request->get('NAME'),
            'CREATED_BY' => $request->get('CREATED_BY'),
        ];

        return $submittedStore;
    }

    private static function validate($store)
    {
        $errors = new ErrorCollection();

        if (empty($store['NAME'])) {
            $errors->setError(new Error("Название торговой точки не задано."));
        }

        if (empty($store['CREATED_BY'])) {
            $errors->setError(new Error('Не указан ответственный.'));
        } else {
            $dbUser = UserTable::getById($store['CREATED_BY']);
            if ($dbUser->getSelectedRowsCount() <= 0) {
                $errors->setError(new Error("Указанный ответственный сотрудник не существует."));
            }
        }

        return $errors;
    }

    private static function isFormSubmitted()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();
        $saveAndView = $request->get('saveAndView');
        $saveAndAdd = $request->get('saveAndAdd');
        $apply = $request->get('apply');
        return !empty($saveAndView) || !empty($saveAndAdd) || !empty($apply);
    }

    private function getRedirectUrl($savedStoreId = null)
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        if (!empty($savedStoreId) && $request->offsetExists('apply')) {
            return \CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                ['ID' => $savedStoreId]
            );
        } elseif (!empty($savedStoreId) && $request->offsetExists('saveAndAdd')) {
            return \CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['EDIT'],
                ['ID' => 0]
            );
        }

        $backUrl = $request->get('backurl');
        if (!empty($backUrl)) {
            return $backUrl;
        }

        if (!empty($savedStoreId) && $request->offsetExists('saveAndView')) {
            return \CComponentEngine::makePathFromTemplate(
                $this->arParams['URL_TEMPLATES']['DETAIL'],
                ['STORE_ID' => $savedStoreId]
            );
        } else {
            return $this->arParams['SEF_FOLDER'];
        }
    }
}