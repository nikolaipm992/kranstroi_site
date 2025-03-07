<?php

include_once dirname(__DIR__) . '/lib/mobiledetect/Mobile_Detect.php';

/**
 * ���������� �������������� �����
 * @author PHPShop Software
 * @version 2.4
 * @package PHPShopClass
 * @subpackage Helper
 */
class PHPShopString {

    /**
     * �������� IDNA
     * @param string $str ��� ������
     * @param bool $path ������ � �������
     * @return string
     */
    static function check_idna($str, $path = false) {
        global $_classPath;
        if (strstr($str, 'xn--')) {

            if (empty($path))
                include_once($GLOBALS['SysValue']['file']['idna']);
            else
                include_once($_classPath . '.' . $GLOBALS['SysValue']['file']['idna']);

            if (class_exists('idna_convert')) {
                $idna_convert = new idna_convert();
                $str = PHPShopString::utf8_win1251($idna_convert->decode($str), true);
            }
        }

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $str = PHPShopString::win_utf8($str, true);

        return $str;
    }

    /**
     * �������� ������������ � ������
     * @param string $data
     * @return Bool
     */
    static function is_serialized($data) {
        return (is_string($data) && preg_match("#^((N;)|((a|O|s):[0-9]+:.*[;}])|((b|i|d):[0-9.E-]+;))$#um", $data));
    }

    /**
     * ��������� Win 1251 � JSON ������
     * @param string $var
     * @return string
     */
    static function json_safe_encode($var) {
        return json_encode(json_fix_cyr($var));
    }

    /**
     * ��������� Win 1251 � UTF8
     * @param string $in_text �����
     * @param bool $utf_check ��������� �������� UTF
     * @return string
     */
    static function win_utf8($in_text, $utf_check = false) {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8' and ! $utf_check)
            return $in_text;

        if (function_exists('iconv')) {
            $output = iconv("windows-1251", "utf-8//IGNORE", $in_text);
        } else {

            $output = null;
            $other[1025] = "�";
            $other[1105] = "�";
            $other[1028] = "�";
            $other[1108] = "�";
            $other[1030] = "I";
            $other[1110] = "i";
            $other[1031] = "�";
            $other[1111] = "�";

            for ($i = 0; $i < strlen($in_text); $i++) {
                if (ord($in_text[$i]) > 191) {
                    $output .= "&#" . (ord($in_text[$i]) + 848) . ";";
                } else {
                    if (array_search($in_text[$i], $other) === false) {
                        $output .= $in_text[$i];
                    } else {
                        $output .= "&#" . array_search($in_text[$i], $other) . ";";
                    }
                }
            }
        }

        return $output;
    }

    /**
     * ����������� utf8 � win1251
     * @param string $s ������
     * @param bool $utf_check �������� ���������
     * @return string
     */
    static function utf8_win1251($s, $utf_check = false) {

        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8' and ! $utf_check)
            $output = $s;

        else if (function_exists('iconv')) {
            $output = iconv("utf-8","windows-1251//IGNORE", $s);
        } else {

            $output = strtr($s, array("\xD0\xB0" => "�", "\xD0\x90" => "�", "\xD0\xB1" => "�", "\xD0\x91" => "�", "\xD0\xB2" => "�", "\xD0\x92" => "�", "\xD0\xB3" => "�", "\xD0\x93" => "�", "\xD0\xB4" => "�", "\xD0\x94" => "�", "\xD0\xB5" => "�", "\xD0\x95" => "�", "\xD1\x91" => "�", "\xD0\x81" => "�", "\xD0\xB6" => "�", "\xD0\x96" => "�", "\xD0\xB7" => "�", "\xD0\x97" => "�", "\xD0\xB8" => "�", "\xD0\x98" => "�", "\xD0\xB9" => "�", "\xD0\x99" => "�", "\xD0\xBA" => "�", "\xD0\x9A" => "�", "\xD0\xBB" => "�", "\xD0\x9B" => "�", "\xD0\xBC" => "�", "\xD0\x9C" => "�", "\xD0\xBD" => "�", "\xD0\x9D" => "�", "\xD0\xBE" => "�", "\xD0\x9E" => "�", "\xD0\xBF" => "�", "\xD0\x9F" => "�", "\xD1\x80" => "�", "\xD0\xA0" => "�", "\xD1\x81" => "�", "\xD0\xA1" => "�", "\xD1\x82" => "�", "\xD0\xA2" => "�", "\xD1\x83" => "�", "\xD0\xA3" => "�", "\xD1\x84" => "�", "\xD0\xA4" => "�", "\xD1\x85" => "�", "\xD0\xA5" => "�", "\xD1\x86" => "�", "\xD0\xA6" => "�", "\xD1\x87" => "�", "\xD0\xA7" => "�", "\xD1\x88" => "�", "\xD0\xA8" => "�", "\xD1\x89" => "�", "\xD0\xA9" => "�", "\xD1\x8A" => "�", "\xD0\xAA" => "�", "\xD1\x8B" => "�", "\xD0\xAB" => "�", "\xD1\x8C" => "�", "\xD0\xAC" => "�", "\xD1\x8D" => "�", "\xD0\xAD" => "�", "\xD1\x8E" => "�", "\xD0\xAE" => "�", "\xD1\x8F" => "�", "\xD0\xAF" => "�"));
        }
        return $output;
    }

    /**
     * ������� � ��������
     * @param string $str ������
     * @param bool $lower ������� � ������ �������
     * @return string
     */
    static function toLatin($str, $lower = true) {

        if ($lower)
            $str = strtolower($str);
        
        $str=trim($str);

        // UTF Fix
        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8')
            $str = iconv("utf-8", "windows-1251//IGNORE", $str);

        $str = str_replace("&nbsp;", "", $str);
        $str = str_replace("/", "-", $str); // ��������� ��� SeoPro
        $str = str_replace("\\", "", $str);
        $str = str_replace("(", "", $str);
        $str = str_replace(")", "", $str);
        $str = str_replace(":", "", $str);
        //$str = str_replace("-", "", $str); // ��������� ��� SeoPro
        //$str = str_replace(" ", "_", $str);
        $str = str_replace("!", "", $str);
        $str = str_replace("|", "_", $str);
        $str = str_replace(".", "_", $str);
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
        $str = str_replace(array('&#43;', '&#43'), '+', $str);

        $new_str = '';
        $_Array = array(" " => "-", "�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d", "�" => "e", "�" => "e", "�" => "e", "�" => "yi", "�" => "e", "�" => "yi", "�" => "zh", "�" => "z", "�" => "i", "�" => "y", "�" => "k", "�" => "l", "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r", "�" => "s", "�" => "t", "�" => "u", "�" => "f", "�" => "h", "�" => "c", "�" => "ch", "�" => "sh", "�" => "sch", "�" => "i", "�" => "y", "�" => "i", "�" => "e", "�" => "u", "�" => "ya", "�" => "a", "�" => "b", "�" => "v", "�" => "g", "�" => "d", "�" => "e", "�" => "e", "�" => "zh", "�" => "z", "�" => "i", "�" => "y", "�" => "k", "�" => "l", "�" => "m", "�" => "n", "�" => "o", "�" => "p", "�" => "r", "�" => "s", "�" => "t", "�" => "Y", "�" => "u", "�" => "f", "�" => "h", "�" => "c", "�" => "ch", "�" => "sh", "�" => "sch", "�" => "e", "�" => "u", "�" => "ya", "." => "_", "$" => "i", "%" => "i", "&" => "_and_");

        $chars = preg_split('//', $str, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($chars as $val)
            if (empty($_Array[$val]))
                $new_str .= $val;
            else
                $new_str .= $_Array[$val];

        return preg_replace('([^a-zA-Z0-9/_\.-])', '', $new_str);
    }

    // �������� �� ����� � ������� 
    static function mySubstr($str, $a, $add = "...") {
        $str = htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'windows-1251');
        $len = strlen($str);

        if ($len < $a)
            return $str;

        for ($i = 1; $i <= $a; $i++) {
            if ($str[$i] == ".")
                $T = $i;
        }

        if (substr($str, -1) == '&')
            $str = substr($str, 0, $len - 1);

        if ($T < 1)
            return substr($str, 0, $a) . $add;
        else
            return substr($str, 0, $T + 1);
    }

    /**
     * �������������� ���� ��� Excel
     * @param float $price ����
     * @param bool $direct �������� ������
     * @return float
     */
    static function toFloat($price, $direct = false) {
        if ($direct) {
            if (strpos($price, ','))
                $price = str_replace(',', '.', $price);
        }
        else {
            if (strpos($price, '.'))
                $price = str_replace('.', ',', $price);
        }
        return trim($price);
    }

    /*
     * ��������� ������� ����� � html ���
     * @param string $str ����
     * @return string
     */

    static function getColor($str) {
        $colorArray = array(
            '�����' => '#ffffff',
            '������' => '#000000',
            '�������' => '#FF0000',
            '�������' => '#008000',
            '�����' => '#0000FF',
            '�������' => '#00FFFF',
            '������' => '#FFFF00',
            '�������' => '#FFC0CB',
            '���������' => '#FFA500',
            '����������' => '#EE82EE',
            '����������' => '#A0522D',
            '�����' => '#808080',
            '����������' => '#C0C0C0'
        );
        $code = $colorArray[trim(mb_strtolower($str, 'windows-1251'))];
        if (empty($code) and ! empty($str))
            $code = '';
        return $code;
    }

    /**
     * ����������� ������ ��������
     * @param string $agent HTTP_USER_AGENT
     * @return string
     */
    static function getBrowser($agent = false) {

        if (empty($agent))
            $agent = $_SERVER["HTTP_USER_AGENT"];

        preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info); // ���������� ���������, ������� ��������� ����������� 90% ���������
        list(, $browser, $version) = $browser_info;
        if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera))
            return 'Opera ' . $opera[1];
        if ($browser == 'MSIE') {
            preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie);
            if ($ie)
                return $ie[1] . ' based on IE ' . $version;
            return 'IE ' . $version;
        }
        if ($browser == 'Firefox') {
            preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff);
            if ($ff)
                return $ff[1] . ' ' . $ff[2];
        }
        if ($browser == 'Opera' && $version == '9.80')
            return 'Opera ' . substr($agent, -5);
        if ($browser == 'Version')
            return 'Safari ' . $version;
        if (!$browser && strpos($agent, 'Gecko'))
            return 'Browser based on Gecko';
        return $browser . ' ' . $version;
    }

    /**
     * ����������� ���������� �������
     * @return boolean
     */
    static function is_mobile() {
        if (defined('isMobil'))
            return isMobil;
    }

}

/**
 * ������� ������� �� Windows-1251 � UTF-8 
 * @param array $var ������ ������
 * @return array
 */
function json_fix_cyr($var) {
    if (is_array($var)) {
        $new = array();
        foreach ($var as $k => $v) {
            $new[json_fix_cyr($k)] = json_fix_cyr($v);
        }
        $var = $new;
    } elseif (is_string($var)) {
        $var = PHPShopString::win_utf8($var);
    }
    return $var;
}

/**
 * ������� ������� �� UTF-8 � Windows-1251
 * @param array $var ������ ������
 * @return array
 */
function json_fix_utf($var) {
    if (is_array($var)) {
        $new = array();
        foreach ($var as $k => $v) {
            $new[json_fix_utf($k)] = json_fix_utf($v);
        }
        $var = $new;
    } elseif (is_string($var)) {
        $var = PHPShopString::utf8_win1251($var);
    }
    return $var;
}

/**
 * Native json_encode function
 */
if (!function_exists('json_encode')) {

    function json_encode($data) {
        if (is_array($data) || is_object($data)) {
            $islist = is_array($data) && ( empty($data) || array_keys($data) === range(0, count($data) - 1) );

            if ($islist) {
                $json = '[' . implode(',', array_map('json_encode', $data)) . ']';
            } else {
                $items = Array();
                foreach ($data as $key => $value) {
                    $items[] = json_encode("$key") . ':' . json_encode($value);
                }
                $json = '{' . implode(',', $items) . '}';
            }
        } elseif (is_string($data)) {
            # Escape non-printable or Non-ASCII characters.
            # I also put the \\ character first, as suggested in comments on the 'addclashes' page.
            $string = '"' . addcslashes($data, "\\\"\n\r\t/" . chr(8) . chr(12)) . '"';
            $json = '';
            $len = strlen($string);
            # Convert UTF-8 to Hexadecimal Codepoints.
            for ($i = 0; $i < $len; $i++) {

                $char = $string[$i];
                $c1 = ord($char);

                # Single byte;
                if ($c1 < 128) {
                    $json .= ($c1 > 31) ? $char : sprintf("\\u%04x", $c1);
                    continue;
                }

                # Double byte
                $c2 = ord($string[++$i]);
                if (($c1 & 32) === 0) {
                    $json .= sprintf("\\u%04x", ($c1 - 192) * 64 + $c2 - 128);
                    continue;
                }

                # Triple
                $c3 = ord($string[++$i]);
                if (($c1 & 16) === 0) {
                    $json .= sprintf("\\u%04x", (($c1 - 224) << 12) + (($c2 - 128) << 6) + ($c3 - 128));
                    continue;
                }

                # Quadruple
                $c4 = ord($string[++$i]);
                if (($c1 & 8 ) === 0) {
                    $u = (($c1 & 15) << 2) + (($c2 >> 4) & 3) - 1;

                    $w1 = (54 << 10) + ($u << 6) + (($c2 & 15) << 2) + (($c3 >> 4) & 3);
                    $w2 = (55 << 10) + (($c3 & 15) << 6) + ($c4 - 128);
                    $json .= sprintf("\\u%04x\\u%04x", $w1, $w2);
                }
            }
        } else {
            # int, floats, bools, null
            $json = strtolower(var_export($data, true));
        }
        return $json;
    }

}
?>