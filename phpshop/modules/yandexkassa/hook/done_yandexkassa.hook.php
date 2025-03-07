<?php

include_once dirname(__FILE__) . '/../class/YandexKassa.php';

function send_to_order_mod_yandexkassa_hook($obj, $value, $rout) {
    if ($rout === 'END' && YandexKassa::isYandexKassaPaymentMethod($value['order_metod'])) {

        $YandexKassa = new YandexKassa();

        // Контроль оплаты от статуса заказа
        if (empty($YandexKassa->options['status'])) {

            $orders = unserialize($obj->order);
            $payment = $YandexKassa->createPayment(
                $YandexKassa->prepareProducts($orders['Cart']['cart'], $obj->discount),
                $value['ouid'],
                $value['mail'],
                $YandexKassa->prepareDelivery($obj->delivery, $obj->PHPShopDelivery->getParam('ofd_nds'))
            );

            if(!empty($payment['confirmation']['confirmation_url'])) {
                $payment_forma = PHPShopText::a($payment['confirmation']['confirmation_url'], $YandexKassa->options['title'], false, false, 14, false, 'btn btn-primary');
            } else {
                $payment_forma = '<p>Ошибка регистрации платежа, обратитесь к администратору.</p>';
            }

            $obj->set('payment_forma', $payment_forma);
            $obj->set('payment_info', $YandexKassa->options['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['yandexkassa']['yandexmoney_payment_forma'], true);
        } else {
            $obj->set('mesageText', $YandexKassa->options['title_end']);
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }

        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array('send_to_order' => 'send_to_order_mod_yandexkassa_hook');
?>