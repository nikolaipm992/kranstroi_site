<?php

$TitlePage = __("��������");

function actionStart() {
    global $PHPShopInterface,$TitlePage;
    
    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'),array('��������'));
    $PHPShopInterface->setCaption(array(null, "3%"), array("����", "70%"), array("", "10%"), array("���� ��������", "20%", array('align' => 'right')));

    // SQL
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['newsletter']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id desc'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {
        
            if(!empty($row['date']))
                $date=PHPShopDate::get($row['date'],true);
            else $date=null;
        
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path='.$_GET['path'].'&id=' . $row['id'], 'align' => 'left') , array('action' => array('edit','|', 'delete','id'=>$row['id']), 'align' => 'center'), array('name'=>$date, 'align' => 'right','order'=>$row['date']));
        }

    $PHPShopInterface->Compile();
}

?>