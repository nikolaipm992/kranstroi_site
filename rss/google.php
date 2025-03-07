<?php

/**
 * Файл выгрузки для Google Merchant
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopXML
 * @example ?ssl [bool] SSL
 * @example ?getall [bool] Выгрузка всех товаров без учета флага YML
 * @example ?from [bool] Метка в ссылки товара from
 */
$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
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
PHPShopObj::loadClass("promotions");
PHPShopObj::loadClass("rssgoogle");

// Настройки
$PHPShopSystem = new PHPShopSystem();

// Мультибаза
$PHPShopBase->checkMultibase();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

$PHPShopRssGoogle= new PHPShopRssGoogle();
header("HTTP/1.1 200");
header("Content-Type: application/xml; charset=utf-8");
echo $PHPShopRssGoogle->compile();