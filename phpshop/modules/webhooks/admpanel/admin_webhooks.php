<?php

function actionStart() {
    global $PHPShopInterface, $PHPShopModules,$select_name,$_classPath;

    $PHPShopInterface->setActionPanel(__('����� WebHooks'), $select_name, array('�������� +',));
    $PHPShopInterface->setCaption(array("", "1%"), array("��������", "50%"), array("��������", "20%"),array("�����", "7%"),  array("", "10%"), array("������ &nbsp;&nbsp;&nbsp;", "7%", array('align' => 'right')));
    
    // ��������
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks();
    $type_value=$PHPShopWebhooks->getType();
    
    $send_value=array('POST','GET');

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.webhooks.webhooks_forms"));
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where = false, array('order' => 'id desc'), array('limit' => 1000));

    if (is_array($data))
        foreach ($data as $row) {

            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=modules.dir.webhooks&id=' . $row['id'], 'align' => 'left'),  $type_value[$row['type']],$send_value[$row['send']],  array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
    $PHPShopInterface->Compile();
}

?>