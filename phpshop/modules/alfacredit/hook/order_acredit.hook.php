<?php


function payment_mod_acredit_hook($obj, $arr) {
    // ��������� ������
    include_once dirname(__FILE__) . '/mod_option.hook.php';
    $PHPShopAlfaCreditArray = new PHPShopAlfaCreditArray();
    $option = $PHPShopAlfaCreditArray->getArray();
    
    // ����� �������    
    $sum = $obj->PHPShopCart->getSum(false);
    $type = $PHPShopAlfaCreditArray->get_type($sum);
    
    // ��������� ������    
    $PHPShopPayment = new PHPShopPaymentArray();
    $Payment = $PHPShopPayment->getArray();
    
    $disp = $showYurDataForPaymentClass = null;
    if (is_array($Payment))
        foreach ($Payment as $val) {
            if (!empty($val['enabled']) OR $val['path'] == 'modules') {
                if ($val['id'] == 10045 && empty($type)) {
                    // ������ �� ������, ��� ����� ����� ��� ����������� �������� � ������/���������
                } else {
                    if ($val['id'] == 10045)
                        $val['name'] = $option[$type . '_name'];
                    if ($val['icon'])
                        $img = "&nbsp;<img src='{$val['icon']}' title='{$val['name']}' height='30'/>&nbsp;";
                    else
                        $img = "";
                    $disp .= PHPShopText::div(PHPShopText::setInput("radio", "order_metod", $val['id'], "none", false, false, false, false, $img . $val['name'], 'payment' . $val['id']), "left", false, false, "paymOneEl");
                }
                // ��������� ����� ������� ��� ��������� ������� ��� ������ ���. ����� ��. ������ � ����������
                // ���� ��� ������� ���� ������ ��� ��������� 
                if (!empty($val['yur_data_flag'])) {
                    $showYurDataForPaymentClass .= " showYurDataForPaymentClass" . $val['id'];
                }
            }
        }
    
    if (!empty($disp)) {
        if (!empty($showYurDataForPaymentClass)) {
            $obj->set('showYurDataForPaymentClass', $showYurDataForPaymentClass);
            if (PHPShopParser::checkFile('payment/showYurDataForPayment.tpl')) {
                $obj->set('showYurDataForPayment', ParseTemplateReturn('payment/showYurDataForPayment.tpl'));
            } else {
                $obj->set('showYurDataForPayment', ParseTemplateReturn('phpshop/lib/templates/order/nt/showYurDataForPayment.tpl', true));
            }
        }

        $obj->set('orderOplata', $disp);
        return true;
    }
    
}

$addHandler = array
(
    'payment' => 'payment_mod_acredit_hook',
);


?>