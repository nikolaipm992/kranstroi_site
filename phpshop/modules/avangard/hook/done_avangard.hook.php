<?php

/**
 * ������� ���, ����������� ������ � ��������� �����
 * @param object $obj ������ �������
 * @param array $value ������ � ������
 * @param string $rout ����� ��������� ����
 */
function send_avangard_hook($obj, $value, $rout) {

    include_once 'phpshop/modules/avangard/class/Avangard.php';

    if ($rout === 'END' and $value['order_metod'] == Avangard::PAYMENT_METHOD) {

        $Avangard = new Avangard();

        // �������� ������ �� ������� ������
        if (!$Avangard->option['status_id']) {

            $Avangard->setAmount($obj->total * 100);
            $Avangard->setOrderNumber($value['ouid']);
            $payment_form = $Avangard->getForm();
            
            $Avangard->log($payment_form, $Avangard->getOrderNumber(), '����� ������������ ��� ��������', '����������� ������');
            $Avangard->orderState($Avangard->getOrderNumber(), Avangard::LOG_STATUS_NEW_ORDER);

            $obj->set('payment_forma', PHPShopText::form($payment_form, 'avangardpay', 'post', $Avangard->getApiURL(), '_blank'));
            
            if($Avangard->option['qr'] == '1'){
                $payment_form_qr = $Avangard->getForm(true);
                $obj->set('payment_forma_qr', PHPShopText::form($payment_form_qr, 'avangardpay', 'post', $Avangard->getApiURL(), '_blank'));
            }
            
            $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['avangard']['avangard_payment_form'], true);
            
        } else {
            $clean_cart = "
            <script>
                if(window.document.getElementById('num')){
                    window.document.getElementById('num').innerHTML='0';
                    window.document.getElementById('sum').innerHTML='0';
                }
            </script>";
            $obj->set('mesageText', $Avangard->option['title_sub'] . $clean_cart);
            $form = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            // ������� �������
            unset($_SESSION['cart']);
        }

        $obj->set('orderMesage', $form);
    }
}

$addHandler = array('send_to_order' => 'send_avangard_hook');
?>