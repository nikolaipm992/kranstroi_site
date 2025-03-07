<?php

if (!defined("OBJENABLED"))
    define("OBJENABLED", dirname(__FILE__));

/**
 * ������������ ����� �������
 * @author PHPShop Software
 * @version 1.8
 * @package PHPShopClass
 */
class PHPShopObj {

    /**
     * �� ������� � ��
     * @var int 
     */
    var $objID;

    /**
     * ��� ��
     * @var string 
     */
    var $objBase;

    /**
     * ������ ������
     * @var array 
     */
    var $objRow;

    /**
     * ����� �������
     * @var bool 
     */
    var $debug = false;

    /**
     * �������� ���������
     * @var bool 
     */
    var $install = true;

    /**
     * ����� �����������
     * @var bool
     */
    var $cache = false;

    /**
     * �������������� ����
     * @var array
     */
    var $cache_format = array();

    /**
     * �����������
     * @param string $var ���� �������, �� ��������� id
     * @param array $import_data ������ ������� ������
     */
    function __construct($var = 'id', $import_data = null) {
        if (is_array($import_data))
            $this->objRow = $import_data;
        else
            $this->setRow($var);
    }

    /**
     * ������ � ��
     * @param string var ���� �������, �� ��������� id
     */
    function setRow($var) {
        $this->loadClass("orm");
        $this->PHPShopOrm = new PHPShopOrm($this->objBase);
        $this->PHPShopOrm->debug = $this->debug;
        $this->PHPShopOrm->cache = $this->cache;
        $this->PHPShopOrm->cache_format = $this->cache_format;
        $this->PHPShopOrm->install = $this->install;
        $this->objRow = $this->PHPShopOrm->select(array('*'), array($var => '="' . $this->objID . '"'), false, array('limit' => 1));
    }

    /**
     * �������� �������� � ����
     * @param array $value ������ ��������
     * @param string $var ���� �������, �� ��������� id
     * @param string $prefix ������� ����� � ����� [_new]
     */
    function updateParam($value, $var = 'id', $prefix = '_new'){
       return $this->PHPShopOrm->update($value, array($var => '="' . $this->objID . '"'), $prefix);
    }
    
   
    /**
     * ��������� ��������� �� �������
     * @param string $paramName ��� ����������
     * @param string $paramValue �������� ����������
     * @return bool
     */
    function ifValue($paramName, $paramValue = false) {
        if (empty($paramValue))
            $paramValue = 1;
        if (!empty($this->objRow[$paramName]))
            if ($this->objRow[$paramName] == $paramValue)
                return true;
    }

    /**
     * �������� ��� �������� ��������
     * @param string $param ��� ���������
     * @param mixed $value �������� ���������
     */
    function setParam($param, $value) {
        $this->objRow[$param] = $value;
    }

    /**
     * ������ ��������� �� ������� �� �����
     * @param string $paramName ����
     * @return mixed
     */
    function getParam($paramName) {
        if (!empty($this->objRow[$paramName]))
            return $this->objRow[$paramName];
    }

    /**
     * ������ ��������� �� ������� �� �����, ����� ������� getParam($paramName)
     * @param string $paramName ����
     * @return mixed
     */
    function getValue($paramName) {
        if (!empty($this->objRow[$paramName]))
            return $this->objRow[$paramName];
    }

    /**
     * ������ ������� �������� �������
     * @return array
     */
    function getArray() {
        return $this->objRow;
    }

    /**
     * �������� ������
     * @param mixed $class_name ��� ������ (������ � �������) �������� config.ini
     */
    static function loadClass($class) {

        if (!is_array($class)) {
            $class_name[] = $class;
        }
        else
            $class_name = $class;

        foreach ($class_name as $name) {
            $class_path = OBJENABLED . "/" . $name . ".class.php";
            if (file_exists($class_path))
                require_once($class_path);
            else
                echo "��� ����� " . $class_path;
        }
    }

    /**
     * ������ ������������������ ��������
     * @param string $paramName ��� ���������
     * @return string
     */
    function unserializeParam($paramName) {
        return unserialize($this->getParam($paramName));
    }

    /**
     * �������� ������ ������� ���� ��� ������������
     * @param string $class_name ��� ������, �������� config.ini
     */
    static function importCore($class_name) {
        global $_classPath;
        $class_path = $_classPath . '/core/' . $class_name . ".core.php";
        if (file_exists($class_path))
            require_once($class_path);
        else
            echo "��� ����� " . $class_path;
    }

}

/**
 *  ������������� ��������� phpshop/class
 */
function PHPShopAutoLoadClass($class_name) {
    global $_classPath;
    if (preg_match("/^[a-zA-Z0-9_\.]{2,20}$/", $class_name)) {
        $class_path = $_classPath . "class/" . strtolower(str_replace('PHPShop', '', $class_name)) . ".class.php";
        if (file_exists($class_path))
            require_once($class_path);
    }
}

if (function_exists('spl_autoload_register')) {
    spl_autoload_register('PHPShopAutoLoadClass');
}
?>