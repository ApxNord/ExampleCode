<?

namespace Exchange;

class Log
{
    private static $logFileExist = false;
    private static $logFilePath = '/log/exchange/';

    public static function writtenLogFile($path, $data) 
    {
        if (empty($path)) {
            return false;
        }

        $fullPath = self::$logFilePath . $path . '/';

        if (self::$logFileExist === false) {
            self::setLogFilePath($fullPath);
        }

        if ($data) {
            file_put_contents(self::$logFilePath . "imports_" . date('Y_m_d_H') . ".log",
                date('Y-m-d H:i:s') . "\n", FILE_APPEND);
            file_put_contents(self::$logFilePath . "imports_" . date('Y_m_d_H') . ".log",
                print_r($data, true), FILE_APPEND);
            file_put_contents(self::$logFilePath . "imports_" . date('Y_m_d_H') . ".log", "\n", FILE_APPEND);
        } else {
            file_put_contents(self::$logFilePath . "imports_" . date('Y_m_d_H') . ".log",
                date('Y-m-d H:i:s') . " - " . $data . "\n", FILE_APPEND);
        }      
        return true;
    }

    private static function setLogFilePath($fullPath)
    {
        $fullPath = self::GetDocRootAndPath($fullPath);
        self::CreateDir($fullPath);
        self::$logFilePath = $fullPath;
        self::$logFileExist = true;
    }

    /**
     * Cоздаст указанный путь
     * @param $savePath
     */
    public static function CreateDir($savePath)
    {
        if (!file_exists($savePath)) {
            $arPath = explode('/', $savePath);

            $dir = '';
            foreach ($arPath as $key => $value) {
                if (!empty($value)) {
                    $dir .= '/' . $value;
                    if (file_exists($dir)) {
                        continue;
                    }
                    mkdir($dir, 0777, true);
                }
            }
        }
    }

    /**
     * Cоберет полный путь до указанного каталога.
     *
     * Передается путь относительно корня сайта.
     *
     * @param $path - путь от корня сайта
     * @return string
     */
    public static function GetDocRootAndPath($path)
    {
        if (stripos($path, $_SERVER['DOCUMENT_ROOT']) === false) {
            $path = $_SERVER['DOCUMENT_ROOT'] . '/logs/' . $path . '/';
            $path = str_replace('//', '/', $path);
        }

        return $path;
    }
}
