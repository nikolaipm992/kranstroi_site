<?php

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.kupivkredit.kupivkredit_system"));

/**
 * ���������� ������ ������
 * @return mixed
 */
function actionBaseUpdate(){
    global $PHPShopModules, $PHPShopOrm;

    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

/**
 * ���������� ��������
 * @return mixed
 */
function actionUpdate(){
    global $PHPShopModules, $PHPShopOrm;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    if (!isset($_POST['dev_mode_new'])) $_POST['dev_mode_new'] = '0';
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

/**
 * ����������� �������� ������
 * @return bool
 */
function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Tab1 = $PHPShopGUI->setField('ShopID', $PHPShopGUI->setInputText(false, 'shop_id_new', $data['shop_id'], 300), 1, "���������� ������������� ��������, �������� ������ ��� �����������.");
    $Tab1 .= $PHPShopGUI->setField('ShowcaseID', $PHPShopGUI->setInputText(false, 'showcase_id_new', $data['showcase_id'], 300), 1, "������������� ������� ��������. � ������ ������������ ������� ����� �� ���������.");
    $Tab1 .= $PHPShopGUI->setField('�������� � ������', $PHPShopGUI->setInputText(false, 'promo_new', $data['promo'], 300));
    
    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay(null, false, $data['version'], true);

    // ����������
    $info = '
        <h4>���������� � ������</h4>
        <p>������ ��������� ������������� ������ "���� � ������":</p>
        <ol>
        <li><strong>� �������� ������</strong>. ��� ����� ���������� ���������� <kbd>@kvk_product@</kbd> � ������� <code>phpshop/templates/��� �������/product/main_product_forma_full.tpl</code> � ������ ��� �����.</li>
        <li><strong>� ������� ������</strong>. ��� �������� ���� ������ � ������ �� �������</li>
        </ol>

        <h4>��������� ������</h4>
        <ol>
 <li>������������ ����������� ��������� � <a href="https://www.tbank.ru/business/loans/?utm_source=partner_rko_sme&utm_medium=ptr.act&utm_campaign=sme.partners&partnerId=5-IV4AJGWE#form-application" target="blank">��������� ������� � �-����</a>.</li>
<li>�������� �����, ��� ������ � ����������� ���������.</li>
<li>�������� <kbd>ShopId</kbd>, <kbd>ShowcaseId</kbd> � <kbd>PromoCode</kbd> ��� ������ � ������ ������ �������� ���������� �������� ����� ��� �������� ���������� � ��������� �����������. ��������� ��� �������� � ���������� ������.</li>
<li>�������� ��������� ��� ������� � ������ ������� ������� "������ ��������" � �������� <kbd>������</kbd>, ������� �������� (�����������, ���� ������� �� ��������� �����).</li>
<li>��� �������������� ����� ������ �������������� ������� <code>phpshop/modules/kupivkredit/templates/</code></li>
</ol>
';

    
    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true), array("����������", $PHPShopGUI->setInfo($info)), array("� ������", $Tab3));

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
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
