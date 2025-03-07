<?php

function userorderpaymentlink_mod_yandexmoney_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem;

    // ��������� ������
    include_once(dirname(__FILE__) . '/mod_option.hook.php');
    $PHPShopYandexmoneyArray = new PHPShopYandexmoneyArray();
    $option = $PHPShopYandexmoneyArray->getArray();


    // �������� ������ �� ������� ������
    if ($PHPShopOrderFunction->order_metod_id  == 10002)
        if ($PHPShopOrderFunction->getParam('statusi') == $option['status'] or empty($option['status'])) {

            // ����� �����
            $mrh_ouid = explode("-", $PHPShopOrderFunction->objRow['uid']);
            $inv_id = $mrh_ouid[0] . "" . $mrh_ouid[1];

            // ����� �������
            $out_summ = $PHPShopOrderFunction->getTotal();

            // ��������� �����
            $payment_forma = PHPShopText::setInput('hidden', 'receiver', trim($option['merchant_id']), false, 10);
            $payment_forma.= PHPShopText::setInput('hidden', 'formcomment', PHPShopString::win_utf8($PHPShopSystem->getParam('name') . ': ����� ') . $PHPShopOrderFunction->objRow['uid'], false, 10);
            $payment_forma.= PHPShopText::setInput('hidden', 'short-dest', PHPShopString::win_utf8($PHPShopSystem->getParam('name') . ': ����� ') . $PHPShopOrderFunction->objRow['uid'], false, 10);
            $payment_forma.=PHPShopText::setInput('hidden', 'writable-targets', "false", false, 10);
            $payment_forma.=PHPShopText::setInput('hidden', 'comment-needed', "false", false, 10);
            $payment_forma.=PHPShopText::setInput('hidden', 'label', $inv_id, false, 10);
            $payment_forma.=PHPShopText::setInput('hidden', 'quickpay-form', 'shop');
            $payment_forma.=PHPShopText::setInput('hidden', 'targets', PHPShopString::win_utf8($PHPShopSystem->getParam('name') . ': ����� ') . $PHPShopOrderFunction->objRow['uid'], false, 10);
            $payment_forma.=PHPShopText::setInput('hidden', 'sum', $out_summ, false, 10);
            $payment_forma.=PHPShopText::setInput('submit', 'send', $option['title'], $float = "none", 250);

            $return = PHPShopText::form($payment_forma, 'yandexpay', 'post', 'https://yoomoney.ru/quickpay/confirm.xml', '_blank');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10002)
            $return = ', ����� �������������� ����������';

    return $return;
}

$addHandler = array
    (
    'userorderpaymentlink' => 'userorderpaymentlink_mod_yandexmoney_hook'
);
?>