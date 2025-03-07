<?php

PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("page");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("category");


$TitlePage = __('�������������� ������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '&brvbar;&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {

            $check = treegenerator(@$tree_array[$k], $i + 1, $curent, $dop_cat_array);

            if ($k == $curent)
                $selected = 'selected';
            else
                $selected = null;

            // �����������
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($check['select'])) {
                $tree_select .= '<option value="' . $k . '" ' . $selected . '>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '>' . $del . $v . '</option>';

                $i = 1;
            } else {
                $tree_select .= '<option value="' . $k . '" disabled>' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" disabled >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopBase, $PHPShopSystem, $CategoryArray, $isFrame, $hideCatalog;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));

    // �������� �� �������� �������
    if ($data['parent_enabled'] == 1 and empty($_GET['view'])) {
        $data_parent = $PHPShopOrm->select(array('id'), array('parent' => "='" . $data['id'] . "' or parent LIKE '," . $data['id'] . "' or  parent LIKE '" . $data['id'] . ",'", 'parent_enabled' => "='0'"), false, array('limit' => 1));
        if (!empty($data_parent['id']))
            header('Location: ?path=' . $_GET['path'] . '&id=' . $data_parent['id'] . '&tab=6&return=' . $_GET['return']);
    }

    // ��� ������
    if (!is_array($data)) {
        header('Location: ?path=' . $_GET['return']);
    }

    // ��� ������
    if (strlen($data['name']) > 47)
        $title_name = mb_substr($data['name'], 0, 47) . '...';
    else
        $title_name = $data['name'];

    if (empty($isFrame))
        $title_name .= ' [ID ' . $data['id'] . ']';

    $PHPShopGUI->action_select['������������'] = array(
        'name' => '������������',
        'url' => '../../shop/UID_' . $data['id'] . '.html',
        'action' => 'front',
        'target' => '_blank',
        'class' => $GLOBALS['isFrame']
    );

    $PHPShopGUI->setActionPanel('<span class="' . $isFrame . '">' . __("�����") . ": </span>" . $title_name, array('������� �����', '������������', '|', '�������'), array('���������', '��������� � �������'), false);

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./js/jquery.tagsinput.min.js', './catalog/gui/catalog.gui.js', './js/jquery.waypoints.min.js', './product/gui/product.gui.js', './js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->addCSSFiles('./css/jquery.tagsinput.css', './css/bootstrap-colorpicker.min.css');

    // ����� ����������
    if ($PHPShopSystem->ifSerilizeParam('admoption.rule_enabled', 1) and ! $PHPShopBase->Rule->CheckedRules('catalog', 'remove')) {
        $where = array('secure_groups' => " REGEXP 'i" . $_SESSION['idPHPSHOP'] . "i' or secure_groups = ''");
        $secure_groups = true;
    } else
        $where = $secure_groups = false;

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $CategoryArray[0]['name'] = '- ' . __('������� �������') . ' -';
    $tree_array = array();

    $getKey = $PHPShopCategoryArray->getKey('parent_to.id', true);

    if (is_array($getKey))
        foreach ($getKey as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
            }
            $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
            $tree_array[$k]['id'] = $k;
        }

    $tree_array[0]['sub'][1000000] = __('�������������� ������');
    $tree_array[1000000]['name'] = __('�������������� ������');
    $tree_array[1000000]['id'] = 1000000;
    $tree_array[1000000]['sub'][1000001] = __('����������� CRM');
    $tree_array[1000000]['sub'][1000002] = __('����������� CSV');
    $tree_array[1000002]['id'] = 0;

    $GLOBALS['tree_array'] = &$tree_array;
    $tree_select = $tree_select_dop = null;

    // �����������
    $dop_cat_array = preg_split('/#/', $data['dop_cat'], -1, PREG_SPLIT_NO_EMPTY);

    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, $data['category'], $dop_cat_array);

            if ($k == $data['category'])
                $selected = 'selected';
            else
                $selected = null;

            // �����������
            $selected_dop = null;
            if (is_array($dop_cat_array))
                foreach ($dop_cat_array as $vs) {
                    if ($k == $vs)
                        $selected_dop = "selected";
                }

            if (empty($tree_array[$k]))
                $disabled = null;
            else
                $disabled = ' disabled';

            $tree_select .= '<option value="' . $k . '" ' . $selected . $disabled . '>' . $v . '</option>';
            $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . $disabled . '>' . $v . '</option>';
            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }

    $tree_select_dop = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="dop_cat[]" data-width="100%" multiple><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select_dop . '</select>';

    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="category_new"  data-width="100%"><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select . '</select>';

    // ������������
    $Tab_info .= $PHPShopGUI->setField("��������", $PHPShopGUI->setTextarea('name_new', $data['name']));

    // �������
    $Tab_info .= $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(null, 'uid_new', $data['uid'], '100%'));

    $icon_title = false;

    if ($PHPShopSystem->ifSerilizeParam("admoption.image_off", 1)) {
        $icon_server = false;
        $icon_title = '����������� ����� ��� ������ � ����� ���������, ������� �������������� �� �������. ��� �������� �������������� ���� ������, ������� ������ �� �����������, ������� ����� � ��������� - ����������� - ��������� �����������.';
    } else
        $icon_server = true;

    $Tab_info .= $PHPShopGUI->setField("�����������", $PHPShopGUI->setIcon($data['pic_big'], "pic_big_new", true, array('load' => false, 'server' => true, 'url' => true, 'view' => $icon_server)), 1, $icon_title);

    if (empty($icon_server))
        $Tab_info .= $PHPShopGUI->setField("������", $PHPShopGUI->setFile($data['pic_small'], "pic_small_new", array('load' => false, 'server' => 'image', 'url' => true, 'view' => $icon_server)), 1, $icon_title);
    else
        $Tab_info .= $PHPShopGUI->setFile($data['pic_small'], "pic_small_new", array('load' => false, 'server' => 'image', 'url' => false, 'view' => $icon_server));

    // ������� ���������
    if (empty($data['ed_izm']))
        $ed_izm = __('��.');
    else
        $ed_izm = $data['ed_izm'];

    if (empty($hideCatalog)) {

        // �������������� �����
        $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
        $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num'), array('limit' => 100));
        if (is_array($dataWarehouse)) {

            $Tab_info .= $PHPShopGUI->setField('����� �����', $PHPShopGUI->setInputText(false, 'items_new', $data['items'], 100, $ed_izm), 'left');

            foreach ($dataWarehouse as $row) {
                $Tab_info .= $PHPShopGUI->setField($row['name'], $PHPShopGUI->setInputText(false, 'items' . $row['id'] . '_new', $data['items' . $row['id']], 100, $ed_izm), 2, $row['description'], null, 'control-label', false);
            }
        } else
            $Tab_info .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'items_new', $data['items'], 100, $ed_izm), 'left');
    }

    // ���
    $Tab_info_size = $PHPShopGUI->setField('���', $PHPShopGUI->setInputText(false, 'weight_new', $data['weight'], 100, __('�&nbsp;&nbsp;&nbsp;&nbsp;')), 'left');

    // ��������
    $Tab_info_size .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'length_new', $data['length'], 100, __('��&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'width_new', $data['width'], 100, __('��&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'height_new', $data['height'], 100, __('��&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('������� ���������', $PHPShopGUI->setInputText(false, 'ed_izm_new', $ed_izm, 100));

    // ����� ��������
    $Tab_info_dop = $PHPShopGUI->setField("�������", $tree_select, 1, __('����� � �������� ID', false) . ' ' . $data['category'], false, 'control-label', true);

    // ��������
    $stat = PHPShopDate::get($data['datas'], true);
    if (!empty($data['import_id'])){
        $import_link = $PHPShopGUI->setLink('./admin.php?path=catalog&cat=0&import=' . $data['import_id'], __('��������'));
        $import_help = '�������� ��� ������ ����� �������';
    }
    else{
        $import_link = __('��������');
        $import_help=null;
    }

    $Tab_info_dop .= $PHPShopGUI->setField($import_link, $PHPShopGUI->setText($stat), 1, $import_help, null, 'control-label', false);

    // ������������� ������
    $Tab_info_dop .= $PHPShopGUI->setField('������������� ������ ��� ���������� �������', $PHPShopGUI->setTextarea('odnotip_new', $data['odnotip'], false, false, false, __('������� ID ������� ��� ��������������') . ' <a href="#" data-target="#odnotip_new"  class="btn btn-sm btn-default tag-search"><span class="glyphicon glyphicon-search"></span> ' . __('������� �������') . '</a>'));

    // �������������� ��������
    $Tab_info_dop .= $PHPShopGUI->setField('�������������� ��������', $tree_select_dop, 1, '������ ������������ ��������� � ���������� ���������.');

    // ����� ������
    $Tab_info .= $PHPShopGUI->setField('����� ������', $PHPShopGUI->setCheckbox('enabled_new', 1, '� ��������', $data['enabled']) . '<br>' .
            $PHPShopGUI->setCheckbox('sklad_new', 1, '��� � �������', $data['sklad']). '<br>' .
            $PHPShopGUI->setCheckbox('spec_new', 1, '���������������', $data['spec']) . '<br>' .
            $PHPShopGUI->setCheckbox('newtip_new', 1, '�������', $data['newtip']));
    $Tab_info .= $PHPShopGUI->setField('����������', $PHPShopGUI->setInputText('&#8470;', 'num_new', $data['num'], 100));

    $type_value[] = array('�����', 1, $data['type']);
    $type_value[] = array('������', 2, $data['type']);
    $Tab_info .= $PHPShopGUI->setField('���', $PHPShopGUI->setSelect('type_new', $type_value, 100, true));

    if (!empty($_GET['view']) and $_GET['view'] == 'option')
        $Tab_info .= $PHPShopGUI->setField('�����', $PHPShopGUI->setRadio('parent_enabled_new', 0, '������� �����', $data['parent_enabled']) . $PHPShopGUI->setRadio('parent_enabled_new', 1, '������ ������', $data['parent_enabled']));

    $Tab_rating = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(null, 'rate_new', $data['rate'], 50), 1, '�������� �� 0 �� 5');
    $Tab_rating .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(null, 'rate_count_new', $data['rate_count'], 50));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab_info);
    $Tab1 .= $PHPShopGUI->setCollapse('����������', $Tab_info_dop);

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            } else
                $check = false;
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $data['baseinputvaluta'], false);
        }

    // ����
    if (!empty($data['parent']) and $PHPShopSystem->ifSerilizeParam('admoption.parent_price_enabled') == 0)
        $price_parent_help = '���� ������� �������, ������� ���� ������ ������������� ������������� �� ���������� ���� �������, ��� ����������� ����������� ���� � ������ ������.';
    else
        $price_parent_help = null;
    $Tab_price = $PHPShopGUI->setField('����', $PHPShopGUI->setInputText(null, 'price_new', $data['price'], 150, $valuta_def_name), 2, $price_parent_help);

    if (empty($data['parent']) or $PHPShopSystem->ifSerilizeParam('admoption.parent_price_enabled')) {
        $Tab_price .= $PHPShopGUI->setField('���� 2', $PHPShopGUI->setInputText(null, 'price2_new', $data['price2'], 150, $valuta_def_name), 2);
        $Tab_price .= $PHPShopGUI->setField('���� 3', $PHPShopGUI->setInputText(null, 'price3_new', $data['price3'], 150, $valuta_def_name), 2);
        $Tab_price .= $PHPShopGUI->setField('���� 4', $PHPShopGUI->setInputText(null, 'price4_new', $data['price4'], 150, $valuta_def_name), 2);
        $Tab_price .= $PHPShopGUI->setField('���� 5', $PHPShopGUI->setInputText(null, 'price5_new', $data['price5'], 150, $valuta_def_name), 2);
    }
    $Tab_price .= $PHPShopGUI->setField('������ ����', $PHPShopGUI->setInputText(null, 'price_n_new', $data['price_n'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('���������� ����', $PHPShopGUI->setInputText(null, 'price_purch_new', $data['price_purch'], 150, $valuta_def_name));

    // ������
    if (empty($hideCatalog))
        $Tab_price .= $PHPShopGUI->setField('������', $valuta_area);

    // YML
    //$data['yml_bid_array'] = unserialize($data['yml_bid_array']);
    $Tab_yml = $PHPShopGUI->setField('<a href="/yml/" target="_blank" title="������� ����">YML</a>', $PHPShopGUI->setCheckbox('yml_new', 1, '����� � ������ �������', $data['yml']) . '<br>' .
            $PHPShopGUI->setRadio('p_enabled_new', 1, '� �������', $data['p_enabled']) . '<br>' .
            $PHPShopGUI->setRadio('p_enabled_new', 0, '��������� (��� �����)', $data['p_enabled'])
    );

    // BID
    //$Tab_yml .= $PHPShopGUI->setField('������ BID', $PHPShopGUI->setInputText(null, 'yml_bid_array[bid]', $data['yml_bid_array']['bid'], 100));

    if (empty($hideCatalog)) {
        $Tab1 .= $PHPShopGUI->setCollapse('����', $Tab_price, 'in', true, true, array('type' => 'price'));
        //$Tab1 .= $PHPShopGUI->setCollapse('������ ������', $Tab_yml, false);
        $Tab1 .= $PHPShopGUI->setCollapse('��������', $Tab_info_size);
    }

    $Tab_rating = $PHPShopGUI->setCollapse('�������', $Tab_rating, false);

    // �������� �������� ��������
    $Tab2 = $PHPShopGUI->setCollapse('������� ��������', $PHPShopGUI->loadLib('tab_description', $data));

    // �������� ���������� ��������
    $Tab3 = $PHPShopGUI->setCollapse('��������� ��������', $PHPShopGUI->loadLib('tab_content', $data));

    // ������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data_page = $PHPShopOrm->select(['*'], ['category' => '!=2000', 'enabled' => "='1'"], array('order' => 'name'), array('limit' => 500));

    if (strstr($data['page'], ',')) {
        $data['page'] = explode(",", $data['page']);
    } else
        $data['page'] = array($data['page']);

    $value = array();
    if (is_array($data_page))
        foreach ($data_page as $val) {
            if (is_numeric(array_search($val['link'], $data['page']))) {
                $check = 'selected';
            } else
                $check = false;

            $value[] = array($val['name'], $val['link'], $check);
        }

    // ������
    $Tab_docs = $PHPShopGUI->setCollapse('������', $PHPShopGUI->setSelect('page_new[]', $value, '100%', false, false, true, false, false, true));

    // �����
    $Tab_docs .= $PHPShopGUI->setCollapse('�����', $PHPShopGUI->loadLib('tab_files', $data));

    // �����������
    $Tab6 = $PHPShopGUI->loadLib('tab_img', $data);

    // ��������������
    $Tab_sorts = $PHPShopGUI->loadLib('tab_sorts', $data);

    // ���������
    $Tab_header = $PHPShopGUI->loadLib('tab_headers', $data);

    // ������� ���
    $Tab_external .= $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField('������� ���', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));


    // ������
    $Tab_comments = $PHPShopGUI->loadLib('tab_comments', $data);
    if (empty($Tab_comments))
        $Tab_comments_enabled = true;

    // �������
    $Tab_option = $PHPShopGUI->loadLib('tab_option', $data);
    $option_help = '<p class="text-muted">' . __('� ����� �������� ����� �������� ������ 2 �������� ��������. ������ ���� - ������ � ����, ������� �� ������ ������������� �� ����. � ������ ��������� ����� ��������� ������ �������� ��������. ��������, � ����������� ����� ������ � ���������, � ������ � ������, ��������.') . '</p>';
    $Tab_option .= $PHPShopGUI->setCollapse('���������', $option_help);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true), array("�����������", $Tab6, true, $PHPShopSystem->ifSerilizeParam("admoption.image_off", 1)), array("��������", $Tab2 . $Tab3, true, false, true), array("�������������", $Tab_docs . $Tab_rating . $Tab_header . $Tab_external, true, false, true), array("��������������", $Tab_sorts, true), array("�������", $Tab_option, true), array("������", $Tab_comments, true, $Tab_comments_enabled));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "but", "actionDelete.catalog.edit") .
            $PHPShopGUI->setInput("submit", "editID", "���������", "right", 70, "", "but", "actionUpdate.catalog.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.catalog.edit");

    $_GET['path'] = 'catalog';

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

/**
 * ����� ����������
 */
function actionSave() {

    // ���������� ������
    actionUpdate();

    header('Location: ?path=' . $_GET['return'] . '&cat=' . $_POST['category_new']);
}

/**
 * ����� ����������
 * @return bool
 */
function actionUpdate() {
    global $PHPShopModules, $PHPShopSystem, $PHPShopOrm;

    $PHPShopProduct = new PHPShopProduct($_POST['rowID']);

    $category = $PHPShopProduct->getParam('category');

    // ����� ��������
    if ($category != $_POST['category_new'])
        $category_update = true;

    // ����� ����������� ���� ��������
    if ($PHPShopSystem->ifSerilizeParam('admoption.parent_price_enabled') != 1) {

        $PHPShopOrm->mysql_error = false;

        $parent_enabled = $PHPShopProduct->getParam('parent_enabled');

        $parentIds = $PHPShopProduct->getParam('parent');
        if (!empty($parentIds)) {
            $parent = @explode(",", $parentIds);
        }

        if (empty($parent_enabled) and ! empty($parent)) {

            // ������� �� 1�
            if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option')) {
                $ParentData = $PHPShopOrm->select(array('min(price) as price, price_n'), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, array('limit' => 1));

                $ParentDataItems = $PHPShopOrm->select(array('sum(items) as items'), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, false);

                if ($category_update) {
                    $PHPShopOrm->update(array('category_new' => $_POST['category_new']), array('uid' => ' IN ("' . @implode('","', $parent) . '")', 'parent_enabled' => "='1'"));
                }
            } else {
                $ParentData = $PHPShopOrm->select(array('min(price) as price, price_n'), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, false);

                $ParentDataItems = $PHPShopOrm->select(array('sum(items) as items'), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'enabled' => "='1'", 'sklad' => "!='1'", 'parent_enabled' => "='1'"), false, false);

                if ($category_update) {
                    $PHPShopOrm->update(array('category_new' => $_POST['category_new']), array('id' => ' IN ("' . @implode('","', $parent) . '")', 'parent_enabled' => "='1'"));
                }
            }

            if (!empty($ParentData['price'])) {

                $_POST['price_new'] = $ParentData['price'];
                $_POST['items_new'] = $ParentDataItems['items'];

                if (!empty($ParentData['price_n']))
                    $_POST['price_n_new'] = $ParentData['price_n'];
            }
        }
    }

    // ���� �����������
    $_POST['datas_new'] = time();

    if (empty($_POST['ajax'])) {
        
        // ����� �� �������
        if ($PHPShopSystem->ifSerilizeParam('admoption.sklad_sum_enabled')) {
            $PHPShopOrmW = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
            $data = $PHPShopOrmW->select(array('*'), false, array('order' => 'num DESC'), array('limit' => 100));
            if (is_array($data)) {
                $items = 0;
                foreach ($data as $row) {
                    if (isset($_POST['items' . $row['id'] . '_new'])) {
                        $items += $_POST['items' . $row['id'] . '_new'];
                    }
                }
            }

            if (!empty($items)) {
                $_POST['items_new'] = $items;
            }
        }

        // ���������� �������������
        if (is_array($_POST['vendor_array_add'])) {
            foreach ($_POST['vendor_array_add'] as $k => $val) {

                $sort_array = $result = null;

                if (!empty($val)) {

                    if (strstr($val, '#')) {
                        $sort_array = explode('#', $val);
                    } else
                        $sort_array[] = $val;

                    if (is_array($sort_array))
                        foreach ($sort_array as $val_sort) {

                            $PHPShopOrmSort = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

                            // �������� ������������
                            $checkName = $PHPShopOrmSort->select(array('id'), array('name' => '="' . trim($val_sort) . '"', 'category' => '=' . intval($k)), false, array('limit' => 1));

                            // ��� ��������������, ������� �����
                            if (empty($checkName['id'])) {
                                $PHPShopOrmSort->clean();

                                $result = $PHPShopOrmSort->insert(array('name_new' => trim($val_sort), 'category_new' => intval($k)));
                                if (!empty($result))
                                    $_POST['vendor_array_new'][$k][] = $result;
                            }
                            // ����, �������� �� ����
                            else {
                                $_POST['vendor_array_new'][$k][] = $checkName['id'];
                            }
                        }
                } else
                    unset($_POST['vendor_array_add'][$k]);
            }
        }

        // ��������� �������������
        $_POST['vendor_new'] = null;

        if (is_array($_POST['vendor_array_new'])) {

            foreach ($_POST['vendor_array_new'] as $k => $v) {
                if (is_array($v)) {
                    $v = array_unique($v);
                    $_POST['vendor_array_new'][$k] = $v;

                    foreach ($v as $key => $p) {
                        $_POST['vendor_new'] .= "i" . $k . "-" . $p . "i";
                        if (empty($p))
                            unset($_POST['vendor_array_new'][$k][$key]);
                    }
                } else
                    $_POST['vendor_new'] .= "i" . $k . "-" . $v . "i";
            }
        }

        $_POST['vendor_array_new'] = serialize($_POST['vendor_array_new']);

        // ������
        if (is_array($_POST['page_new'])) {

            if (empty($_POST['saveID']))
                array_pop($_POST['page_new']);

            $_POST['page_new'] = implode(",", $_POST['page_new']);
        } else
            $_POST['page_new'] = '';


        // �����
        if (isset($_POST['editID'])) {
            if (!empty($_POST['files_new']) and is_array($_POST['files_new'])) {
                foreach ($_POST['files_new'] as $k => $files) {

                    if (empty($files['name']))
                        $files['name'] = pathinfo($files['path'])['basename'];

                    $files_new[] = @array_map("urldecode", $files);
                }

                $_POST['files_new'] = serialize($files_new);
            } else
                $_POST['files_new'] = array();
        } else
            $_POST['files_new'] = serialize($_POST['files_new']);

        // ��� ��������
        $_POST['dop_cat_new'] = "";
        if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
            $_POST['dop_cat_new'] = "#";
            foreach ($_POST['dop_cat'] as $v)
                if ($v != 'null' and ! strstr($v, ','))
                    $_POST['dop_cat_new'] .= $v . "#";
        }

        // ��������� �����
        if (!empty($_POST['parent2_new']) and empty($_POST['color_new']))
            $_POST['color_new'] = PHPShopString::getColor($_POST['parent2_new']);

        // ����������� �������
        if (!empty($_POST['editParent']) and ! empty($_POST['pic_big_new'])) {
            $_POST['pic_small_new'] = $_POST['pic_big_new'];
        }

        // �������
        if ($_POST['rate_new'] > 5)
            $_POST['rate_new'] = 5;

        $postOdnotip = explode(',', $_POST['odnotip_new']);
        $odnotip = [];
        if (is_array($postOdnotip)) {
            foreach ($postOdnotip as $value) {
                if ((int) $value > 0) {
                    $odnotip[] = (int) $value;

                    // ����� ���������
                    if ($PHPShopSystem->ifSerilizeParam('admoption.odnotip')) {
                        $odnotip_data = $PHPShopOrm->getOne(['odnotip'], ['id' => '=' . (int) $value]);
                        $odnotip_array = explode(',', $odnotip_data['odnotip']);
                        if (is_array($odnotip_array)) {
                            if (!in_array($_POST['rowID'], $odnotip_array))
                                $odnotip_array[] = $_POST['rowID'];
                        } else
                            $odnotip_array[] = $_POST['rowID'];

                        $PHPShopOrm->update(['odnotip_new' => implode(',', $odnotip_array)], ['id' => '=' . (int) $value]);
                    }
                }
            }

            $_POST['odnotip_new'] = implode(',', $odnotip);
        }

        // ������������� ������ ��������
        $PHPShopOrm->updateZeroVars('newtip_new', 'enabled_new', 'spec_new', 'yml_new', 'sklad_new', 'pic_small_new', 'pic_big_new');

        if (empty($_POST['pic_small_new']) || empty($_POST['pic_big_new'])) {
            $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
            $photo = $orm->getOne(['name'], ['parent' => sprintf('="%s"', $_POST['rowID'])], ['order' => 'id asc']);
            if (empty($_POST['pic_big_new'])) {
                $_POST['pic_big_new'] = $photo['name'];
            }
            if (empty($_POST['pic_small_new'])) {
                $_POST['pic_small_new'] = str_replace(".", "s.", $photo['name']);
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $_POST['pic_small_new'])) {
                    $_POST['pic_small_new'] = $photo['name'];
                }
            }
        }
    }

    if (isset($_POST['price_new'])) {
        $_POST['price_new'] = str_replace(',', '.', $_POST['price_new']);
    }
    if (isset($_POST['price_n_new'])) {
        $_POST['price_n_new'] = str_replace(',', '.', $_POST['price_n_new']);
    }
    if (isset($_POST['price2_new'])) {
        $_POST['price2_new'] = str_replace(',', '.', $_POST['price2_new']);
    }
    if (isset($_POST['price3_new'])) {
        $_POST['price3_new'] = str_replace(',', '.', $_POST['price3_new']);
    }
    if (isset($_POST['price4_new'])) {
        $_POST['price4_new'] = str_replace(',', '.', $_POST['price4_new']);
    }
    if (isset($_POST['price5_new'])) {
        $_POST['price5_new'] = str_replace(',', '.', $_POST['price5_new']);
    }

    // ���������� ������������� ��� ����� ��������
    if (!empty($category_update)) {
        $PHPShopCategory = new PHPShopCategory($category);
        $sort_old = $PHPShopCategory->unserializeParam('sort');

        if (is_array($sort_old)) {
            $PHPShopCategory = new PHPShopCategory($_POST['category_new']);
            $sort_new = $PHPShopCategory->unserializeParam('sort');

            if (is_array($sort_new)) {
                foreach ($sort_old as $val) {
                    if (!in_array($val, $sort_new))
                        $sort_new[] = $val;
                }
            } else
                $sort_new = $sort_old;

            $PHPShopCategory->updateParam(array('sort_new' => serialize($sort_new)));
        }
    }

    // �������� ������ �� ������ � ��
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // ���������� ����������� � �����������
    $insert = fotoAdd();
    if (empty($_POST['pic_small_new']) and ! empty($insert['pic_small_new']))
        $_POST['pic_small_new'] = $insert['pic_small_new'];
    if (empty($_POST['pic_big_new']) and ! empty($insert['name_new']))
        $_POST['pic_big_new'] = $insert['name_new'];

    // ����� ������������
    $_POST['user_new'] = $_SESSION['idPHPSHOP'];


    if (strstr($_POST['rowID'], ","))
        $where = ['id' => ' IN (' . $_POST['rowID'] . ')'];
    else
        $where = ['id' => '=' . $_POST['rowID']];

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST, $where);
    $PHPShopOrm->clean();

    // ���������� �� ������
    if (isset($_POST['items_new'])) {
        $PHPShopProduct->objRow['items'] = $_POST['items_new'];
        $PHPShopProduct->objRow['enabled'] = $_POST['enabled_new'];
        $PHPShopProduct->objRow['sklad'] = $_POST['sklad_new'];
        $PHPShopProduct->applyWarehouseControl();
    }
    
     // �������� ������ ����� ������ � ��
    $PHPShopModules->setAdmHandler(__FILE__, 'actionSave', $_POST);

    return array('success' => $action, 'enabled' => $PHPShopProduct->objRow['enabled'], 'sklad' => $PHPShopProduct->objRow['sklad'], 'id' => $_POST['rowID']);
}

// ���������� ����������� � �����������
function fotoAdd() {
    global $PHPShopSystem;
    require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

    // ��������� ����������
    $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw');
    $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th');
    $img_w = $PHPShopSystem->getSerilizeParam('admoption.img_w');
    $img_h = $PHPShopSystem->getSerilizeParam('admoption.img_h');
    $img_tw = empty($img_tw) ? 150 : $img_tw;
    $img_th = empty($img_th) ? 150 : $img_th;
    $img_w = empty($img_w) ? 300 : $img_w;
    $img_h = empty($img_h) ? 300 : $img_h;

    $img_adaptive = $PHPShopSystem->getSerilizeParam('admoption.image_adaptive_resize');
    $image_save_source = $PHPShopSystem->getSerilizeParam('admoption.image_save_source');
    $width_kratko = $PHPShopSystem->getSerilizeParam('admoption.width_kratko');
    $width_podrobno = $PHPShopSystem->getSerilizeParam('admoption.width_podrobno');

    // ����� ����������
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // ��������� � ����� ���������
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_catalog')) {

        $PHPShopCategory = new PHPShopCategory($_POST['category_new']);
        $parent_to = $PHPShopCategory->getParam('parent_to');
        $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName()));

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
            $parent_to = $PHPShopCategory->getParam('parent_to');
        }

        if (!empty($parent_to)) {
            $PHPShopCategory = new PHPShopCategory($parent_to);
            $pathName = '/' . ucfirst(PHPShopString::toLatin($PHPShopCategory->getName())) . '/' . $pathName;
        }

        $path .= $pathName . '/';

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $path))
            @mkdir($_SERVER['DOCUMENT_ROOT'] . $path, 0777, true);
    }

    // ��������
    $path = str_replace('//', '/', $path);

    // ����
    $RName = substr(abs(crc32(time())), 0, 5);

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'webp'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'])) {
                $file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
                $file_name = $_FILES['file']['name'];
                $path_parts = pathinfo($file);
                $tmp_file = $_SERVER['DOCUMENT_ROOT'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['img_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST['img_new'])) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $_POST['img_new'];
        $path_parts = pathinfo($file);
        $file_name = $path_parts['basename'];

        // ���������� ���� �����
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_path'))
            $path = $GLOBALS['SysValue']['dir']['dir'] . str_replace($_SERVER['DOCUMENT_ROOT'], '', $path_parts['dirname']) . '/';
    }

    if (!empty($file)) {

        // ��������� ����������� (��������)
        $thumb = new PHPThumb($file);
        $thumb->setOptions(array('jpegQuality' => $width_kratko));

        // ������������
        if (!empty($img_adaptive))
            $thumb->adaptiveResize($img_tw, $img_th);
        else
            $thumb->resize($img_tw, $img_th);

        $watermark = $PHPShopSystem->getSerilizeParam('admoption.watermark_image');
        $watermark_text = $PHPShopSystem->getSerilizeParam('admoption.watermark_text');

        // �������� ��������
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_save_name')) {
            $name_s = $path_parts['filename'] . 's.' . strtolower($thumb->getFormat());
            $name = $path_parts['filename'] . '.' . strtolower($thumb->getFormat());
            $name_big = $path_parts['filename'] . '_big.' . strtolower($thumb->getFormat());

            if (!empty($image_save_source)) {
                $file_big = $_SERVER['DOCUMENT_ROOT'] . $path . $name_big;
                @copy($file, $file_big);
            }
        }
        // SEO ��������
        elseif ($PHPShopSystem->ifSerilizeParam('admoption.image_save_seo')) {

            if (!empty($_POST['prod_seo_name'])) {
                $seo_name = $_POST['prod_seo_name'];
            } else {
                PHPShopObj::loadClass("string");
                $seo_name = str_replace(array("_", "+", '&#43;'), array("-", "", ""), PHPShopString::toLatin($_POST['name_new']));
            }
            $name_s = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . 's.' . strtolower($thumb->getFormat());
            $name = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . '.' . strtolower($thumb->getFormat());
            $name_big = $seo_name . '-' . $_POST['rowID'] . '-' . $RName . '_big.' . strtolower($thumb->getFormat());
        } else {
            $name_s = 'img' . $_POST['rowID'] . '_' . $RName . 's.' . strtolower($thumb->getFormat());
            $name = 'img' . $_POST['rowID'] . '_' . $RName . '.' . strtolower($thumb->getFormat());
            $name_big = 'img' . $_POST['rowID'] . '_' . $RName . '_big.' . strtolower($thumb->getFormat());
        }


        // ��������� ��������
        if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_small_enabled')) {

            // Image
            if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
            // Text
            elseif (!empty($watermark_text))
                $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
        }

        // ���������� � webp
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
            $thumb->setFormat('WEBP');
            $name_s = str_replace([".png", ".jpg", ".jpeg", ".gif", ".PNG", ".JPG", ".JPEG", ".GIF"], '.webp', $name_s);
        }

        $thumb->save($_SERVER['DOCUMENT_ROOT'] . $path . $name_s);

        // ������� �����������
        $thumb = new PHPThumb($file);
        $thumb->setOptions(array('jpegQuality' => $width_podrobno));

        // ������������
        if (!empty($img_adaptive))
            $thumb->adaptiveResize($img_w, $img_h);
        else
            $thumb->resize($img_w, $img_h);

        // ��������� �������� �����������
        if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_big_enabled')) {

            // Image
            if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
            // Text
            elseif (!empty($watermark_text))
                $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
        }

        // ���������� � webp
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
            $thumb->setFormat('WEBP');
            $name = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $name);
        }

        $thumb->save($_SERVER['DOCUMENT_ROOT'] . $path . $name);

        // �������� �����������
        if (!empty($image_save_source)) {

            // ���������� � webp
            if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
                $thumb->setFormat('WEBP');
                $name_big = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $name_big);
            }

            if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name')) {
                $file_big = $_SERVER['DOCUMENT_ROOT'] . $path . $name_big;
                @copy($file, $file_big);
            }

            // ���������
            if ($PHPShopSystem->ifSerilizeParam('admoption.watermark_source_enabled')) {

                $thumb = new PHPThumb($file_big);
                $thumb->setOptions(array('jpegQuality' => $width_podrobno));
                $thumb->setWorkingImage($thumb->getOldImage());

                // Image
                if (!empty($watermark) and file_exists($_SERVER['DOCUMENT_ROOT'] . $watermark))
                    $thumb->createWatermark($_SERVER['DOCUMENT_ROOT'] . $watermark, $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));
                // Text
                elseif (!empty($watermark_text))
                    $thumb->createWatermarkText($watermark_text, $PHPShopSystem->getSerilizeParam('admoption.watermark_text_size'), $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/font/' . $PHPShopSystem->getSerilizeParam('admoption.watermark_text_font') . '.ttf', $PHPShopSystem->getSerilizeParam('admoption.watermark_right'), $PHPShopSystem->getSerilizeParam('admoption.watermark_bottom'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_color'), $PHPShopSystem->getSerilizeParam('admoption.watermark_text_alpha'), 0, $PHPShopSystem->getSerilizeParam('admoption.watermark_center_enabled'));

                $thumb->save($file_big);
            }
        }

        if (!$PHPShopSystem->ifSerilizeParam('admoption.image_save_name') and ! empty($tmp_file))
            unlink($tmp_file);

        // ���������� � ������� �����������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
        $insert['parent_new'] = $_POST['rowID'];
        $insert['name_new'] = $path . $name;
        $insert['pic_small_new'] = $path . $name_s;
        $PHPShopOrm->insert($insert);
        return $insert;
    }
}

// �������� �����������
function fotoDelete($where = null) {

    if (!is_array($where))
        $where = array('parent' => '=' . intval($_POST['rowID']));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);
    $data = $PHPShopOrm->select(array('*'), $where, false, array('limit' => 100));
    if (is_array($data)) {
        foreach ($data as $row) {
            $name = $row['name'];
            $pathinfo = pathinfo($name);
            $oldWD = getcwd();
            $dirWhereRenameeIs = $_SERVER['DOCUMENT_ROOT'] . $pathinfo['dirname'];
            $oldFilename = $pathinfo['basename'];

            @chdir($dirWhereRenameeIs);
            @unlink($oldFilename);
            $oldFilename_s = str_replace(".", "s.", $oldFilename);
            @unlink($oldFilename_s);
            $oldFilename_big = str_replace(".", "_big.", $oldFilename);
            @unlink($oldFilename_big);
            @chdir($oldWD);
        }
        $PHPShopOrm->clean();
        $result = $PHPShopOrm->delete($where);

        // �������� �������� ����������� ������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $data_main = $PHPShopOrm->getOne(array('pic_big'), array('id' => '=' . intval($row['parent'])));

        if (is_array($data_main) and $name == $data_main['pic_big']) {
            $result = $PHPShopOrm->update(array('pic_small_new' => '', 'pic_big_new' => ''), array('id' => '=' . intval($row['parent'])));
        }


        return $result;
    }
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules, $PHPShopSystem;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // �������� ��������
    $data = $PHPShopOrm->select(array('parent'), array('id' => '=' . intval($_POST['rowID'])));
    $PHPShopOrm->clean();

    $action = $PHPShopOrm->delete(array('id' => '=' . intval($_POST['rowID'])));

    // �������� �����������
    if ($action)
        fotoDelete();

    // �������� �������� ��� �������� �������� ������
    if ($action and ! empty($data['parent'])) {

        $parent = @explode(",", $data['parent']);
        $PHPShopOrm->mysql_error = false;

        if ($PHPShopSystem->ifSerilizeParam('1c_option.update_option'))
            $PHPShopOrm->delete(array('uid' => ' IN ("' . @implode('","', $parent) . '")'));
        else
            $PHPShopOrm->delete(array('id' => ' IN ("' . @implode('","', $parent) . '")'));
    }

    // �������� �������, ��������� ��������� ������ �� �������
    if (!empty($_POST['parent_enabled'])) {

        $PHPShopProduct = new PHPShopProduct($_POST['parent']);
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $parent_array = @explode(",", $PHPShopProduct->getParam('parent'));
        if (is_array($parent_array)) {
            foreach ($parent_array as $v)
                if (!empty($v) and $v != $_POST['rowID'])
                    $parent_array_true[] = $v;
        }

        if (is_array($parent_array_true))
            $PHPShopOrm->update(array('parent_new' => @implode(",", $parent_array_true)), array('id' => '=' . intval($_POST['parent'])));
        else
            $PHPShopOrm->update(array('parent_new' => ''), array('id' => '=' . intval($_POST['parent'])));
    }

    return array("success" => $action);
}

/**
 * ������������� �����
 */
function actionOptionEdit() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm;

    PHPShopObj::loadClass('sort');

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    if (empty($data['name'])) {
        $data['name'] = $_REQUEST['parent_name'] . ' ' . $data['parent'] . ' ' . $data['parent2'];
    }

    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->tab_key = 1000;

    // ��������� �����
    if (!empty($data['parent2']) and empty($data['color']))
        $data['color'] = PHPShopString::getColor($data['parent2']);

    $PHPShopCategoryArray = new PHPShopCategoryArray(array('id' => '=' . $data['category']));
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $PHPShopParentNameArray = new PHPShopParentNameArray(array('id' => '=' . $CategoryArray[$data['category']]['parent_title']));
    $parent_title = $PHPShopParentNameArray->getParam($CategoryArray[$data['category']]['parent_title'] . ".name");
    $parent_color = $PHPShopParentNameArray->getParam($CategoryArray[$data['category']]['parent_title'] . ".color");
    if (empty($parent_title))
        $parent_title = '������';

    if (empty($parent_color))
        $parent_color = '����';

    $Tab1 = $PHPShopGUI->setField(array($parent_title, '&#8470;'), array($PHPShopGUI->setInputArg(array('name' => 'parent_new', 'type' => 'text', 'value' => $data['parent'])), $PHPShopGUI->setInputArg(array('name' => 'num_new', 'type' => 'text', 'value' => $data['num'], 'size' => 110))), array(array(2, 6), array(1, 2)), null, null, 'control-label', false, false);
    $Tab1 .= $PHPShopGUI->setField(array($parent_color, '���'), array($PHPShopGUI->setInputArg(array('name' => 'parent2_new', 'type' => 'text', 'value' => $data['parent2'])), $PHPShopGUI->setInputColor('color_new', $data['color'], 110)), array(array(2, 6), array(1, 2)));
    $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setInputArg(array('name' => 'name_new', 'type' => 'text.required', 'value' => $data['name'])) . $PHPShopGUI->setHelp(__('������') . ' <a href="?path=product&return=catalog.' . $data['category'] . '&id=' . $_REQUEST['id'] . '&view=option" target="_blank">' . __('�������� ������') . '</a>, ' . __('����������� � �������'), false, false));
    $Tab1 .= $PHPShopGUI->setField('�������', $PHPShopGUI->setInputArg(array('name' => 'uid_new', 'type' => 'text', 'value' => $data['uid'], 'size' => '100%')));

    // �����
    if (empty($data['ed_izm']))
        $ed_izm = __('��.');
    else
        $ed_izm = $data['ed_izm'];

    // �������������� �����
    $PHPShopOrmWarehouse = new PHPShopOrm($GLOBALS['SysValue']['base']['warehouses']);
    $dataWarehouse = $PHPShopOrmWarehouse->select(array('*'), array('enabled' => "='1'"), array('order' => 'num DESC'), array('limit' => 100));
    if (is_array($dataWarehouse)) {

        $warehouse_main = '����� �����';
        foreach ($dataWarehouse as $row) {
            $warehouse_name[] = $row['name'];
            $warehouse[] = $PHPShopGUI->setInputText(false, 'items' . $row['id'] . '_new', $data['items' . $row['id']], 80, $ed_izm);
            $warehouse_size[] = array(2, 1);
        }

        $Tab1 .= $PHPShopGUI->setField($warehouse_name, $warehouse, $warehouse_size);
    } else
        $warehouse_main = '�����';

    // ����� � ���
    $Tab1 .= $PHPShopGUI->setField(array($warehouse_main, '���'), array($PHPShopGUI->setInputText(false, 'items_new', $data['items'], 150, $ed_izm), $PHPShopGUI->setInputText(false, 'weight_new', $data['weight'], 150, __('�&nbsp;&nbsp;&nbsp;&nbsp;'))), array(array(2, 4), array(2, 4)));

    // ������
    $PHPShopValutaArray = new PHPShopValutaArray();
    $valuta_array = $PHPShopValutaArray->getArray();
    $valuta_area = null;
    if (is_array($valuta_array))
        foreach ($valuta_array as $val) {
            if ($data['baseinputvaluta'] == $val['id']) {
                $check = 'checked';
                $valuta_def_name = $val['code'];
            } else
                $check = false;
            $valuta_area .= $PHPShopGUI->setRadio('baseinputvaluta_new', $val['id'], $val['name'], $data['baseinputvaluta'], false);
        }

    // ����
    $Tab1 .= $PHPShopGUI->setField(array('���� 1', '���� 2'), array($PHPShopGUI->setInputText(null, 'price_new', $data['price'], 150, $valuta_def_name), $PHPShopGUI->setInputText(null, 'price2_new', $data['price2'], 150, $valuta_def_name)), array(array(2, 4), array(2, 4)));

    // �����
    $Tab1 .= $PHPShopGUI->setField(array('�����', '������'), array($PHPShopGUI->setCheckbox('enabled_new', 1, '����� � ������', $data['enabled']), $valuta_area), array(array(2, 4), array(2, 4)));


    $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'rowID', 'type' => 'hidden', 'value' => $_REQUEST['id']));
    $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'parentID', 'type' => 'hidden', 'value' => $_REQUEST['parentID']));

    $Tab1 .= $PHPShopGUI->setField("�����������", $PHPShopGUI->setIcon($data['pic_big'], "pic_big_new", false, array('load' => false, 'server' => false, 'url' => false, 'view' => true)) . $PHPShopGUI->setHelp('�� ������ ������� ���� � �������� <a href="#" class="set-image-tab">�����������</a>.'));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ��������
    $Tab_info_size = $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'length_new', $data['length'], 150, __('��&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'width_new', $data['width'], 150, __('��&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('������', $PHPShopGUI->setInputText(false, 'height_new', $data['height'], 150, __('��&nbsp;')), 'left');
    $Tab_info_size .= $PHPShopGUI->setField('������� ���������', $PHPShopGUI->setInputText(false, 'ed_izm_new', $ed_izm, 150));
    $Tab2 = $PHPShopGUI->setCollapse('��������', $Tab_info_size);

    $Tab_price .= $PHPShopGUI->setField('���� 3', $PHPShopGUI->setInputText(null, 'price3_new', $data['price3'], 150, $valuta_def_name), 2);
    $Tab_price .= $PHPShopGUI->setField('���� 4', $PHPShopGUI->setInputText(null, 'price4_new', $data['price4'], 150, $valuta_def_name), 2);
    $Tab_price .= $PHPShopGUI->setField('���� 5', $PHPShopGUI->setInputText(null, 'price5_new', $data['price5'], 150, $valuta_def_name), 2);
    $Tab_price .= $PHPShopGUI->setField('������ ����', $PHPShopGUI->setInputText(null, 'price_n_new', $data['price_n'], 150, $valuta_def_name));
    $Tab_price .= $PHPShopGUI->setField('���������� ����', $PHPShopGUI->setInputText(null, 'price_purch_new', $data['price_purch'], 150, $valuta_def_name));
    $Tab2 .= $PHPShopGUI->setCollapse('����', $Tab_price);

    // ������� ���
    $Tab2 .= $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField('������� ���', $PHPShopGUI->setInputText(null, 'external_code_new', $data['external_code'], '100%')));

    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("�������������", $Tab2));

    writeLangFile();
    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

/**
 * ������������� ����
 */
function actionFileEdit() {
    global $PHPShopGUI, $PHPShopModules;


    $PHPShopGUI->field_col = 2;
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('��������', $PHPShopGUI->setInputArg(array('name' => 'modal_file_name', 'type' => 'text.required', 'value' => urldecode($_GET['name']))));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setField('����', $PHPShopGUI->setFile($_GET['file'], 'lfile', array('server' => true)));
    $PHPShopGUI->_CODE .= $PHPShopGUI->setInput('hidden', 'selectID', $_POST['selectID']);
    $PHPShopGUI->_CODE .= $PHPShopGUI->setInput('hidden', 'fileID', $_POST['fileID']);

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
}

// ������� �������� �����������
function actionImgDelete() {
    global $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = fotoDelete(array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// ������� �������������� �����������
function actionImgEdit() {
    global $PHPShopModules;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['foto']);

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->update($_POST, array('id' => '=' . intval($_POST['rowID'])));

    return array("success" => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>