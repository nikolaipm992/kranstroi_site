<?php

/**
 * ��������� �������
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("compare");
PHPShopObj::loadClass("lang");

// ������
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// �������� ������.
$xid = intval($_REQUEST['xid']);

// �������� �������� ���������� ������� ��� ���������
if (is_array($_SESSION['compare']))
    $compar = count($_SESSION['compare']);
else
    $compar = 0;

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// ����� ���������
$PHPShopCompare = new PHPShopCompare();

// �������� �����
$PHPShopCompare->add($xid);

if ($compar == $PHPShopCompare->getNum()) {
    $same = '1';
} else {
    $same = '0';
}

// ��������� ���������
$_RESULT = array(
    "num" => $PHPShopCompare->getNum(),
    "same" => $same,
    "message" => $PHPShopCompare->getMessage(),
    "success" => 1
);


$_RESULT['message'] = PHPShopString::win_utf8($_RESULT['message']);
echo json_encode($_RESULT);
?>