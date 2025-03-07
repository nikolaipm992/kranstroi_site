<?php

function changeStatusOrder($data)
{

    // SMS ���������� ������������ � ����� ������� ������
    if ($data['statusi'] != $_POST['statusi_new']) {

        // ������� �������
        $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
        $GetOrderStatusArray = $PHPShopOrderStatusArray->getArray();

        // ��������� ������
        include_once(dirname(__FILE__) . '/mod_option.php');
        $PHPShopModule = new PHPShopModule();

        // ��������� �������
        $statusOrderTpl = $PHPShopModule->getTplStatusOrder();

        if ($PHPShopModule->getCascadeEnabled()) {
            $statusOrderTpl = $PHPShopModule->getTplStatusOrderViber();
        }


        $PHPShopSystem = new PHPShopSystem();
        $nameShop = $PHPShopSystem->objRow['name'];

        // ������ ������ ��� ������� ��� �������� ������
        $datainsert = array(
            '@NameShop@' => $nameShop,
            '@OrderNum@' => $data['uid'],
            '@OrderStatus@' => $_POST['statusi_new'] ? $GetOrderStatusArray[$_POST['statusi_new']]['name'] : '�����������'
        );

        // ������� �� ������� ������������ ���������
        $phone = array($PHPShopModule->true_num($data['tel']));

        // ���������
        $msg = $PHPShopModule->parseString($statusOrderTpl, $datainsert);


        if ($PHPShopModule->getCascadeEnabled()) {
            $PHPShopModule->sendSms($phone, $msg, 'change_status_order_template_viber');
        } else {
            $PHPShopModule->sendSms($phone, $msg);
        }



    }

}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => false,
    'actionUpdate' => 'changeStatusOrder'
);

?>