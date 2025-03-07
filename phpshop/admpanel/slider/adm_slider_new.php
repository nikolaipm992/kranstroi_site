<?php

$TitlePage = __('�������� ��������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['slider']);

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopModules, $TitlePage;

    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� � �������'));
    $PHPShopGUI->field_col = 3;

    $data['enabled'] = 1;
    $data = $PHPShopGUI->valid($data, 'image', 'link', 'mobile', 'num', 'alt', 'servers', 'link_text', 'name');

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setField("��������", $PHPShopGUI->setInput("text", "name_new", $data['name'], null, 500));

    // ����
    $Tab1 .= $PHPShopGUI->setField("�������� �����", $PHPShopGUI->setInputText(null, "color_new", (int) $data['color'], 100, '%'));

    $Tab1 .= $PHPShopGUI->setField("�����������", $PHPShopGUI->setIcon($data['image'], "image_new", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false))) .
            $PHPShopGUI->setField("����", $PHPShopGUI->setInput("text", "link_new", $data['link'], "none", 500) . $PHPShopGUI->setHelp("������: /pages/info.html ��� http://google.com")) .
            $PHPShopGUI->setField("����� ������", $PHPShopGUI->setInput("text", "link_text_new", $data['link_text'], null, 500)) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "��������", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "���������", $data['enabled'])) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setCheckbox("mobile_new", 1, "���������� ������ �� ��������� �����������", $data['mobile'])) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setInputText(false, 'num_new', $data['num'], 100)) .
            $PHPShopGUI->setField("��������", $PHPShopGUI->setTextarea("alt_new", $data['alt'], true, 500)) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->loadLib('tab_multibase', $data, 'catalog/'));

    $Tab1 = $PHPShopGUI->setCollapse('����������', $Tab1);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.slider.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    $_POST['image_new'] = iconAdd();

    // ����������
    $_POST['servers_new'] = "";
    if (is_array($_POST['servers']))
        foreach ($_POST['servers'] as $v)
            if ($v != 'null' and ! strstr($v, ','))
                $_POST['servers_new'] .= "i" . $v . "i";

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

// ���������� ����������� 
function iconAdd() {
    global $PHPShopSystem;

    // ����� ����������
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', $_FILES['file']['name'])) . '.' . $_FILES['file']['ext'];
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['image_new'];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST['image_new'])) {
        $file = $_POST['image_new'];
    }

    if (empty($file))
        $file = '';

    // �������
    if ($PHPShopSystem->ifSerilizeParam('admoption.image_slider') and ! empty($file)) {
        require_once $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['SysValue']['dir']['dir'] . '/phpshop/lib/thumb/phpthumb.php';

        // ��������� ����������
        $img_tw = $PHPShopSystem->getSerilizeParam('admoption.img_tw_s');
        $img_th = $PHPShopSystem->getSerilizeParam('admoption.img_th_s');
        $img_tw = empty($img_tw) ? 1440 : $img_tw;
        $img_th = empty($img_th) ? 300 : $img_th;
        $img_adaptive = $PHPShopSystem->getSerilizeParam('admoption.image_slider_adaptive');

        // ��������� ����������� (��������)
        $thumb = new PHPThumb($_SERVER['DOCUMENT_ROOT'] . $file);
        $thumb->setOptions(array('jpegQuality' => $PHPShopSystem->getSerilizeParam('admoption.width_kratko')));

        // ������������
        if (!empty($img_adaptive))
            $thumb->adaptiveResize($img_tw, $img_th);
        else
            $thumb->resize($img_tw, $img_th);

        // ���������� � webp
        if ($PHPShopSystem->ifSerilizeParam('admoption.image_webp_save')) {
            $thumb->setFormat('WEBP');
            $file = str_replace(['.jpg', '.JPG', '.png', '.PNG', '.gif', '.GIF'], '.webp', $file);
        }

        $thumb->save($_SERVER['DOCUMENT_ROOT'] . $file);
    }

    return $file;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>