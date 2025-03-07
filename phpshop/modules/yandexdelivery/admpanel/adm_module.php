<?php

include_once dirname(__DIR__) . '/class/include.php';

// SQL
$PHPShopOrm = new PHPShopOrm('phpshop_modules_yandexdelivery_system');

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
    if (empty($_POST['delivery_id_new']))
        $_POST['delivery_id_new'] = '';

    if (!isset($_POST['paid_new'])) {
        $_POST['paid_new'] = '0';
    }

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexdelivery.yandexdelivery_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;


    // ����� ��� ��������
    $PHPShopGUI->addJSFiles('../modules/yandexdelivery/admpanel/gui/warehouse.gui.js');


    // �������
    $data = $PHPShopOrm->select();

    if (empty($data['warehouse_id']))
        $buttonText = '�������';
    else
        $buttonText = '��������';

    $Tab1 .= $PHPShopGUI->setField('����� ������.OAuth', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 400));
    $Tab1 .= $PHPShopGUI->setField('������� ��������', $PHPShopGUI->setInputText(false, 'warehouse_id_new', $data['warehouse_id'], 400, '<a id="yandexdelivery-select-warehouse" href="#">' . __($buttonText) . '</a>'));
    $Tab1 .= $PHPShopGUI->setField('������ ��� ��������', $PHPShopGUI->setSelect('status_new', YandexDelivery::getDeliveryStatuses($data['status']), 300));
    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('delivery_id_new', YandexDelivery::getDeliveryVariants($data['delivery_id']), 300));
    $Tab1 .= $PHPShopGUI->setField('����� �� ����� �� ���������', $PHPShopGUI->setInputText(false, 'city_new', $data['city'], 300));
    $Tab1 .= $PHPShopGUI->setField('�������� �������', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="0.1" min="0" value="' . $data['fee'] . '" name="fee_new" style="width:300px;">');
    $Tab1 .= $PHPShopGUI->setField('��� �������', $PHPShopGUI->setSelect('fee_type_new', array(array('%', 1, $data['fee_type']), array('���.', 2, $data['fee_type'])), 300, null, false, $search = false, false, $size = 1));
    $Tab1 .= $PHPShopGUI->setField('������ ������', $PHPShopGUI->setCheckbox('paid_new', 1, '����� �������', $data["paid"]));


    $Tab1 .= $PHPShopGUI->setCollapse('��� � �������� �� ���������', $PHPShopGUI->setField('���, ��.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">') .
            $PHPShopGUI->setField('������, ��.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['width'] . '" name="width_new" style="width:300px;">') .
            $PHPShopGUI->setField('������, ��.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['height'] . '" name="height_new" style="width:300px;">') .
            $PHPShopGUI->setField('�����, ��.', '<input class="form-control input-sm " onkeypress="yadeliveryvalidate(event)" type="number" step="1" min="1" value="' . $data['length'] . '" name="length_new" style="width:300px;">')
    );

    PHPShopParser::set('yandexdelivery_weight', $data['weight']);
    PHPShopParser::set('yandexdelivery_city', $data['city']);
    PHPShopParser::set('yandexdelivery_station', $data['warehouse_id']);
    $Tab1 .= ParseTemplateReturn('../modules/yandexdelivery/templates/template.tpl', true);


    $info = '<h4>��������� ������� ������ � ���������</h4>
       <ol>
        <li>����������������� � <a href="https://dostavka.yandex.ru" target="_blank">������.��������</a> ��������� ��� ����������� ������.
        <li>��������  <a href="https://dostavka.yandex.ru/account2/integration" target="_blank">����� ������.OAuth</a> � ������� ��� � ���� <kbd>����� ������.OAuth</kbd>.</li>
        <li>������� ������� �������� � ���������� �������� ������.</li>
        <li>������� ������ �������� ��� ������ ������.</li>
        <li>������� ������ ��� �������� ������ � ������ ������� ������.��������.</li>
        </ol>
        
       <h4>��������� ��������</h4>
        <ol>
        <li>� �������� �������������� �������� � �������� <kbd>��������� ��������� ��������</kbd> ��������� �������������� �������� ���������� ��������� �������� ��� ������. ����� "�� �������� ���������" ������ ���� �������.</li>
         <li>� �������� �������������� �������� � �������� <kbd>������ ������������</kbd> �������� <kbd>�������</kbd> "���." � "������������".</li>
        </ol>

';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab4));

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