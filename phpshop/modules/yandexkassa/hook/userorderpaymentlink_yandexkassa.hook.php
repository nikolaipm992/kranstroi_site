<?php

include_once dirname(__FILE__) . '/../class/YandexKassa.php';

function userorderpaymentlink_mod_yandexkassa_hook($obj, $PHPShopOrderFunction) {

    if (YandexKassa::isYandexKassaPaymentMethod((int) $PHPShopOrderFunction->order_metod_id)) {
        $YandexKassa = new YandexKassa();
        PHPShopObj::loadClass('delivery');

        if ($PHPShopOrderFunction->getParam('statusi') == $YandexKassa->options['status'] or empty($YandexKassa->options['status'])) {
            if((int) $_GET['kassaPay'] == 1) {
                $order = $PHPShopOrderFunction->unserializeParam('orders');
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                $payment = $YandexKassa->createPayment(
                    $YandexKassa->prepareProducts($order['Cart']['cart'], $order['Person']['discount']),
                    $PHPShopOrderFunction->objRow['uid'],
                    $PHPShopOrderFunction->getMail(),
                    $YandexKassa->prepareDelivery($order['Cart']['dostavka'], $PHPShopDelivery->getParam('ofd_nds'))
                );

                if(!empty($payment['confirmation']['confirmation_url'])) {
                    header('Location: ' . $payment['confirmation']['confirmation_url']);
                } else {
                    $return = 'Ошибка регистрации платежа, обратитесь к администратору.';
                }
            } else {
                $return = PHPShopText::a('?order_info=' . $PHPShopOrderFunction->objRow['uid'] . '&kassaPay=1#Order', $YandexKassa->options['title'], false, false, 14, false, 'btn btn-primary');
            }

        } elseif (YandexKassa::isYandexKassaPaymentMethod((int) $PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod'))) {
            $return = ' Заказ обрабатывается менеджером';
        }

        return $return;
    }
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_yandexkassa_hook');
?>