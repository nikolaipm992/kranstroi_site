<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productsgroup.productsgroup_system"));

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
    global $PHPShopModules,$PHPShopOrm;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id='.$_GET['id']);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Tab1 = '<p>������ ��������� �������� ��������� ������ � ���� ������ �������� � ��������� �������� ������� ��� �� ����������. �������� ��� ������� ������, ����������� � ����� � �.�.</p>
        <h4>��������� ������</h4>
        <p>��� �������������� ������ �� ������� <kbd>������</kbd> - "<b>������</b>" ���� ����������� ��������� ������ ��������� ������� � ������.</p>
<h4>��������� �������</h4>
    <p><kbd>@productsgroup_list@</kbd> - ���������� �������� �� ����� ����� � ������� ���������� �������� ������ <code>/phpshop/templates/���_�������/product/main_product_forma_full.tpl</code></p>
    <p><kbd>@productsgroup_button_buy@</kbd> - ������ ������� ��� ������� �������, �������� ���� �������: <code>/phpshop/templates/���_�������/product/main_product_forma_2.tpl</code></p>
    <p>��� ��������� ����������� ���� ��� ������ ���-�� ������ � �������� ������, ����� ������ ��������� � ������� �������� ������ <code>/phpshop/templates/���_�������/product/main_product_forma_full.tpl</code>. �������� ����� "<b>priceGroupeR</b>" � ���, ���������� ����. ������: <pre>
&lt;div class="tovarDivPrice12"&gt;����: &lt;span class="priceGroupeR"&gt;@productPrice@&lt;/span&gt; &lt;span&gt;@productValutaName@&lt;/span>&lt;/div&gt;</pre>
    <p>��� ��������������� ���������� ��� � ����� ������� �� ���������� ������� �������� ����� ������ � ������ <a href="https://docs.phpshop.ru/moduli/razrabotchikam/cron" target="_blank">������</a> � ������� ������������ ����� <code>phpshop/modules/productsgroup/cron/products.php</code>. ���� ����������� ��� �� ��� �������������� �������� ������ � ��������.</p>

    ';

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab1), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], true)));

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