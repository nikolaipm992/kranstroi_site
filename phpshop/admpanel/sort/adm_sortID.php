<?php

PHPShopObj::loadClass(['sort', 'array', 'category']);

$TitlePage = __('�������������� ��������������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);

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
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' >' . $del . $v . '</option>';
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

    if (!empty($_GET['type']))
        $TitlePage = __("������ �������������");
    else
        $TitlePage = __("��������������");

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./sort/gui/sort.gui.js');
    $PHPShopGUI->setActionPanel($TitlePage . ': ' . $data['name'] . ' [ID ' . $data['id'] . ']', array('�������', '������� �����', '|', '�������'), array('���������', '��������� � �������'));

    // ��������
    $page_value[] = array('- ' . __('��� ��������') . ' - ', null, $data['page']);
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data_page = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1000));
    if (is_array($data_page))
        foreach ($data_page as $v)
            $page_value[] = array($v['name'], $v['link'], $data['page']);

    // ���������
    $PHPShopSort = new PHPShopSortCategoryArray(array('category' => '=0'));
    $PHPShopSortArray = $PHPShopSort->getArray();
    if (is_array($PHPShopSortArray))
        foreach ($PHPShopSortArray as $v)
            $category_value[] = array($v['name'], $v['id'], $data['category']);

    // ������ ��������� / optionname
    if (empty($_GET['type'])) {
        $Tab3 = $PHPShopGUI->setField("������:", $PHPShopGUI->setSelect('category_new', $category_value, '100%', false, false, true) .
                        $PHPShopGUI->setHelp('����� ������ ������ �������� ������� � ����� ������� ���-�. � �������� ���������� �������� <a href="?path=system#2" target="_blank">���������� �������� �������</a>.')) .
                $PHPShopGUI->setField("�����:", $PHPShopGUI->setCheckbox('brand_new', 1, null, $data['brand']), 1, '�������������� ���������� ������� � ������������ � ������ �������') .
                $PHPShopGUI->setField("������������", $PHPShopGUI->setCheckbox('product_new', 1, null, $data['product']), 1, '������ �������� ���-�� �������� ������������� ������ ��� ���������� �������, ��������� � �������� ������') .
                $PHPShopGUI->setField('������', $PHPShopGUI->setCheckbox('filtr_new', 1, null, $data['filtr'])) .
                $PHPShopGUI->setField('�������� �����', $PHPShopGUI->setCheckbox('goodoption_new', 1, null, $data['goodoption']) . '<br>' .
                        $PHPShopGUI->setCheckbox('optionname_new', 1, '�� ����������� ��� ���������� � �������', $data['optionname'])
                ) .
                $PHPShopGUI->setField('����������� �������', $PHPShopGUI->setCheckbox('virtual_new', 1, null, $data['virtual'])) .
                $PHPShopGUI->setField('���������� � ������ ������', $PHPShopGUI->setCheckbox('show_preview_new', 1, null, $data['show_preview'])) .
                $PHPShopGUI->setField("��������", $PHPShopGUI->setSelect('page_new', $page_value, '100%', false, false, true), 1, '��� �������������� (� ������� ������������� � ��������� �������� ������) ���������� ������� �� ��������� �������� � ���������.');

        $help = '<p class="text-muted">' . __('����� �������� ��������������, �� ����� ������� � <a href="?path=catalog&action=new" class=""><span class="glyphicon glyphicon-share-alt"></span> �������� �������</a>') . '</p>';
    } else
        $Tab3 = null;

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField("������������", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'name_new', 'value' => $data['name']))) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'num_new', 'value' => $data['num'], 'size' => 100))) .
            $Tab3
    );

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (is_array($CategoryArray))
        $GLOBALS['count'] = count($CategoryArray);

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

            $tree_select .= '<option value="' . $k . '"  ' . $selected . '>' . $v . '</option>';

            $tree_select .= $check['select'];
        }


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    // ��������
    if (empty($_GET['type'])) {
        $Tab1 .= $PHPShopGUI->setCollapse('���������', $help);
        $Tab1 .= $PHPShopGUI->setCollapse('��������', $PHPShopGUI->loadLib('tab_value', $data));
    } else {

        // ����� ��������
        $Tab1 .= $PHPShopGUI->setCollapse('����� ��������', $PHPShopGUI->setField("��������", $tree_select . $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.'));
    }

    // �������������
    $Tab1 .= $PHPShopGUI->setCollapse('�������������', $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/', '100%')) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setTextarea('description_new', $data['description'])));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.sort.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.sort.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.sort.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // �������� ��������
    $PHPShopOrmValue = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
    $PHPShopOrmValue->delete(['category' => '=' . $_POST['rowID']]);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return array('success' => $action);
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    if (!empty($_GET['type']))
        header('Location: ?path=' . $_GET['path'] . '&cat=' . $_POST['rowID']);
    else
        header('Location: ?path=' . $_GET['path'] . '&cat=' . $_POST['category_new']);
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    if (empty($_POST['ajax'])) {

        if (!empty($_POST['category_new']))
            $_POST['category_new'] = intval($_POST['category_new']);

        // ������������� ������ ��������
        $PHPShopOrm->updateZeroVars('brand_new', 'filtr_new', 'goodoption_new', 'optionname_new', 'product_new', 'virtual_new', 'show_preview_new');

        // ����������
        if (is_array($_POST['servers'])) {
            $_POST['servers_new'] = "";
            foreach ($_POST['servers'] as $v)
                if ($v != 'null' and ! strstr($v, ','))
                    $_POST['servers_new'] .= "i" . $v . "i";
        }

        // ���������
        if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

            foreach ($_POST['categories'] as $v)
                if (!empty($v) and ! strstr($v, ','))
                    $cat_array[] = $v;

            if (is_array($cat_array)) {

                $data_sort = $PHPShopOrm->select(['id'], ['category' => '=' . $_POST['rowID'], 'filtr' => "='1'"], ['order' => 'id DESC'], ['limit' => 1000]);

                if (is_array($data_sort))
                    foreach ($data_sort as $val)
                        $sort_new[] = $val['id'];

                $update['sort_new'] = serialize($sort_new);

                $where = array('id' => ' IN ("' . implode('","', $cat_array) . '")');
                $PHPShopOrmCat = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
                $PHPShopOrmCat->debug = false;
                $PHPShopOrmCat->update($update, $where);
            }
        }
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