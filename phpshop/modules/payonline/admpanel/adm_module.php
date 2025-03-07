<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payonline']['payonline_system']);

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;
    $PHPShopOrm->debug = false;

    if (empty($_POST["fiskalization_new"]))
        $_POST["fiskalization_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab2  = $PHPShopGUI->setField('Merchant ID:', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab2 .= $PHPShopGUI->setField('��������� ����:', $PHPShopGUI->setInput("password", 'key_new', $data['key'], false, 300));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab2 .= $PHPShopGUI->setField('������ ��� �������:', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'],true,300));
    $Tab2 .= $PHPShopGUI->setField('�������� ������:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment'],true,300));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $page = $PHPShopOrm->select(array('*'), false, array('order' => 'name asc'));

    $value = array();
    $value[] = array(__('�� ������������'), 0, $data['page_id']);
    if (is_array($page))
        foreach ($page as $val) {
            $value[] = array($val['name'], $val['id'], $data['page_id']);
        }

    $Tab2.=$PHPShopGUI->setField('�������� �������� ������:', $PHPShopGUI->setSelect('page_id_new', $value, 300));
    $Tab2 .= $PHPShopGUI->setField('������������', $PHPShopGUI->setCheckbox("fiskalization_new", 1, "�������� ������������ ��������", $data["fiskalization"]));

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � <a href="http://www.payonline.ru/" target="_blank">PayOnline</a></li>
<li>�� �������� ��������� ������ "Merchant ID".</li>
<li>�� �������� ��������� ������ "��������� ����".</li>
<li>� ������ �������� <a href="http://www.payonline.ru/" target="_blank">PayOnline</a> ������� Check URL: <code>https://' . $_SERVER['SERVER_NAME'] . '/phpshop/modules/payonline/payment/check.php</code> <br></li>
</ol>';
    
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