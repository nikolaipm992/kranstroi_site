<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm("phpshop_modules_vtb_system");

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab2 = $PHPShopGUI->setField('����� API ��������', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab2 .= $PHPShopGUI->setField('������ API ��������', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    
    $api = array(
        array('platezh.vtb24.ru', 'https://platezh.vtb24.ru/payment/rest/register.do', $data['api_url']),
    );
    
    $dev = array(
        array(__('������ �� �������'), 0, $data['dev_mode']),
        array('vtb.rbsuat.com', 'https://vtb.rbsuat.com/payment/rest/register.do', $data['dev_mode']),
    );
    
    $Tab2.= $PHPShopGUI->setField('URL ����� API', $PHPShopGUI->setSelect('api_url_new', $api, 300));
    $Tab2 .= $PHPShopGUI->setField('�������� URL ����� API', $PHPShopGUI->setSelect('dev_mode_new', $dev, 300),1,"�������� ������ �� �������� ����� ���");

    // ������� ���������������
    $tax_system = array(
        array("����� ������� ���������������", 0, $data["taxationSystem"]),
        array("���������� ������� ��������������� (�����)", 1, $data["taxationSystem"]),
        array("���������� ������� ��������������� (����� ����� ������)", 2, $data["taxationSystem"]),
        array("������ ����� �� ��������� �����", 3, $data["taxationSystem"]),
        array("������ �������������������� �����", 4, $data["taxationSystem"]),
        array("��������� ������� ���������������", 5, $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('C������ ���������������', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab2 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="https://www.vtb.ru/malyj-biznes/acquiring/internet-acquiring/" target="blank">���</a>.</li>
<li>�� �������� ��������� ������ ��������������� ��� ����� API �������� (*********-api) � ������ ��������.</li>
<li>�� ����� ������������ �������� "����� ����������", ������ ����� ������������ �� �������� ����� ���.</li>
<li>������� ����������� ��� URL Callback-����������� <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/vtb/payment/check.php</code></li>
<li>��� �������� ������ � ������� �����, ��������� "����� ����������".</a></li>
</ol>
<p>����� ����������� � ��� ��� ��������������� ������� � �������� �����, � ������ ����� �������� ������� "����� ����������". ����� ���������� ��������� ������ (����� ��� ������������ ����� ����� � ������������ ���) ����� ��������� ������� � ������� ����� ���, ������ � ���������� ������ ������� ����� API � ������, ��������� "����� ����������". </p>
';

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab2, true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'],true)));

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