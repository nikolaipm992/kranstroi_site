<?php

/**
 * ���������� �������� ������
 * @author PHPShop Software
 * @version 2.1
 * @package PHPShopParser
 */
class PHPShopParser {

    /**
     * �������� ������� �� ����������� ����������
     * @param string $path ���� � ����� �������
     * @param string $value ���������� �������������
     * @return boolean 
     */
    static function check($path, $value) {
        $string = null;
        $path = $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $path;
        if (file_exists($path))
            $string = @file_get_contents($path);
        else
            echo "Error Tmp File: $path";
        if (stristr($string, '@' . $value . '@'))
            return true;
    }

    /**
     * ��������  ����� ������� �� ����������� � ��� ����� �������
     * @param string $path ���� � ����� �������
     * @return boolean 
     */
    static function checkFile($path, $mod = false) {
        if (!$mod)
            $path = $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $path;
        if (file_exists($path))
            return true;
        else
            return false;
    }

    static function replacedir($string) {
        $replaces = array(
            "/(\"|\'|=)images\//i" => "\\1" . $GLOBALS['SysValue']['dir']['dir'] . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/images/",
            "/!images!\//i" => "images/",
            "/java\//i" => "/java/",
            "/css\//i" => "/css/",
            "/phpshop\//i" => "/phpshop/",
            "/\/id\//i" => $GLOBALS['SysValue']['dir']['dir'] . "/id/",
        );
        return $string = preg_replace(array_keys($replaces), array_values($replaces), $string);
    }

    /**
     * ��������� ����� �������, ������� ����������
     * @param string $path ���� � ����� �������
     * @param bool $return ����� ������ ���������� ��� �������� ����������
     * @param bool $replace ����� ������ 
     * @param bool $check_template ����� ����� � �������
     * @return string
     */
    static function file($path, $return = false, $replace = true, $check_template = false) {

        $string = null;

        // ����� ������� ������ � �������� �������
        if ($check_template) {

            $path_template = str_replace('./phpshop', $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $_SESSION['skin'], $path);
            if (is_file($path_template))
                $path = $path_template;
        }



        if (is_file($path))
            $string = @file_get_contents($path);
        else
            echo "Error Tpl File: $path";

        if (!empty($_SESSION['skin']))
            $skin = $_SESSION['skin'];
        else
            $skin = null;

        $replaces = array(
            "/(\"|\'|=)images\//i" => "\\1" . $GLOBALS['SysValue']['dir']['dir'] . $GLOBALS['SysValue']['dir']['templates'] . chr(47) . $skin . "/images/",
            "/!images!\//i" => "images/",
            "/java\//i" => "/java/",
            "/phpshop\//i" => "/phpshop/",
        );

        $string = preg_replace_callback("/(@php)(.*)(php@)/sU", "phpshopparserevalstr", $string);
        $string = preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'PHPShopParser::SysValueReturn', $string);
        $string = preg_replace_callback("/({)([�-��-߸�0-9_ ,.\"\-\/]+)(})/", "PHPShopParser::locale", $string);

        // ������ ����� �����������
        //writeLangFile();

        if (!empty($replace)) {

            // ���������������� ������ � �������
            if (is_array($replace))
                $string = str_replace(array_keys($replace), array_values($replace), $string);

            $string = preg_replace(array_keys($replaces), array_values($replaces), $string);
        }

        if (!empty($return))
            return $string;
        else
            echo $string;
    }

    /**
     * �������� ��������� ����������
     * @param string $name ���
     * @param mixed $value ��������
     * @param bool $flag [1] - ��������, [0] - ����������
     */
    static function set($name, $value, $flag = false) {
        if ($flag)
            $GLOBALS['SysValue']['other'][$name] .= $value;
        else
            $GLOBALS['SysValue']['other'][$name] = $value;
    }

    /**
     * ������ ��������� ����������
     * @param string $name
     * @return string
     */
    static function get($name) {
        return $GLOBALS['SysValue']['other'][$name];
    }

    static function SysValueReturn($m) {
        global $SysValue;
        if (isset($SysValue["other"][$m[1]]))
            return $SysValue["other"][$m[1]];
    }

    static function locale($str) {
        if (str_replace(" ", "", $str[0]) == "{}")
            return "{}";
        else
            return __($str[2]);
    }

}

// ��������� php �����
function phpshopparserevalstr($str) {
    ob_start();
    if (!empty($str[2]) and eval(stripslashes($str[2])) !== NULL) {
        echo ('<div class="alert alert-danger"><h4>� ������� ���������� ������ ���������� PHP</h4>');
        echo ('��� ���������� ������:');
        echo ('<pre>');
        echo ($str[2]);
        echo ('</pre></div>');
        return ob_get_clean();
    }
    return ob_get_clean();
}

/**
 * ���������� �������� CSS
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopParser
 */
class PHPShopCssParser {

    var $file;
    var $css_array;

    function __construct($file) {
        $this->file = $file;
    }

    function parse() {
        if (file_exists($this->file)) {
            $css = file_get_contents($this->file);
            preg_match_all('/(?ims)([a-z0-9\s\.\:#_\-@,>]+)\{([^\}]*)\}/', $css, $arr);
            $result = array();
            foreach ($arr[0] as $i => $x) {
                $selector = trim($arr[1][$i]);
                $rules = explode(';', trim($arr[2][$i]));
                $rules_arr = array();
                foreach ($rules as $strRule) {
                    if (!empty($strRule)) {
                        $rule = explode(":", $strRule);

                        if (!empty($rule[1]))
                            $rules_arr[trim($rule[0])] = trim($rule[1]);
                    }
                }

                $result[$selector] = $rules_arr;
            }

            $this->css_array = $result;
        }
        return $this->css_array;
    }

    function getParam($element, $param) {
        if (!empty($this->css_array[$element][$param]))
            return $this->css_array[$element][$param];
    }

    function setParam($element, $param, $value, $add = ' !important') {

        switch ($param) {

            // ������
            case "filter":
                $filters = array('filter', '-webkit-filter', '-ms-filter', '-o-filter', '-moz-filter');
                foreach ($filters as $set) {
                    $this->css_array[$element][$set] = 'hue-rotate(' . $value . 'deg)' . $add;
                }
                $this->css_array[$element]['-editor-filter'] = $value;

                break;

            // ���������
            default: $this->css_array[$element][$param] = $value . $add;
        }
    }

    function compile() {
        $css = null;
        if (is_array($this->css_array))
            foreach ($this->css_array as $k => $v) {
                $css .= '
' . $k . '{
';
                if (is_array($v))
                    foreach ($v as $name => $rule)
                        $css .= $name . ':' . $rule . ';
';

                $css .= '}';
            }
        return $css;
    }

}

/**
 * ������ �������� ������� � ����� ���������� �� �����
 * @package PHPShopParser
 * @param string $TemplateName ��� ����� �������
 * @return string
 */
function ParseTemplate($TemplateName) {
    global $SysValue;

    $file = tmpGetFile($SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $TemplateName);
    $string = Parser($file);

    // �������� ����
    $path_parts = pathinfo($_SERVER['PHP_SELF']);
    if (getenv("COMSPEC"))
        $dirSlesh = "\\";
    else
        $dirSlesh = "/";
    $root = $path_parts['dirname'] . "/";
    if ($path_parts['dirname'] != $dirSlesh) {
        $replaces = array(
            "/(\"|\'|=)images\//i" => "\\1" . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/images/",
            "/!images!\//i" => "images/",
            "/\/favicon.ico/i" => $root . "favicon.ico",
            "/java\//i" => $root . "java/",
            "/css\//i" => "/css/",
            "/phpshop\//i" => $root . "phpshop/",
            "/\/order\//i" => $root . "order/",
            "/\/done\//i" => $root . "done/",
            "/\/print\//i" => $root . "print/",
            "/\/links\//i" => $root . "links/",
            "/\/opros\//i" => $root . "opros/",
            "/\/page\//i" => $root . "page/",
            "/\/news\//i" => $root . "news/",
            "/\/gbook\//i" => $root . "gbook/",
            "/\/users\//i" => $root . "users/",
            "/\/clients\//i" => $root . "clients/",
            "/\/price\//i" => $root . "price/",
            "/\/pricemail\//i" => $root . "pricemail/",
            "/\/compare\//i" => $root . "compare/",
            "/\/wishlist\//i" => $root . "wishlist/",
            "/\/shop\/CID/i" => $root . "shop/CID",
            "/\/shop\/UID/i" => $root . "shop/UID",
            "/\/search\//i" => $root . "search/",
            "/\"\/\"/i" => $root,
            "/\/notice\//i" => $root . "notice/",
            "/\/map\//i" => $root . "map/",
            "/\/success\//i" => $root . "success/",
            "/\/fail\//i" => $root . "fail/",
            "/\/rss\//i" => $root . "rss/",
            "/\/newtip\//i" => $root . "newtip/",
            "/\/spec\//i" => $root . "spec/",
            "/\/forma\//i" => $root . "forma/",
            "/\/newprice\//i" => $root . "newprice/",
            "/\/selection\//i" => $root . "selection/",
        );
    } else {
        $replaces = array(
            "/(\"|\'|=)images\//i" => "\\1" . $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . "/images/",
            "/!images!\//i" => "images/",
            "/java\//i" => "/java/",
            "/css\//i" => "/css/",
            "/phpshop\//i" => "/phpshop/",
        );
    }
    echo preg_replace(array_keys($replaces), array_values($replaces), $string);
}

/**
 * ������ ��������������� ������� � ������� ����������
 * @package PHPShopParser
 * @param string $TemplateName ��� ����� �������
 * @param bool $mod ������ ��� ������
 * @return string
 */
function ParseTemplateReturn($TemplateName, $mod = false, $debug = false) {
    global $SysValue;

    if ($mod)
        $file = tmpGetFile($TemplateName);
    else
        $file = tmpGetFile($SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'] . chr(47) . $TemplateName);
    $dis = Parser($file);

    $add = ' id="data-source" class="data-source" contenteditable="false" data-toggle="tooltip" data-placement="auto" data-source="' . $TemplateName . '" title="' . __('��������') . ' [Ctrl + &crarr;]" ';


    if ($debug and ! empty($_COOKIE['debug_template'])) {
        if (strstr($dis, '<li') or strstr($dis, 'class="product-col"'))
            $result = str_replace(array('<li', 'class="product-col"'), array('<li' . $add, 'class="product-col"' . $add), $dis);
        elseif (strstr($dis, 'product-block-wrapper-fix'))
            $result = str_replace(array('product-block-wrapper-fix"'), array('product-block-wrapper-fix" ' . $add), $dis);
        else
            $result = '<div ' . $add . '>' . $dis . '</div>';
    } else
        $result = $dis;

    return $result;
}

// ��������� PHP �����
function evalstr($str) {
    ob_start();
    if (parser_function_guard == 'true') {
        if (!allowedFunctions($str[2]))
            return ob_get_clean();
    }
    if (eval(stripslashes($str[2])) !== NULL) {
        echo ('<div class="alert alert-danger"><h4>� ������� ���������� ������ ���������� PHP</h4>');
        echo ('��� ���������� ������:');
        echo ('<pre>');
        echo ($str[2]);
        echo ('</pre></div>');
        return ob_get_clean();
    }
    return ob_get_clean();
}

// ��������� PHP �����, ������ ����������� �������
function allowedFunctions($str) {
    $Functions = array(
        'if',
        'else',
        'switch',
        'for',
        'foreach',
        'echo',
        'print',
        'print_r',
        'array',
        'isset',
        'empty',
        'chr',
        '__',
        'str_replace',
        '__hide',
        'empty'
    );

    $allowFunctions = array_merge($Functions, explode(',', parser_function_allowed));
    preg_match_all('/\s*([A-Za-z0-9_$]+)\s*\(/isU', $str, $findedFunctions);
    $remElements = array_diff($findedFunctions[1], $allowFunctions);

    $denyFunctions = explode(',', parser_function_deny);
    foreach ($denyFunctions as $deny)
        if (stristr($str, $deny))
            $remElements[] = $deny;

    if (count($remElements) > 0) {
        echo ('<div class="alert alert-warning"><h4>� ������� ���������� ����������� �������</h4>');
        echo ('������ ��������� ����������� �������:');
        echo ('<pre>');
        foreach ($remElements as $remElement) {
            echo ($remElement . '()');
        }
        echo ('</pre>');
        echo ('������ ����������� ������� (�������� ���� ������� ����� � phpshop/inc/config.ini ������ [function]):');
        echo ('<pre>');
        foreach ($allowFunctions as $allowFunction) {
            echo ($allowFunction . '()<br>');
        }
        echo ('</pre></div>');
        return false;
    } else {
        return true;
    }
}

// ������� �������
function SysValueReturn($m) {
    global $SysValue;
    return $SysValue["other"][$m[1]];
}

/**
 * ������ PHP ����� � ������
 * @package PHPShopParser
 * @param string $string �����
 * @param string $debug ��� ����� ������� ��� �������
 * @return string
 */
function Parser($string, $debug = false) {

    $dis = @preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'SysValueReturn', @preg_replace_callback("/(@php)(.*)(php@)/sU", "evalstr", str_replace('&#43;', '+', $string)));
    $dis = preg_replace_callback("/({)([�-��-߸�0-9_ ,.\"\-\/]+)(})/", "PHPShopParser::locale", $dis);

    $add = ' id="data-source" class="data-source" contenteditable="false"  data-toggle="tooltip" data-placement="auto" data-source="' . $debug . '" title="' . __('��������') . ' [Ctrl + &crarr;]" ';

    if ($debug and ! empty($_COOKIE['debug_template'])) {
        if (strstr($dis, '<li') or strstr($dis, 'class="product-col"'))
            $result = str_replace(array('<li', 'class="product-col"'), array('<li' . $add, 'class="product-col"' . $add), $dis);
        elseif (strstr($dis, 'product-block-wrapper-fix'))
            $result = str_replace(array('product-block-wrapper-fix"'), array('product-block-wrapper-fix" ' . $add), $dis);
        else
            $result = '<div ' . $add . '>' . $dis . '</div>';
    } else
        $result = $dis;


    return $result;
}

/**
 * ������ ����� �������
 * @param string $path ��� ����� �������
 * @return mixed
 */
function tmpGetFile($path) {
    if (strpos($path, '.tpl')) {
        if (is_file($path))
            $file = file_get_contents($path);
        if (empty($file))
            return false;
        return $file;
    } else
        return false;
}

/**
 * ������� ��������� �������������
 * @param string $name ��� ���������� ��� ���������
 * @param string $typ ��� ���������� ��� ��������� [ parser | cookie | session | global | requet]
 */
function __hide($name, $type = 'parser', $class = 'hide d-none') {
    if ($type == 'parser' and empty($GLOBALS['SysValue']['other'][$name]))
        echo $class;
    else if ($type == 'cookie' and ! empty($_COOKIE[$name]))
        echo $class;
    else if ($type == 'session' and ! empty($_SESSION[$name]))
        echo $class;
    else if ($type == 'global' and ! empty($GLOBALS[$name]))
        echo $class;
    else if ($type == 'requet' and ! empty($_REQUEST[$name]))
        echo $class;
    else if ($type == 'isset' and isset($GLOBALS['SysValue']['other'][$name]))
        echo $class;
    else if ($type == 'empty' and ! empty($GLOBALS['SysValue']['other'][$name]))
        echo $class;
    else if ($type == 'class' and isset($GLOBALS['SysValue']['other'][$name]))
        echo $class;
    else if ($type == 'zero' and empty((int) $GLOBALS['SysValue']['other'][$name]))
        echo $class;
}

/**
 * ������ ���������� �������������
 * @param string $name
 * @return string
 */
function __get($name) {
    return PHPShopParser::get($name);
}

/**
 * �������� ���������� �������������
 * @param string $name ���
 * @param mixed $value ��������
 * @param bool $flag [1] - ��������, [0] - ����������
 */
function __set($name, $value, $flag = false) {
    return PHPShopParser::set($name, $value, $flag);
}