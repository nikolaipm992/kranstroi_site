<?php

include_once dirname(__DIR__) . '/class/Sberbank.php';

/**
 * ������� ���, ����� ������ ������ � �� � ����������� ����������� ������ � ��������� ����� ��������� ���������� ���������
 * @param object $obj ������ �������
 * @param array $PHPShopOrderFunction ������ � ������
 */
function userorderpaymentlink_mod_sberbankrf_hook($obj, $PHPShopOrderFunction) {

    $Sberbank = new Sberbank();

    // ������
    if ($_REQUEST["paynow"] == "Y") {

        $order = $PHPShopOrderFunction->unserializeParam('orders');
        $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

        $payment = $Sberbank->createPayment(
            $Sberbank->prepareProducts($order['Cart']['cart'], $order['Person']['discount']),
            $PHPShopOrderFunction->objRow['uid'],
            $PHPShopOrderFunction->getMail(),
            $Sberbank->prepareDelivery($order['Cart']['dostavka'], $PHPShopDelivery->getParam('ofd_nds'))
        );
        if(!empty($payment["formUrl"])) {
            header('Location: ' . $payment["formUrl"]);
        }
    }

    // �������� ������ �� ������� ������
    if ($PHPShopOrderFunction->order_metod_id == Sberbank::SBERBANK_PAYMENT_ID)
        if ($PHPShopOrderFunction->getParam('statusi') == $Sberbank->options['status'] or empty($Sberbank->options['status'])) {

            $order_uid = $PHPShopOrderFunction->objRow['uid'];

            $return = PHPShopText::a("/users/order.html?order_info=$order_uid&paynow=Y#Order", '�������� ������', '�������� ������', false, false, '_blank', 'btn btn-success pull-right');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10010)
            $return = ', ����� �������������� ����������';

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_sberbankrf_hook');
?>