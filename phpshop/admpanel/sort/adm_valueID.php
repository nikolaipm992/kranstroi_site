<?php

PHPShopObj::loadClass('sort');

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));

    return array('success' => $action);
}

// ���������� ����������� 
function iconAdd() {
    global $PHPShopSystem;

    // ����� ����������
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        if (in_array($_FILES['file']['ext'], array('gif', 'png', 'jpg', 'jpeg', 'svg'))) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
    }

    // ������ ���� �� URL
    elseif (!empty($_POST['furl'])) {
        $file = $_POST['icon_value'];
    }

    // ������ ���� �� ��������� ���������
    elseif (!empty($_POST['icon_value'])) {
        $file = $_POST['icon_value'];
    }

    if (empty($file))
        $file = '';

    return $file;
}

/**
 * ����� �������������� �� ���������� ���� 
 */
function actionValueEdit() {
    global $PHPShopGUI, $PHPShopModules, $PHPShopOrm, $PHPShopSystem;

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_REQUEST['id'])));

    $PHPShopGUI->field_col = 2;
    $Tab1 = $PHPShopGUI->setField('��������', $PHPShopGUI->setInputArg(array('name' => 'name_value', 'type' => 'text.required', 'value' => $data['name'])));
    $Tab1 .= $PHPShopGUI->setField(
            array('������', '���������'), array(
        $PHPShopGUI->setIcon($data['icon'], "icon_value", true, array('load' => true, 'server' => true, 'url' => true)),
        $PHPShopGUI->setInputArg(array('name' => 'num_value', 'type' => 'text', 'value' => $data['num']))
            ), array(
        array(2, 6),
        array(2, 2)
    ));

    // �������� � ���������
    $page_value[] = array('- ' . __('��� ��������') . ' - ', null, $data['page']);
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['page']);
    $data_page = $PHPShopOrm->select(array('*'), false, false, array('limit' => 1000));
    if (is_array($data_page))
        foreach ($data_page as $v)
            $page_value[] = array($v['name'], $v['link'], $data['page']);

    $Tab1 .= $PHPShopGUI->setField("�������� ��������", $PHPShopGUI->setSelect('page_value', $page_value, '100%', false, false, false, false, false, false, false, 'form-control'));

    // ���������
    $PHPShopSort = new PHPShopSortCategoryArray(array('category' => '!=0'));
    $PHPShopSortArray = $PHPShopSort->getArray();

    if (is_array($PHPShopSortArray))
        foreach ($PHPShopSortArray as $v)
            $sort_value[] = array($v['name'], $v['id'], $data['category']);

    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setSelect('category_value', $sort_value, '100%', false, false, false, false, false, false, false, 'form-control'));

    if ($_GET['brand'] == 'true' or $_GET['virtual'] == 'true') {

        // �������� 
        $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
        $oFCKeditor = new Editor('description_value');
        $oFCKeditor->Height = '100';
        $oFCKeditor->Value = $data['description'];

        $Tab1 .= $PHPShopGUI->setField("��������", $oFCKeditor->AddGUI());

        $Tab2 = $PHPShopGUI->setField("Meta ���������:", '
        <textarea class="form-control" style="height:100px;" name="title_value">' . $data['title'] . '</textarea>
            <div class="btn-group" role="group" aria-label="...">
                <input type="button" value="' . __('�����') . '" data-seo="@System@" data-target="title_value" class="seo-button btn btn-default btn-sm">
                <input type="button" value="' . __('�������� ��������������') . '" data-seo="@valueTitle@" data-target="title_value" class="seo-button btn btn-default btn-sm">
            </div>');

        $Tab2 .= $PHPShopGUI->setField("Meta ��������:", '
        <textarea class="form-control" style="height:100px" name="meta_description_value">' . $data['meta_description'] . '</textarea>
            <div class="btn-group" role="group" aria-label="...">

                <input type="button" value="' . __('�����') . '" data-seo="@System@" data-target="meta_description_value" class="seo-button btn btn-default btn-sm">
                <input type="button" value="' . __('�������� ��������������') . '" data-seo="@valueTitle@" data-target="meta_description_value" class="seo-button btn btn-default btn-sm">
            </div>');
    }

    $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'rowID', 'type' => 'hidden', 'value' => $_REQUEST['id']));
    $Tab1 .= $PHPShopGUI->setInputArg(array('name' => 'parentID', 'type' => 'hidden', 'value' => $_REQUEST['parentID']));

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    $PHPShopGUI->tab_key = 100;

    if ($_GET['brand'] == 'true' or $_GET['virtual'] == 'true')
        $PHPShopGUI->setTab(["��������", $Tab1, true], ["�������������", $Tab2, true]);
    else
        $PHPShopGUI->setTab(["��������", $Tab1, true]);

    $PHPShopGUI->_CODE .= '<script>$(document).ready(function () {
        $(".seo-button").on("click", function() {
            var seo = $(this).attr("data-seo");
            var area = $("[name=" + $(this).attr("data-target") + "]").val();
            $("[name=" + $(this).attr("data-target") + "]").val(area + seo);
        });
    });</script>';

    exit($PHPShopGUI->_CODE . '<p class="clearfix"> </p>');
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

    if (!empty($_POST['name_value'])) {
        $_POST['name_value'] = html_entity_decode($_POST['name_value']);
    }

    if (isset($_POST['category_value']))
        $_POST['icon_value'] = iconAdd();

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']), '_value');
    return array('success' => $action);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>