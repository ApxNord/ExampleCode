<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/prolog_before.php');
set_time_limit(0);

use Exchange\Import;
use Bitrix\Main\Localization\Loc;

if (empty($_SERVER['HTTP_X_REQUESTED_WITH'] || !isset($_SERVER['HTTP_X_REQUESTED_WITH']))
    || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' || !check_bitrix_sessid()
) {
    die();
}

// проверим права
global $APPLICATION;
if ($APPLICATION->GetUserRight('exchange') < 'W') {
    die();
}

Loc::loadMessages(__FILE__);

CModule::IncludeModule('exchange');
if ($_REQUEST['action']) {
    if ($_FILES[$_REQUEST['action']]['tmp_name']) {
        $buffer = '';
        $filePath = $_FILES[$_REQUEST['action']]['tmp_name'];
        switch ($_REQUEST['action']) {
            case 'data_structures':
                $buffer = Import\Structure::run($filePath);
                break;
            case 'project_indicators':
                if ($_REQUEST['name_project']){
                    $buffer = Import\Project::run($filePath, $_REQUEST['name_project']);
                }
                else {
                    $buffer = Loc::getMessage('NO_NAME_PROJECT');
                }
                break;          
            default:
                $buffer = Loc::getMessage('NO_EVENT_HANDLER');
        }
        echo '<pre>';
        print_r($buffer);
        echo '</pre>';
    } else {
        echo Loc::getMessage('NO_FILE');
    }
} else {
    echo Loc::getMessage('ERROR_SENDING_REQUEST');
}

require($_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/main/include/epilog_after.php');
