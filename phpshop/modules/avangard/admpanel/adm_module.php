<?php
PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['avangard']['avangard_system']);

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate(number_format($option['version'], 1, '.', false));
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

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

    include_once '../modules/avangard/class/Avangard.php';

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setInfo('<p>������ ���������� ��������-�������� � ��������� ������ ����� ��������, ��������� ��������� ������ ������ ���������� ������.
 ����� ������� ������, ���������� ���������� ����������� ��������� �� ��������������� �������.</p>');

    $Tab2 = $PHPShopGUI->setField('ID ��������:', '<input class="form-control input-sm" type="number" step="1" min="0" value="' . $data['shop_id'] . '" name="shop_id_new" style="width:300px; ">');
    $Tab2 .= $PHPShopGUI->setField('������ ��������:', $PHPShopGUI->setInput('password', 'password_new', $data['password'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('������� ��������:', $PHPShopGUI->setInput('text', 'shop_sign_new', $data['shop_sign'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('������� ������� ����������:', $PHPShopGUI->setInput('text', 'av_sign_new', $data['av_sign'], false, 300));
    
    $qr_value = array(
        array('Off', 0, $data['qr']),
        array('On', 1, $data['qr'])
    );
    $Tab2 .= $PHPShopGUI->setField('������ �� QR:', $PHPShopGUI->setSelect('qr_new', $qr_value, 0));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status_id']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status_id']);

    // ������ ������
    $Tab2 .= $PHPShopGUI->setField('������ ��� �������:', $PHPShopGUI->setSelect('status_id_new', $order_status_value, 300));
    $Tab2 .= $PHPShopGUI->setField('��������� ��������������� ��������:', $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub'], false, 300));
    $Tab2 .= $PHPShopGUI->setField('�������� ������:', $PHPShopGUI->setTextarea('title_payment_new', $data['title_payment'], false, 300));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $page = $PHPShopOrm->select(array('*'), false, array('order' => 'name asc'));

    $value = array();
    $value[] = array(__('�� ������������'), 0, $data['page_id']);
    if (is_array($page))
        foreach ($page as $val) {
            $value[] = array($val['name'], $val['id'], $data['page_id']);
        }

    $Tab2.=$PHPShopGUI->setField('�������� �������� ������:', $PHPShopGUI->setSelect('page_id_new', $value, 300));

    // ����������
    $info = '
        <h4>��������� ������</h4>
        <ol>
<li>������������ ����������� ��������� � ��������� ������� � ������ <a href="https://www.avangard.ru/rus/" target="_blank">��������</a></li>
<li>�� �������� ��������� ������ "ID ��������", ���������� �� �����.</li>
<li>�� �������� ��������� ������ "������ ��������", ���������� �� �����.</li>
<li>�� �������� ��������� ������ "������� ��������", ���������� �� �����.</li>
<li>�� �������� ��������� ������ "������� ������� ����������", ���������� �� �����.</li>
<li>� ������ �������� <a href="https://www.avangard.ru/rus/" target="_blank">��������</a> ������� URL ����������� �� �������� �������: <code>' . Avangard::getProtocol() . $_SERVER['SERVER_NAME'] . '/phpshop/modules/avangard/payment/check.php</code> <br></li>
</ol>
';
	$Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);
	
	$contacts = '�� �������� ������ ������ ��������� � ���� e-com@avangard.ru';

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������", $Tab2, true), array("����������", $PHPShopGUI->setInfo($info), true), array("� ������", $Tab3), array("���������", $PHPShopGUI->setInfo($contacts)));

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