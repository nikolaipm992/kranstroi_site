<?php

function sendSmsfly($data) {
    global $_classPath,$PHPShopSystem;
    
    // ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $GetOrderStatusArray = $PHPShopOrderStatusArray->getArray();

    include $_classPath."modules/smsfly/hook/mod_option.hook.php";
    
    // SMS ���������� ������������ � ����� ������� ������
    if (intval($data['statusi']) != intval($_POST['statusi_new']) && (int) $GetOrderStatusArray[$_POST['statusi_new']]['sms_action'] === 1) {
        
        // ��������� ������
        $PHPShopSmsfly = new PHPShopSmsfly();

        // ���������
        $msg = __('����� ������ ������ �').  $data['uid'] . ' - ' . $GetOrderStatusArray[$_POST['statusi_new']]['name'];

        $phone=$_POST['tel_new'];

        if(!$phone)
            $phone = $data['tel'];

        $PHPShopSmsfly->send($msg,$phone);
    }
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => false,
    'actionUpdate' => 'sendSmsfly'
);
?>