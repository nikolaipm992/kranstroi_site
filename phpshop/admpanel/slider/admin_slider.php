<?php

$TitlePage = __("�������");

function actionStart() {
    global $PHPShopInterface,$TitlePage;

    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'), array('��������'));
    $PHPShopInterface->addJSFiles('slider/gui/slider.gui.js');

    $PHPShopInterface->setCaption(array(null, "3%"), array("�����������", "30%"), array("����������", "20%"), array("���������", "10%", array('align' => 'center','locale'=>true)), array("", "10%"), array("������", "10%", array('align' => 'right')));


    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['slider']);
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'num, id DESC'), array("limit" => "1000"));
    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['image']))
                $row['image']=$row['name'];

            $PHPShopInterface->setRow($row['id'], array('name' => $row['image'], 'link' => '?path=slider&id=' . $row['id'], 'align' => 'left','popover'=>'<img src=\'' . $row['image'] . '\' onerror=\'imgerror(this)\' class=\'popover-img\'></img>','popover-title'=>__('������')), $row['link'], array('name' => $row['num'], 'align' => 'center'), array('action' => array('edit','|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }

    $PHPShopInterface->Compile();
}

?>