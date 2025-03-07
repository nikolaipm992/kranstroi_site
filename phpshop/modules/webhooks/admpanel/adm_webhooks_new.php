<?php
PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.webhooks.webhooks_forms"));

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $_classPath;
    
    $data['enabled']=1;
    $data['name']='����� WebHook';
    $data['send']=0;
    $data['type']=0;

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['create_order_status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['create_order_status']);
    
   
    // �����
    $send_value[]=array('POST',0,$data['send']);
    $send_value[]=array('GET',1,$data['send']);
    
    // ��������
    include_once($_classPath . 'modules/webhooks/class/webhooks.class.php');
    $PHPShopWebhooks = new PHPShopWebhooks();
    $type_array=$PHPShopWebhooks->getType();
    foreach($type_array as $k=>$type)
         $type_value[]=array($type,$k,$data['type']);

    $Tab1 =$PHPShopGUI->setField('��������', $PHPShopGUI->setInputText('', 'name_new', $data['name'], 400));
    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setRadio("enabled_new", 1, "����������", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "������", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("URL WebHook", $PHPShopGUI->setInputText(false, 'url_new', $data['url'], 400));
    //$Tab1 .= $PHPShopGUI->setField('�������� ��� �������:', $PHPShopGUI->setSelect('create_order_status_new', $order_status_value, 400));
    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('type_new', $type_value, 400));
    $Tab1 .= $PHPShopGUI->setField('����� ��������', $PHPShopGUI->setSelect('send_new', $send_value, 100));
    
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =  $PHPShopGUI->setInput("submit", "saveID", "���������", "right", false, false, false, "actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm;
    
    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>