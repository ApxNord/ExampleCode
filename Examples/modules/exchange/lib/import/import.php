<?php

/**
 * ===============================================
 * Класс Import - super класс для основных классов импорта
 * ===============================================
 *
 * @author  Company
 * @since  2024
 *
 */

namespace Exchange\Import;

use Exchange;

class Import
{
    const LINE_SEPARATOR = ';';

    public static function run($file_path, $nameProject = false)
    {
        global $DB;
        $DB->Query('SET wait_timeout=28800');
        if (($handle = fopen($file_path, 'r')) !== false) {
            $arImport = [];

            $buffer = fgets($handle, 1024);
            if (strpos($buffer, self::LINE_SEPARATOR) !== false) {
                $delimiter = self::LINE_SEPARATOR;
            } else if (strpos($buffer, '\t') !== false) {
                $delimiter = "\t";
            } else if (strpos($buffer, ',') !== false) {
                $delimiter = ',';
            } else {
                $errMsg = 'Неизвестный разделитель в файле';
                Log::writtenLog2FileNow('/' . (new \ReflectionClass(static::class))->getShortName() . '/',
                    $errMsg);
                return $errMsg;
            }
            rewind($handle);

            while (($data = fgetcsv($handle, 100000, $delimiter)) !== false) {
                if (!mb_check_encoding($data, 'UTF-8')) {
                    $data = mb_convert_encoding($data, 'UTF-8', 'Windows-1251');
                }

                foreach ($data as &$value) {
                    $value = trim($value);
                }
                $arImport[] = $data;
            }

            fclose($handle);
        }      
        
        return static::runImport($arImport, $nameProject);
    }
}
