<?php

$TitlePage = __('�������������� ������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['discount']);
PHPShopObj::loadClass(['user', 'category']);

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;';
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

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['path']);
    }

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->setActionPanel(__("����������") . ' / ' . __('������') . ' / ' . $data['sum'], array('�������'), array('���������', '��������� � �������'), false);

    $action_value[] = array('����������� ��������� ������', 1, $data['action']);
    $action_value[] = array('����������� � ���������������� �������', 2, $data['action']);

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

    $blockCategories = preg_split('/,/', $data['block_categories'], -1, PREG_SPLIT_NO_EMPTY);
    $tree_select=null;

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $k, $blockCategories);

            // �����������
            $selected = null;
            if (is_array($blockCategories))
                foreach ($blockCategories as $vs) {
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


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="block_categories[]"  data-width="300px" multiple>' . $tree_select . '</select>';

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField("�����", $PHPShopGUI->setInput('text.required', "sum_new", $data['sum'], null, 100)) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInputText('%', "discount_new", $data['discount'], 100)) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled'])) .
            $PHPShopGUI->setField("������� �������", $PHPShopGUI->setSelect('action_new', $action_value, 300)) .
            $PHPShopGUI->setField("�� ��������� ������ � ���������", $tree_select) .
            $PHPShopGUI->setField("������ ����", $PHPShopGUI->setCheckbox("block_old_price_new", 1, "������������ ������ �� ������ �����", $data['block_old_price']))
            , 'in', false);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true,false,'block-grid'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.shopusers.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.shopusers.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
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

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    $PHPShopOrm->updateZeroVars('block_old_price_new');

    $_POST['block_categories_new'] = "";
    if (is_array($_POST['block_categories']) and $_POST['block_categories'][0] != 'null') {
        foreach ($_POST['block_categories'] as $v)
            if (!empty($v) and !strstr($v, ','))
                $_POST['block_categories_new'] .= $v . ",";
    }

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>