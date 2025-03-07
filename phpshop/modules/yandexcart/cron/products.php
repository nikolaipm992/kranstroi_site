<?php

session_start();

// ���������
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
    $_classPath = "../../../";

include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// �����������
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("������ �����������!");


// ��������� ������
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$count = 0;

include_once dirname(__FILE__) . '/../class/YandexMarket.php';
$Market = new YandexMarket();

// �������� 1
$products = $PHPShopOrm->getList(['*'], ['yml' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);
if (is_array($products) and count($products) > 0) {

    // �����
    $Market->updateStocks($products, false);

    // ����
    $Market->updatePrices($products, false);

    $count += count($products);
}

// �������� 2
$products = $PHPShopOrm->getList(['*'], ['yml_2' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);
if (is_array($products) and count($products) > 0) {

    // �����
    $Market->updateStocks($products, 2);

    // ����
    $Market->updatePrices($products, 2);

    $count += count($products);
}

// �������� 3
$products = $PHPShopOrm->getList(['*'], ['yml_3' => "='1'"], ['order' => 'datas desc'], ['limit' => 1000]);
if (is_array($products) and count($products) > 0) {

    // �����
    $Market->updateStocks($products, 3);

    // ����
    $Market->updatePrices($products, 3);

    $count += count($products);
}

echo "������ ���������� ��� " . $count . " �������";