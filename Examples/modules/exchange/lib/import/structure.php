<?php

/**
 * ===============================================
 * Класс Structure - класс импорта структуры данных
 * ===============================================
 *
 * @author  Company
 * @since  2024
 *
 */

namespace Exchange\Import;

use Exchange;

class Structure extends Import
{
    const IBLOCK_CODE = 'structure';
    public static $logPath = 'structure_data';

    /**
     * @param mixed $imports
     * @return string
     */
    public static function runImport($imports)
    {
        $iblockId = Helper::getIBlockIdByCode(self::IBLOCK_CODE);
        $structured = self::getStructuredArray($imports);

        $resultImports = [];
        $resultImports['SECTIONS'] = self::createSections($iblockId, $structured);
        $resultImports['ELEMENTS'] = self::addElements($iblockId, $structured);

        $completedImports = self::getSuccessfulImports($resultImports);
        Log::writtenLogFile(self::$logPath, $completedImports);
        return $completedImports;
    }

    /**
     * Создаст массив с иерархией
     *
     * @param $imports
     * @return array|bool
     */
    public static function getStructuredArray($imports)
    {
        if (empty($imports)) {
            return false;
        }

        $structured = [];
        $section = '';
        $element = [];

        foreach ($imports as $key => $sections) {         
            if (!empty($sections[1])) {
                if (!empty($sections[9]) && $sections[9] == 'TRUE') {
                    $section = $sections[1];  
                } 
                else if ($section != '') {
                    if (!empty($sections[5]) || !empty($sections[7])) {
                        $element = [
                            'NAME' => $sections[1],
                            'CODE' => $sections[0],
                            'DESKTOP' =>$sections[5] == 'TRUE' ? 'Y': 'N',
                            'MOBILE' => $sections[7] == 'TRUE' ? 'Y': 'N',
                            'WEIGHT' => $sections[3],
                        ];
                        
                        $structured[$section][] = $element;
                    }
                }                   
            }           
        }
        
        return $structured;
    }

    /**
     * Добавит недостающие разделы из массива в инфоблок
     * @param mixed $iblockId
     * @param mixed $structured
     * @return array[]
     */
    public static function createSections($iblockId, $structured)
    {
        $sectionList = Helper::getIbSectionList($iblockId);

        $addedSections = [];

        if (!empty($structured)) {
            foreach ($structured as $sectionName => $sections) {
                if (!in_array($sectionName, $sectionList)) {
                    $sectionId = self::addSection($iblockId, $sectionName, true, false);
                    
                    if ($sectionId) {
                        $addedSections[] = $sectionName;
                    }
                }               
            }          
        }
        return $addedSections;
    }

    /**
     * Добавит раздел в инфоблок
     *
     * @param $iblockId
     * @param $name
     * @param $active
     * @param $parentId
     * @return int | bool
     */
    public static function addSection($iblockId, $name, $isActive = false, $parentId = false) 
    {
        $code = Helper::getTranslitCode($name);
        $fields = [
            'ACTIVE' => $isActive? 'Y' : 'N',
            'IBLOCK_SECTION_ID' => $parentId,
            'IBLOCK_ID' => $iblockId,
            'NAME' => $name,
            'CODE' => $code,
        ];       

        return Helper::addSection($iblockId, $fields);      
    }

    public static function addElements($iblockId, $structured)
    {
        if (!$iblockId) {
            return false;
        }
        $addedElements = [];
        foreach ($structured as $sectionName => $elements) {
            $sectionId = Helper::getSectionIdByCode($iblockId,Helper::getTranslitCode($sectionName));
            
            foreach ($elements as $element) {             
                    $elementId = self::addElement($iblockId, $sectionId, $element, true);

                    if ($elementId) {
                        $addedElements[] = $element['NAME'];
                    }
            }
        }
        return $addedElements;
    }
    public static function addElement($iblockId, $sectionId, $element, $isActive = false) 
    {
        $params = [
            'MODIFIED_BY' => $GLOBALS['USER']->GetID(), // элемент изменен текущим пользователем  
            'IBLOCK_SECTION_ID' => $sectionId, // элемент лежит в корне раздела  
            'IBLOCK_ID' => $iblockId,
            'PROPERTY_VALUES' => $element,  
            'NAME' => $element['NAME'],
            'CODE' => $element['CODE'],
            'ACTIVE' => $isActive? 'Y' : 'N', // активен  
        ];

        if ($elementId = Helper::getElementIdByCode($iblockId, $element['CODE'])) {
            return Helper::updateElement($iblockId, $elementId, $params);
        }
        else {
            return Helper::addElement($iblockId, $params);
        }
    }

    public static function getSuccessfulImports($resultImports) 
    {
        return date('Y-m-d H:i:s') . ' добавлено разделов: '. count($resultImports['SECTIONS']) 
        .', добавлено/обновлено элементов: '. count($resultImports['ELEMENTS']);
    }
}
