<?php

include_once dirname(__DIR__) . '/class/SitemapPro.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sitemappro.sitemappro_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST["use_filter_combinations_new"]))
        $_POST["use_filter_combinations_new"] = 0;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

// ��������� ����� ��� SSL
function setGeneration() {
    (new SitemapPro())->generateSitemap();
}

// ������� ����������
function actionUpdateSSl() {
    (new SitemapPro())->generateSitemap(true);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->action_button['�������'] = array(
        'name' => __('������� Sitemap'),
        'action' => 'saveIDsitemap',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );

    $PHPShopGUI->action_button['������� SSL'] = array(
'name' => __('������� Sitemap SSL'),
        'action' => 'saveIDssl',
        'class' => 'btn  btn-default btn-sm navbar-btn hidden-xs',
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-import'
    );
    
    $PHPShopGUI->action_button['�������'] = array(
        'name' => __('������� Sitemap'),
        'action' => '../../sitemap.xml',
        'class' => 'btn  btn-default btn-sm navbar-btn btn-action-panel-blank hidden-xs',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-export'
    );

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('�������','������� SSL','�������','��������� � �������'));

    $data = $PHPShopOrm->select();

    switch ($data['step']) {
        case SitemapPro::FILTER_COMBINATIONS_STEP:
            $status = sprintf('��������� ���������� ������� ������� � %s �� %s', (int) $data['processed'], (int) $data['processed'] + SitemapPro::FILTER_COMBINATIONS_STEP_LIMIT);
            break;
        case SitemapPro::PRODUCTS_STEP:
            $status = sprintf('��������� ������� � %s �� %s', (int) $data['processed'], (int) $data['processed'] + (int) $data['limit_products']);
            break;
        default:
            $status = '��������� ���������, �������, ��������';
    }

    $Tab1 = $PHPShopGUI->setField('������� � ����� �����', $PHPShopGUI->setInputText(false, 'limit_products_new', $data['limit_products'], 150));
    $Tab1 .= $PHPShopGUI->setField('���������� ��������', $PHPShopGUI->setCheckbox("use_filter_combinations_new", 1, "�������� � ����� ����� �������� ����������� ���������", $data["use_filter_combinations"]));
    $Tab1 .= $PHPShopGUI->setField('��������� ����',sprintf('<div class="well well-sm" style="max-width:300px" role="alert">%s.</div>', __($status)));

    $Info = '
        <ol>
        <li>��� ��������������� �������� sitemap.xml ���������� ������ <kbd>Cron</kbd> � �������� � ���� ����� ������ � �������
        ������������ �����:<br>  <code>phpshop/modules/sitemappro/cron/sitemap_generator.php</code> ��� <code>phpshop/modules/sitemappro/cron/sitemap_generator.php?ssl</code> ��� ��������� HTTPS.
        <li>��� ������������� ����� <kbd>���������� �������� �������</kbd> � �������� ������������� ������� ������ ���� ��������� ���� <kbd>Meta ���������</kbd> � <kbd>Meta ��������</kbd> ��� ������ ���� ��������� <kbd>������� ������� � ��������</kbd> � <kbd>SEO ���������</kbd>.</li>
        <li>� ����������� (������.��������� � �.�.) ������� ����� <code>http://' . $_SERVER['SERVER_NAME'] . '/sitemap.xml</code> ��� �������������� ��������� ���������� ������.         
        <li>��� ��������� ����� ����� � �������������� ������ ������� �������� ��������� ������ ����� ������ <kbd>Cron</kbd> � � ���������� ������ ������ ������� ��������� �������. ����� ����� ����� ������� ������ ��� <code>http://�����_�������/sitemap_��.xml</code>, ��� �� - ID �������. ID ������� ����� ������� � ���������� ��������� ������� (1 - 10).
        <li>���������� ����� CHMOD 775 �� ����� <code>/</code> � <code>/UserFiles/Files/</code> ��� ������ � ��� ������ sitemap.xml
        </ol>';
    $Tab2 = $PHPShopGUI->setInfo($Info);

    $Tab3 = $PHPShopGUI->setPay(false,true);

// ����� ����� ��������
    $PHPShopGUI->setTab(['��������', $Tab1, true], ['����������', $Tab2], ['� ������', $Tab3]);

// ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit").
            $PHPShopGUI->setInput("submit", "saveIDsitemap", "���������", "right", 80, "", "but", "setGeneration.modules.edit");
            $PHPShopGUI->setInput("submit", "saveIDssl", "���������", "right", 80, "", "but", "actionUpdateSSL.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>