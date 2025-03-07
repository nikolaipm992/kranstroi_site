<?php

if (!defined("OBJENABLED")) {
    require_once(dirname(__FILE__) . "/obj.class.php");
}
require_once(dirname(__FILE__) . "/array.class.php");

/**
 * ���������� �����
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopObj
 */
class PHPShopValuta extends PHPShopObj {

    /**
     * �����������
     * @param int $objID �� ������
     */
    function __construct($objID) {
        $this->objID = $objID;
        $this->cache = true;
        $this->objBase = $GLOBALS['SysValue']['base']['currency'];
        parent::__construct();
    }

    /**
     * ����� ����� ������
     * @return string
     */
    function getName() {
        return parent::getParam("name");
    }

    /**
     * ����� ISO ������
     * @return string
     */
    function getIso() {
        return parent::getParam("iso");
    }

    /**
     * ����� ����� ������
     * @return float
     */
    function getKurs() {
        return parent::getParam("kurs");
    }

    /**
     * ����� ���� ������
     * @return string
     */
    function getCode() {
        return parent::getParam("code");
    }

    /**
     * ������ ���� �������� �� ����� ISO
     * @param bool $key_iso ��������� ������� � ������� ISO
     * @return array
     */
    static function getAll($key_iso = false) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);
        $PHPShopOrm->cache = true;
        $data = $PHPShopOrm->select(array('*'), false, false, array('limit' => 100));
        if (is_array($data))
            foreach ($data as $row) {
                $id = $row['id'];
                $iso = $row['iso'];
                $array[$iso] = $id;
            }

        if (empty($key_iso))
            $result = $data;
        else
            $result = $array;

        return $result;
    }

    function getArray() {
        return self::getAll();
    }

}

/**
 * ������ ������ �� �������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopArray
 */
class PHPShopValutaArray extends PHPShopArray {

    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['currency'];
        $this->objSQL = array('enabled' => "='1'");
        parent::__construct('id', "name", 'code', 'iso', 'kurs');
    }

}

?>