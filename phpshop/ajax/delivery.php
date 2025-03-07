<?php

/**
 * ��������
 * @package PHPShopAjaxElements
 */
session_start();
$_classPath = "../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(["base", "order", "modules", "lang", "delivery", "cart"]);

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);

// ����������
$PHPShopBase->checkMultibase("../../");

// ������� ��� ������
$PHPShopSystem = new PHPShopSystem();
$PHPShopOrder = new PHPShopOrderFunction();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));

// ������
$PHPShopModules = new PHPShopModules($_classPath . "modules/",false,"../../");

// ���������� ���������� ��������
require_once $_classPath . "core/order.core/delivery.php";

$PHPShopDelivery = new PHPShopDelivery((int) $_REQUEST['xid']);

$GetDeliveryPrice = $PHPShopDelivery->getPrice($_REQUEST['sum'], floatval($_REQUEST['wsum']));
$GetDeliveryPrice *= $PHPShopSystem->getDefaultValutaKurs(true);

$PHPShopCart = new PHPShopCart();

// ����� ������ �� �����
$totalsumma = (float) $PHPShopOrder->returnSumma($PHPShopCart->getSumPromo(true));

// ����� ������ ��� �����
$totalsumma += (float) $PHPShopOrder->returnSumma($PHPShopCart->getSumWithoutPromo(true), $PHPShopOrder->ChekDiscount((int) $_REQUEST['sum']), '', (float) $GetDeliveryPrice);

// ����� � ������ �������
$totalsumma -= (float) (new PHPShopBonus((int) $_SESSION['UsersId']))->getUserBonus($totalsumma);

$deliveryArr = delivery(false, intval($_REQUEST['xid']), $_REQUEST['sum']);
$dellist = $deliveryArr['dellist'];
$adresList = $deliveryArr['adresList'];
$format = $PHPShopSystem->getSerilizeParam("admoption.price_znak");

// ���������
$_RESULT = array(
    'delivery' => number_format($GetDeliveryPrice, $format, '.', ' '),
    'dellist' => $dellist,
    'discount' => $PHPShopOrder->ChekDiscount($_REQUEST['sum']),
    'adresList' => $adresList,
    'free_delivery' => (int) $PHPShopDelivery->isFree($_REQUEST['sum']),
    'total' => number_format($totalsumma, $PHPShopOrder->format, '.', ' '),
    'wsum' => floatval($_REQUEST['wsum']),
    'success' => 1
);

// �������� ������ � ������ �������
$hook = $PHPShopModules->setHookHandler('delivery', 'delivery', false, array($_RESULT, $_REQUEST['xid']));
if (is_array($hook))
    $_RESULT = $hook;


$_RESULT['dellist'] = PHPShopString::win_utf8($_RESULT['dellist']);
$_RESULT['adresList'] = PHPShopString::win_utf8($_RESULT['adresList']);

echo json_encode($_RESULT);
?>