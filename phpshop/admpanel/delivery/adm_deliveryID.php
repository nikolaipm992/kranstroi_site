<?php

PHPShopObj::loadClass(array('delivery', 'payment', 'category'));

$TitlePage = __('�������������� ��������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $k, $dop_cat_array);

            $selected = null;
            $disabled = null;

            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

/**
 * ����� �������� ���� ��������������
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    $PHPShopDelivery = new PHPShopDelivery();

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './delivery/gui/delivery.gui.js');

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    if (!empty($data['is_folder']))
        $catalog = true;
    else
        $catalog = false;

    $PHPShopGUI->setActionPanel(__("��������") . ' &rarr; ' . $data['city'], array('�������', '|', '�������',), array('���������', '��������� � �������'));

    // ������������
    $Tab_info = $PHPShopGUI->setField("��������", $PHPShopGUI->setInputText(false, 'city_new', $data['city'], '100%'));

    $PHPShopDeliveryArray = new PHPShopDeliveryArray(array('is_folder' => "='1'"));
    $CategoryDeliveryArray = $PHPShopDeliveryArray->getArray();

    $CategoryDeliveryArray[0]['city'] = '- ' . __('�������� �������') . ' -';
    $CategoryDeliveryArray[0]['id'] = 0;

    foreach ($CategoryDeliveryArray as $val) {
        $city_value[] = array($val['city'], $val['id'], $data['PID']);
    }

    $_GET['parent_to'] = $data['PID'];

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (is_array($CategoryArray))
        $GLOBALS['count'] = count($CategoryArray);

    $CategoryArray[0]['name'] = '- ' . __('�������� �������') . ' -';
    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
        if (!empty($data['parent_to']) and $k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    // �����������
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);
    $tree_select = $tree_select = null;

    if (!empty($tree_array[0]['sub']) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $k, $dop_cat_array);


            // �����������
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    // ����� ��������
    if (!$catalog)
        $Tab_info .= $PHPShopGUI->setField("�������", $PHPShopGUI->setSelect('PID_new', $city_value, '100%'));

    // �����
    $Tab_info .= $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox('enabled_new', 1, null, $data['enabled']));
    $Tab_info .= $PHPShopGUI->setField("�������� �� ���������", $PHPShopGUI->setCheckbox('flag_new', 1, null, $data['flag']));

    // ����
    $Tab_price = $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(false, 'price_new', $data['price'], '150', $PHPShopSystem->getDefaultValutaCode()));

    $Tab_price .= $PHPShopGUI->setField("���������� �������� �����", $PHPShopGUI->setInputText(false, 'price_null_new', $data['price_null'], '150', $PHPShopSystem->getDefaultValutaCode()) . $PHPShopGUI->setCheckbox('price_null_enabled_new', 1, "���������", $data['price_null_enabled']));

    // �������� �������
    $Tab_price .= $PHPShopGUI->setField('���������', $PHPShopGUI->setHelp('�������� ��������� ������� ��� ���������� ��������.') .
            $PHPShopGUI->setCheckbox("categories_check_new", 1, "��������� ��������� ������", $data['categories_check']) . '<br>' .
            $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0) . '<br>' . $tree_select);

    // �����
    $Tab_price .= $PHPShopGUI->setField(sprintf("����� �� ������ %s � ����", $PHPShopDelivery->fee), $PHPShopGUI->setInputText(false, 'taxa_new', $data['taxa'], '150', $PHPShopSystem->getDefaultValutaCode()) .
            $PHPShopGUI->setHelp(sprintf('������������ ��� ������� �������������� ����������� (��������, ��� "����� ������").<br>������ �������������� %s ����� ����� ������� %s ����� ����� ������ ��������� �����.', $PHPShopDelivery->fee, $PHPShopDelivery->fee)));

    if ($data['ofd_nds'] == '')
        $data['ofd_nds'] = $PHPShopSystem->getParam('nds');

    $Tab_price .= $PHPShopGUI->setField("�������� ���", $PHPShopGUI->setInputText(null, 'ofd_nds_new', $data['ofd_nds'], 100, '%'));

    // ��� ����������
    $Tab_info .= $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText('&#8470;', "num_new", $data['num'], 150));

    // ��������� ������ ������� �� ��
    $city_select_value[] = array('�� ������������', 0, $data['city_select']);
    $city_select_value[] = array('������ ������� � ������ ��', 1, $data['city_select']);
    $city_select_value[] = array('��� ������ ����', 2, $data['city_select']);

    if (!$catalog)
        $Tab_info .= $PHPShopGUI->setField("������ �������", $PHPShopGUI->setSelect('city_select_new', $city_select_value, null, true));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab_info);

    $Tab1 .= $PHPShopGUI->setCollapse('������� ���', $PHPShopGUI->setField("�����������", $PHPShopGUI->setIcon($data['icon'], "icon_new", false)) .
            $PHPShopGUI->setField("�����������", $PHPShopGUI->setTextarea('comment_new', $data['comment'], false)));

    $PHPShopPaymentArray = new PHPShopPaymentArray(array('enabled' => "='1'"));
    if (strstr($data['payment'], ","))
        $payment_array = explode(",", $data['payment']);
    else
        $payment_array[] = $data['payment'];

    $PaymentArray = $PHPShopPaymentArray->getArray();
    if (is_array($PaymentArray))
        foreach ($PaymentArray as $payment) {

            if (in_array($payment['id'], $payment_array))
                $payment_check = $payment['id'];
            else
                $payment_check = null;
            $payment_value[] = array($payment['name'], $payment['id'], $payment_check);
        }

    // ������ � �����������
    if (empty($data['is_folder'])) {
        $Tab2 = $PHPShopGUI->setField("���������� �����", $PHPShopGUI->setSelect('payment_new[]', $payment_value, '100%', false, false, $search = false, false, 1, true));


        $Tab2 .= $PHPShopGUI->setField('�� �������� ���������', $PHPShopGUI->setRadio('is_mod_new', 1, '���������', $data['is_mod'], true, 'text-warning') . $PHPShopGUI->setRadio('is_mod_new', 2, '��������', $data['is_mod']));
    }

    // ������
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    $warehouse_value[] = array(__('����� �����'), 0, $data['warehouse']);
    if (is_array($dataWarehouse)) {
        foreach ($dataWarehouse as $val) {
            $warehouse_value[] = array($val['name'], $val['id'], $data['warehouse']);
        }
    }

    $Tab1 .= $PHPShopGUI->setCollapse('�������������', $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/')) .
            $PHPShopGUI->setField("����� ��� ��������", $PHPShopGUI->setSelect('warehouse_new', $warehouse_value, 300)));

    // ������� ���
    $Tab1 .= $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField('������� ���', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));

    // ����� ������
    if (empty($data['is_folder'])) {
        $Tab2 .= $PHPShopGUI->setField("���������� ��� ��������� �����", $PHPShopGUI->setInputText(null, "sum_max_new", $data['sum_max'], 150, $PHPShopSystem->getDefaultValutaCode()));
        $Tab2 .= $PHPShopGUI->setField("���������� ��� ��������� �����", $PHPShopGUI->setInputText(null, "sum_min_new", $data['sum_min'], 150, $PHPShopSystem->getDefaultValutaCode()));
        $Tab2 .= $PHPShopGUI->setField("���������� ��� ���� �����", $PHPShopGUI->setInputText(null, "weight_max_new", $data['weight_max'], 150, __('�����')));
        $Tab2 .= $PHPShopGUI->setField("���������� ��� ���� �����", $PHPShopGUI->setInputText(null, "weight_min_new", $data['weight_min'], 150, __('�����')));
    }

    if (!$catalog)
        $Tab1 .= $PHPShopGUI->setCollapse('����������', $Tab2);

    // ����
    if (!$catalog)
        $Tab1 .= $PHPShopGUI->setCollapse('����', $Tab_price);


    // �������������� ����
    if (!$catalog)
        $Tab2 = $PHPShopGUI->loadLib('tab_option', $data);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    if (!$catalog)
        $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true, true), array("������ ������������", $Tab2));
    else
        $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.delivery.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.delivery.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.delivery.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    $result = actionUpdate();

    if (isset($_REQUEST['ajax'])) {
        exit(json_encode($result));
    } else
        header('Location: ?path=' . $_GET['path'] . '&cat=' . $_POST['PID_new']);
}

/**
 * ����� ����������
 * @return bool
 */
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    if (is_array($_POST['data_fields'])) {

        if (is_array($_POST['data_fields']['enabled']))
            foreach ($_POST['data_fields']['enabled'] as $k => $v) {
                $_POST['data_fields']['enabled'][$k] = array_map("urldecode", $v);
            }


        $_POST['data_fields_new'] = serialize($_POST['data_fields']);
    }

    if (empty($_POST['ajax'])) {

        // ������������� ������ ��������
        $PHPShopOrm->updateZeroVars('flag_new', 'enabled_new', 'price_null_enabled_new', 'categories_check_new');
    }

    if (!empty($_POST['icon_new']))
        $_POST['icon_new'] = iconAdd('icon_new');

    // ������
    if (isset($_POST['payment_new'])) {
        if (is_array($_POST['payment_new']))
            $_POST['payment_new'] = @implode(',', $_POST['payment_new']);
    } else {
        $_POST['payment_new'] = '';
    }

    // ��������� �������
    $_POST['categories_new'] = "";
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        $_POST['categories_check_new'] = 1;

        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $_POST['categories_new'] .= $v . ",";
    } else
        $_POST['categories_check_new'] = 0;

    // ����������
    if (is_array($_POST['servers'])) {
        $_POST['servers_new'] = "";
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ',') and ! empty($v))
                $_POST['servers_new'] .= "i" . $v . "i";
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $PHPShopOrm->debug = false;

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . intval($_POST['rowID'])));

    return array("success" => $action);
}

// ���������� ����������� 
function iconAdd($name = 'icon_new') {

    // ����� ����������
    $path = '/UserFiles/Image/';

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST[$name];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST[$name])) {
        $file = $_POST[$name];
    }

    if (empty($file))
        $file = '';

    return $file;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    $PHPShopOrm->debug = true;
    return array('success' => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>