<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cron.cron_job"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['enabled_new']))
        $_POST['enabled_new'] = 0;

    $_POST['used_new'] = 0;

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";
    }


    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['path']);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm;
    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->setActionPanel(__("������") . ": " . $data['name'] . ' [ID ' . $data['id'] . ']', array('�������'), array('��������� � �������'), false);

    $work[] = array('�������', '');
    $work[] = array('����� ��', 'phpshop/modules/cron/sample/dump.php');
    $work[] = array('����� ����� ��� ������', 'phpshop/modules/cron/sample/currency.php');
    $work[] = array('����� ����� ��� ����������', 'phpshop/modules/cron/sample/currencykz.php');
    $work[] = array('����� ����� ��� �������', 'phpshop/modules/cron/sample/currencyua.php');
    $work[] = array('������ � ������ �������', 'phpshop/modules/cron/sample/product.php');
    $work[] = array('������������ �����', 'phpshop/modules/cron/sample/pricesearch.php');
    $work[] = array('����������� ������� �������', 'phpshop/modules/cron/sample/filter.php');
    $work[] = array('����������� ������� ������', 'phpshop/modules/cron/sample/filterpro.php');

    // ���� ������ SiteMap
    if (!empty($GLOBALS['SysValue']['base']['sitemap']['sitemap_system'])) {
        $work[] = array("|");
        $work[] = array('����� �����', 'phpshop/modules/sitemap/cron/sitemap_generator.php');
        $work[] = array('����� ����� SSL', 'phpshop/modules/sitemap/cron/sitemap_generator.php?ssl');
    }

    // ���� ������ SiteMap Pro
    if (!empty($GLOBALS['SysValue']['base']['sitemappro']['sitemappro_system'])) {
        $work[] = array("|");
        $work[] = array('������� ����� �����', 'phpshop/modules/sitemappro/cron/sitemap_generator.php');
        $work[] = array('������� ����� ����� SSL', 'phpshop/modules/sitemappro/cron/sitemap_generator.php?ssl');
    }

    // ���� ������ VisualCart
    if (!empty($GLOBALS['SysValue']['base']['visualcart']['visualcart_system'])) {
        $work[] = array("|");
        $work[] = array('������� ��������� ������', 'phpshop/modules/visualcart/cron/clean.php');
    }

    // �������� CSV
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $exchanges_data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array("limit" => "1000"));
    if (is_array($exchanges_data)) {

        foreach ($exchanges_data as $row) {

            if ($row['type'] == 'import')
                $import[] = array($row['name'], 'phpshop/modules/cron/sample/import.php?id=' . $row['id']);
            elseif ($row['type'] == 'export')
                $export[] = array($row['name'], 'phpshop/modules/cron/sample/export.php?id=' . $row['id'] . '&file=export_' . md5($row['name']));
        }

        if (is_array($import))
            $work[] = array('������ ������', $import);

        if (is_array($export))
            $work[] = array('������� ������', $export);
    }

    $Tab1 = $PHPShopGUI->setField("�������� ������:", $PHPShopGUI->setInput("text.requared", "name_new", $data['name']));
    $Tab1 .= $PHPShopGUI->setField("����������� ����:", $PHPShopGUI->setInputArg(array('type' => "text.requared", 'name' => "path_new", 'size' => '70%', 'float' => 'left', 'placeholder' => 'phpshop/modules/cron/sample/testcron.php', 'value' => $data['path'])) . '&nbsp;' . $PHPShopGUI->setSelect('work', $work, '29%', true, false, false, false, false, false, false, 'selectpicker', '$(\'input[name=path_new]\').val(this.value);'));

    parse_str($data['path'], $path);

    if (!empty($path['file'])) {

        $file = './csv/' . $path['file'];

        if (file_exists($file . '.xml'))
            $file_ext = '.xml';
        else if (file_exists($file . '.csv'))
            $file_ext = '.csv';

        $Tab1 .= $PHPShopGUI->setField("���� ��������", $PHPShopGUI->setLink($file . $file_ext, $path['file'] . $file_ext), false, false, false, 'text-right');
    }

    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, "��������", $data['enabled']));
    $Tab1 .= $PHPShopGUI->setField("���-�� �������� � ����", $PHPShopGUI->setInputText(null, 'execute_day_num_new', (int) $data['execute_day_num'], 70));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));


    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart');
?>