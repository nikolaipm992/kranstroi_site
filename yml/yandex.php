<?php
/**
 * Файл выгрузки для Яндекс Маркет
 * @author PHPShop Software
 * @version 4.2
 * @package PHPShopXML
 * @example ?retailcrm [bool] Выгрузка для RetailCRM
 * @example ?marketplace=cdek [bool] Выгрузка для СДЭК (упрощенный тип YML с использованием count)
 * @example ?marketplace=aliexpress [bool] Выгрузка для AliExpress (товары отмеченные для AliExpress)
 * @example ?marketplace=sbermarket [bool] Выгрузка для СберМаркет (товары отмеченные для СберМаркет)
 * @example ?getall [bool] Выгрузка всех товаров без учета флага YML. Выгрузка всех изображений.
 * @example ?from [bool] Метка в ссылки товара from
 * @example ?amount [bool] Добавление склада в тег amount для CRM
 * @example ?search [bool] Убрать подтипы из выгрузки (для Яндекс.Поиск по сайту)
 * @example ?utf [bool] Вывод в кодировке UTF-8
 * @example ?price [int] Колонка цен (2/3/4/5)
 * @example ?available [bool] Выводить только в наличии
 * @example ?image_source [bool]  Показывать исходные изображения _big
 * @example ?striptag [bool] Очистка html тегов  в описании
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
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("promotions");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("yml");

// Настройки
$PHPShopSystem = new PHPShopSystem();

// Мультибаза
$PHPShopBase->checkMultibase();

// Модули
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// YML
$PHPShopYml = new PHPShopYml();
header("HTTP/1.1 200");
header("Content-Type: application/xml; charset=" . $PHPShopYml->charset);
echo $PHPShopYml->compile();