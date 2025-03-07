<?php

PHPShopObj::loadClass("category");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.gift.gift_forms"));

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator($tree_array[$k], $i + 1, $k, $dop_cat_array);

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
                $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . ' >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));


    // ����� ����
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './js/bootstrap-datetimepicker.min.js', './promotions/gui/promotions.gui.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->field_col = 3;

    $Tab1 = $PHPShopGUI->setCollapse('��������', $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText('', 'name_new', $data['name'])) .
            $PHPShopGUI->setField('������', $PHPShopGUI->setRadio("enabled_new", 1, "����������", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "������", $data['enabled'])) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setRadio("discount_tip_new", 0, "��� ������ ������ �, ������ � ������� ����� �", $data['discount_tip']) . '<br>' .
                    $PHPShopGUI->setRadio("discount_tip_new", 1, "��� ������ N ���������� ������ �, ������ � ������� M ���������� ������ �", $data['discount_tip']) . '<br>' .
                    $PHPShopGUI->setRadio("discount_tip_new", 2, "��� ������ ������ � ������� ����� A", $data['discount_tip'])).
            $PHPShopGUI->setField('����� �� �����', $PHPShopGUI->setInputText('', 'label_new', $data['label']))
            );


    $Tab1 .= $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField('������', $PHPShopGUI->setInputDate("active_date_ot_new", $data['active_date_ot'])) . $PHPShopGUI->setField('����������', $PHPShopGUI->setInputDate("active_date_do_new", $data['active_date_do'])));


    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
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
        if ($k == $data['parent_to'])
            $tree_array[$k]['selected'] = true;
    }

    $GLOBALS['tree_array'] = &$tree_array;

    // �����������
    $dop_cat_array = preg_split('/,/', $data['categories'], -1, PREG_SPLIT_NO_EMPTY);

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], 1, $k, $dop_cat_array);


            // �����������
            $selected = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected = "selected";
                }


            $disabled = null;

            $tree_select .= '<option value="' . $k . '"  ' . $selected . $disabled . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container=""  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';


    $Tab1 .= $PHPShopGUI->setCollapse('�������', $PHPShopGUI->setField('���������', $PHPShopGUI->setHelp('�������� ��������� ������� �/��� ������� ID ������� ��� �����.') .
                    $PHPShopGUI->setCheckbox("categories_check_new", 1, "��������� ��������� ������", $data['categories_check']) .'<br>'.
                    $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0) .'<br><br>'.
                    $tree_select) .
            $PHPShopGUI->setField('������', $PHPShopGUI->setCheckbox("products_check_new", 1, "��������� ������", $data['products_check']) .'<br>'. 
                    $PHPShopGUI->setCheckbox("block_old_price_new", 1, "������������ ������ �� ������ �����", $data['block_old_price']).'<br><br>'.
                    $PHPShopGUI->setTextarea('products_new', $data['products'], false, false, false, __('������� ID ������� ��� ��������������') . ' <a href="#" data-target="#products_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('������� �������') . '</a>'))
    );

    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"), true);


    $oFCKeditor = new Editor('description_new', true);
    $oFCKeditor->Height = '120';
    $oFCKeditor->ToolbarSet = 'Normal';
    $oFCKeditor->Value = $data['description'];

   $Tab2 = $PHPShopGUI->setCollapse('�������� ����� �� �����',$oFCKeditor->AddGUI());

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true,false,true), array("�������������", $Tab2,true,false,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.modules.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    if (empty($_POST['ajax'])) {

        $_POST['categories_new'] = "";
        if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

            $_POST['categories_check_new'] = 1;

            foreach ($_POST['categories'] as $v)
                if (!empty($v) and ! strstr($v, ','))
                    $_POST['categories_new'] .= $v . ",";
        }

        // ������� ������ ��������� 
        if (!empty($_POST['active_date_do_new']))
            $_POST['active_check_new'] = 1;
        else
            $_POST['active_check_new'] = 0;

        $PHPShopOrm->updateZeroVars('discount_tip_new', 'products_check_new', 'block_old_price_new', 'categories_check_new', 'active_check_new', 'enabled_new');
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
    global $PHPShopOrm, $PHPShopModules;

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();


// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>