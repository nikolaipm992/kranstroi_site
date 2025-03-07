<?php

$TitlePage = __('�������� �������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers_status']);
PHPShopObj::loadClass('user');

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopModules,$hideCatalog,$hideSite;

    // ��������� ������
    $data['enabled'] = $data['warehouse'] = 1;
    $data = $PHPShopGUI->valid($data, 'name', 'discount', 'price', 'cumulative_discount', 'cumulative_discount_check','warehouse');

    // ������ �������� ����
    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('./shopusers/gui/shopusers.gui.js');
    $PHPShopGUI->setActionPanel(__("����������") . ' / ' . $TitlePage, false, array('��������� � �������', '������� � �������������'));
    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setCollapse('����������', $PHPShopGUI->setField("��������", $PHPShopGUI->setInput('text.required', "name_new", $data['name'])) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setInputText('%', "discount_new", $data['discount'], 100)) .
            $PHPShopGUI->setField("������� ���", $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setRadio("enabled_new", 1, "���.", $data['enabled']) . $PHPShopGUI->setRadio("enabled_new", 0, "����.", $data['enabled'])) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setRadio("warehouse_new", 1, "���.", $data['warehouse']) . $PHPShopGUI->setRadio("warehouse_new", 0, "����.", $data['warehouse']))
    );

    if(empty($hideCatalog))
    $Tab1 .= $PHPShopGUI->setCollapse('������������� ������', '<p class="text-muted hidden-xs">' . __('��� ����� ���������� ������ �� ������� ��������� ������ ��� �������� � ������� ������������ � ���������� ��������� � ������') . ' <a href="?path=shopusers.discount"><span class="glyphicon glyphicon-share-alt"></span> ' . __('������ �� ������') . '</a>.<br>' . __('��� ����� ������������� ������ ��������� �������� ����� ����� ������ ���������� � ������ ������� ������, �������� "��������"') . '.</p>' .
            $PHPShopGUI->setCheckbox('cumulative_discount_check_new', 1, '������������� ������������� ������', $data['cumulative_discount_check']) .
            $PHPShopGUI->loadLib('tab_discount', $data['cumulative_discount'], 'shopusers/')
    );

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1,true,false,true));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.shopusers.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;

    // ������������� ������
    foreach ($_POST['cumulative_sum_ot'] as $key => $value) {
        if ($_POST['cumulative_discount'][$key] != '') {
            $cumulative_array[$key]['cumulative_sum_ot'] = $value;
            $cumulative_array[$key]['cumulative_sum_do'] = $_POST['cumulative_sum_do'][$key];
            $cumulative_array[$key]['cumulative_discount'] = $_POST['cumulative_discount'][$key];
            $cumulative_array[$key]['cumulative_enabled'] = intval($_POST['cumulative_enabled'][$key]);
        }
    }

    // ������������
    $_POST['cumulative_discount_new'] = serialize($cumulative_array);

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('cumulative_discount_check_new');

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);

    if ($_POST['saveID'] == '������� � �������������')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=' . $_GET['path']);

    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>