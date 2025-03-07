<?php

include_once("phpshop/core/shop.core.php");

class PHPShopSeoProCore extends PHPShopShop {

    function __construct() {

        // �������
        $this->debug = false;

        $this->path = '/shop';

        // ������ �������
        $this->action = array("nav" => "index");

        parent::__construct();
    }

    function index() {
        parent::UID();
    }

    function image_gallery($row) {

        // �������� ������
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function option_select($row) {

        // �������� ������
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function comment_rate($row, $type = "") {

        // �������� ������
        if ($this->setHook(__CLASS__, __FUNCTION__, array("row" => $row, "type" => "$type")))
            return true;
        $this->doLoadFunction(__CLASS__, __FUNCTION__, array("row" => $row, "type" => "$type"),'shop');
    }

    function rating($row) {

        // �������� ������
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function set_meta($row) {
        $this->PHPShopNav->objNav['nav'] = 'UID';
        $GLOBALS['SysValue']['nav']['nav'] = 'UID';
        return parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

    function sort_table($row) {

        // �������� ������
        if ($this->setHook(get_parent_class(), __FUNCTION__, $row))
            return true;

        parent::doLoadFunction(__CLASS__, __FUNCTION__, $row, 'shop');
    }

}

class PHPShopId extends PHPShopCore {

    function __construct() {

        // ��������� ������� ������
        //$GLOBALS['PHPShopSeoPro']->catCacheToMemory();
        $GLOBALS['PHPShopSeoPro']->setRout(2);

        $PHPShopSeoProCore = new PHPShopSeoProCore();
        $PHPShopSeoProCore->loadAction();
    }

}

?>