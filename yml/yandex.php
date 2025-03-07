<?php
/**
 * ���� �������� ��� ������ ������
 * @author PHPShop Software
 * @version 4.2
 * @package PHPShopXML
 * @example ?retailcrm [bool] �������� ��� RetailCRM
 * @example ?marketplace=cdek [bool] �������� ��� ���� (���������� ��� YML � �������������� count)
 * @example ?marketplace=aliexpress [bool] �������� ��� AliExpress (������ ���������� ��� AliExpress)
 * @example ?marketplace=sbermarket [bool] �������� ��� ���������� (������ ���������� ��� ����������)
 * @example ?getall [bool] �������� ���� ������� ��� ����� ����� YML. �������� ���� �����������.
 * @example ?from [bool] ����� � ������ ������ from
 * @example ?amount [bool] ���������� ������ � ��� amount ��� CRM
 * @example ?search [bool] ������ ������� �� �������� (��� ������.����� �� �����)
 * @example ?utf [bool] ����� � ��������� UTF-8
 * @example ?price [int] ������� ��� (2/3/4/5)
 * @example ?available [bool] �������� ������ � �������
 * @example ?image_source [bool]  ���������� �������� ����������� _big
 * @example ?striptag [bool] ������� html �����  � ��������
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

// ���������
$PHPShopSystem = new PHPShopSystem();

// ����������
$PHPShopBase->checkMultibase();

// ������
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// YML
$PHPShopYml = new PHPShopYml();
header("HTTP/1.1 200");
header("Content-Type: application/xml; charset=" . $PHPShopYml->charset);
echo $PHPShopYml->compile();