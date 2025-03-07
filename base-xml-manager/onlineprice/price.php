<?php

/**
 * Синхронизация с Excel
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.3
 */

include("../../phpshop/class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("basexml");

// Подключаем БД
$PHPShopBase=new PHPShopBase("../../phpshop/inc/config.ini");

/*
// Пример запроса
$_POST['sql_test']='<?xml version="1.0" encoding="windows-1251"?>
<phpshop><sql><from>table_name2</from>
<method>select</method>
<vars>name,id,items,price,ed_izm,pic_small,category,newtip,spec,baseinputvaluta,price2</vars>
<where>category=55 and enabled="1"</where>
<order>num</order><limit>1000</limit></sql></phpshop>';

*/

class PHPShopPrice extends PHPShopBaseXml {
    
    function __construct() {
        $this->debug=false;
        $this->true_method=array('select','option');
        $this->true_from=array('table_name','table_name2','table_name3','table_name24','');
        $this->log='phpshop';
        $this->pas='b244ba41f5309a6ef2405a4ab4dd031d';

        $this->option['price_col']=2;
        $this->option['color_new']='33ff99';
        $this->option['color_spec']='ffff33';
        $this->option['dir']='/';
        $this->option['img_new']='http://'.$_SERVER['SERVER_NAME'].'/base-xml-manager/onlineprice/img/new.png';
        $this->option['img_spec']='http://'.$_SERVER['SERVER_NAME'].'/base-xml-manager/onlineprice/img/spec.png';
        $this->option['sklad']=true;
        $this->option['ed_izm']=true;
        $this->option['price']="Розница";
        $this->option['price2']="Цена 2";
        $this->option['price3']="Цена 3";
        $this->option['price4']="Цена 5";
        $this->option['price5']="Цена 7";
        $this->option['pict']=true;
        $this->option['paginator_off']=false;
        parent::__construct();
    }
    
    function admin() {
       if($_POST['log']==$this->log and md5($_POST['pas'])==$this->pas)
            return true;
    }
    
    function option() {
        $this->data[]=$this->option;
    }
}

new PHPShopPrice();
?>