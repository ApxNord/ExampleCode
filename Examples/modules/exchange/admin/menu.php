<?
IncludeModuleLangFile(__FILE__);

global $APPLICATION;
$module_id = 'exchange';

if ($APPLICATION->GetGroupRight($module_id) > 'R') {
    // сформируем верхний пункт меню
    $aMenu = [
        'parent_menu' => 'global_menu_content',
        'sort'        => 5,
        'url'         => 'exchange_manager.php',  // ссылка на пункте меню
        'text'        => GetMessage('EXCHANGE_TEXT'),       // текст пункта меню
        'title'       => GetMessage('EXCHANGE_TITLE'), // текст всплывающей подсказки
        'icon'        => 'default_menu_icon', // малая иконка
        'page_icon'   => 'default_menu_icon', // большая иконка
    ];
    return $aMenu;
}
// если нет доступа, вернем false
return false;
