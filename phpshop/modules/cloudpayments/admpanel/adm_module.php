<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudpayments.cloudpayment_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    PHPShopObj::loadClass('order');

    // �������
    $data = $PHPShopOrm->select();


    $Tab1 = $PHPShopGUI->setField('������ �� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1.=$PHPShopGUI->setField('������������� ����� publicId', $PHPShopGUI->setInputText(false, 'publicId_new', $data['publicId'], 300));
    $Tab1.=$PHPShopGUI->setField('������ ��� API', $PHPShopGUI->setInputText(false, 'api_new', $data['api'], 300));
    $Tab1.=$PHPShopGUI->setField('�������� ���������� ������ � ������������ �������', $PHPShopGUI->setInputText(false, 'description_new', $data['description'], 300));

    // ������� ���������������
    $tax_system = array (
        array("����� ������� ���������������", 0, $data["taxationSystem"]),
        array("���������� ������� ��������������� (�����)", 1, $data["taxationSystem"]),
        array("���������� ������� ��������������� (����� ����� ������)", 2, $data["taxationSystem"]),
        array("������ ����� �� ��������� �����", 3, $data["taxationSystem"]),
        array("������ �������������������� �����", 4, $data["taxationSystem"]),
        array("��������� ������� ���������������", 5, $data["taxationSystem"])
    );
    $Tab1.= $PHPShopGUI->setField('C������ ���������������', $PHPShopGUI->setSelect('taxationSystem_new', $tax_system, 300,true));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab1.= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    $Tab1.=$PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    $info = '
        <h4>��� ������������ � CloudPayments?</h4>
        <ol>
<li>������������ � ������������ ��������.</li>
<li>������������ � <a href="https://cloudpayments.ru/Docs/Oferta" target="_blank">���������-�������</a></li>
<li>��������� <a href="https://cloudpayments.ru/Docs/%D0%9F%D1%80%D0%B8%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%201.docx" target="_blank">���������� �1</a>, ���������, ��������� ������ � �������� �� ����� sales@cloudpayments.ru</li>
<li>��������� ���� �� ������������ <a href="https://cloudpayments.ru/Docs/Requirements" target="_blank">�����������</a></li>
<li>� ������ �������� CloudPayments ������� ����, ������� ����� <code>http://' . $_SERVER['SERVER_NAME'] . '</code></li>
<li>�� �������� ����� � ������ �������� CloudPayments ����������� Public ID � ������ ��� API, �� �������� "��������" ������ ������ �� � ��������������� ����</li>
<li>� ������ �������� CloudPayments ������� ����� ��� Pay ����������� <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/cloudpayments/payment/pay.php</code> HTTP ����� POST, ��������� Windows-1251</li>
<li>� ������ �������� CloudPayments ������� ����� ��� Check ����������� <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/cloudpayments/payment/check.php</code> HTTP ����� POST, ��������� Windows-1251</li>
</ol>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>