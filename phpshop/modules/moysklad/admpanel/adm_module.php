<?php

PHPShopObj::loadClass('order');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['moysklad']['moysklad_system']);

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionUpdate() {
    global $PHPShopOrm, $_classPath,$PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $data = $PHPShopOrm->select();

    if ($data['webhooks'] != $_POST['webhooks_new']) {
        include_once($_classPath . 'modules/moysklad/class/MoySklad.php');
        $MoySklad = new MoySklad();

        // ��������� ��������
        if ($_POST['webhooks_new'] == 1) {
            $MoySklad->webhook('on');
        } elseif ($_POST['webhooks_new'] == 2) {
            $MoySklad->webhook('off');
        }
    }

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $_classPath;

    $data = $PHPShopOrm->select();
    include_once($_classPath . 'modules/moysklad/class/MoySklad.php');
    $MoySklad = new MoySklad();

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    $Tab1 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 400, '<a target="_blank" href="https://api.moysklad.ru/app/#token">' . __('��������') . '</a>'));

    if (empty($data['token'])) {
        $Tab1 .= $PHPShopGUI->setField(null, $PHPShopGUI->setAlert('��� ������� � �������������� ����������, ������� "�����" � ������� "���������"', 'warning', true, 400));
    } else {
        try {
            $Tab1 .= $PHPShopGUI->setField('�����������', $PHPShopGUI->setSelect('organization_new', $MoySklad->getOrganizations($data['organization']), 400, null, false, false, false, 1, false)) . $PHPShopGUI->setInput('hidden', 'account_new', $MoySklad->account);
            $Tab1 .= $PHPShopGUI->setField('������ � ������', $PHPShopGUI->setSelect('currency_new', $MoySklad->getCurrencys($data['currency']), 100, null, false, false, false, 1, false));
            $Tab1 .= $PHPShopGUI->setField('���� ���', $PHPShopGUI->setSelect('pricetype_new', $MoySklad->getPricetype($data['pricetype']), 400, null, false, false, false, 1, false));
            $Tab1 .= $PHPShopGUI->setField('�������� ��� �������:', $PHPShopGUI->setSelect('status_new', $order_status_value, 400));

            $e_value[] = array('���', 1, $data['webhooks']);
            $e_value[] = array('����', 2, $data['webhooks']);

            $Tab1 .= $PHPShopGUI->setField('����������� ��������� � ����������', $PHPShopGUI->setSelect('webhooks_new', $e_value, 100, true), 1, '������������� �������� � ����������, ������ ��� ������� �������.');
        } catch (\Exception $exception) {
            $Tab1 .= $exception->getMessage();
        }
    }

    // ����������
    $info = '
    <h4>��� ������������ � ��������?</h4>
<ol>
 <li>������������������ �� ����� <a href="https://moysklad.ru/register/?p=F264" target="_blank">��������</a>
</li></ol>     


        <h4>��������� ������</h4>
        <ol>
<li>� ���� "�����" ������ ����� � ������ �������� � ������� ��������.</li>
<li>������� �����������.</li>
<li>������� ������ � ������.</li>
<li>������� ��� ���.</li>
<li>������� ������ ������ ��� �������� ������.</li>
<li>�������� ������������ ��������� � ��������. �������� ������ �� ������� �������.</li>
</ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3), array("������ ��������", null, '?path=modules.dir.moysklad'));

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