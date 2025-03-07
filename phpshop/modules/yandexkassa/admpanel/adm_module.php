<?php

include_once dirname(__FILE__) . '/../class/YandexKassa.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.yandexkassa.yandexkassa_system"));

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $action = $PHPShopOrm->update(array('version_new' => $new_version));
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id='.$_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
    PHPShopObj::loadClass('order');

    // �������
    $data = $PHPShopOrm->select();
    
    
    
    $Tab1 = $PHPShopGUI->setField('������ �� ������', $PHPShopGUI->setInputText(false, 'title_new', $data['title'], 300));
    $Tab1 .= $PHPShopGUI->setField('ShopID', $PHPShopGUI->setInputText(false, 'shop_id_new', $data['shop_id'], 300));
    $Tab1 .= $PHPShopGUI->setField('��������� ����', $PHPShopGUI->setInputText(false, 'api_key_new', $data['api_key'], 300));
    $Tab1 .= $PHPShopGUI->setField('������ ��� �������', $PHPShopGUI->setSelect('status_new', YandexKassa::getOrderStatuses($data['status']) , 300));
    
    
    $payment_mode_value[] = array('������ ����������', 1, $data['payment_mode']);
    $payment_mode_value[] = array('������ ������', 2, $data['payment_mode']);
    
    $Tab1 .= $PHPShopGUI->setField('������ �������', $PHPShopGUI->setSelect('payment_mode_new', $payment_mode_value , 300, true));
    $Tab1 .= $PHPShopGUI->setField('�������� ������', $PHPShopGUI->setTextarea('title_end_new', $data['title_end'], true, 300,200));

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    $protocol = YandexKassa::isHttps() ? 'https://' : 'http://';

    $info = '
        <h4>��� ������������ � �Kassa?</h4>
        <ol>
<li>������� ������ �� ����������� �� ������ <a href="https://yookassa.ru/joinups/?source=phpshop" target="_blank">https://yookassa.ru/joinups/?source=phpshop</a> � �������� ������ � ������ �������.</li>
<li>��������� ������.</li>
<li>�������� ������ ����������� API.</li>
<li>��������� �������.</li>
</ol>

<h4>����������� ������ ����������� ��� ����������� � ����������� � �Kassa</h4>
            <p>� ������ �������� �Kassa ������� � ���� <bkd>����������</bkd> � � ���� "URL ��� �����������" �������<code>' . $protocol . $_SERVER['SERVER_NAME'] . '/phpshop/modules/yandexkassa/payment/check.php</code> <br>
                <p>���� "<b>��������� ����</b>" ���������� ����������� � ������� �������� �Kassa (���������/����� API, ��������� ����).</p>
                <p>���� "<b>ShopID</b>" ���������� ����������� � ������� �������� �Kassa (���������/�������, shopId).</p>
                <p>� ��������� "������ ��� �������" �������� ������ ������, ��� ������� ������������ ������ ��������� ����������� �������� ����� ������ ��������. ���� ������ ������ "����� �����", ������������ ������ �������� ����� ����� ����� ����������. ��������� �������� � ���� "�������� ������" ��������� ����� ���������� ������ � ������, ����� ������ ������ �� ��������� �� �������� ��������� � ��������� "������ ��� �������".</p>
                
        <h4>��������� ��������</h4>
        <p>�������� ������ ��� ��� �������� ����� ��������� � �������� �������������� ��������.
        </p>
                <h4>������� �������</h4>
                <p>������ ������ ���������� � �������� ������� ����� ���������: <code>phpshop/modules/yandexkassa/templates/payment_forma.tpl</code><br>
                ������ ��������� �� �������� ������: <code>phpshop/modules/yandexkassa/templates/success_forma.tpl</code><br>
                ������ ��������� �� �������� ������: <code>phpshop/modules/yandexkassa/templates/fail_forma.tpl</code></p>
                <h4>��������� ��������</h4>
                ��� ������ ����������� � �������� �� �Kassa �� �������� ������ ���� �������� ������� <kbd>allow_url_fopen</kbd> � ���������� PHP.
    ';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("����������", $Tab2), array("� ������", $Tab3));

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