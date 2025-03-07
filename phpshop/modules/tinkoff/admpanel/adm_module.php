<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.tinkoff.tinkoff_system"));

/**
 * ���������� ������ ������
 * @return mixed
 */
function actionBaseUpdate(){
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

/**
 * ���������� ��������
 * @return mixed
 */
function actionUpdate(){
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    if (empty($_POST["force_payment_new"]))
        $_POST["force_payment_new"] = 0;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

/**
 * ����������� �������� ������
 * @return bool
 */
function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    PHPShopObj::loadClass('order');

    $PHPShopOrm->objBase = $GLOBALS['SysValue']['base']['tinkoff']['tinkoff_system'];
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������������ ���� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('����', $PHPShopGUI->setInputText(false, 'gateway_new', $data['gateway'], 300));
    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'terminal_new', $data['terminal'], 300));
    $Tab1 .= $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setInputText(false, 'secret_key_new', $data['secret_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('���������� � ������ ��� �������������', $PHPShopGUI->setCheckbox("force_payment_new", 1, "����� ���������� ������ ��������� �������� ������", $data["force_payment"]));

    $onclick = "function toggleTaxation() { document.getElementsByClassName('tinkoff-taxation')[0].classList.toggle('hidden'); }     
        toggleTaxation();";

    $Tab1 .= $PHPShopGUI->setField("���������� ������ ��� ������������ ����", $PHPShopGUI->setRadio("enabled_taxation_new", 1, "��", $data['enabled_taxation'], $onclick)
        . $PHPShopGUI->setRadio("enabled_taxation_new", 0, "���", $data['enabled_taxation'], $onclick));
    
    
    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � <a href="https://www.tbank.ru/kassa/form/partner/phpshop/" target="blank">��������� ������� � �-����</a>.</li>
<li>�� �������� ��������� ������ ��������������� ������ �-���� ����� "�����", ��� "���������" � "��������� ����".</li>
<li>������� ����� ��������������� ������� ��� ���������� ����� �������� ������ ������ ��� ������������ ����.</a></li>
<li>������� ����� ��������������� �������� � �������� �������������� ��������.</a></li>
<li>� ������ �������� �-���� � ������� "��������" ������� ����� ��� ����������� � �������� ����� <code>http://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/tinkoff/payment/notification.php</code></li>
<li>� ������ �������� �-���� � ������� "��������" ������� URL �������� ��������� ������� <code>http://' . $_SERVER['SERVER_NAME'] . '/success/?payment=tinkoff</code></li>
<li>� ������ �������� �-���� � ������� "��������" ������� URL �������� ����������� ������� <code>http://' . $_SERVER['SERVER_NAME'] . '/fail/</code></li>
</ol>';

    $taxation = array(
        array('����� ��', 'osn', $data['taxation']),
        array('���������� �� (������)', 'usn_income', $data['taxation']),
        array('���������� �� (������ ����� �������) ', 'usn_income_outcome', $data['taxation']),
        array('������ ����� �� ��������� �����', 'envd', $data['taxation']),
        array('������ �������������������� �����', 'esn', $data['taxation']),
        array('��������� ��', 'patent', $data['taxation']),
    );
    $taxationSelect = $PHPShopGUI->setSelect('taxation_new', $taxation, 300,true);
    $Tab1 .= $PHPShopGUI->setField('������� ���������������', $taxationSelect, 1, null, 'tinkoff-taxation' . ($data['enabled_taxation'] ? '' : ' hidden'));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status){
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
            $order_status_confirmed_value[] = array($order_status['name'], $order_status['id'], $data['status_confirmed']);
        }

    // ������ ������
    $Tab1.= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab1.= $PHPShopGUI->setField('������ ����� ������������ ������', $PHPShopGUI->setSelect('status_confirmed_new', $order_status_confirmed_value, 300));

    $Tab1.=$PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end']));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], true);
    
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true),array("����������", $PHPShopGUI->setInfo($info)),array("� ������", $Tab3));

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
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
