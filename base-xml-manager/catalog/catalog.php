<?php

/**
 * Синхронизация с Shop4Partner
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.3
 */

$_classPath = "../../phpshop/";
include($_classPath . "class/obj.class.php");
include($_classPath . "lib/phpass/passwordhash.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("basexml");

// Подключаем БД
$PHPShopBase = new PHPShopBase("../../phpshop/inc/config.ini");

// Пример запроса
/*
$_POST['sql'] = '<?xml version="1.0" encoding="windows-1251"?>
<phpshop><sql><from>table_name2</from>
<method>select</method>
<vars>name,id,items,price,ed_izm,pic_small,category,newtip,spec,baseinputvaluta,price2</vars>
<where>category=55 and enabled="1"</where>
<order>num</order><limit>1000</limit></sql></phpshop>';
$_POST['log'] = "admin";
$_POST['pas'] = '123456';
*/

class PHPShopHtmlCatalog extends PHPShopBaseXml {

    function __construct() {
        $this->debug = false;
        $this->true_method = array('select', 'option');
        $this->true_from = array('table_name', 'table_name2', 'table_name3', 'table_name24', '');
        $this->log = $_POST['log'];
        $this->pas = $_POST['pas'];
        parent::__construct();
    }

    function admin() {
        $PHPShopOrm = new PHPShopOrm($this->PHPShopBase->getParam('base.table_name19'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('login,password'), array('enabled' => "='1'"), false, array('limit'=>100));
        $hasher = new PasswordHash(8, false);
        if (is_array($data)) {
            foreach ($data as $v)
                if ($_POST['log'] == $v['login']) {
                    if ($hasher->CheckPassword(base64_decode($_POST['pas']), $v['password'])) {
                        return true;
                    }
                }
        }
    }

}

new PHPShopHtmlCatalog();
?>