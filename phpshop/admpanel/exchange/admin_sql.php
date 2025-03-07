<?php

PHPShopObj::loadClass("file");

$TitlePage = __("SQL ������ � ����");

// �������� ������
$sqlHelper = array(
    'phpshop_categories' => __('��������� �������'),
    'phpshop_orders' => __('������ �������������'),
    'phpshop_products' => __('�������� �������') . '. <a href="https://help.phpshop.ru/knowledgebase/article/171" target="_blank"><span class="glyphicon glyphicon-share-alt"></span> ' . __('�������� �����') . '</a>',
    'phpshop_system' => __('��������� �����'),
    "phpshop_gbook" => __("������ � ����� �� �������� �����"),
    "phpshop_news" => __('�������'),
    "phpshop_jurnal" => __('������ ����������� ���������������'),
    "phpshop_page" => __('�������� ����� (������� ����, �������� � �.�.)'),
    "phpshop_menu" => __('��������� �������������� �����'),
    "phpshop_baners" => __('��������� �������'),
    "phpshop_links" => __('�������� ������'),
    "phpshop_search_jurnal" => __('������ ������ �� �����'),
    "phpshop_users" => __('�������������� �����'),
    "phpshop_sort_categories" => __('������ ������������� ��� �������� � ��������� �������'),
    "phpshop_sort" => __('�������������� �� ��������'),
    "phpshop_shopusers" => __('������������ �����, ����������'),
    "phpshop_page_categories" => __('��������� �������'),
    "phpshop_foto" => __('����������� �������'),
    "phpshop_comment" => __('����������� � �������, ����������� ��������������'),
    "phpshop_messages" => __('��������� ��� �������������, ����������� ��������������'),
    "phpshop_modules" => __('������������ �������������� ������'),
    "phpshop_newsletter" => __('������ ��������'),
    "phpshop_slider" => __('������� �� ������� ��������'),
    "phpshop_slider" => __('������� �� ������� ��������'),
);

// �������� �����
$key_name = array(
    'id' => 'Id',
    'name' => '������������',
    'uid' => '�������',
    'price' => '���� 1',
    'price2' => '���� 2',
    'price3' => '���� 3',
    'price4' => '���� 4',
    'price5' => '���� 5',
    'price_n' => '������ ����',
    'sklad' => '��� �����',
    'newtip' => '�������',
    'spec' => '���������������',
    'items' => '�����',
    'weight' => '���',
    'num' => '���������',
    'enabled' => '�����',
    'content' => '��������� ��������',
    'description' => '������� ��������',
    'pic_small' => '��������� �����������',
    'pic_big' => '������� �����������',
    'yml' => '������.������',
    'icon' => '������',
    'parent_to' => '��������',
    'category' => '�������',
    'title' => '���������',
    'login' => '�����',
    'tel' => '�������',
    'cumulative_discount' => '������������� ������',
    'seller' => '������ �������� � 1�',
    'fio' => '�.�.�',
    'city' => '�����',
    'street' => '�����',
    'odnotip' => '������������� ������',
    'page' => '��������',
    'parent' => '����������� ������',
    'dop_cat' => '�������������� ��������',
    'ed_izm' => '������� ���������',
    'baseinputvaluta' => '������',
    'vendor_array' => '��������������',
    'p_enabled' => '������� � ������.������',
    'parent_enabled' => '������',
    'descrip' => 'Meta description',
    'keywords' => 'Meta keywords',
    "prod_seo_name" => 'SEO ������',
    'num_row' => '������� � �����',
    'num_cow' => '������� �� ��������',
    'count' => '�������� �������',
    'cat_seo_name' => 'SEO ������ ��������',
    'sum' => '�����',
    'servers' => '�������',
    'items1' => '����� 2',
    'items2' => '����� 3',
    'items3' => '����� 4',
    'items4' => '����� 5',
    'data_adres' => '�����',
    'color' => '��� �����',
    'parent2' => '����',
    'rate' => '�������',
    'productday' => '����� ���',
    'hit' => '���',
    'sendmail' => '�������� �� ��������',
    'statusi' => '������ ������',
    'country' => '������',
    'state' => '�������',
    'index' => '������',
    'house' => '���',
    'porch' => '�������',
    'door_phone' => '�������',
    'flat' => '��������',
    'delivtime' => '����� ��������',
    'org_name' => '�����������',
    'org_inn' => '���',
    'org_kpp' => '���',
    'org_yur_adres' => '����������� �����',
    'dop_info' => '����������� �����������',
    'tracking' => '��� ������������',
    'path' => '���� ��������',
    'length' => '�����',
    'width' => '������',
    'height' => '������',
    'moysklad_product_id' => '�������� Id',
    'bonus' => '�����',
    'price_purch' => '���������� ����',
    'files' => '�����',
    'external_code' => '������� ���',
    'barcode' => '��������',
    'rate_count' => '������'
);

// ������� ����������
function actionSave() {
    global $PHPShopGUI, $result_message, $result_error_tracert, $link_db;

    // ���������� ������ �� �����
    if (!empty($_POST['sql_text'])) {
        $sql_query = explode(";\r", trim($_POST['sql_text']));

        foreach ($sql_query as $v) {
            $result = mysqli_query($link_db, trim($v));
        }

        // ��������� �������
        if ($result)
            $result_message = $PHPShopGUI->setAlert('SQL ������ ������� ��������');
        else {
            $result_message = $PHPShopGUI->setAlert('SQL ������: ' . mysqli_error($link_db), 'danger');
            $result_error_tracert = $_POST['sql_text'];
        }
    }

    // �������� csv �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if ($_FILES['file']['ext'] == "sql") {
            if (move_uploaded_file($_FILES['file']['tmp_name'], "csv/" . $_FILES['file']['name'])) {
                $csv_file = "csv/" . $_FILES['file']['name'];
                $csv_file_name = $_FILES['file']['name'];
            } else
                $result_message = $PHPShopGUI->setAlert('������ ���������� �����' . ' <strong>' . $csv_file_name . '</strong> � phpshop/admpanel/csv', 'danger');
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $csv_file = $_POST['furl'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST['lfile'])) {
        $csv_file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $_POST['lfile'];
        $path_parts = pathinfo($csv_file);
        $csv_file_name = $path_parts['basename'];
    }


    // ��������� sql
    if (!empty($csv_file)) {
        $result_error_tracer = $error_line = null;

        // GZIP
        if ($path_parts['extension'] == 'gz') {
            ob_start();
            readgzfile($csv_file);
            $sql_file_content = ob_get_clean();
        } else
            $sql_file_content = file_get_contents($csv_file);

        // ��������� UTF
        if ($GLOBALS['PHPShopBase']->codBase == 'utf-8') {
            $sql_file_content = str_replace("CHARSET=cp1251", "CHARSET=utf8", $sql_file_content);
            $sql_file_content = PHPShopString::win_utf8($sql_file_content, true);
        }

        $sql_query = PHPShopFile::sqlStringToArray($sql_file_content);

        foreach ($sql_query as $k => $v) {

            if (strlen($v) > 10) {
                $result = mysqli_query($link_db, $v);
            }

            if (!$result) {
                $error_line .= '[Line ' . $k . '] ';
                $result_error_tracert .= '������: ' . $v . '
������: ' . mysqli_error($link_db);
            }
        }

        // �������� ����� ����� ����������
        if (isset($_POST['clean']))
            @unlink($csv_file);

        // ��������� �������
        if (empty($result_error_tracert)) {
            if (!empty($_POST['ajax']))
                return array("success" => true);
            else
                $result_message = $PHPShopGUI->setAlert('SQL ������ ������� �������� ' . $csv_file_name);
        }
        else {
            if (!empty($_POST['ajax']))
                return array("success" => false, "error" => mysqli_error($link_db) . ' -> ' . $error_line);
            else
                $result_message = $PHPShopGUI->setAlert('SQL ������ ' . mysqli_error($link_db), 'danger');
        }
    }
}

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules, $result_message, $result_error_tracert, $PHPShopSystem, $selectModalBody, $sqlHelper, $key_name;

    $PHPShopGUI->action_button['���������'] = array(
        'name' => __('���������'),
        'class' => 'btn btn-primary btn-sm navbar-btn ace-save',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-ok'
    );

    $bases = $DROP = $TRUNCATE = $selectModal = null;
    $baseArray = array();

    foreach ($GLOBALS['SysValue']['base'] as $val) {
        if (is_array($val)) {
            foreach ($val as $mod_base)
                $baseArray[$mod_base] = $mod_base;
        } else
            $baseArray[$val] = $val;
    }

    foreach ($baseArray as $val) {
        if (!empty($val)) {
            $bases .= "`" . $val . "`, ";
            $DROP .= 'DROP TABLE ' . $val . ';
';
            if (!empty($sqlHelper[$val]))
                $selectModal .= '<tr><td><kbd>' . $val . '</kbd></td><td>' . $sqlHelper[$val] . '</td></tr>';
        }
    }

    unset($baseArray['phpshop_system']);
    unset($baseArray['phpshop_users']);
    unset($baseArray['phpshop_valuta']);
    unset($baseArray['phpshop_modules_key']);
    unset($baseArray['phpshop_payment_systems']);
    unset($baseArray['phpshop_exchanges']);
    unset($baseArray['phpshop_baners']);
    unset($baseArray['phpshop_parent_name']);
    unset($baseArray['phpshop_delivery']);
    unset($baseArray['phpshop_order_status']);
    unset($baseArray['phpshop_payment_systems']);
    unset($baseArray['phpshop_page']);
    unset($baseArray['phpshop_jurnal']);

    $TRUNCATE = null;

    foreach ($baseArray as $val) {
        if (!strstr($val, '_modules'))
            $TRUNCATE .= 'TRUNCATE `' . $val . '`;
';
    }

    $bases = substr($bases, 0, strlen($bases) - 2) . ';';

    // ������ �������� ����
    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->addJSFiles('./exchange/gui/exchange.gui.js', './tpleditor/gui/ace/ace.js');

    $PHPShopGUI->_CODE = $result_message;
    $help = '<p class="text-muted">' . __('��� ������� ����-���� � ����-������� ������� ������� SQL ������� <kbd>�������� ����</kbd></p> <p class="text-muted">��� ���������� ������������������ ����� ������� SQL ������� <kbd>�������������� ����</kbd></p> <p class="text-muted">���������� �������� SQL ������ ��� �������� ��������� ������� �������� � <a href="https://help.phpshop.ru/knowledgebase/article/398" target="_blank" class="btn btn-default btn-xs"><span class="glyphicon glyphicon-book"></span> ���� ������</a>') . '</p>';


    $PHPShopGUI->setActionPanel($TitlePage, false, array('���������'));

    if (!empty($_GET['query']) and $_GET['query'] == 'optimize')
        $optimize_sel = 'selected';
    else
        $optimize_sel = null;

    $query_value[] = array('������� SQL �������', 0, '');
    $query_value[] = array('�������������� ����', 'OPTIMIZE TABLE ' . $bases, $optimize_sel);
    $query_value[] = array('�������� ����', 'REPAIR TABLE ' . $bases, '');
    $query_value[] = array('��������� ������������� ������', 'UPDATE ' . $GLOBALS['SysValue']['base']['products'] . ' SET enabled=\'0\' WHERE items<1', '');
    $query_value[] = array('������� ��� ���� �������', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['foto'] . ';
UPDATE ' . $GLOBALS['SysValue']['base']['products'] . ' set pic_small=\'\', pic_big=\'\';', '');
    $query_value[] = array('������� ��������������', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['sort'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['sort_categories'] . ';
UPDATE ' . $GLOBALS['SysValue']['base']['products'] . ' set vendor=\'\', vendor_array=\'\';
UPDATE ' . $GLOBALS['SysValue']['base']['categories'] . ' set sort=\'\';', '');
    $query_value[] = array('������� ������� �������', 'DELETE FROM ' . $GLOBALS['SysValue']['base']['categories'] . ' WHERE ID=', '');
    $query_value[] = array('������� ��� ��������', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['categories'], '');
    $query_value[] = array('������� ��� ������', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['products'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['foto'] . ';', '');
    $query_value[] = array('������� ������ � ��������', 'DELETE FROM ' . $GLOBALS['SysValue']['base']['products'] . ' WHERE category=', '');
    $query_value[] = array('������� ��������', 'DELETE FROM ' . $GLOBALS['SysValue']['base']['page'] . ' WHERE ID=', '');
    $query_value[] = array('�������� ��������������� ��������', 'UPDATE ' . $GLOBALS['SysValue']['base']['categories'] . ' SET parent_to=0 WHERE parent_to=id', '');
    $query_value[] = array('��������� ����� ��������� ���� ���������', "UPDATE phpshop_categories SET phpshop_categories.vid = '0' WHERE phpshop_categories.parent_to IN (select * from ( SELECT phpshop_categories.id
 FROM phpshop_categories WHERE phpshop_categories.parent_to='0')t );
 UPDATE phpshop_categories SET vid='1' where parent_to !='0';");
    $query_value[] = array('�������� ���� �������', 'TRUNCATE ' . $GLOBALS['SysValue']['base']['citylist_country'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['citylist_region'] . ';
TRUNCATE ' . $GLOBALS['SysValue']['base']['citylist_city'] . ';', '');



    $query_value[] = array('�������� ����', $TRUNCATE, '');
    //$query_value[] = array('���������� ���� (!)', $DROP, '');
    // ����������� �� ������
    if (!empty($_GET['query']) and $_GET['query'] == 'optimize')
        $result_error_tracert = 'OPTIMIZE TABLE ' . $bases;

    // ����
    $theme = $PHPShopSystem->getSerilizeParam('admoption.ace_theme');
    if (empty($theme))
        $theme = 'dawn';

    $PHPShopGUI->_CODE .= '<textarea class="hide" id="editor_src" name="sql_text" data-mod="sql" data-theme="' . $theme . '">' . $result_error_tracert . '</textarea><pre id="editor">' . __('��������') . '...</pre>';

    $PHPShopGUI->_CODE .= '<p class="text-right data-row"><a href="#" id="vartable" data-toggle="modal" data-target="#selectModal" data-title="' . __('�������� �������') . '"><span class="glyphicon glyphicon-question-sign"></span>' . __('�������� ������') . '</a></p>';

    // ��������� ���� ������� �������� ���������
    $selectModalBody = '<table class="table table-striped"><tr><th>' . __('�������') . '</th><th>' . __('��������') . '</th></tr>' . $selectModal . '</table>';

    $Tab1 = $PHPShopGUI->setField('�������', $PHPShopGUI->setSelect('sql_query', $query_value, 400, true, false, false, false, 1, false, false, 'selectpicker')) .
            $PHPShopGUI->setField("����", $PHPShopGUI->setFile());

    // �����������
    $query_table_value[] = ['�� �������', '', $_POST['query_table']];
    $query_table_value[] = ['������', $GLOBALS['SysValue']['base']['products'], $_POST['query_table']];
    $query_table_value[] = ['��������', $GLOBALS['SysValue']['base']['categories'], $_POST['query_table']];
    $query_table_value[] = ['������������', $GLOBALS['SysValue']['base']['shopusers'], $_POST['query_table']];
    $query_table_value[] = ['������', $GLOBALS['SysValue']['base']['orders'], $_POST['query_table']];

    $query_action_value[] = ['�� �������', '',$_POST['query_action']];
    $query_action_value[] = ['��������', 'update', $_POST['query_action']];
    $query_action_value[] = ['�������', 'delete', $_POST['query_action']];
    $query_action_value[] = ['�������', 'select', $_POST['query_action']];

    $query_var_value[] = ['�� �������', '', ''];
    foreach ($key_name as $k => $v)
        $query_var_value[] = [$v, $k, $_POST['query_var']];

    $query_condition_value[] = ['�� �������', '', $_POST['query_condition']];
    $query_condition_value[] = ['�����', '=', $_POST['query_condition']];
    $query_condition_value[] = ['�� �����', '!=', $_POST['query_condition']];
    $query_condition_value[] = ['������', '>', $_POST['query_condition']];
    $query_condition_value[] = ['������', '<', $_POST['query_condition']];

    $query_val_value[] = ['�� �������', '', $_POST['query_val']];
    $query_val_value[] = ['0', "'0'", $_POST['query_val']];
    $query_val_value[] = ['1', "'1'", $_POST['query_val']];
    $query_val_value[] = ['�����', "''", $_POST['query_val']];
    $query_val_value[] = ['������', "prompt", $_POST['query_val']];

    $Tab2 = $PHPShopGUI->setField('��� ������', $PHPShopGUI->setSelect('query_table', $query_table_value, 200, true, false, false, false, 1, false, false, 'selectpicker'). $PHPShopGUI->setButton('�������������','play','query_generation'));
    $Tab2 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('query_action', $query_action_value, 200, true, false, false, false, 1, false, false, 'selectpicker'));
    $Tab2 .= $PHPShopGUI->setField('����', $PHPShopGUI->setSelect('query_var', $query_var_value, 200, true, false, true, false, 1, false, false, 'selectpicker'));
     $Tab2 .= $PHPShopGUI->setField('�������', $PHPShopGUI->setSelect('query_condition', $query_condition_value, 200, true, false, false, false, 1, false, false, 'selectpicker'));
    $Tab2 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('query_val', $query_val_value, 200, true, false, false, false, 1, false, false, 'selectpicker'));
   
    $PHPShopGUI->tab_return = true;
    $PHPShopGUI->setTab(array('���������', $Tab1, true), array('����������� ��������', $Tab2, true));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);


    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "saveID", "���������", "right", 80, "", "but", "actionSave.system.edit");
    $ContentFooter .= $PHPShopGUI->setInput("hidden", "restoreID", "���������", "right", 80, "", "but", "actionRestore.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    // �����
    $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopGUI->loadLib('tab_menu_service', false, './exchange/'));
    $sidebarleft[] = array('title' => '���������', 'content' => $help);
    $PHPShopGUI->setSidebarLeft($sidebarleft, 2);
    $PHPShopGUI->Compile(2);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();
?>