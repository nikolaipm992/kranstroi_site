<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.paykeeper.paykeeper_system"));

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

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    PHPShopObj::loadClass('order');

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������������ ���� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('����� ����� ������', $PHPShopGUI->setInputText(false, 'form_url_new', $data['form_url'], 500));
    $Tab1 .= $PHPShopGUI->setField('��������� �����', $PHPShopGUI->setInputText(false, 'secret_new', $data['secret'], 500));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);


    // �������������� ���� ������
    $value_arr = $data['forced_discount_check'] == 1 ? array(array('���', 1, 'selected'), array('����', 2, false)) : array(array('���', 1, false), array('����', 2, 'selected'));
    $Tab1 .= $PHPShopGUI->setField('�������������� ���� ������', $PHPShopGUI->setSelect('forced_discount_check_new', $value_arr, 300));

    // ������ ������
    //$Tab1.= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value));
    $Tab1 .= $PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));


    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay($data['serial'], false, $data['version'], true);

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
        <li>������������������, ��������� ������� � <a href="https://paykeeper.ru/paykeeper/register/registerform/" target="_blank">PayKeeper</a>.</li>
        <li>��������� ����� ���������� ������������� � ������ �������� PayKeeper, ����������� � �������� � ���� <kbd>��������� ����</kbd> � ���������� ������. </li>
        <li>� ���� ����� ����� ������ ������� URL ����� ���������� ����: <code>https://���_�����.server.paykeeper.ru/order/inline/cp1251</code></li>
        </ol>
        <h4>��������� ������� �������� PayKeeper</h4>
        <ol>
         <li>�������� "������ ��������� ����������� � ��������" �� <kbd>POST-����������</kbd>.</li>
         <li>� ���� "URL, �� ������� ����� ������������ POST-�������" ������� URL-����� ����: <code>http://���_�����.ru/success/</code></li>
         <li>��������� ����� ����� ��������� �������������� ��� ������������� � ������� ������ <kbd>�������������</kbd>.</li>
         <li>� ������� "������ ��������������� �������" � ����� "URL ��������, �� ������� ������ ��������� ��� �������� ���������� ������" � "URL ��������, �� ������� ������ ��������� ��� ������� � �������� ������" ������� <code>http://���_�����.ru/</code></li>
        </ol>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>
