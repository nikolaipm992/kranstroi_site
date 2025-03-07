<?php

include_once dirname(__DIR__) . '/class/Shiptor.php';

function shiptorwidgetSend($data) {

    $Shiptor = new Shiptor();
    $order = unserialize($data['orders']);

    if($Shiptor->isShiptorDeliveryMethod((int) $order['Person']['dostavka_metod'])) {
        if ((int) $_POST['statusi_new'] === (int) $Shiptor->options['status'] or !empty($_POST['shiptor_send_now'])) {
            $Shiptor->send($data);
        }
    }
}

function addShiptorTab($data) {
    global $PHPShopGUI;

    $Shiptor = new Shiptor();
    $order = unserialize($data['orders']);

    if($Shiptor->isShiptorDeliveryMethod((int) $order['Person']['dostavka_metod'])) {

        $shiptorData = unserialize($order['shiptor_order_data']);

        if(isset($shiptorData['status']) && $shiptorData['status'] === Shiptor::STATUS_SENT) {
            $Tab1 = sprintf('<div class="alert alert-success" role="alert">%s</div>', __('Заказ успешно отправлен.'));
        } else {
            $PHPShopGUI->addJSFiles('../modules/shiptor/admpanel/gui/script.gui.js');

            $Tab1 = $PHPShopGUI->setField('Статус оплаты', $PHPShopGUI->setCheckbox('shiptor_payment_status', 1, 'Заказ оплачен', $data['paid']));
            $Tab1 .= $PHPShopGUI->setField('Синхронизация заказа', $PHPShopGUI->setCheckbox('shiptor_send_now', 1, 'Отправить заказ в Shiptor сейчас', 0));
            $Tab1 .= $PHPShopGUI->setInput('hidden', 'shiptor_order_id', $data['id']);
        }

        $PHPShopGUI->addTab(array("Shiptor", $Tab1, true));
    }
}

$addHandler = array(
    'actionStart'  => 'addShiptorTab',
    'actionDelete' => false,
    'actionUpdate' => 'shiptorwidgetSend'
);
?>