<?php

PHPShopObj::loadClass('order');

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.acquiropay.acquiropay_system"));

// ���������� ������ ������
function actionBaseUpdate()
{
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    return $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate()
{
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=acquiropay');
    return $action;
}

function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField(
        'ID ��������',
        $PHPShopGUI->setInputText(false, 'product_id_new', $data['product_id'], 250)
    );
    $Tab1 .= $PHPShopGUI->setField(
        'ID ��������',
        $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 250)
    );
    $Tab1 .= $PHPShopGUI->setField(
        '��������� ����',
        $PHPShopGUI->setInputText(false, 'merchant_skey_new', $data['merchant_skey'], 250)
    );
    $Tab1 .= $PHPShopGUI->setField(
        'URL ��������� �����',
        $PHPShopGUI->setInputText(false, 'endpoint_url_new', $data['endpoint_url'], 250)
    );
    $Tab1 .= '<input type="hidden" name="use_cashbox_new" value="0" />';
    $Tab1 .= $PHPShopGUI->setField(
        '������ ����� ����� AcquiroPay',
        $PHPShopGUI->setCheckbox(
            'use_cashbox_new',
            1,
            '������������',
            (int)$data['use_cashbox'] === 1
        )
    );

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray)) {
        foreach ($OrderStatusArray as $order_status) {
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);
        }
    }

    // ������ ������
    $Tab1 .= $PHPShopGUI->setField('������ ��� �������',
        $PHPShopGUI->setSelect('status_new', $order_status_value, 250));

    $Tab1 .= $PHPShopGUI->setField('��������� ����� �������', $PHPShopGUI->setTextarea('title_new', $data['title']));
    $Tab1 .= $PHPShopGUI->setField('��������� ��������������� ��������',
        $PHPShopGUI->setTextarea('title_sub_new', $data['title_sub']));

    $info = '
<h4>��������� ������</h4>
<ol>
    <li>������������������ � <a href="http://acquiropay.com/" target="_blank">AcquiroPay</a></li>
    <li>���������� � ���������� ����������� id ��������, id �������� � ��������� ���� ������ � ��������� ������� ������</li>
    <li>������� URL ��������� ����� - https://secure.acqp.co (�������� ����) ��� https://secure.acquiropay.com</li>
    <li>� ������ �������� ������, ������������ ����� ������������� �� <code>http://'.$_SERVER['SERVER_NAME'].'/success/</code></li>
    <li>���� ������ �� �����-�� �������� �� ������, ������������ ����� ������������� �� <code>http://'.$_SERVER['SERVER_NAME'].'/fail/</code></li>
    <li>���������� � �������� �� AcquiroPay ����� ��������� �� <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/acquiropay/payment/result.php</code></li>
</ol>
<p>�������������� ��������� �� ������������ ������ 54 ���������� �� ��������� � ���������� ������ ������.</p>
';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3));

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>