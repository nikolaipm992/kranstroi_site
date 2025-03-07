<?php

/**
 * �������
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("user");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");

$_REQUEST['addname'] = PHPShopString::utf8_win1251($_REQUEST['addname']);

// ����������
$PHPShopBase->checkMultibase("../../");

// ������ �����
$PHPShopValutaArray = new PHPShopValutaArray();

// ��������� ���������
$PHPShopSystem = new PHPShopSystem();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// �������
$PHPShopCart = new PHPShopCart();

// ������
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// �������� �����
if (PHPShopSecurity::true_param($_REQUEST['xid'], $_REQUEST['num'])) {
    $add = $PHPShopCart->add($_REQUEST['xid'], $_REQUEST['num'], $_REQUEST['xxid']);
}

// ���� ���������� �������
setcookie("cart_update_time", time(), 0, "/", $_SERVER['SERVER_NAME'], 0);

// ��������� ���������
$_RESULT = array(
    "num" => $PHPShopCart->getNum(),
    "sum" => $PHPShopCart->getSum(true, ' '),
    "message" => $PHPShopCart->getMessage(),
    "success" => $add
);

// �������� ������ � ������ �������
$hook = $PHPShopModules->setHookHandler('cartload', 'cartload', false, array($_RESULT, $_REQUEST, $PHPShopCart));
if (is_array($hook))
    $_RESULT = $hook;

// JSON 
$_RESULT['message'] = PHPShopString::win_utf8($_RESULT['message']);
echo json_encode($_RESULT);
?>