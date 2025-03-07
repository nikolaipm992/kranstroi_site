<?php

/**
 * ������� ���, ����� ������ ������ � �� � ����������� ����������� ������ � ��������� ����� �����������
 * @param object $obj ������ �������
 * @param array $PHPShopOrderFunction ������ � ������
 */
function userorderpaymentlink_mod_modulbank_hook($obj, $PHPShopOrderFunction)
{
    global $PHPShopSystem;

    include_once 'phpshop/modules/modulbank/class/ModulBank.php';
    $Modulbank = new ModulBank();

    // �������� ������ �� ������� ������
    if ($PHPShopOrderFunction->order_metod_id == 10012)
        if ($PHPShopOrderFunction->getParam('statusi') == $Modulbank->option['status'] or empty($Modulbank->option['status'])) {

            $tax = $Modulbank->getNds();

            $Modulbank->parameters['order_id'] = $PHPShopOrderFunction->objRow['uid'];
            $Modulbank->parameters['receipt_contact'] = $PHPShopOrderFunction->getMail();
            $Modulbank->parameters['description'] = PHPShopString::win_utf8(str_replace('"', '', $PHPShopSystem->getName() . __(' ������ ������ ') . $Modulbank->parameters['order_id']));

            $order = $PHPShopOrderFunction->unserializeParam('orders');

            // ���������� �������
            $total = 0;
            foreach ($order['Cart']['cart'] as $key => $arItem) {

                // ������
                $price = $order['Person']['discount'] > 0 ?
                    $price = number_format($arItem['price'] - ($arItem['price'] * $order['Person']['discount'] / 100), 2, '.', '') : $price = number_format($arItem['price'], 2, '.', '');

                $aItem[] = array(
                   "name" => PHPShopString::win_utf8(str_replace(array('&#43;'),array(''), $arItem['name'])),
                    "quantity" => $arItem['num'],
                    "price" => $price,
                    "sno" => $Modulbank->option['taxationSystem'],
                    "payment_object" => "commodity",
                    "payment_method" => "full_prepayment",
                    "vat" => $tax
                );
                $total = number_format($total + (int) $arItem['num'] * $price, 2, '.', '');
            }

            // ��������
            if (!empty($order['Cart']['dostavka'])) {

                PHPShopObj::loadClass('delivery');
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                switch ($PHPShopDelivery->getParam('ofd_nds')) {
                    case 0:
                        $tax_delivery = 'vat0';
                        break;
                    case 10:
                        $tax_delivery = 'vat10';
                        break;
                    case 18:
                        $tax_delivery = 'vat18';
                        break;
                    case 20:
                        $tax_delivery = 'vat20';
                        break;
                    default:
                        $tax_delivery = $tax;
                }

                $aItem[] = array(
                    "name" => PHPShopString::win_utf8('��������'),
                    "quantity" => 1,
                    "price" => (float) number_format($order['Cart']['dostavka'], 2, '.', ''),
                    "sno" => $Modulbank->option['taxationSystem'],
                    "payment_object" => 'service',
                    "payment_method" => 'full_prepayment',
                    'vat' => $tax_delivery
                );
                $total = number_format($total + $order['Cart']['dostavka'], 2, '.', '');
            }

            $Modulbank->parameters['receipt_items'] = json_encode($aItem);
            $Modulbank->parameters['amount'] = number_format($total, 2, '.', '');

            $payment_form = $Modulbank->getForm();

            $Modulbank->log($Modulbank->parameters, $Modulbank->parameters['order_id'], _-('����� ������������ ��� ��������'), __('����������� ������'));

            $return = PHPShopText::form($payment_form, 'modulbankpay', 'post', 'https://pay.modulbank.ru/pay', '_blank');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10012)
            $return = __(' ����� �������������� ����������');

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_modulbank_hook');
?>