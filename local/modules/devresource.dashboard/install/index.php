<?php defined('B_PROLOG_INCLUDED') || die;

use Devresource\Dashboard\Entity\DashboardTable;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Application;
use \Bitrix\Main\Loader;

class devresource_dashboard extends \CModule
{
    public $MODULE_ID = "devresource.dashboard";
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;

    public function __construct()
    {
        $arModuleVersion = [];

        $path = str_replace("\\", "/", __FILE__);
        $path = substr($path, 0, strlen($path) - strlen("/index.php"));
        include($path . "/version.php");

        if (is_array($arModuleVersion) && array_key_exists("VERSION", $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
        }

        $this->MODULE_NAME = Loc::getMessage('DASHBOARD_MODULE_NAME_LABEL');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DASHBOARD_MODULE_DESCRIPTION_LABEL');
    }

    public function DoInstall()
    {
        RegisterModule($this->MODULE_ID);

        $this->InstallDB();
        $this->registerAgent();
    }

    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->unRegisterAgent();
        Option::delete($this->MODULE_ID);

        UnRegisterModule($this->MODULE_ID);
    }

    public function InstallDB()
    {
        Loader::includeModule('devresource.dashboard');

        $db = Application::getConnection();

        $storeEntity = DashboardTable::getEntity();
        if (!$db->isTableExists($storeEntity->getDBTableName())) {
            $storeEntity->createDbTable();
        }
    }

    public function UnInstallDB()
    {
        // TODO не планируется удаление таблиц
    }

    public function registerAgent()
    {
        \CAgent::AddAgent(
            "\\Devresource\\Dashboard\\Agents\\Dashboard::addRow();",
            $this->MODULE_ID,
            'Y'
        );
    }

    public function unRegisterAgent()
    {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    }
}