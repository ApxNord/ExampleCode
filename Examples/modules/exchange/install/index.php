<?
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Localization\LanguageTable;
use Bitrix\Main\SiteTable;

Loc::loadMessages(__FILE__);

Class Exchange extends CModule
{
    const MODULE_ID = 'exchange';

    function __construct()
    {
        $arModuleVersion = [];

        $path = str_replace('\\', '/', __FILE__);
        $path = substr($path, 0, strlen($path) - strlen('/index.php'));
        include($path . '/version.php');

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_ID = self::MODULE_ID;
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = GetMessage('exchange_MODULE_NAME');
        $this->MODULE_DESCRIPTION = GetMessage('exchange_MODULE_DESC');
        $this->PARTNER_NAME = GetMessage('exchange_PARTNER_NAME');
        $this->PARTNER_URI = GetMessage('exchange_PARTNER_URI');
    }

    function DoInstall()
    {
        if ($GLOBALS['APPLICATION']->GetGroupRight('exchange') < 'W') {
            return;
        }

        if (!$this->InstallDB() || !$this->InstallFiles()) {
            return;
        }

        RegisterModule($this->MODULE_ID);
    }

    function DoUninstall()
    {
        if ($GLOBALS['APPLICATION']->GetGroupRight('exchange') < 'W') {
            return;
        }

        if (!$this->UnInstallDB() || !$this->UnInstallFiles()) {
            return;
        }
        UnRegisterModule($this->MODULE_ID);
    }

    function InstallFiles()
    {

        CopyDirFiles($_SERVER['DOCUMENT_ROOT'] . '/local/modules/' . self::MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin', true, true);

        return true;
    }

    function UnInstallFiles()
    {
        DeleteDirFiles($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/' . self::MODULE_ID . '/install/admin',
            $_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin');

        return true;
    }

    function InstallDB()
    {
        return true;
    }

    function UnInstallDB()
    {
        return true;
    }
}
