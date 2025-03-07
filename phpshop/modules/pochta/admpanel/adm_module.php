<?php

include_once dirname(__DIR__) . '/class/include.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.pochta.pochta_system'));

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

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.pochta.pochta_system'));
    $PHPShopOrm->debug = false;

    if(!isset($_POST['easy_return_new'])) {
        $_POST['easy_return_new'] = '0';
    }
    if(!isset($_POST['no_return_new'])) {
        $_POST['no_return_new'] = '0';
    }
    if(!isset($_POST['fragile_new'])) {
        $_POST['fragile_new'] = '0';
    }
    if(!isset($_POST['wo_mail_rank_new'])) {
        $_POST['wo_mail_rank_new'] = '0';
    }
    if(!isset($_POST['completeness_checking_new'])) {
        $_POST['completeness_checking_new'] = '0';
    }
    if(!isset($_POST['sms_notice_new'])) {
        $_POST['sms_notice_new'] = '0';
    }
    if(!isset($_POST['electronic_notice_new'])) {
        $_POST['electronic_notice_new'] = '0';
    }
    if(!isset($_POST['order_of_notice_new'])) {
        $_POST['order_of_notice_new'] = '0';
    }
    if(!isset($_POST['simple_notice_new'])) {
        $_POST['simple_notice_new'] = '0';
    }
    if(!isset($_POST['vsd_new'])) {
        $_POST['vsd_new'] = '0';
    }
    if(!isset($_POST['paid_new'])) {
        $_POST['paid_new'] = '0';
    }
    
    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm,$PHPShopBase;

    $PHPShopGUI->addJSFiles('../modules/pochta/admpanel/gui/script.gui.js?v=1.0');

    // �������
    $data = $PHPShopOrm->select();
    
    // ����-�����
    if ($PHPShopBase->getParam('template_theme.demo') == 'true') {
        $data['token'] = $data['login'] = $data['password']= '';
    }

    $Tab1 = $PHPShopGUI->setField('����� ����������� ����������', $PHPShopGUI->setInputText(false, 'token_new', $data['token'], 300));
    $Tab1.= $PHPShopGUI->setField('����� ������������', $PHPShopGUI->setInputText(false, 'login_new', $data['login'], 300));
    $Tab1.= $PHPShopGUI->setField('������ ������������', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], 300));
    $Tab1.= $PHPShopGUI->setField('ID �������', $PHPShopGUI->setInputText(false, 'widget_id_new', $data['widget_id'], 300));
    $Tab1.= $PHPShopGUI->setField('ID ������� ���������� ��������', $PHPShopGUI->setInputText(false, 'courier_widget_id_new', $data['courier_widget_id'], 300));
    $Tab1.= $PHPShopGUI->setField('������ ��� ��������', $PHPShopGUI->setSelect('status_new', Settings::getStatusesVariants($data['status']), 300));
    $Tab1.= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('delivery_id_new', Settings::getDeliveryVariants($data['delivery_id']), 300));
    $Tab1.= $PHPShopGUI->setField('�������� ��������', $PHPShopGUI->setSelect('delivery_courier_id_new', Settings::getDeliveryVariants($data['delivery_courier_id']), 300));
    $Tab1.= $PHPShopGUI->setField('��������� ���', $PHPShopGUI->setSelect('mail_category_new', Settings::getMailCategoryVariants($data['mail_category']), 300));
    $Tab1.= $PHPShopGUI->setField('��� ���', $PHPShopGUI->setSelect('mail_type_new', Settings::getMailTypeVariants($data['mail_type']), 300));
    $Tab1.= $PHPShopGUI->setField('����������', $PHPShopGUI->setSelect('dimension_type_new', Settings::getDimensionVariants($data['dimension_type']), 300));
    $Tab1.= $PHPShopGUI->setField('�������� ������ ������ �����������', '<input class="form-control input-sm " onkeypress="pochtavalidate(event)" type="text" value="' . $data['index_from'] . '" name="index_from_new" style="width:300px; ">');
    $Tab1.= $PHPShopGUI->setField('����������� ��������', $PHPShopGUI->setInputText('�� ����� �������', 'declared_percent_new', $data['declared_percent'], 300,'%'));
    $Tab1= $PHPShopGUI->setCollapse('���������',$Tab1);
    $Tab1.= $PHPShopGUI->setCollapse('��������� ������������� ������',
        $PHPShopGUI->setField('˸���� �������', $PHPShopGUI->setCheckbox('easy_return_new', 1, '������� "˸���� �������"', $data["easy_return"])) .
        $PHPShopGUI->setField('�������� �� ��������', $PHPShopGUI->setCheckbox('no_return_new', 1, '������� "�������� �� ��������"', $data["no_return"])) .
        $PHPShopGUI->setField('���������/�������', $PHPShopGUI->setCheckbox('fragile_new', 1, '������� "���������/�������"', $data["fragile"])) .
        $PHPShopGUI->setField('��� �������', $PHPShopGUI->setCheckbox('wo_mail_rank_new', 1, '������� "��� �������"', $data["wo_mail_rank"])) .
        $PHPShopGUI->setField('�������������', $PHPShopGUI->setCheckbox('completeness_checking_new', 1, "������ �������� �������������", $data["completeness_checking"])) .
        $PHPShopGUI->setField('SMS �����������', $PHPShopGUI->setCheckbox('sms_notice_new', 1, '������ SMS �����������', $data["sms_notice"])) .
        $PHPShopGUI->setField('����������� �����������', $PHPShopGUI->setCheckbox('electronic_notice_new', 1, '������ ����������� �����������', $data["electronic_notice"])) .
        $PHPShopGUI->setField('�������� �����������', $PHPShopGUI->setCheckbox('order_of_notice_new', 1, '������ �������� �����������', $data["order_of_notice"])) .
        $PHPShopGUI->setField('������� �����������', $PHPShopGUI->setCheckbox('simple_notice_new', 1, '������ ������� �����������', $data["simple_notice"])) .
        $PHPShopGUI->setField('���������������� ���������', $PHPShopGUI->setCheckbox('vsd_new', 1, '������� ���������������� ����������', $data["vsd"])).
        $PHPShopGUI->setField('������ ������', $PHPShopGUI->setCheckbox('paid_new', 1, '����� �������', $data["paid"]))
    );

    $info = '<h4>��������� ������ �����������</h4>
       <ol>
        <li>������������������ �� ������-������� <a href="https://otpravka.pochta.ru/" target="_blank">���������</a></li>
        <li>����� ����������� ������������ ����� ������ � <a href="https://otpravka.pochta.ru/settings#/api-settings" target="_blank">���������� ������� ��������</a>.</li>
        <li>������� ������ � <a href="https://widget.pochta.ru/widgets" target="_blank">������ ����� ������</a>.</li>
        <li>� ���������� �������� �������� <code>Callback function name</code> � ������� �������� <code>pochtaCallback</code> 
            ��� ������� <kbd>� ����� ������</kbd> � <code>pochtaCallbackCourier</code> ��� ������� <kbd>��������</kbd>.
        </li>
        <li>�� ���� ������� <kbd>� ����� ������</kbd> <code>ecomStartWidget({
        id: 1234,
        callbackFunction: pochtacallback,
        containerId: \'ecom-widget\'
      });</code> ����������� �������� id, � ������� 1234, �������� ��� � ���� <kbd>ID �������</kbd> � ���������� ������.</li>
              <li>�� ���� ������� <kbd>��������</kbd> <code>courierStartWidget({
        id: 1234,
        callbackFunction: pochtaCallbackCourier,
        containerId: \'ecom-widget-courier\'
      });</code> ����������� �������� id, � ������� 1234, �������� ��� � ���� <kbd>ID ������� ���������� ��������</kbd> � ���������� ������.</li>
        </ol>
        
       <h4>��������� ������</h4>
        <ol>
        <li>������ ����� ����������� ������������.</li>
        <li>������ ����� � ������ �� ������� �������� ����� ������.</li>
        <li>������ ������ ������ �������� �����������.</li>
        <li>������� ������ �������� � ������ ���������� ��������.</li>
        <li>������ ��� �� ���������, �� ����� ����������� ���� � ������ �� ����� ���.</li>
        <li>��������� �������������� ������.</li>
        </ol>
        
       <h4>��������� ��������</h4>
        <ol>
        <li>� �������� �������������� �������� � �������� <kbd>��������� ��������� ��������</kbd> ��������� �������������� �������� ���������� ��������� �������� ��� ������. ����� "�� �������� ���������" ������ ���� �������.</li>
        <li>� �������� �������������� �������� � �������� <kbd>������ ������������</kbd> �������� <kbd>������</kbd> "���." � "������������"</li>
        </ol>

';

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