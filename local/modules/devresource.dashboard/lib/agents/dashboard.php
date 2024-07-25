<?php

namespace Devresource\Dashboard\Agents;

use \Bitrix\Main\Loader;
use \Bitrix\Crm\DealTable;
use \Bitrix\Main\Diag\Debug;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Type\DateTime;
use \Bitrix\Main\Entity\ExpressionField;
use Devresource\Dashboard\Entity\DashboardTable;

Loader::includeModule('devresource.dashboard');
Loader::includeModule('crm');

class Dashboard
{
    static private $module_id = 'devresource.dashboard';

    public static function addRow()
    {

        $result = self::getDealsByOptionStages();

        $record = self::getDashboardRow();

        if ($record) {
            $update = DashboardTable::update($record['ID'], [
                'DATA' => $result,
                'DATE_CREATE' => new DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s')
            ]);

            if ($update->isSuccess()) {
                Debug::writeToFile(
                    ['ID' => $update->getId()],
                    date('Y-m-d H:i:s') . PHP_EOL . ' UPDATE ID=' . $update->getId(),
                    'local/modules/devresource.dashboard/logs/add_row_update_success.log'
                );
            } else {
                Debug::writeToFile(
                    ['ERROR' => $update->getErrorMessages()],
                    date('Y-m-d H:i:s') . PHP_EOL . ' UPDATE ERROR',
                    'local/modules/devresource.dashboard/logs/add_row_update_error.log'
                );
            }

        } else {

            $add = DashboardTable::add([
                'DATA' => $result,
                'DATE_CREATE' => new DateTime(date('Y-m-d H:i:s'), 'Y-m-d H:i:s')
            ]);

            if ($add->isSuccess()) {
                Debug::writeToFile(
                    ['ID' => $add->getId()],
                    date('Y-m-d H:i:s') . PHP_EOL . ' ADD ID=' . $add->getId(),
                    'local/modules/devresource.dashboard/logs/add_row_add_success.log'
                );
            } else {

                Debug::writeToFile(
                    ['ERROR' => $add->getErrorMessages()],
                    date('Y-m-d H:i:s') . PHP_EOL . ' ADD ERROR',
                    'local/modules/devresource.dashboard/logs/add_row_add_error.log'
                );
            }
        }

        return '\\Devresource\\Dashboard\\Agents\\Dashboard::addRow();';

    }

    protected static function getDealsByOptionStages()
    {
        $options = [
            'agents' => array_map(static function ($item) {
                return trim($item);
            }, explode(',', Option::get(self::$module_id, 'agents'))),
            'citizenship' => array_map(static function ($item) {
                return trim($item);
            }, explode(
                    ',',
                    Option::get(
                        self::$module_id,
                        'citizenship_deal',
                        Option::get(self::$module_id, 'citizenship_deal')
                    )
                )
            ),
            'citizenshipUserFieldCode' => Option::get(
                self::$module_id, 'citizenship_deal_user_field_code', 'UF_CRM_61E55B8853173'
            ),
        ];

        Debug::writeToFile(
            ['options' => $options],
            date('Y-m-d H:i:s') . PHP_EOL . ' OPTIONS INFO',
            'local/modules/devresource.dashboard/logs/get_deal_options_stages.log'
        );

        $dealObj = DealTable::getList([
            'filter' => [
                '=STAGE_ID' => $options['agents'],
                "=" . $options['citizenshipUserFieldCode'] => $options['citizenship'],
                'CATEGORY_ID' => 493,
            ],
            'select' => ['ID', 'citizenship' => $options['citizenshipUserFieldCode']],
            'cache' => ['ttl' => 3600, 'cache_joins' => true]
        ]);

        while ($item = $dealObj->fetch()) $result[$item['citizenship'] ?: 3741][] = $item;

        $ru = (is_array($result[3741])) ? count($result[3741]) : 0;
        $by = (is_array($result[3743])) ? count($result[3743]) : 0;
        $kz = (is_array($result[3745])) ? count($result[3745]) : 0;
        $kg = (is_array($result[8285])) ? count($result[8285]) : 0;
        $uz = (is_array($result[8286])) ? count($result[8286]) : 0;


        Debug::writeToFile(
            [
                'result' => [
                    'ru' => $ru,
                    'by' => $by,
                    'kz' => $kz,
                    'kg' => $kg,
                    'uz' => $uz,
                    'all' => $ru + $by + $kz + $kg + $uz,
                ]
            ],
            date('Y-m-d H:i:s') . PHP_EOL . ' DEALS INFO',
            'local/modules/devresource.dashboard/logs/get_deal_options_stages.log'
        );

        return json_encode([
            'ru' => $ru,
            'by' => $by,
            'kz' => $kz,
            'kg' => $kg,
            'uz' => $uz,
            'all' => $ru + $by + $kz + $kg + $uz,
        ]);
    }

    protected static function getDashboardRow()
    {
        $result = DashboardTable::getList([
            'order' => ['date' => 'DESC'],
            'group' => ['date'],
            'filter' => [
                '>=DATE_CREATE' => new DateTime(date('Y-m-d') . '00:00:00', "Y-m-d H:i:s"),
                '<=DATE_CREATE' => new DateTime(date('Y-m-d') . '23:59:59', "Y-m-d H:i:s"),
            ],
            'runtime' => [
                new ExpressionField('date', 'DATE_FORMAT(%s, "%%Y-%%m-%%d")', ['DATE_CREATE']),
            ],
            "select" => ['ID', 'DATA', 'date'],
            'cache' => ['ttl' => 3600, 'cache_joins' => true]
        ])->fetch();

        Debug::writeToFile(
            ['result' => $result],
            date('Y-m-d H:i:s') . PHP_EOL . ' DASHBOARD TABLE INFO',
            'local/modules/devresource.dashboard/logs/get_dashboard_row.log'
        );

        return $result;
    }
}