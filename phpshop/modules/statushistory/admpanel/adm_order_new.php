<?php

function addModStatusHistoryNew($id) {
    // �������� ������ (�����������, ���� ����� ����� ������������)
    require_once(dirname(__FILE__) . '/../class/statushistory.class.php');
    $PHPShopStatusHistory = new PHPShopStatusHistory();
    $PHPShopStatusHistory->add($id, 0, true);
}

$addHandler=array(
        'actionStart'=>'addModStatusHistoryNew',
        'actionDelete'=>false,
        'actionUpdate'=>false,
);

?>