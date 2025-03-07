<?php

include_once dirname(__DIR__) . '/class/Shiptor.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.shiptor.shiptor_system"));

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
    if(empty($_POST['delivery_id_new']))
        $_POST['delivery_id_new'] = '';

    $_POST['companies_new'] = serialize(array_unique($_POST['companies']));

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.shiptor.shiptor_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->addJSFiles('../modules/shiptor/admpanel/gui/script.gui.js?v=1.0');

    // �������
    $data = $PHPShopOrm->select();

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
    }

    $Tab1 = $PHPShopGUI->setField('��������� ���� API Shiptor', $PHPShopGUI->setInputText(null, 'api_key_new', $data['api_key'], 300));
    $Tab1.= $PHPShopGUI->setField('��������� ���� API Shiptor', $PHPShopGUI->setInputText(null, 'private_api_key_new', $data['private_api_key'], 300));
    $Tab1.= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('delivery_id_new', $delivery_value, 300));
    $Tab1.= $PHPShopGUI->setField('������ ��� ��������', $PHPShopGUI->setSelect('status_new', $status, 300));
    $Tab1.= $PHPShopGUI->setField('������ ��������', $PHPShopGUI->setSelect('companies[]', Shiptor::getCompanyVariants($data['companies']), 300, false, false, false, false,  1, true));
    $Tab1.= $PHPShopGUI->setField('���������� ������', $PHPShopGUI->setSelect('cod_new', [['��', 1, $data['cod']], ['���', 0, $data['cod']]], 300));
    $Tab1.= $PHPShopGUI->setField('����������� ��������', $PHPShopGUI->setInputText('�� ����� �������', 'declared_percent_new', $data['declared_percent'], 300,'%'));
    $Tab1.= $PHPShopGUI->setField('�������� �������', $PHPShopGUI->setInputText(null, 'fee_new', $data['fee'], 300,'%'));
    $Tab1.= $PHPShopGUI->setField('���������� ������ ��������', $PHPShopGUI->setInputText(null, 'add_days_new', $data['add_days'], 300,'����'));
    $Tab1.= $PHPShopGUI->setField('��������� ���������', $PHPShopGUI->setSelect('round_new', Shiptor::getRoundVariants($data['round']), 300));
    $Tab1.= $PHPShopGUI->setCollapse('��� � �������� �� ���������',
        $PHPShopGUI->setField('���, ��.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">') .
        $PHPShopGUI->setField('������, ��.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['width'] . '" name="width_new" style="width:300px;">') .
        $PHPShopGUI->setField('������, ��.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['height'] . '" name="height_new" style="width:300px;">') .
        $PHPShopGUI->setField('�����, ��.', '<input class="form-control input-sm " onkeypress="shiptorvalidate(event)" type="number" step="1" min="1" value="' . $data['length'] . '" name="length_new" style="width:300px;">')
    );

    $info = '<h4>��������� ������</h4>
        <ol>
        <li>������������������ � <a href="https://shiptor.ru/" target="_blank">Shiptor</a>, ��������� �������.</li>
        <li>������� ������ �������� ��� ������ ������.</li>
        <li>������ <b>��������� ���� API Shiptor</b>.</li>
        <li>������ <b>��������� ���� API Shiptor</b>.</li>
        <li>������� ������ ��� �������� ������ � ������ ������� Shiptor.</li>
        </ol>
        
       <h4>��������� ��������</h4>
        <ol>
        <li>� �������� �������������� �������� � �������� <kbd>��������� ��������� ��������</kbd> ��������� �������������� �������� ���������� ��������� �������� ��� ������. ����� "�� �������� ���������" ������ ���� �������.</li>
        <li>� �������� �������������� �������� � �������� <kbd>������ ������������</kbd> �������� <kbd>�������</kbd> "���." � "������������"</li>
        <li>� �������� �������������� �������� � �������� <kbd>������ ������������</kbd> �������� ���� ������ �������� ������������� ����. 
            ��� ����� �������������� ��� ���������� ��������. ��� ������ �������� � ����� ������ ��� ���� ����� ������������� �������������.</li>
        <li>�������� <kbd>������</kbd> "���." � "������������"</li>
        <li>�������� <kbd>������/����</kbd> "���." � "������������"</li>
        <li>�������� <kbd>�����</kbd> "���." � "������������"</li>
        <li>�������� <kbd>�����</kbd> "���." � "������������"</li>
        <li>�������� <kbd>���</kbd> "���." � "������������"</li>
        <li>�������� <kbd>��������</kbd> "���."</li>
        </ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab4));

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