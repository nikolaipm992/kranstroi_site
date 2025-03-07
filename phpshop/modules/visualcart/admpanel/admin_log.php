<?php

function getUserName($id, $ip) {
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $data = $PHPShopOrm->select(array('name'), array('id' => '=' . intval($id)), false, array('limit' => 1));
    if (is_array($data))
        return array('name' => $data['name'], 'link' => '?path=shopusers&id=' . $id);
    else
        return $ip;
}

function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name;
    
    unset($select_name[0]);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel(__('������ ���������� � �������'), $select_name, false);

    $PHPShopInterface->setCaption(array("�����", "45%"), array("����", "10%"), array("���-��", "10%"),array("������������", "15%"), array("����", "10%"), array("������", "15%", array('align' => 'right')));

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.visualcart.visualcart_log"));
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            if ($row['status'] == 1)
                $status = __('���������');
            else
                $status = '<span class="text-warning">'.__('��� �� ������').'</span>';

            $PHPShopInterface->setRow(array('name' => $row['content'], 'link' => '?path=product&id=' . $row['product_id']), $row['price'],$row['num'], getUserName($row['user'], $row['ip']), array('name' => PHPShopDate::get($row['date'], true), 'order' => $row['date']), $status);
        }
    $PHPShopInterface->Compile();
}

?>