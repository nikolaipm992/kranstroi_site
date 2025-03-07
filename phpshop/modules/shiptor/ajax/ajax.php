<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("order");

$PHPShopBase->chekAdmin();

if(isset($_REQUEST['operation']) && strlen($_REQUEST['operation']) > 2) {
    $result = array();
    try {
        switch ($_REQUEST['operation']) {
            case 'changePaymentStatus':
                $order = new PHPShopOrderFunction((int) $_REQUEST['orderId']);
                if(!empty($order->objRow)) {
                    $order->changePaymentStatus((int) $_REQUEST['value']);
                }
                $result['success'] = true;
                break;
        }
    } catch (\Exception $exception) {
        $result = array('success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage()));
    }
} else {
    $result = array('success' => false, 'error' => PHPShopString::win_utf8('Не найден параметр operation'));
}

echo (json_encode($result)); exit;