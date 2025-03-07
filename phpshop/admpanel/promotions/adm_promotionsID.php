<?php

PHPShopObj::loadClass("category");

$TitlePage = __('�������������� ����������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['promotion']);

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

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // ����� ����
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './promotions/gui/promotions.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->setActionPanel($data['name'], array('�������', '|', '�������'), array('���������', '��������� � �������'), false);

    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText('', 'name_new', $data['name'])) .
            $PHPShopGUI->setField('������', $PHPShopGUI->setRadio("enabled_new[]", 1, "����������", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new[]", 0, "������", $data['enabled']));

    $Tab1 .= $PHPShopGUI->setField('������', $PHPShopGUI->setCheckbox("active_check_new", 1, "��������� ����������", $data['active_check'])) . $PHPShopGUI->setField('������', $PHPShopGUI->setInputDate("active_date_ot_new", $data['active_date_ot'])) . $PHPShopGUI->setField('����������', $PHPShopGUI->setInputDate("active_date_do_new", $data['active_date_do'])
    );

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);

    if ($data['sum_order_check'] == 0) {
        $status = '������';
        $status_pre = '-';
    } else {
        $status = '�������';
        $status_pre = '+';
    }

    if ($data['discount_tip'] == 0)
        $cur = $PHPShopSystem->getValutaIcon();
    else
        $cur = '%';

    $action_value[] = array('����������� ��������� ������', 1, $data['action']);
    $action_value[] = array('����������� �� �������� �� ������� ������������', 2, $data['action']);

    $Tab1 .= $PHPShopGUI->setCollapse($status, $PHPShopGUI->setField('���', $PHPShopGUI->setRadio("discount_tip_new", 1, "%", $data['discount_tip']) . $PHPShopGUI->setRadio("discount_tip_new", 0, "�����", $data['discount_tip']), 'left') .
            $PHPShopGUI->setField($status, $PHPShopGUI->setInputText($status_pre, 'discount_new', $data['discount'], '160', $cur)) .
            $PHPShopGUI->setField('��������', $PHPShopGUI->setRadio("sum_order_check_new", 0, "���������", $data['sum_order_check']) . $PHPShopGUI->setRadio("sum_order_check_new", 1, "���������", $data['sum_order_check'])) .
            $PHPShopGUI->setField("������� �������", $PHPShopGUI->setSelect('action_new', $action_value, 300, true))
    );


    $Tab1 .= $PHPShopGUI->setCollapse('�������������', $PHPShopGUI->setField('���������� � �������', $PHPShopGUI->setInputText('', 'num_check_new', $data['num_check'], 150, __('��.'))) . $PHPShopGUI->setField('����� ������ �� �����', $PHPShopGUI->setInputText('', 'label_new', $data['label'])));


    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();
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


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';


    // ������� ����������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
    $data_user_status = $PHPShopOrm->select(array('id,name'), false, array('order' => 'name'), array('limit' => 100));
    $status_array = unserialize($data['statuses']);

    if (is_array($data_user_status))
        array_unshift($data_user_status, array('id' => '0', 'name' => __('���������� ��� �������')));

    if (!is_array($data_user_status))
        $data_user_status[] = array('id' => '0', 'name' => __('���������� ��� �������'));

    foreach ($data_user_status as $value) {
        if (is_array($status_array) && in_array($value['id'], $status_array))
            $sel = 'selected';
        else
            $sel = null;
        $value_user_status[] = array($value['name'], $value['id'], $sel);
    }

    $Tab1 .= $PHPShopGUI->setCollapse('�������', $PHPShopGUI->setField('������ ����������', $PHPShopGUI->setSelect('statuses[]', $value_user_status, '100%')) .
            $PHPShopGUI->setField('���������', $PHPShopGUI->setHelp('�������� ��������� ������� �/��� ������� ID ������� ��� �����.') .
                    $PHPShopGUI->setCheckbox("categories_check_new", 1, "��������� ��������� ������", $data['categories_check']) . '<br>' .
                    $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0) . '<br>' .
                    $PHPShopGUI->setCheckbox("disable_categories_new", 1, "������ �� ���� ����������, ����� ���������", $data['disable_categories']) . '<br><br>' .
                    $tree_select) .
            $PHPShopGUI->setField('������', $PHPShopGUI->setCheckbox("products_check_new", 1, "��������� ������", $data['products_check']) . '<br>' .
                    $PHPShopGUI->setCheckbox("block_old_price_new", 1, "������������ ������ �� ������ �����", $data['block_old_price']) . '<br>' .
                    $PHPShopGUI->setCheckbox("hide_old_price_new", 1, "�� ���������� ���� ��� ������/�������", $data['hide_old_price']) . '<br><br>' .
                    $PHPShopGUI->setTextarea('products_new', $data['products'], false, false, false, __('������� ID ������� ��� ��������������') . ' <a href="#" data-target="#products_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('������� �������') . '</a>'))
    );

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.system.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.system.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.system.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['enabled_new'] = $_POST['enabled_new'][0];

    if (empty($_POST['ajax'])) {

        $_POST['categories_new'] = "";
        if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

            $_POST['categories_check_new'] = 1;

            foreach ($_POST['categories'] as $v)
                if (!empty($v) and ! strstr($v, ','))
                    $_POST['categories_new'] .= $v . ",";
        }

        if (!empty($_POST['products_new'])) {
            $products = array();
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $parents = $orm->getList(array('id', 'parent'), array('parent_enabled' => "='0' AND id IN (" . $_POST['products_new'] . ")"));

            foreach ($parents as $parent) {
                $products[] = $parent['id'];
                if (!empty($parent['parent'])) {
                    $products = array_merge($products, explode(',', $parent['parent']));
                }
            }

            $_POST['products_new'] = implode(',', array_unique($products));
        }

        if (is_array($_POST['statuses']))
            $_POST['statuses_new'] = serialize($_POST['statuses']);

        $PHPShopOrm->updateZeroVars(
                'block_old_price_new', 'status_check_new', 'hide_old_price_new', 'discount_tip_new', 'products_check_new', 'categories_check_new', 'discount_check_new', 'active_check_new', 'enabled_new', 'disable_categories_new'
        );
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
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
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();


// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>