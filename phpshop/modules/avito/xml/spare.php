<?php

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/avito/class/Xml/BaseAvitoXml.php");
include_once($_classPath . "modules/avito/class/Xml/AvitoSpare.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("promotions");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("lang");

// Массив валют
$PHPShopValutaArray = new PHPShopValutaArray();

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale'=>$_SESSION['lang'],'path'=>'shop'));

// Корзина
$PHPShopCart = new PHPShopCart();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

header("HTTP/1.1 200");
header("Content-Type: application/xml; charset=utf-8");
$Spare = new AvitoSpare(3);
ob_start();
$Spare->compile();
$xml = ob_get_clean();
echo str_replace(' x ','×',$xml);
