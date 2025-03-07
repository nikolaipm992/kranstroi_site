<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['modulbank']['modulbank_system']);
// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    if (empty($_POST["dev_mode_new"]))
        $_POST["dev_mode_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setInfo('<p>������ ���������� ��������-�������� � ��������� ������ �����������, ��������� ��������� ������ ������ ������ ����� ����������.
 ����� ������� ������, ���������� ���������� ����������� ��������� �� ��������������� �������. ����� ���������� ��������� ���������� ������� �� �������� ����� �����������</p>');

    $Tab2 = $PHPShopGUI->setField('������������� ��������:', $PHPShopGUI->setInputText(false, 'merchant_new', $data['merchant'], 300));
    $Tab2 .= $PHPShopGUI->setField('��������� ����:', $PHPShopGUI->setInput("password", 'key_new', $data['key'], false, 300));

    // ������� ���������������
    $tax_system = array (
        array("����� ������� ���������������", 'osn', $data["taxationSystem"]),
        array("���������� ������� ��������������� (�����)", 'usn_income', $data["taxationSystem"]),
        array("���������� ������� ��������������� (����� ����� ������)", 'usn_income_outcome', $data["taxationSystem"]),
        array("������ ����� �� ��������� �����", 'envd', $data["taxationSystem"]),
        array("������ �������������������� �����", 'esn', $data["taxationSystem"]),
        array("��������� ������� ���������������", 'patent', $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('C������ ���������������:', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab2 .= $PHPShopGUI->setField('������ ��� �������:', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    $Tab2 .= $PHPShopGUI->setField('����� ����������:', $PHPShopGUI->setCheckbox("dev_mode_new", 1, "�������� ������ �� �������� �����", $data["dev_mode"]));

    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $Tab2 .= $PHPShopGUI->setField('�������� ������:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment']));

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="https://modulbank.ru/ekvayring/internet" target="_blank">������������</a></li>
<li>�� �������� ��������� ������ "������������� ��������", ���������� �� �����������.</li>
<li>�� �������� ��������� ������ "��������� ����", ���������� �� �����������.</li>
<li>�� ����� ������������ �������� "����� ����������", ������ �������� "��������� ����", ������ ����� ������������ �� �������� ����� �����������</li>
<li>��� �������� ������ � ������� �����, ��������� "����� ����������", ������ ������� "��������� ����".</a></li>
</ol>
';

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab2, true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $Tab1));

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