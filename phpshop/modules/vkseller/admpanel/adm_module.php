<?php

include_once dirname(__FILE__) . '/../class/VkSeller.php';

PHPShopObj::loadClass("order");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("category");
PHPShopObj::loadClass("delivery");

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_system"));
$VkSeller = new VkSeller();

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

// ������� ����������
function actionUpdate() {
    global $PHPShopModules, $PHPShopOrm;

    // ������������� ������ ��������
    $PHPShopOrm->updateZeroVars('link_new');

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.vkseller.vkseller_system"));
    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);


    // ���������
    if (is_array($_POST['categories']) and $_POST['categories'][0] != 'null') {

        $cat_array = array();
        foreach ($_POST['categories'] as $v)
            if (!empty($v) and ! strstr($v, ','))
                $cat_array[] = $v;

        if (is_array($cat_array)) {
            $where = array('category' => ' IN ("' . implode('","', $cat_array) . '")');
            $PHPShopOrmProducts = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $PHPShopOrmProducts->debug = false;

            if ($_POST['enabled_all'] == 1)
                $PHPShopOrmProducts->update(array('export_vk_new' => (int) $_POST['enabled_all']), $where);
            else
                $PHPShopOrmProducts->update(array('export_vk_new' => (int) $_POST['enabled_all'], 'export_vk_task_status_new' => 0, 'export_vk_id_new' => 0), $where);
        }
    }

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

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
                $tree_select .= '<option value="' . $k . '" ' . $selected . ' disabled>' . $del . $v . '</option>';
            }

            $tree_select .= $check['select'];
        }
    }
    return array('select' => $tree_select);
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $TitlePage, $select_name;

    $PHPShopGUI->field_col = 4;
    $PHPShopGUI->addJSFiles('../modules/vkseller/admpanel/gui/vkseller.gui.js');

    // �������
    $data = $PHPShopOrm->select();

    if ($data['model'] === 'API') {
        $PHPShopGUI->action_button['�������������� ������'] = [
            'name' => __('�������������� ������'),
            'class' => 'btn btn-default btn-sm navbar-btn vk-export',
            'type' => 'button',
            'icon' => 'glyphicon glyphicon-export'
        ];
        $PHPShopGUI->setActionPanel($TitlePage, $select_name, ['�������������� ������', '��������� � �������']);
    }

    // ������
    $status[] = [__('����� �����'), 0, $data['status']];
    $statusArray = (new PHPShopOrm('phpshop_order_status'))->getList(['id', 'name']);
    foreach ($statusArray as $statusParam) {
        $status[] = [$statusParam['name'], $statusParam['id'], $data['status']];
    }

    // �������� ������� �������
    $PHPShopOrderStatusArray = new PHPShopOrderStatusArray();
    $OrderStatusArray = $PHPShopOrderStatusArray->getArray();
    $order_status_value[] = array(__('����� �����'), 0, $data['status']);
    if (is_array($OrderStatusArray))
        foreach ($OrderStatusArray as $order_status)
            $order_status_value[] = array($order_status['name'], $order_status['id'], $data['status']);

    $PHPShopCategoryArray = new PHPShopCategoryArray($where);
    $CategoryArray = $PHPShopCategoryArray->getArray();
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


    $tree_select = '<select class="selectpicker show-menu-arrow hidden-edit" data-live-search="true" data-container="body" data-style="btn btn-default btn-sm" name="categories[]"  data-width="100%" multiple>' . $tree_select . '</select>';

    $models = [
        ['O��������� ������ ����� YML', 'YML', $data['model']],
        ['���������� ������ ����� API � YML', 'API', $data['model']]
    ];

    if (empty($_SESSION['mod_pro']))
        $models = [
            ['���������� ������ ����� API � YML (�������� � ������ Pro)', 'YML', $data['model']],
            ['O��������� ������ ����� YML', 'YML', $data['model']]
        ];

    $Tab1 = $PHPShopGUI->setField('������ ������', $PHPShopGUI->setSelect('model_new', $models, '100%', true));
    
    // ������� �������������� ��������
    $status_import_array=['����� �����','���������������','����������','������������','��������','�������','�������'];
    foreach ($status_import_array as $k => $status_val) {
        $order_status_import_value[] = array(__($status_val), $k, $data['status_import']);
    }
    
        // ��������
    $PHPShopDeliveryArray = new PHPShopDeliveryArray();

    $DeliveryArray = $PHPShopDeliveryArray->getArray();
    if (is_array($DeliveryArray))
        foreach ($DeliveryArray as $delivery) {

            // ������� ������������
            if (strpos($delivery['city'], '.')) {
                $name = explode(".", $delivery['city']);
                $delivery['city'] = $name[0];
            }

            $delivery_value[] = array($delivery['city'], $delivery['id'], $data['delivery'], 'data-subtext="' . $delivery['price'] . '"');
        }
        

    if ($data['model'] === 'API') {
        $Tab1 .= $PHPShopGUI->setField('API key', $PHPShopGUI->setTextarea('token_new', $data['token'], false, '100%', '100') . $PHPShopGUI->setHelp('�������� <a href="../modules/vkseller/token.php?client_id=" id="client_token" target="_blank">������������ ����</a>'));
        $Tab1 .= $PHPShopGUI->setField("ID ����������", $PHPShopGUI->setInputText(null, "client_id_new", $data['client_id'], '100%'));
        $Tab1 .= $PHPShopGUI->setField("���������� ���� ����������", $PHPShopGUI->setInputText(null, "client_secret_new", $data['client_secret'], '100%'));
        $Tab1 .= $PHPShopGUI->setField("ID ����������", $PHPShopGUI->setInputText('public', "owner_id_new", $data['owner_id'], '100%'));
        $Tab1 .= $PHPShopGUI->setField('������ ������ ������', $PHPShopGUI->setSelect('status_new', $order_status_value, '100%'));
        $Tab1 .= $PHPShopGUI->setField('������ ������ � VK ��� �������������� ��������', $PHPShopGUI->setSelect('status_import_new', $order_status_import_value, '100%'));
       $Tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setSelect('delivery_new', $delivery_value, '100%'));
        $Tab1 .= $PHPShopGUI->setField('������ �� �����', $PHPShopGUI->setCheckbox('link_new', 1, '�������� ������ �� ����� � ��', $data['link']));
    }

    $catOption = $PHPShopGUI->setField("����������", $tree_select . $PHPShopGUI->setCheckbox("categories_all", 1, "������� ��� ���������?", 0), 1, '�������� ��������������. ��������� �� �����������.');
    $catOption .= $PHPShopGUI->setField("����� � VK", $PHPShopGUI->setRadio("enabled_all", 1, "���.", 1) . $PHPShopGUI->setRadio("enabled_all", 0, "����.", 1));

    $catOption .= $PHPShopGUI->setField('������ YML-�����', $PHPShopGUI->setInputText(false, 'password_new', $data['password'], '100%', $PHPShopGUI->setLink('http://' . $_SERVER['SERVER_NAME'] . '/yml/?marketplace=vk&pas=' . $data['password'], '<span class=\'glyphicon glyphicon-eye-open\'></span>', '_blank', false, __('�������'))));
    $catOption .= $PHPShopGUI->setField('���� ����������', $PHPShopGUI->setRadio("type_new", 1, "ID ������", $data['type']) . $PHPShopGUI->setRadio("type_new", 2, "������� ������", $data['type']));

    $Tab1 = $PHPShopGUI->setCollapse('���������', $Tab1);
    $Tab1 .= $PHPShopGUI->setCollapse('������', $catOption);

    if ($data['fee_type'] == 1) {
        $status_pre = '-';
    } else {
        $status_pre = '+';
    }

    $Tab3 = $PHPShopGUI->setCollapse('����', $PHPShopGUI->setField('������� ��� VK', $PHPShopGUI->setSelect('price_new', $PHPShopGUI->setSelectValue($data['price'], 5), 100)) .
            $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText($status_pre, 'fee_new', $data['fee'], 100, '%')) .
            $PHPShopGUI->setField('��������', $PHPShopGUI->setRadio("fee_type_new", 1, "���������", $data['fee_type']) . $PHPShopGUI->setRadio("fee_type_new", 2, "���������", $data['fee_type']))
    );

    // ����������
    $Tab2 = $PHPShopGUI->loadLib('tab_info', $data, '../modules/' . $_GET['id'] . '/admpanel/');

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1 . $Tab3, true, false, true), array("����������", $Tab2), array("� ������", $Tab4));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("hidden", "locale_vk_start_export", __('�� ������������� ������ ��������� ������� � ���������?')) .
            $PHPShopGUI->setInput("hidden", "locale_vk_stop_export", __('�� ������������� ������ �������� ������� ������?')) .
            $PHPShopGUI->setInput("hidden", "locale_vk_export", __('������� ������� � ���������')) .
            $PHPShopGUI->setInput("hidden", "locale_vk_export_done", __('������� � ��������� ��������, ��������� % �������')) .
            $PHPShopGUI->setInput("hidden", "stop", 0) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
