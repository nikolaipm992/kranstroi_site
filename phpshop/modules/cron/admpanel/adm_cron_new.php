<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cron.cron_job"));

// ������� ����������
function actionInsert() {
    global $PHPShopOrm;

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI;

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

    $Tab1 = $PHPShopGUI->setField("�������� ������:", $PHPShopGUI->setInput("text.requared", "name_new", __('����� ������')));
    $Tab1 .= $PHPShopGUI->setField("����������� ����:", $PHPShopGUI->setInputArg(array('type' => "text.requared", 'name' => "path_new", 'size' => '70%', 'float' => 'left', 'placeholder' => 'phpshop/modules/cron/sample/testcron.php')) . '&nbsp;' . $PHPShopGUI->setSelect('work', $work, '29%', true, false, false, false, false, false, false, 'selectpicker', '$(\'input[name=path_new]\').val(this.value);'));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, "��������", 1));
    $Tab1 .= $PHPShopGUI->setField("���-�� �������� � ����", $PHPShopGUI->setInputText(null,'execute_day_num_new', 1, 70));
    $Tab1 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', null, 'catalog/'));



    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "���������", "right", false, false, false, "actionInsert.modules.create");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>