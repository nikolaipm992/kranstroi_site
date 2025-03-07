<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/sberbankrf/class/Sberbank.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");

$PHPShopBase->chekAdmin();
$Sberbank = new Sberbank();
if(isset($_REQUEST['operation']) && strlen($_REQUEST['operation']) > 2) {
    $result = array();
    try {
        switch ($_REQUEST['operation']) {
            case 'refund':
                $Sberbank->refund((int) $_REQUEST['orderId']);
                break;
        }
        $result['success'] = true;
        $result['message'] = 'Возврат успешно выполнен';
    } catch (\Exception $exception) {

        $result = array('success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage()));
    }
} else {
    $result = array('success' => false, 'error' => PHPShopString::win_utf8('Не найден параметр operation'));
}

echo (json_encode($result)); exit;
