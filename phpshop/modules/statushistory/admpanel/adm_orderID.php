<?php

// ���������� �����
require_once(dirname(__FILE__) . '/../class/statushistory.class.php');

function addModStatusHistory($data) {
    global $PHPShopGUI;
    
    // ����� ������� �������
    $PHPShopStatusHistory = new PHPShopStatusHistory();
    $Tab = $PHPShopStatusHistory->table($data['id']);
    if(!empty($Tab))
    $PHPShopGUI->addTab(array("������� ��������", $Tab));
}

function updateModStatusHistory($post) {
    global $PHPShopOrm;
    
    $PHPShopOrm->debug = false;
    $dataorder = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_POST['rowID'])));
    
    //���� ������� ������
    if ($dataorder['statusi'] != $_POST['statusi_new']) {
        $PHPShopStatusHistory = new PHPShopStatusHistory();
        $PHPShopStatusHistory->add($_POST['rowID'], $_POST['statusi_new'], true);
    }
}

function deleteModStatusHistory($post) {
    // �������� ������� ��������� �������� ������
    $PHPShopStatusHistory = new PHPShopStatusHistory();
    $PHPShopStatusHistory->delete($post['rowID']);    
}

$addHandler=array(
    'actionStart' => 'addModStatusHistory',
    'actionDelete' => 'deleteModStatusHistory',
    'actionUpdate' => 'updateModStatusHistory'
);

?>