<?php

session_start();

$_classPath = "../../../";
include_once($_classPath . "class/obj.class.php");
include_once($_classPath . "modules/boxberrywidget/class/BoxberryWidget.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("string");
PHPShopObj::loadClass("lang");
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

$PHPShopBase->chekAdmin();
$BoxberryWidget = new BoxberryWidget();

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
                $result['error'] = 'success';
                break;
            case 'changeAddress':
                $BoxberryWidget->changeAddress($_REQUEST);
                $result['success'] = true;
                break;
        }
        
    } catch (\Exception $exception) {
        $result = array('success' => false, 'error' => PHPShopString::win_utf8($exception->getMessage()));
    }
} else {
    $result = array('success' => false, 'error' => PHPShopString::win_utf8('�� ������ �������� operation'));
}

echo (json_encode($result)); exit;