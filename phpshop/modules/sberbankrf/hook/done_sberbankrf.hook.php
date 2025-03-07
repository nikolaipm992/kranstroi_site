<?php

include_once dirname(__DIR__) . '/class/Sberbank.php';

/**
 * ������� ���, ����������� ������ � ��������� ����� ��������� ���������� ���������, ������������� �� ��������� �����
 * @param object $obj ������ �������
 * @param array $value ������ � ������
 * @param string $rout ����� ��������� ����
 * API docs: https://securepayments.sberbank.ru/wiki/doku.php/integration:api:rest:requests:register
 */
function send_sberbankrf_hook($obj, $value, $rout) {
    if ($rout === 'END' and (int) $value['order_metod'] === Sberbank::SBERBANK_PAYMENT_ID) {

        $Sberbank = new Sberbank();

        // �������� ������ �� ������� ������
        if (empty($Sberbank->options['status'])) {

            $orders = unserialize($obj->order);

            $payment = $Sberbank->createPayment(
                $Sberbank->prepareProducts($orders['Cart']['cart'], $obj->discount),
                $value['ouid'],
                $value['mail'],
                $Sberbank->prepareDelivery($obj->delivery, $obj->PHPShopDelivery->getParam('ofd_nds'))
            );

            if(!empty($payment["formUrl"])) {
                if((int) $Sberbank->options['force_payment'] === 1) {
                    header('Location: ' . $payment["formUrl"]);
                } else {
                    $obj->set('paymenturl', $payment["formUrl"]);
                    $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['sberbankrf']['sberbankrf_payment_forma'], true);
                }
            }
        } else {
            $obj->set('mesageText', $Sberbank->options['title_sub'] );
            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);
        }
        $obj->set('orderMesage', $forma);
    }
}

$addHandler = array('send_to_order' => 'send_sberbankrf_hook');
?>