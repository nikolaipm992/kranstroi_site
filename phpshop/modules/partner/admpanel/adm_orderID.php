<?php

function partner_addBonus($data) {
    global $_classPath;

    include_once($_classPath . 'modules/partner/class/partner.class.php');
    $PHPShopPartnerOrder = new PHPShopPartnerOrder();
    $PHPShopPartnerOrder->option = $PHPShopPartnerOrder->option();

    // ������ �������
    if ($PHPShopPartnerOrder->option['enabled'] == 1) {

        // ���� ����� ��������, ������� ��� ������ � ��� ��������, ��������� % ��������
        if ($_POST['statusi_new'] == $PHPShopPartnerOrder->option['order_status']) {
            
            // ��������� ����� ��������
            $PHPShopPartnerOrder->addBonus($data['id'],$data['sum']);

            // ����� �������
            $PHPShopPartnerOrder->updateLog($data['id']);
        }
    }
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => false,
    'actionUpdate' => 'partner_addBonus'
);
