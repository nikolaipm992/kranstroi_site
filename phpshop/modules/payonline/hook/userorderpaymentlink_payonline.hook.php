<?php

/**
 * ������� ���, ����� ������ ������ � �� � ����������� ����������� ������ � ��������� �����
 * @param object $obj ������ �������
 * @param array $PHPShopOrderFunction ������ � ������
 */
function userorderpaymentlink_mod_payonline_hook($obj, $PHPShopOrderFunction)
{
    include_once 'phpshop/modules/payonline/class/PayOnline.php';

    $PayOnline = new PayOnline();

    // �������� ������ �� ������� ������
    if ($PHPShopOrderFunction->order_metod_id == PayOnline::PAYMENT_ID)
        if ($PHPShopOrderFunction->getParam('statusi') == $PayOnline->option['status'] or empty($PayOnline->option['status'])) {
            $PayOnline->setAmount(number_format($PHPShopOrderFunction->getTotal(), 2, '.', ''));
            $PayOnline->setOrderId($PHPShopOrderFunction->objRow['uid']);

            $PayOnline->log(array('form' => $PayOnline->getForm()), $PayOnline->getOrderId(), '����� ������������ ��� ��������', '����������� ������');

            $obj->set('payment_forma', PHPShopText::form($PayOnline->getForm(), 'payonlinepay', 'post', PayOnline::FORM_ACTION, '_blank'));

            $return = ParseTemplateReturn($GLOBALS['SysValue']['templates']['payonline']['payonline_payment_forma'], true);

        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == PayOnline::PAYMENT_ID)
            $return = ' ����� �������������� ����������';

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_payonline_hook');
?>