<?php

/**
 * ������������ ����� �������� ���������
 * ������� ������������� ��������� � ����� phpshop/inc/
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopClass
 */
class PHPShopElements {

    /**
     * @var string ��� ��
     */
    var $objBase;
    var $objPath;

    /**
     * @var bool ����� �������
     */
    var $debug = false;
    var $template_debug = false;

    /**
     * @var bool �����������
     */
    var $cache = false;

    /**
     * @var bool �������������� ����� ����
     */
    var $cache_format = array();

    /**
     * @var bool ������������� ������
     */
    var $memory = false;

    /**
     * @var array ������ �������
     */
    var $action = array();

    /**
     * @var string ������� ���� ������� �������
     */
    var $action_prefix = null;

    /**
     * @var array ������ ����������� ��������
     */
    var $disp_format = array();

    /**
     * @var string ��������� ������ �������
     */
    var $Disp;

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopSystem, $PHPShopNav, $PHPShopModules;

        if ($this->objBase) {
            $this->PHPShopOrm = new PHPShopOrm($this->objBase);

            $this->PHPShopOrm->cache_format = $this->cache_format;
            $this->PHPShopOrm->cache = $this->cache;
            $this->PHPShopOrm->debug = $this->debug;
        }
        $this->SysValue = &$GLOBALS['SysValue'];
        $this->PHPShopSystem = &$PHPShopSystem;
        $this->PHPShopNav = &$PHPShopNav;
        $this->LoadItems = &$GLOBALS['LoadItems'];
        $this->PHPShopModules = &$PHPShopModules;
        $this->webp = $this->PHPShopSystem->getSerilizeParam('admoption.image_webp');
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * ���������� � ���������� ������ ����� ������
     * @param string $template ��� ������� ��� ��������
     */
    function addToTemplate($template) {
        $this->Disp .= ParseTemplateReturn($template);
    }

    /**
     * ���������� � ���������� ������
     * @param sting $content �������
     */
    function add($content) {
        $this->Disp .= $content;
    }

    /**
     * ������� �������
     * @param string $template ��� �������
     * @param bool $mod ������������� ������� � ������
     * @return string
     */
    function parseTemplate($template, $mod = false) {
        return ParseTemplateReturn($template, $mod, $this->template_debug);
    }

    /**
     * �������� ��������� ���������� ��� ��������
     * @param string $name ���
     * @param mixed $value ��������
     * @param bool $flag [1] - ��������, [0] - ����������
     */
    function set($name, $value, $flag = false) {
        if ($flag)
            @$this->SysValue['other'][$name] .= $value;
        else
            $this->SysValue['other'][$name] = $value;
    }

    /**
     * ������ ��������� ����������
     * @param string $name
     * @return string
     */
    function get($name) {
        if (isset($this->SysValue['other'][$name]))
            return $this->SysValue['other'][$name];
    }

    /**
     * ������ ��������� ����������
     * @param string $param ������.��� ����������
     * @return mixed
     */
    function getValue($param) {
        $param = explode(".", $param);
        if (is_array($param) and isset($this->SysValue[$param[0]][$param[1]]))
            return $this->SysValue[$param[0]][$param[1]];
    }

    /**
     * ��������� ��������� ����������
     * @param string $param ������.��� ����������
     * @param mixed $value �������� ���������
     */
    function setValue($param, $value) {
        $param = explode(".", $param);
        if ($param[0] == "var")
            $param[0] = "other";
        $this->SysValue[$param[0]][$param[1]] = $value;
    }

    /**
     * ������ ���������� �� ����
     * @param string $param ������.��� ����������
     * @return string
     */
    function getValueCache($param) {
        return $this->LoadItems[$param];
    }

    /**
     * ������������� ���������� �� ���������� ���������� �������
     * @param string $method_name ��� �������
     * @param bool $flag ���������� ������ � ����������
     */
    function init($method_name, $flag = false) {

        if (!in_array($this->SysValue['nav']['path'], $this->disp_format)) {

            // ���� ���������� �� ���������� �������
            if (!empty($flag) and $this->isAction($method_name))
                $this->set($method_name, call_user_func(array(&$this, $method_name)), true);

            elseif (empty($this->SysValue['other'][$method_name])) {
                if ($this->isAction($method_name))
                    $this->set($method_name, call_user_func(array(&$this, $method_name)));
                elseif ($this->isAction("index"))
                    $this->set($method_name, call_user_func(array(&$this, 'index')));
                else
                    $this->setError("index", "����� �� ����������");
            }
        }
    }

    /**
     * �������� ������
     * @param string $method_name ��� ������
     * @return bool
     */
    function isAction($method_name) {
        if (method_exists($this, $method_name))
            return true;
    }

    /**
     * ��������� �� ������
     * @param string $name ��� �������
     * @param string $action ���������
     */
    function setError($name, $action) {
        echo '<p><span style="color:red">������ ����������� �������: </span> <strong>' . __CLASS__ . '->' . $name . '()</strong>
	 <br><em>' . $action . '</em></p>';
    }

    /**
     * ����� ��������� ��������� �� ����� [config.ini]
     * @param string $str ���� ��������� �������
     * @return string
     */
    function lang($str) {
        if ($this->SysValue['lang'][$str])
            return $this->SysValue['lang'][$str];
        else
            return $str;
    }

    /**
     * ������ � ������
     * @param string $param ��� ��������� [catalog.param]
     * @param mixed $value ��������
     */
    function memory_set($param, $value) {
        if (!empty($this->memory)) {
            $param = explode(".", $param);
            $_SESSION['Memory'][__CLASS__][$param[0]][$param[1]] = $value;
            $_SESSION['Memory'][__CLASS__]['time'] = time();
        }
    }

    /**
     * ������� �� ������
     * @param string $param ��� ��������� [catalog.param]
     * @param bool $check �������� � �����
     * @return
     */
    function memory_get($param, $check = false) {
        $this->memory_clean();
        if (!empty($this->memory)) {
            $param = explode(".", $param);
            if (isset($_SESSION['Memory'][__CLASS__][$param[0]][$param[1]])) {
                if (!empty($check)) {
                    if (!empty($_SESSION['Memory'][__CLASS__][$param[0]][$param[1]]))
                        return true;
                } else
                    return $_SESSION['Memory'][__CLASS__][$param[0]][$param[1]];
            }
            elseif (!empty($check))
                return true;
        } else
            return false;
    }

    /**
     * ������ ������ �� �������
     * @param bool $clean_now �������������� ������
     */
    function memory_clean($clean_now = false) {
        if (!empty($_SESSION['Memory'])) {
            if (!empty($clean_now))
                unset($_SESSION['Memory'][__CLASS__]);
            elseif (@$_SESSION['Memory'][__CLASS__]['time'] < (time() - 60 * 10))
                unset($_SESSION['Memory'][__CLASS__]);
        }
    }

    /**
     * ���������� ������ ��������� ���������� POST � GET
     */
    function setAction($action) {

        if (!empty($action))
            $this->action = $action;

        if (is_array($this->action)) {
            foreach ($this->action as $k => $v) {

                switch ($k) {

                    // ����� POST
                    case("post"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if (!empty($_POST[$function]) and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // ���� ���� �����
                            if (!empty($_POST[$v]) and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }
                        break;

                    // ����� GET
                    case("get"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if (!empty($_GET[$function]) and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // ���� ���� �����
                            if (!empty($_GET[$v]) and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }

                        break;

                    // ����� NAME
                    case("name"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function)
                                if ($this->PHPShopNav->getName() == $function and $this->isAction($function))
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                        } else {
                            // ���� ���� �����
                            if ($this->PHPShopNav->getName() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }

                        break;


                    // ����� NAV
                    case("nav"):

                        // ���� ��������� �������
                        if (is_array($v)) {
                            foreach ($v as $function) {
                                if ($this->PHPShopNav->getNav() == $function and $this->isAction($function)) {
                                    return call_user_func(array(&$this, $this->action_prefix . $function));
                                    $call_user_func = true;
                                }
                            }
                        } else {
                            // ���� ���� �����
                            if ($this->PHPShopNav->getNav() == $v and $this->isAction($v))
                                return call_user_func(array(&$this, $this->action_prefix . $v));
                        }
                        break;
                }
            }
        } else
            $this->setError("action", "������ ��������� �������");
    }

    /**
     * ���������� ��������� ������� ���������� �������
     * @param string $class_name ��� ������
     * @param string $function_name ��� ������
     * @param mixed $data ������ ��� ���������
     * @param string $rout ������� ������ � ������� [END | START | MIDDLE], �� ��������� END
     * @return bool
     */
    function setHook($class_name, $function_name, $data = false, $rout = false) {
        if (!empty($this->PHPShopModules))
            return $this->PHPShopModules->setHookHandler($class_name, $function_name, array(&$this), $data, $rout);
    }

    /**
     * ���������� HTML ���������� �������
     * @param string $class_name ��� ������
     */
    function setHtmlOption($class_name) {

        if (!empty($GLOBALS['SysValue']['html'][strtolower($class_name)])) {

            $html = $GLOBALS['SysValue']['html'][strtolower($class_name)];

            // ���������� �����
            if (strstr($html, '-')) {
                $option = explode("-", $html);
                $html = $option[0];
                $this->cell = $option[1];
            }

            $this->cell_type = $html;
            //$this->cell=1;
            $this->product_grid = null;
        }
    }

    /**
     * ����������� ������� �� ������ ����
     * @param string $class_name ��� ������
     * @param string $function_name ��� �������
     * @param array $function_row ������ ������������� ������ �� �������
     * @param string $path ��� �������
     * @return mixed
     */
    function doLoadFunction($class_name, $function_name, $function_row = false, $path = false) {

        if (empty($path))
            $path = $GLOBALS['SysValue']['nav']['path'];

        $function_path = './phpshop/core/' . $path . '.core/' . $function_name . '.php';
        if (is_file($function_path)) {
            include_once($function_path);
            if (function_exists($function_name)) {
                return call_user_func_array($function_name, array(&$this, $function_row));
            }
        }
    }

    /**
     * ��������� webp
     * @param string $image ��� �����
     * @return string
     */
    function setImage($image) {
        global $_classPath;

        if (!empty($image)) {

            // �������������� webp -> jpg ��� iOS < 14
            if (PHPShopSecurity::getExt($image) == 'webp') {
                if (defined('isMobil') and defined('isIOS')) {

                    if (!class_exists('PHPThumb'))
                        include_once($_classPath . 'lib/thumb/phpthumb.php');

                    if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                        $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                        $thumb->setFormat('STRING');
                        $image = 'data:image/jpg;base64, ' . base64_encode($thumb->getImageAsString('webp'));
                    }
                }
            }
            // �������������� � webp
            elseif ($this->webp) {

                if (!class_exists('PHPThumb'))
                    include_once($_classPath . 'lib/thumb/phpthumb.php');

                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $image)) {
                    $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $image);
                    $thumb->setFormat('WEBP');
                    $image = 'data:image/webp;base64, ' . base64_encode($thumb->getImageAsString(PHPShopSecurity::getExt($image)));
                }
            }
        }

        return $image;
    }

}

?>