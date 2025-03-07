<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm("phpshop_modules_twocan_system");

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    if (empty($_POST["autocharge_new"]))
        $_POST["autocharge_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();
    @extract($data);

    // ��������� ��� �����
    $Tab2 = $PHPShopGUI->setField('����� API', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab2 .= $PHPShopGUI->setField('������ API', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Terminal ID', $PHPShopGUI->setInputText(false, 'terminal_new', $data['terminal'], 300));
    $Tab2 .= $PHPShopGUI->setField('URL �����', $PHPShopGUI->setInputText(false, 'url_new', $data['url'], 300));
    $Tab2 .= $PHPShopGUI->setField('URL ��������� �����', $PHPShopGUI->setInputText(false, 'test_url_new', $data['test_url'], 300));
    
    $Tab2 .= $PHPShopGUI->setField('������������� ������', $PHPShopGUI->setCheckbox("autocharge_new", 1, "", $data["autocharge"]));
    $Tab2 .= $PHPShopGUI->setField('����� ����������', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "�������� ������ �� �������� �����", $data["dev_mode"]));

    $Tab2 .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'template_new', $data['template'], 300));
    $Tab2 .= $PHPShopGUI->setField('������� ������', $PHPShopGUI->setInputText(false, 'exptimeout_new', $data['exptimeout'], 300));
    
 
    // ��������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = $order_status_auth_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status){
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
            $order_status_auth_value[] = array($order_status['name'], $order_status['id'], $data['status_auth']);
        }

    // ������ ������
    $Tab2 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('������ ��� ������������ �������', $PHPShopGUI->setSelect('status_auth_new', $order_status_auth_value, 300));
    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'],true,300));
    
    
   
    

    // ����������
    $info = file_get_contents('../../phpshop/modules/twocan/inc/instructions.html');
        

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab2, true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], false)));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>