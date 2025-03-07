<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.seourlpro.seourlpro_system"));

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
    global $PHPShopOrm, $PHPShopModules;

    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    // ������� ������ ��������
    unset($_SESSION['Memory']['PHPShopSeourlOption']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $hideSite;
    
    $PHPShopGUI->field_col = 4;

    // �������
    $data = $PHPShopOrm->select();


    // ���������� ��������
    $Info = '<p>���������� ������ �������� � ������ ������� ������� ���� <code>/knigi.html</code> �� <code>/shop/CID_1.html</code>. ��������� ��������� ������ ������� seo-url, �� ������ �������� ���������, �������, ��� ����, ���� �� ������� ������� url � ����, �� �� ����������.</p>';

    if (empty($hideSite)) {
        $Tab1 = $PHPShopGUI->setField('SEO ���������', $PHPShopGUI->setRadio('paginator_new', 2, '��������', $data['paginator']) . $PHPShopGUI->setRadio('paginator_new', 1, '���������', $data['paginator']), false, '��������� � ���� Title � Description ��������� ������� ��� ������������ ����������');
        $Tab1 .= $PHPShopGUI->setField('�������� �������� �� ���������� ���������', $PHPShopGUI->setRadio('cat_content_enabled_new', 1, '��������', $data['cat_content_enabled']) . $PHPShopGUI->setRadio('cat_content_enabled_new', 2, '���������', $data['cat_content_enabled']), false, '������� �������� �������� ��� ���������� ������� ��� ���������� ������������ ������.');
        $Tab1 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInfo($Info));
        $Tab1 .= $PHPShopGUI->setField('SEO ������ �������', $PHPShopGUI->setRadio('seo_brands_enabled_new', 2, '��������', $data['seo_brands_enabled']) . $PHPShopGUI->setRadio('seo_brands_enabled_new', 1, '���������', $data['seo_brands_enabled']), false, false);
        $Tab1 .= $PHPShopGUI->setField('SEO ���������', $PHPShopGUI->setRadio('redirect_enabled_new', 2, '��������', $data['redirect_enabled']) . $PHPShopGUI->setRadio('redirect_enabled_new', 1, '���������', $data['redirect_enabled']), false, '301 ��������� ��� �������� � ������ CMS');
    }
    $Tab1 .= $PHPShopGUI->setField('SEO ������ ��������', $PHPShopGUI->setRadio('seo_news_enabled_new', 2, '��������', $data['seo_news_enabled']) . $PHPShopGUI->setRadio('seo_news_enabled_new', 1, '���������', $data['seo_news_enabled']), false, false);
    $Tab1 .= $PHPShopGUI->setField('SEO ������ �������', $PHPShopGUI->setRadio('seo_page_enabled_new', 2, '��������', $data['seo_page_enabled']) . $PHPShopGUI->setRadio('seo_page_enabled_new', 1, '���������', $data['seo_page_enabled']), false, false);

    $Tab2 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("� ������", $Tab2));

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