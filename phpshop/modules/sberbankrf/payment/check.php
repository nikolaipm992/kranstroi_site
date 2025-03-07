<?php

/**
 * Обработчик оповещения о платеже Сбербанк России
 * @author PHPShop Software
 * @version 1.0
 */
session_start();

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
include_once($_classPath . "class/mail.class.php");
include_once($_classPath . "modules/sberbankrf/class/Sberbank.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("payment");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("string");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopSystem = new PHPShopSystem();
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('sberbankrf');

class Payment extends PHPShopPaymentResult {

    /** @var Sberbank */
    private $Sberbank;
    private $orderNumber;

    function __construct() {

        // Проверка номера заказа.
        $this->orderNumber = $_REQUEST['orderNumber'];
        if(strstr($this->orderNumber, "#")){
            $orderUidArr = explode ("#", $this->orderNumber);
            $this->orderNumber = $orderUidArr[0];
        }
        if(!PHPShopSecurity::true_order($this->orderNumber)) {
            return;
        }

        $this->option();
        parent::__construct();
    }

    /**
     * Настройка модуля 
     */
    function option() {

        $this->payment_name = 'Sberbank';
        $this->log = false;

        $this->Sberbank = new Sberbank();
    }

    /**
     * Обновление данных по заказу 
     */
    function updateorder() {

        // Не доверяем полученному уведомлению и делаем повторный запрос в Сбербанк.
        $paid = $this->Sberbank->isOrderPaid($this->orderNumber, $_REQUEST['mdOrder']);

        if($paid) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
            $PHPShopOrm->debug = false;
            $row = $PHPShopOrm->getOne(array('*'), array('uid' => "='" . $this->orderNumber . "'"));
            if (!empty($row['id'])) {
                (new PHPShopOrderFunction((int) $row['id']))->changeStatus((int) $this->set_order_status_101(), $row['statusi']);

                // Лог оплат
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                $PHPShopOrm->insert(array('uid_new' => str_replace('-', '', $row['uid']), 'name_new' => $this->payment_name,
                    'sum_new' => $row['sum'], 'datas_new' => time()));

                if($this->Sberbank->options['notification'] == 1){
                    $PHPShopSystem = new PHPShopSystem();
                    $content = 'Заказ №' . $row['uid'] . ' оплачен платежной системой Сбербанк России.';
                    new PHPShopMail($PHPShopSystem->getParam('adminmail2'), $PHPShopSystem->getParam('adminmail2'),"Оплата заказа №".$row['uid'], $content);
                }

                $this->ofd($row);
            }
        }

        header("HTTP/1.0 200");
        exit();
    }
}

new Payment();

?>