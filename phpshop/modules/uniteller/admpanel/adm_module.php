<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['uniteller']['uniteller_system']);
// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab2  = $PHPShopGUI->setField('Uniteller Point ID:', $PHPShopGUI->setInputText(false, 'shop_idp_new', $data['shop_idp'], 300));
    $Tab2 .= $PHPShopGUI->setField('������:', $PHPShopGUI->setInput("password", 'password_new', $data['password'], false, 300));

    // ������� ���������������
    $tax_system = array (
        array("����� ������� ���������������", 0, $data["taxationSystem"]),
        array("���������� ������� ��������������� (�����)", 1, $data["taxationSystem"]),
        array("���������� ������� ��������������� (����� ����� ������)", 2, $data["taxationSystem"]),
        array("������ ����� �� ��������� �����", 3, $data["taxationSystem"]),
        array("������ �������������������� �����", 4, $data["taxationSystem"]),
        array("��������� ������� ���������������", 5, $data["taxationSystem"])
    );
    $Tab2 .= $PHPShopGUI->setField('������� ���������������:', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab2 .= $PHPShopGUI->setField('������ ��� �������:', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $Tab2 .= $PHPShopGUI->setField('�������� ������:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment']));

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="https://www.uniteller.ru" target="_blank">Uniteller</a></li>
<li>�� �������� ��������� ������ "Uniteller Point ID", ������� ����� ����� � ������ �������� Uniteller, � ���� "����� ������".</li>
<li>�� �������� ��������� ������ "������", ������� ����� ����� � ������ �������� Uniteller, � ���� "��������� �����������".</li>
<li>� ������ �������� <a href="https://www.uniteller.ru" target="_blank">Uniteller</a> ������� Check URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/uniteller/payment/check.php</code> <br></li>
</ol>
';
    
    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab2, true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $Tab3));

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