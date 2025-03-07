<?php

PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");

$TitlePage = __('�������������� �������') . ' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['banner']);

// ����� ������� �������
function GetSkinList($skin) {
    global $PHPShopGUI;
    $dir = "../templates/";

    $value[] = array('�� �������', '', '');

    if (is_dir($dir)) {
        if (@$dh = opendir($dir)) {
            while (($file = readdir($dh)) !== false) {
                if (file_exists($dir . '/' . $file . "/main/index.tpl")) {

                    if ($skin == $file)
                        $sel = "selected";
                    else
                        $sel = "";

                    if ($file != "." and $file != ".." and ! strpos($file, '.'))
                        $value[] = array($file, $file, $sel);
                }
            }
            closedir($dh);
        }
    }

    return $PHPShopGUI->setSelect('skin_new', $value, 300);
}

// ���������� ������ ���������
function treegenerator($array, $i, $curent, $dop_cat_array) {
    global $tree_array;
    $del = '�&nbsp;&nbsp;&nbsp;&nbsp;';
    $tree_select = $tree_select_dop = $check = false;

    $del = str_repeat($del, $i);
    if (!empty($array) and is_array($array['sub'])) {
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
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' >' . $del . $v . '</option>';
                $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '  >' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
            $tree_select_dop .= $check['select_dop'];
        }
    }
    return array('select' => $tree_select, 'select_dop' => $tree_select_dop);
}

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopOrm, $PHPShopModules,$shop_type;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . $_GET['id']));
    $PHPShopGUI->field_col = 3;
    $PHPShopGUI->addJSFiles('./js/bootstrap-colorpicker.min.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-colorpicker.min.css');

    $PHPShopGUI->setActionPanel(__("�������������� �������") . ": " . $data['name'], array('�������'), array('���������', '��������� � �������'));

    if (empty($data['color']))
        $data['color'] = '#000000';

    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "name_new", $data['name'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setRadio("flag_new", 1, "��������", $data['flag']) .
                    $PHPShopGUI->setRadio("flag_new", 0, "���������", $data['flag'])) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setCheckbox("mobile_new", 1, "���������� ������ �� ��������� �����������", $data['mobile']) . $PHPShopGUI->setHelp('�� ���������, ������ ��������� ������ �� PC')) .
            $PHPShopGUI->setField("��� ������", $PHPShopGUI->setRadio("type_new", 0, "� �������", $data['type']) .
                    $PHPShopGUI->setRadio("type_new", 2, "��������������", $data['type']) .
                    $PHPShopGUI->setRadio("type_new", 1, "����������� ����", $data['type']) . '<br>' .
                    $PHPShopGUI->setRadio("type_new", 3, "� ���� ��������", $data['type'])
    );

    $Tab2 = $PHPShopGUI->setField("���������:", $PHPShopGUI->setInput("text", "dir_new", $data['dir']) . $PHPShopGUI->setHelp('/ - �������, /page/page.html - ��������. ������ �����: /, /page/dostavka.html'));

    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $CategoryArray = $PHPShopCategoryArray->getArray();

    $tree_array = array();

    foreach ($PHPShopCategoryArray->getKey('parent_to.id', true) as $k => $v) {
        foreach ($v as $cat) {
            $tree_array[$k]['sub'][$cat] = $CategoryArray[$cat]['name'];
        }
        $tree_array[$k]['name'] = $CategoryArray[$k]['name'];
        $tree_array[$k]['id'] = $k;
    }


    $GLOBALS['tree_array'] = &$tree_array;

    // �����������
    $dop_cat_array = preg_split('/#/', $data['dop_cat'], -1, PREG_SPLIT_NO_EMPTY);
    $tree_select_dop = null;

    if (!empty($tree_array[0]['sub']) and is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], 1, @$data['category'], $dop_cat_array);

            if ($k == @$data['category'])
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


            $tree_select_dop .= '<option value="' . $k . '" ' . $selected_dop . '>' . $v . '</option>';

            $tree_select_dop .= $check['select_dop'];
        }

    $tree_select_dop = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body"  data-style="btn btn-default btn-sm" name="dop_cat[]" data-width="100%" multiple><option value="0">' . $CategoryArray[0]['name'] . '</option>' . $tree_select_dop . '</select>';

    // �������������� ��������
    if(empty($shop_type))
    $Tab2 .= $PHPShopGUI->setField('��������', $tree_select_dop . $PHPShopGUI->setHelp('������ ��������� ������ � �������� ���������.'));


    // �������� 
    $Tab1 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setTextarea('description_new', $data['description']));

    // ����
    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setInput("text", "link_new", $data['link']) . $PHPShopGUI->setHelp("������: /pages/info.html ��� https://google.com"));
    
    // ����
    $Tab1 .= $PHPShopGUI->setField("�������� �����", $PHPShopGUI->setInputText(null, "color_new", (int)$data['color'], 100, '%'));

    // ������
    $Tab1 .= $PHPShopGUI->setField("����������� ��� ����", $PHPShopGUI->setIcon($data['image'], "image_new", false));
    
    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);

    // �������� 
    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '400';
    $oFCKeditor->Value = $data['content'];


    $Tab_tip1 = $PHPShopGUI->setField("������� ������", $PHPShopGUI->setRadio("display_new", 0, "���������� ������", $data['display']) .
            $PHPShopGUI->setRadio("display_new", 1, "������ ����� �� ����", $data['display'])
    );

    $size_value[] = array('���������', 0, $data['size']);
    $size_value[] = array('�������', 1, $data['size']);
    $size_value[] = array('�������', 2, $data['size']);

    $Tab_tip1 .= $PHPShopGUI->setField("������ ����", $PHPShopGUI->setSelect('size_new', $size_value, 150, true));

    // �������
    $Tab2 .= $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab2_help = $PHPShopGUI->setHelp('�������� ���� �������, ���� ����� ����� �� ���� ���������/���������, ��� ������� ������ ��������/�������:');
    $Tab1 .= $PHPShopGUI->setCollapse("�������� �����", $Tab2_help . $Tab2);

    // ���������� 
    $Tab1 .= $PHPShopGUI->setCollapse("����������", '<div>' . $oFCKeditor->AddGUI() . '</div>');

    $Tab1 .= $PHPShopGUI->setCollapse('����������� ����', $Tab_tip1);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true, false, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("button", "delID", "�������", "right", 70, "", "btn-danger", "actionDelete.banner.edit") .
            $PHPShopGUI->setInput("submit", "editID", "��", "right", 70, "", "btn-success", "actionUpdate.banner.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.banner.edit");

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

// ���������� ����������� 
function iconAdd($name = 'icon_new') {
    global $PHPShopSystem;

    // ����� ����������
    $path = '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'svg'))) {
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

    if (empty($_POST['ajax'])) {

        // ����������
        if (is_array($_POST['servers'])) {
            $_POST['servers_new'] = "";
            foreach ($_POST['servers'] as $v)
                if ($v != 'null' and ! strstr($v, ','))
                    $_POST['servers_new'] .= "i" . $v . "i";
        }

        if (empty($_POST['mobile_new']))
            $_POST['mobile_new'] = 0;

        // ��� ��������
        $_POST['dop_cat_new'] = "";
        if (is_array($_POST['dop_cat']) and $_POST['dop_cat'][0] != 'null') {
            $_POST['dop_cat_new'] = "#";
            foreach ($_POST['dop_cat'] as $v)
                if ($v != 'null' and ! strstr($v, ','))
                    $_POST['dop_cat_new'] .= $v . "#";
        }

        // �����������
        $_POST['image_new'] = iconAdd('image_new');
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
