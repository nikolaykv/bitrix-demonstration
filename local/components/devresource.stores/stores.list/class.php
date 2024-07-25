<?php defined('B_PROLOG_INCLUDED') || die;

use \Bitrix\Main\Grid;
use \Bitrix\Main\Context;
use \Bitrix\Main\Web\Uri;
use \Bitrix\Main\Web\Json;
use \Bitrix\Main\UI\Filter;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\UI\PageNavigation;
use \Bitrix\Iblock\Elements\ElementOutletsTable;

class CDevresourceStoresStoresListComponent extends \CBitrixComponent
{
    const GRID_ID = 'STORES_LIST';

    const SORTABLE_FIELDS = [
        'ID', 'NAME', 'DATE_CREATE',
        'TIMESTAMP_X', 'CREATED_BY', 'ACTIVE',
    ];
    const FILTERABLE_FIELDS = [
        'ID', 'NAME', 'DATE_CREATE',
        'TIMESTAMP_X', 'CREATED_BY', 'ACTIVE',
    ];

    const SUPPORTED_ACTIONS = ['delete'];

    const SUPPORTED_SERVICE_ACTIONS = ['GET_ROW_COUNT'];

    private static $headers;
    private static $filterFields;
    private static $filterPresets;

    public function __construct(\CBitrixComponent $component = null)
    {
        global $USER;

        parent::__construct($component);

        self::$headers = [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'first_order' => 'desc',
                'type' => 'int',
            ],
            [
                'id' => 'NAME',
                'name' => 'Имя',
                'sort' => 'NAME',
                'default' => true,
            ],
            [
                'id' => 'DATE_CREATE',
                'name' => 'Дата создания',
                'sort' => 'DATE_CREATE',
                'default' => true,
            ],
            [
                'id' => 'TIMESTAMP_X',
                'name' => 'Дата изменения',
                'sort' => 'TIMESTAMP_X',
                'default' => true,
            ],
            [
                'id' => 'CREATED_BY',
                'name' => 'Автор',
                'sort' => 'CREATED_BY',
                'default' => true,
            ],
            [
                'id' => 'ACTIVE',
                'name' => 'Активность',
                'sort' => 'ACTIVE',
                'default' => true,
            ],
        ];

        self::$filterFields = [
            [
                'id' => 'ID',
                'name' => 'ID',
                'type' => 'int',
                'default' => true,
            ],
            [
                'id' => 'NAME',
                'name' => 'Имя',
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'DATE_CREATE',
                'name' => 'Дата создания',
                'type' => 'date',
                'default' => true,
            ],
            [
                'id' => 'TIMESTAMP_X',
                'name' => 'Дата изменения',
                'type' => 'date',
                'default' => true,
            ],
            [
                'id' => 'CREATED_BY',
                'name' => 'Автор',
                'type' => 'dest_selector',
                'params' => [
                    'multiple' => 'Y',
                ],
                'default' => true,
            ],
            [
                'id' => 'ACTIVE',
                'name' => 'Активность',
                'type' => 'list',
                'items' => [
                    "" => 'Не указано',
                    "Y" => 'Активна',
                    "N" => 'Отключена'
                ],
                'default' => true,
            ],
        ];

        self::$filterPresets = [
            'my_stores' => [
                'name' => 'Кастомный пресет фильтра',
                'fields' => [
                    'CREATED_BY' => $USER->GetID(),
                    'CREATED_BY_name' => $USER->GetFullName(),
                ]
            ]
        ];
    }

    public function executeComponent()
    {
        $context = Context::getCurrent();
        $request = $context->getRequest();

        $grid = new Grid\Options(self::GRID_ID);

        /** Сортировка START **/
        $gridSort = $grid->getSorting();
        $sort = array_filter(
            $gridSort['sort'],
            function ($field) {
                return in_array($field, self::SORTABLE_FIELDS);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (empty($sort)) $sort = ['NAME' => 'asc'];
        /** Сортировка START **/

        /** Фильтр START **/
        $gridFilter = new Filter\Options(self::GRID_ID, self::$filterPresets);
        $gridFilterValues = $gridFilter->getFilter(self::$filterFields);

        $gridFilterValues = array_filter(
            $gridFilterValues,
            function ($fieldName) {
                return in_array($fieldName, self::FILTERABLE_FIELDS);
            },
            ARRAY_FILTER_USE_KEY
        );

        if (!empty($gridFilterValues['CREATED_BY'])) {
            $gridFilterValues["CREATED_BY"] = str_replace("U", "", $gridFilterValues['CREATED_BY']);
        }
        /** Фильтр END **/

        $this->processGridActions($gridFilterValues);
        $this->processServiceActions($gridFilterValues);


        /** Пагинация START **/
        $gridNav = $grid->GetNavParams();
        $pager = new PageNavigation('');
        $pager->setPageSize($gridNav['nPageSize']);

        $pager->setRecordCount(ElementOutletsTable::getCount($gridFilterValues));

        if ($request->offsetExists('page')) {
            $currentPage = $request->get('page');
            $pager->setCurrentPage($currentPage > 0 ? $currentPage : $pager->getPageCount());
        } else {
            $pager->setCurrentPage(1);
        }
        /** Пагинация END **/

        $stores = ElementOutletsTable::getList([
            'filter' => $gridFilterValues,
            'select' => ['*'],
            'limit' => $pager->getLimit(),
            'offset' => $pager->getOffset(),
            'order' => $sort
        ]);


        $requestUri = new Uri($request->getRequestedPage());
        $requestUri->addParams(['sessid' => bitrix_sessid()]);

        $this->arResult = [
            'GRID_ID' => self::GRID_ID,
            'STORES' => $stores,
            'HEADERS' => self::$headers,
            'PAGINATION' => [
                'PAGE_NUM' => $pager->getCurrentPage(),
                'ENABLE_NEXT_PAGE' => $pager->getCurrentPage() < $pager->getPageCount(),
                'URL' => $request->getRequestedPage(),
            ],
            'SORT' => $sort,
            'FILTER' => self::$filterFields,
            'FILTER_PRESETS' => self::$filterPresets,
            'ENABLE_LIVE_SEARCH' => false,
            'DISABLE_SEARCH' => true,
            'SERVICE_URL' => $requestUri->getUri(),
        ];

        $this->includeComponentTemplate();

    }

    private function processGridActions($currentFilter)
    {
        if (!check_bitrix_sessid()) return;

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $action = $request->get('action_button_' . self::GRID_ID);

        if (!in_array($action, self::SUPPORTED_ACTIONS)) return;

        $allRows = $request->get('action_all_rows_' . self::GRID_ID) == 'Y';
        if ($allRows) {
            $dbStores = ElementOutletsTable::getList([
                'filter' => $currentFilter,
                'select' => ['ID'],
            ]);

            $storeIds = [];

            foreach ($dbStores as $store) $storeIds[] = $store['ID'];

        } else {

            $storeIds = $request->get('ID');
            if (!is_array($storeIds)) $storeIds = [];

        }

        if (empty($storeIds)) return;

        switch ($action) {
            case 'delete':
                foreach ($storeIds as $storeId) {
                    ElementOutletsTable::delete($storeId);
                }
                break;

            default:
                break;
        }
    }

    private function processServiceActions($currentFilter)
    {
        global $APPLICATION;

        if (!check_bitrix_sessid()) return;

        $context = Context::getCurrent();
        $request = $context->getRequest();

        $params = $request->get('PARAMS');

        if (empty($params['GRID_ID']) || $params['GRID_ID'] != self::GRID_ID) return;

        $action = $request->get('ACTION');

        if (!in_array($action, self::SUPPORTED_SERVICE_ACTIONS)) return;

        $APPLICATION->RestartBuffer();
        header('Content-Type: application/json');

        switch ($action) {
            case 'GET_ROW_COUNT':
                $count = ElementOutletsTable::getCount($currentFilter);
                echo Json::encode([
                    'DATA' => [
                        'TEXT' => Loc::getMessage('STORES_GRID_ROW_COUNT', ['#COUNT#' => $count])
                    ]
                ]);
                break;

            default:
                break;
        }

        die;
    }
}