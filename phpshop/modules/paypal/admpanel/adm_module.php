<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.paypal.paypal_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
    PHPShopObj::loadClass('order');
    PHPShopObj::loadClass('valuta');


    // �������
    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('������������ ���� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'],300));
    $Tab1.=$PHPShopGUI->setField('������������', $PHPShopGUI->setInputText(false, 'merchant_id_new', $data['merchant_id'], 300));
    $Tab1.=$PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'merchant_pwd_new', $data['merchant_pwd'], 300));
    $Tab1.=$PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(false, 'merchant_sig_new', $data['merchant_sig'], 300));

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    // ������ ������
    $Tab1.= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', $order_status_value, 300));

    // ������
    $Tab1.= $PHPShopGUI->setField('����� ������ �� ������', $PHPShopGUI->setInputText(null, 'link_new', $data['link'], 300));

    // Sandbox
    $sandbox_value[] = array('�������', 1, $data['sandbox']);
    $sandbox_value[] = array('��������', 2, $data['sandbox']);
    $Tab1.= $PHPShopGUI->setField('�������� �����', $PHPShopGUI->setSelect('sandbox_new', $sandbox_value,300,true));

    // �������
    $logo_value[] = array('�����', 1, $data['logo_enabled']);
    $logo_value[] = array('������', 2, $data['logo_enabled']);
    $logo_value[] = array('��������', 3, $data['logo_enabled']);
    $Tab1.= $PHPShopGUI->setField('������� PayPal', $PHPShopGUI->setSelect('logo_enabled_new', $logo_value,300,true));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['currency_id'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            }
            else
                $check = false;
            $valuta_area.=$PHPShopGUI->setRadio('currency_id_new', $val['id'], $val['name'], $check,false, false, false, false);
        }
    $Tab1.= $PHPShopGUI->setLine().$PHPShopGUI->setField('������ �������',$valuta_area);    

    $Tab4 = $PHPShopGUI->setField('��������� �� ���������� �������', $PHPShopGUI->setTextarea('title_end_new',$data['title_end']));
    $Tab4.=$PHPShopGUI->setField('��������� ��������� ����� ������', $PHPShopGUI->setInputText(null, 'message_header_new', $data['message_header']));
    $Tab4.=$PHPShopGUI->setField('C�������� ����� ������', $PHPShopGUI->setTextarea('message_new', $data['message']));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    $info = '��� ������ ������ ��������� ������������������ � PayPal �� ������: <a href="https://www.paypal.com/ru/webapps/mpp/solutions" target="_blank">https://www.paypal.com/ru/webapps/mpp/solutions</a>. 
                <p>
� ���� "������������", "������" � "�������" ������ ����������� ������, ���������� ����� ����������� ������ �������� � PayPal.</p> <p>
��� ������������ ������ ����������� ����� "�������� �����" � �������� "�����������". ��� ����������� ������� ������� ������� ������ ������ ������ � �������� "�����������". </p><p>����� "������� PayPal" ���������� ������������ ������� ��������� �������. ������ �������� ��������� � ����� <code>phpshop/modules/paypal/templates/paypal_logo.tpl</code>. �������������� ������������ �������� �������� �� ������: <a href="https://www.paypal.com/ru/webapps/mpp/logos" target="_blank">https://www.paypal.com/ru/webapps/mpp/logos</a>.</p> <p> ������ �������� ��������� �������: <code>phpshop/modules/paypal/templates/paypal_forma.tpl</code></p><p>IPN ���������� ������: <code>http://'.$_SERVER['SERVER_NAME'].'/phpshop/modules/paypal/payment/ipn.php</code></p>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("�����������", $Tab1, true), array("���������", $Tab4, true), array("����������", $Tab2), array("� ������", $Tab3));

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
    $PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');

?>