<?php

include_once dirname(__DIR__) . '/class/BoxberryWidget.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.boxberrywidget.boxberrywidget_system"));

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

    if (!isset($_POST['paid_new'])) {
        $_POST['paid_new'] = '0';
    }

    $Boxberry = new BoxberryWidget();
    if (!empty($_POST['token_new'])) {
        $request = $Boxberry->requestGet(BoxberryWidget::GET_API_KEY_METHOD, array());
        $_POST['api_key_new'] = $request['key'];
    }

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
    if (isset($_POST['express_delivery_id_new'])) {
        if (is_array($_POST['express_delivery_id_new'])) {
            foreach ($_POST['express_delivery_id_new'] as $val) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
                $PHPShopOrm->update(array('is_mod_new' => 2), array('id' => '=' . intval($val)));
            }
            $_POST['express_delivery_id_new'] = @implode(',', $_POST['express_delivery_id_new']);
        }
    }
    if (empty($_POST['express_delivery_id_new']))
        $_POST['express_delivery_id_new'] = '';
    if (empty($_POST['delivery_id_new']))
        $_POST['delivery_id_new'] = '';

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.boxberrywidget.boxberrywidget_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopBase;

    // �������
    $data = $PHPShopOrm->select();
    $PHPShopGUI->field_col = 4;

    // ����-�����
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $data['token'] = '';
    }

    // ����� ��� ��������
    $PHPShopGUI->addJSFiles('//points.boxberry.ru/js/boxberry.js');
    $PHPShopGUI->addJSFiles('../modules/boxberrywidget/admpanel/gui/boxberrywidget.gui.js');

    if (empty($data['pvz_id']))
        $buttonText = '�������';
    else
        $buttonText = '��������';

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();

    $status[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status) {
            $status[] = array($order_status['name'], $order_status['id'], $data['status']);
        }

    // ��������
    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "!='1'", 'enabled' => "='1'"));

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray)) {
        foreach ($DeliveryArray as $delivery) {

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
        foreach ($DeliveryArray as $delivery) {

            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            if (in_array($delivery['id'], @explode(",", $data['express_delivery_id'])))
                $express_delivery_id = $delivery['id'];
            else
                $express_delivery_id = null;

            $express_delivery_value[] = array($delivery['city'], $delivery['id'], $express_delivery_id);
        }
    }

    $api = array(
        array('http://api.boxberry.de', 'http://api.boxberry.de', $data['api_url']),
        array('http://api.boxberry.ru', 'http://api.boxberry.ru', $data['api_url'])
    );

    $Tab1 = $PHPShopGUI->setField('API token', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 300));
    $Tab1 .= $PHPShopGUI->setField('URL ����� API', $PHPShopGUI->setSelect('api_url_new', $api, 300));
    $Tab1 .= $PHPShopGUI->setField('ID ������ ����������� ��', $PHPShopGUI->setInputText(false, 'pvz_id_new', $data['pvz_id'], 300, '<a id="link-activate-ddelivery" onclick="getPVZ()" href="#">' . __($buttonText) . '</a>'));
    $Tab1 .= $PHPShopGUI->setField('������ ��� ��������', $PHPShopGUI->setSelect('status_new', $status, 300));
    $Tab1 .= $PHPShopGUI->setField('�������� ��������� �� ���', $PHPShopGUI->setSelect('delivery_id_new[]', $delivery_value, 300, null, false, $search = false, false, $size = 1, $multiple = true));
    $Tab1 .= $PHPShopGUI->setField('���������� ��������', $PHPShopGUI->setSelect('express_delivery_id_new[]', $express_delivery_value, 300, null, false, $search = false, false, $size = 1, $multiple = true));
    $Tab1 .= $PHPShopGUI->setField('����� �� ����� �� ���������', $PHPShopGUI->setInputText(false, 'city_new', $data['city'], 300));
    $Tab1 .= $PHPShopGUI->setField('�������� �������', '<input class="form-control input-sm " type="number" step="0.1" min="0" value="' . $data['fee'] . '" name="fee_new" style="width:300px;">');
    $Tab1 .= $PHPShopGUI->setField('��� �������', $PHPShopGUI->setSelect('fee_type_new', array(array('%', 1, $data['fee_type']), array('���.', 2, $data['fee_type'])), 300, null, false, $search = false, false, $size = 1));
    $Tab1 .= $PHPShopGUI->setField('������ ������', $PHPShopGUI->setCheckbox('paid_new', 1, '����� �������', $data["paid"]));

    $Tab1 = $PHPShopGUI->setCollapse('���������', $Tab1);

    $Tab1 .= $PHPShopGUI->setCollapse('��� � �������� �� ���������', $PHPShopGUI->setField('���, ��.', $PHPShopGUI->setInputText('', 'weight_new', $data['weight'], 300)) .
            $PHPShopGUI->setField('������, ��.', $PHPShopGUI->setInputText('', 'width_new', $data['width'], 300)) .
            $PHPShopGUI->setField('������, ��.', $PHPShopGUI->setInputText('', 'height_new', $data['height'], 300)) .
            $PHPShopGUI->setField('�����, ��.', $PHPShopGUI->setInputText('', 'depth_new', $data['depth'], 300))
    );

    $info = '<h4>��������� ����� ���������� � API token</h4>
       <ol>
        <li>������������������ � <a href="https://api.boxberry.ru" target="_blank">api.boxberry.ru</a>.</li>
        <li>API token �������� �� ������ <a target="_blank" href="https://api.boxberry.ru/?act=info&sub=api_info_lk">������� API ��</a>.</li>
        </ol>
        
       <h4>��������� ������</h4>
        <ol>
        <li>������� �������� "�������� ��������� �� ���" � "���������� ��������". ����������� ����� ������ ������ ���� ��������, ������ ����� �������� �� �����������. ������ ������� ���� � �� �� �������� � ��� "�������� ��������� �� ���" � ��� "���������� ��������".</li>
        <li>"��� API token" ����������� � ���� �������� "API token" ������.</li>
        <li>"���� ����������" ����������� � ���� �������� "���� ����������" ������.</li>
        <li>������� "URL ����� API" �� �����������. ����� api.boxberry.ru �� ������ ������� �������� Boxberry.</li>
        <li>"ID ������ ����������� ��" ������� ��� �������� �������.</li>
        <li>"����� �� ����� �� ���������" ������� ����� ������������ ��� �������� �����. ��� ��������������� ����������� ������ ������������ ������ "GeoIP Redirect".</li>
        <li>��������� ��� � �������� �� ���������</li>
        </ol>
        
       <h4>��������� ��������</h4>
        <ol>
        <li>� �������� �������������� �������� � �������� <kbd>��������� ��������� ��������</kbd> ��������� �������������� �������� ���������� ��������� �������� ��� ������. ����� "�� �������� ���������" ������ ���� �������.</li>
         <li>� �������� �������������� ��������  <kbd>������ ������������</kbd> �������� <kbd>���</kbd> "���." � "������������"</li>
         <li>� �������� �������������� ��������  <kbd>������ ������������</kbd> �������� <kbd>�������</kbd> "���." � "������������"</li>
         <li>� �������� �������������� �������� ��� ���������� �������� � �������� <kbd>������ ������������</kbd> �������� <kbd>������</kbd> "���." � "������������"</li>
        </ol>

';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true), array("����������", $Tab2), array("� ������", $Tab4));

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