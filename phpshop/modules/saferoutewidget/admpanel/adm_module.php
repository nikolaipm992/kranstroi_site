<?php

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.saferoutewidget.saferoutewidget_system"));

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
    global $PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    // ��������
    if (isset($_POST['delivery_id_new'])) {
        if (is_array($_POST['delivery_id_new'])) {
            foreach ($_POST['delivery_id_new'] as $val) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
                $PHPShopOrm->update(array('is_mod_new' => 2), array('id' => '=' . intval($val)));
            }
            $_POST['delivery_id_new'] = @implode(',', $_POST['delivery_id_new']);
        }
    }

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.saferoutewidget.saferoutewidget_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();

    $PHPShopGUI->addJSFiles('../modules/saferoutewidget/admpanel/gui/saferoutewidget.gui.js');

    // ��������
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray))
        foreach ($DeliveryArray as $delivery) {

            // ������� ������������
            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            if (in_array($delivery['id'], @explode(",", $data['delivery_id'])))
                $delivery_id = $delivery['id'];
            else
                $delivery_id = null;

            $delivery_value[] = array($delivery['city'], $delivery['id'], $delivery_id);
        }


    $Tab1 = $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'key_new', $data['key'], 300));
    $Tab1 .= $PHPShopGUI->setField('ID ��������', $PHPShopGUI->setInputText(false, 'shop_id_new', $data['shop_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('delivery_id_new[]', $delivery_value, 300, null, false, $search = false, false, 1, true));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

    $status[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $status[] = array($order_status['name'], $order_status['id'], $data['status']);
        }

    // ������ ������
    $Tab1 .= $PHPShopGUI->setField('������ ��� ��������', $PHPShopGUI->setSelect('status_new', $status, 300));

    // ��������� ������
    $Tab1 .= $PHPShopGUI->setField('��������� ������:', $PHPShopGUI->setRadio('prod_enabled_new', 1, '��������', $data['prod_enabled']) . $PHPShopGUI->setRadio('prod_enabled_new', 2, '���������', $data['prod_enabled']), false, '����� ���������� ������� �� ��������� �������.');

    $Tab2 = $PHPShopGUI->setFrame('seopult', 'https://cabinet.saferoute.ru/cabinet/widgets/cart?shopId=' . $data['shop_id'], '99%', '700', 'none', 0);


    $info = '<h4>��������� API �����</h4>
       <ol>
        <li>������������������ � <a href="https://saferoute.ru/" target="_blank">Saferoute.ru</a>.</li>
        <li>������� �� ������  <a target="_blank" href="https://cabinet.saferoute.ru/user2/#/shops">������ � ��������</a>. ����������� ID �������� � ���� "ID ��������".</li>
        <li>"�����" ����������� �� �������� ������ ������� � ������ �������� SafeRoute.</li>
        </ol>
        
       <h4>��������� ������</h4>
        <ol>
        <li>�������� ������ �������� ������ �� ������ "������������".</li>
        <li>������� ��� �������� ��� ��������� ������.</li>
        <li>������� ������ ������ ��� �������������� �������� ������ �� ������ Saferoute.ru</li>
        </ol>

       <h4>��������� ���������� �������</h4>
        <ol>
        <li>��� ���������� ������ "��������� ������" ����� �������� ���������� <code>@saferouteCart@</code> � ���� ������.</li>
        <li>��� �������������� ����� ������ �������������� ������ <code>phpshop/modules/saferoutewidget/templates/product_widget.tpl</code></li>
        </ol>
        
       <h4>��������� ��������</h4>
        <ol>
        <li>� �������� �������������� �������� � �������� <kbd>��������</kbd> ��������� �������������� �������� ���������� ��������� �������� ��� ������. ����� "�� �������� ���������" ������ ���� �������.</li>
        </ol>

';

    $Tab3 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("������ ��������", $Tab2), array("����������", $Tab3), array("� ������", $Tab4));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>