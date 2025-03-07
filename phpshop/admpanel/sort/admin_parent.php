<?php

$TitlePage = __("�������� ��������");

function actionStart() {
    global $PHPShopInterface,$TitlePage ;
    
    $PHPShopInterface->setActionPanel($TitlePage , array('������� ���������'),array('��������'));
    $PHPShopInterface->setCaption(array(null, "3%"), array("��������", "80%"),array("", "10%"), array("������", "10%", array('align' => 'right')));

    // SQL
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['parent_name']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'name desc'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {
        
             if(!empty($row['color']))
            $row['name'].=' + '.$row['color'];
        
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path='.$_GET['path'].'&id=' . $row['id'], 'align' => 'left'), array('action' => array('edit', '|', 'delete','id'=>$row['id']), 'align' => 'center'), array('status' => array('enable'=>$row['enabled'], 'align' => 'right','caption'=>array('����', '���'))));
        }

    $PHPShopInterface->Compile();
}

?>