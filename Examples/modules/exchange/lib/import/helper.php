<?php

/**
 * ===============================================
 * Класс Helper - класс дополнительных функций для импорта
 * ===============================================
 *
 * @author  Company
 * @since  2024
 *
 */

namespace Exchange\Import;

class Helper
{
    /**
     * вернет id инфоблока по его символьному коду
     * @param string $iblockCode символьный код
     * @return bool|int
     */
    public static function getIBlockIdByCode($iblockCode)
    {
        if (!\CModule::IncludeModule('iblock')) {
            return false;
        }

        $res = \CIBlock::GetList([], ['CODE' => $iblockCode]);
        if ($iblock = $res->Fetch()) {
            return $iblock['ID'];
        } else {
            return false;
        }
    }

    /**
     * вернет список названий разделов инфоблока
     * @param mixed $iblockId
     * @return array|bool
     */
    public static function getIbSectionList($iblockId)
    {
        if ($iblockId <= 0 || !\CModule::IncludeModule('iblock')) {
            return false;
        }

        $result = [];
        $rsSect = \CIBlockSection::GetList([], ['IBLOCK_ID' => $iblockId], false, ['NAME']);
        while ($section = $rsSect->fetch()) {
            $result[] = $section['NAME'];
        }

        return $result;
    }

    /**
     * вернет id раздела по его символьному коду
     * @param mixed $iblockId
     * @param mixed $sectionCode
     * @return mixed
     */
    public static function getSectionIdByCode($iblockId, $sectionCode)
    {
        if ($iblockId <= 0 || !$sectionCode || !\CModule::IncludeModule('iblock')) {
            return false;
        }

        $res = \CIBlockSection::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => $sectionCode, 'SITE_ID' => 's1']);
        $section = $res->Fetch();
  
        return $section['ID'];
    }

    /**
     * вернет id элемента по его символьному коду
     * @param mixed $iblockId
     * @param mixed $elementCode
     * @return mixed
     */
    public static function getElementIdByCode($iblockId, $elementCode)
    {
        if ($iblockId <= 0 || !$elementCode || !\CModule::IncludeModule('iblock')) {
            return false;
        }

        $res = \CIBlockElement::GetList([], ['IBLOCK_ID' => $iblockId, 'CODE' => $elementCode, 'SITE_ID' => 's1']);
        $element = $res->Fetch();
  
        return $element['ID'];
    }

    public static function getTranslitCode($code)
    {
        $params = [
            'max_len' => '100', // обрезает символьный код до 100 символов
            'change_case' => 'L', // буквы преобразуются к нижнему регистру
            'replace_space' => '_', // меняем пробелы на нижнее подчеркивание
            'replace_other' => '_', // меняем левые символы на нижнее подчеркивание
            'delete_repeat_replace' => 'true', // удаляем повторяющиеся нижние подчеркивания
            'use_google' => 'false', // отключаем использование google
        ];


        return \CUtil::translit($code, 'ru', $params);
    }

    /**
     * добавит элемент в инфоблок
     * @param mixed $iblockId
     * @param mixed $params
     * @return bool|int|mixed|string
     */
    public static function addElement($iblockId, $params)
    {
        if ($iblockId <= 0 || !\CModule::IncludeModule('iblock')) {
            return false;
        }

        $element = new \CIBlockElement;

        if ($id = $element->Add($params)) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * добавит раздел в инфоблок
     * @param mixed $iblockId
     * @param mixed $params
     * @return bool|int
     */
    public static function addSection($iblockId, $params)
    {
        if ($iblockId <= 0 || !\CModule::IncludeModule('iblock')) {
            return false;
        }

        $section = new \CIBlockSection;      
        if ($id = $section->Add($params)) {
            return $id;
        } else {
            return false;            
        }
    }
    
    /**
     * обновит пользовательские свойства элемента
     * @param mixed $iblockId
     * @param mixed $elementId
     * @param mixed $params
     * @return bool
     */
    public static function updateElement($iblockId, $elementId, $params)
    {
        if ($iblockId <= 0 || !\CModule::IncludeModule('iblock')) {
            return false;
        }

        $element = new \CIBlockElement;
        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex($iblockId, $elementId);
        return $element->Update($elementId, $params);   
    }
}
