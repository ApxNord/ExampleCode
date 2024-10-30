<?php

/**
 * ===============================================
 * Класс Project - класс импорта показателей проекта
 * ===============================================
 *
 * @author  Company
 * @since  2024
 *
 */

namespace Exchange\Import;

use Exchange;

class Project extends Import
{
    const IBLOCK_CODE = 'project';
    public static $logPath = 'project_indicator';

    /**
     * @param mixed $imports
     * @param mixed $nameProject
     * @return string
     */
    public static function runImport($imports, $nameProject)
    {
        $iblockId = Helper::getIBlockIdByCode(self::IBLOCK_CODE);
        $structured = self::getStructuredArray($imports);
        $structured['NAME_PROJECT'] = $nameProject;

        $resultImports['NAME_PROJECT'] = self::createSection($iblockId, $structured);
        $resultImports['ELEMENTS'] = self::addElements($iblockId, $structured);

        $completedImports = self::getCompletedImports($resultImports);
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

        $structured['URL_PROJECT'] = $imports[0][1];

        foreach ($imports as $key => $sections) {
            if ($sections[2] == 'TRUE'){
                $element = [
                    'NAME' => $sections[1],
                    'CODE' => $sections[0],
                ];
                $structured['ELEMENTS'][] = $element;
            }                             
        }
        
        return $structured;
    }

     /**
     * Добавит недостающие разделы из массива в инфоблок
     * @param mixed $iblockId
     * @param mixed $structuredArr
     * @return string|bool
     */
    public static function createSection($iblockId, $structured)
    {
        $sectionList = Helper::getIbSectionList($iblockId);       
    
        if (!in_array($structured['NAME_PROJECT'], $sectionList)) {
            $sectionId = self::addSection($iblockId, $structured, 'Y', false);               
            
            if ($sectionId) {
                return $structured['NAME_PROJECT'];
            }
        }                     

        return false;
    }

    /**
     * Добавит раздел в инфоблок
     * @param mixed $iblockId
     * @param mixed $name
     * @param mixed $urlProject
     * @param mixed $isActive
     * @param mixed $parentId
     * @return bool|int
     */
    public static function addSection($iblockId, $structured, $isActive = false, $parentId = false) 
    {
        $code = Helper::getTranslitCode($structured['NAME_PROJECT']);
        $fields = [
            'ACTIVE' => $isActive? 'Y' : 'N',
            'IBLOCK_SECTION_ID' => $parentId,
            'IBLOCK_ID' => $iblockId,
            'NAME' => $structured['NAME_PROJECT'],
            'CODE' => $code,
            'UF_URL_PROJECT' => $structured['URL_PROJECT']
        ];       

        return Helper::addSection($iblockId, $fields);
    }

    public static function addElements($iblockId, $structured)
    {
        if (!$iblockId) {
            return false;
        }
        
        $iblockIdStructure = Helper::getIBlockIdByCode('structure');
        $sectionId = Helper::getSectionIdByCode($iblockId,Helper::getTranslitCode($structured['NAME_PROJECT']));
        $addedElements = [];
        foreach ($structured['ELEMENTS'] as $element) {     
            $element['RAITING_POINT'] = Helper::getElementIdByCode($iblockIdStructure, $element['CODE']); 
            if (!empty($element['RAITING_POINT'])) {               
                $addedElements[] = self::addElement($iblockId, $sectionId, $element, true);   
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

    public static function getCompletedImports($resultImports) 
    {
        $completed = date('Y-m-d H:i:s');
        if (!$resultImports['NAME_PROJECT']) {
            $completed .= ' раздел уже существует';
        }
        else {
            $completed .= ' добавлен раздел: ';
        }

        return $completed . $resultImports['NAME_PROJECT'] .', добавлено/обновлено элементов: '. count($resultImports['ELEMENTS']);;
    }
}
