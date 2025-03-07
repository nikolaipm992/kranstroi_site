<?php

/**
 * Добавление данных в заказ, регистрация заказа в службе доставки
 * param obj $obj
 * param array $row
 * param string $rout
 */
function send_to_order_boxberrywidget_hook($obj, $row, $rout)
{
    include_once 'phpshop/modules/boxberrywidget/class/BoxberryWidget.php';
    $BoxberryWidget = new BoxberryWidget();

    if($BoxberryWidget->isBoxberryDeliveryMethod((int) $_POST['d'])) {
        if(!empty($_POST['DeliverySum'])) {
            if ($rout === 'START') {
                $obj->delivery_mod = round((float) $_POST['DeliverySum'], $BoxberryWidget->format);
                $obj->manager_comment = $_POST['boxberryInfo'];
                $obj->set('deliveryInfo', $_POST['boxberryInfo']);
            }
            if ($rout === 'END' && $BoxberryWidget->option['status'] == 0) {
                $orm = new PHPShopOrm('phpshop_orders');
                $order = $orm->getOne(array('*'), array('uid' => "='" . $obj->ouid . "'"));
                if(is_array($order)) {
                    $BoxberryWidget->isPvzDelivery((int) $_POST['d']) ? $vid = 1 : $vid = 2;
                    $BoxberryWidget->setData($order, $vid, (int) $obj->discount);
                    $result = $BoxberryWidget->request('ParselCreate', $order['id']);

                    if($result) {
                        $_POST['boxberry_pvz_id_new'] = '';
                    }
                }
            }
        }
    }
}

$addHandler = array ('send_to_order' => 'send_to_order_boxberrywidget_hook');
?>