<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.productservices.productservices_system"));

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
    global $PHPShopModules, $PHPShopOrm;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id='.$_GET['id']);
}


function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;
    
        // �������
    $data = $PHPShopOrm->select();

    $Info = '<p>������ ��������� �������� �������������� ������ � �������� ������.</p>
        <h4>��������� ������</h4>
        <p>��� �������������� ������ �� ������� <kbd>������</kbd> - <kbd>������</kbd> ���� ����������� ��������� ������ ����� � ������ �� ������. ������ - �������� ��������� ����� (����� ���� �������� �� �����).</p>
<h4>��������� �������</h4>
    <p><kbd>@productservices_list@</kbd> - ���������� �������� �� ����� ����� � ������� ���������� �������� ������ <code>/phpshop/templates/���_�������/product/main_product_forma_full.tpl</code></p>
    <p>��� ��������� ����������� ���� ��� ������ ���-�� ������ � �������� ������, ����� ������ ��������� � ������� �������� ������ <code>/phpshop/templates/���_�������/product/main_product_forma_full.tpl</code>. �������� ����� "<b>priceService</b>" � ���, ���������� ����. ������: <pre>
&lt;div class="tovarDivPrice12"&gt;����: &lt;span class="priceService"&gt;@productPrice@&lt;/span&gt; &lt;span&gt;@productValutaName@&lt;/span>&lt;/div&gt;</pre>';

    // ���������� �������� 1
    $Tab2 = $PHPShopGUI->setInfo($Info);

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>