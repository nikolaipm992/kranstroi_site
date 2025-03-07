<?php

/**
 * ���������� ��� ������ � �������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopClass
 */
class PHPShopFile {

    /**
     * ������� � �������� ����� �����
     * @param string $str
     * @return string
     */
    static function toLatin($str) {
 
        // UTF Fix
        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $str = iconv("utf-8", "windows-1251//IGNORE", $str);

        $str = str_replace("%20", "_", $str);
        $str = str_replace("/", "-", $str); 
        $str = str_replace("\\", "", $str);
        $str = str_replace("(", "", $str);
        $str = str_replace(")", "", $str);
        $str = str_replace(":", "", $str);
        $str = str_replace(" ", "_", $str);
        $str = str_replace(",", "", $str);
        $str = str_replace("!", "", $str);
        $str = str_replace("|", "_", $str);
        $str = str_replace("�", "N", $str);
        $str = str_replace("?", "", $str);
        $str = str_replace("&nbsp", "_", $str);
        $str = str_replace("&amp;", '_', $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace(",", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("�", "", $str);
        $str = str_replace("%", "", $str);
        $str = str_replace("*", "", $str);

        $new_str = '';
        $_Array = array(" " => "_", "�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d", "�" => "e", "�" => "e", "�" => "e", "�" => "yi", "�" => "e", "�" => "yi", "�" => "zh", "�" => "z", "�" => "i", "�" => "y", "�" => "k", "�" => "l", "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r", "�" => "s", "�" => "t", "�" => "u", "�" => "f", "�" => "h", "�" => "c", "�" => "ch", "�" => "sh", "�" => "sch", "�" => "i", "�" => "y", "�" => "i", "�" => "e", "�" => "u", "�" => "ya", "�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d", "�" => "e", "�" => "e", "�" => "zh", "�" => "z", "�" => "i", "�" => "y", "�" => "k", "�" => "l", "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r", "�" => "s", "�" => "t", "�" => "Y", "�" => "u", "�" => "f", "�" => "h", "�" => "c", "�" => "ch", "�" => "sh", "�" => "sch", "�" => "e", "�" => "u", "�" => "ya",  "$" => "i", "%" => "i", "&" => "_and_");

        $chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $val)
            if (empty($_Array[$val]))
                $new_str .= $val;
            else
                $new_str .= $_Array[$val];

        return $new_str;
    }

    /**
     * ����� �� ������ �����
     * @param string $file ��� �����
     */
    static function chmod($file, $error = false, $rul = 0775) {
        if (function_exists('chmod')) {
            if (@chmod($file, $rul))
                return true;
            elseif ($error)
                return '��� ����� ' . $file;
        }
        elseif ($error)
            return __FUNCTION__ . '() ���������';
    }

    /**
     * ������ ������ � ����
     * @param string $file ���� �� �����
     * @param string $csv ������ ��� ������
     * @param string $type �������� ������
     * @param bool $error ����� ������
     */
    static function write($file, $csv, $type = 'w+', $error = false) {
        $fp = @fopen($file, $type);
        if ($fp) {
            //stream_set_write_buffer($fp, 0);
            fputs($fp, $csv);
            fclose($fp);
            return true;
        } elseif ($error)
            echo '��� ����� ' . $file;
    }

    /**
     * ������ ������ � csv ����
     * @param string $file ���� �� �����
     * @param array $csv ������ ��� ������
     * @param bool $error ����� ������
     */
    static function writeCsv($file, $csv, $error = false) {
        $fp = fopen($file, "w+");
        if ($fp) {
            if (is_array($csv))
                foreach ($csv as $value) {
                    fputcsv($fp, $value, ';', '"');
                }
            fclose($fp);
        } elseif ($error)
            echo '��� ����� ' . $file;
    }

    /**
     * ������ CSV �����
     * @param string $file ����� �����
     * @param string $function ��� ������� �����������
     * @param string $delim �����������
     * @return bool
     */
    static function readCsv($file, $function, $delim = ';') {

        $opts = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $fp = @fopen($file, "r", false, stream_context_create($opts));
        if ($fp) {
            while (($data = @fgetcsv($fp, 0, $delim)) !== FALSE) {
                call_user_func($function, $data);
            }
            fclose($fp);
            return true;
        }
    }

    /**
     * ���������
     */
    static function getLines($file, $delim) {
        $fp = fopen($file, 'r');
        try {
            while ($line = fgetcsv($fp, 0, $delim)) {
                yield $line;
            }
        } finally {
            fclose($fp);
        }
    }

    /**
     * ������ CSV ����� � �����������
     * @param string $file ����� �����
     * @param string $function ��� ������� �����������
     * @param string $delim �����������
     * @param string $limit ������ �������� ��������� (0,500)
     * @return bool
     */
    static function readCsvGenerators($file, $function, $delim = ';', $limit = array(0, 500)) {

        foreach (self::getLines($file, $delim) as $n => $line) {
            if ($n == 0 or ( $limit[0] <= $n and $n < $limit[1]))
                call_user_func($function, $line);
            else
                continue;
        }

        if (file_exists($file))
            return true;
    }

    /**
     * GZIP ���������� �����
     * @param string $source ���� �� �����
     * @param int $level ������� ������
     * @return bool
     */
    static function gzcompressfile($source, $level = false) {
        $dest = $source . '.gz';
        $mode = 'wb' . $level;
        $error = false;
        if ($fp_out = @gzopen($dest, $mode)) {
            if ($fp_in = @fopen($source, 'rb')) {
                while (!feof($fp_in))
                    gzwrite($fp_out, fread($fp_in, 1024 * 512));
                fclose($fp_in);
            } else
                $error = true;
            gzclose($fp_out);
            unlink($source);
            //rename($dest, $source . '.bz2');
        } else
            $error = true;
        if ($error)
            return false;
        else
            return $dest;
    }

    /**
     * ����� ������
     * @param string $dir �����
     * @param string $function ������� ���������
     * @param bool $return ������� ���������� ������ ��������� ����
     * @return mixed
     */
    static function searchFile($dir, $function, $return = false) {
        $user_func_result = null;
        if (is_dir($dir))
            if (@$dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if ($file != '.' and $file != '..') {
                        $user_func_result .= call_user_func_array($function, array($file));
                        if ($return)
                            return $user_func_result;
                    }
                }

                return $user_func_result;
                closedir($dh);
            }
    }

    /**
     * @param string $sql
     * @return array
     */
    public static function sqlStringToArray($sql) {
        $sql = trim($sql);

        if (strpos($sql, "\r\n")) {
            $eol = "\r\n";
        } elseif (strpos($sql, "\n")) {
            $eol = "\n";
        } else {
            $eol = "\r";
        }

        $array = explode(";" . $eol, $sql);

        foreach ($array as $key => $element) {
            $array[$key] = trim($element);
            if (substr($element, -1) !== ';') {
                $array[$key] = $element . ';';
            }
        }

        return $array;
    }

}

/**
 * ����� ��������
 * @param string $file ��� �����
 * @return string
 */
function getLicense($file) {
    $fstat = explode(".", $file);
    if ($fstat[1] == "lic")
        return $file;
}

?>