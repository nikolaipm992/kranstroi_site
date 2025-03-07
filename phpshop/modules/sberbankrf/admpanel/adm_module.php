<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm("phpshop_modules_sberbankrf_system");

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $PHPShopOrm->debug = false;

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;
    if (empty($_POST["notification_new"]))
        $_POST["notification_new"] = 0;
    if (empty($_POST["force_payment_new"]))
        $_POST["force_payment_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab2 = $PHPShopGUI->setField('����� ��������', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab2 .= $PHPShopGUI->setField('������ ��������', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('Token �����������', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 300), 1,
        '����� �������������� ������ ������ � ������ ��������. ����� �������� � ��������� ���������.'
    );

    // ������� ���������������
    $tax_system = array (
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
    $Tab2 .= $PHPShopGUI->setField('����� ����������', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "�������� ������ �� �������� ����� ��������� ��", $data["dev_mode"]));
    $Tab2 .= $PHPShopGUI->setField('���������� � ������ ��� �������������', $PHPShopGUI->setCheckbox("force_payment_new", 1, "����� ���������� ������ ��������� �������� ������", $data["force_payment"]));
    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'], true, 300));
    $Tab2 .= $PHPShopGUI->setField('����������� �� ������', $PHPShopGUI->setCheckbox("notification_new", 1, "����������� �� ������ �� Email ��������������", $data["notification"]));

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� �� ���������� ��</li>
<li>�� �������� ��������� ������ ��������������� ���������� �� ����� API �������� (*********-api) � ������ ��������.</li>
<li>������� ����������� ��������� URL Callback-����������� <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/sberbankrf/payment/check.php</code></li>
<li>������ ������ � ������ �������� ����� ������������ token �����������, ��� ���������� ��������� � ��������� ��������� ��.</li>
<li>�� ����� ������������ �������� "����� ����������", ������ ����� ������������ �� �������� ����� ��������� ��</li>
<li>��� �������� ������ � ������� �����, ��������� "����� ����������"</a></li>
</ol>
';

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