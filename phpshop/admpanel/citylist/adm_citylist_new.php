<?php

$TitlePage = __('�������� ������');

/**
 * ����� �������� ���� ��������������
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules;

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $regions = $orm->getList(['region_id', 'name']);
    $regionsSelector = array_map(function ($region) {
        return [
            $region['name'],
            $region['region_id'],
            0
        ];
    }, $regions);

    // ��������� ������
    $PHPShopGUI->setActionPanel(__("�������� ������"), false, ['������� � �������������', '��������� � �������']);

    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './citylist/gui/citylist.gui.js');

    // ������������
    $Tab = $PHPShopGUI->setField("������������ ������", $PHPShopGUI->setInputText(false, 'name_new', null, '100%'));
    $Tab.= $PHPShopGUI->setField("������", $PHPShopGUI->setSelect('region_id_new', $regionsSelector, '100%', false, false, true));
    $collapse = $PHPShopGUI->setCollapse('����������', $Tab);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    $PHPShopGUI->setTab(["��������", $collapse]);

    // ����� ������� ������ ���������
    $tree_array = [];
    foreach ($regions as $region) {
        $tree_array[$region['region_id']] = [
            'name' => $region['name'],
            'id'   => $region['region_id'],
        ];
    }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree = '<table class="tree table table-hover">';

    $PHPShopGUI->dropdown_action_form = false;
    foreach ($regions as $region) {
        $link = [
            [
                'url'  => sprintf('?path=citylist.region&id=%s', $region['region_id']),
                'name' => __('�������������')
            ]
        ];

        $tree.='<tr class="treegrid-' . $region['region_id'] . ' data-tree">
                    <td>
                        <a href="?path=citylist&cat=' . $region['region_id'] . '">' . $region['name'] . '</a>
                        <span class="pull-right">' . $PHPShopGUI->setDropdownAction($link) . '</span>
                    </td>
	            </tr>';
    }
    $tree.='
        </table>';
    $PHPShopGUI->dropdown_action_form = true;

    $sidebarleft[] = [
        'title'      => '�������',
        'content'    => $tree,
        'title-icon' => '<a href="/phpshop/admpanel/admin.php?path=citylist.region&action=new" style="color: #333;"><span class="glyphicon glyphicon-plus newregion" data-toggle="tooltip" data-placement="top" 
                        title="'.__('�������� ������').'"></span></a>'
    ];


    $PHPShopGUI->setSidebarLeft($sidebarleft, 3);
    $PHPShopGUI->sidebarLeftCell = 3;

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.citylist.create");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� ������
function actionInsert() {
    global $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $region = $orm->getOne(['country_id'], ['region_id' => sprintf('="%s"', (int) $_POST['region_id_new'])]);
    $_POST['country_id_new'] = $region['country_id'];

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_city']);
    $action = $orm->insert($_POST);

    if ($_POST['saveID'] == '������� � �������������')
        header('Location: ?path=citylist&id=' . $action);
    else
        header('Location: ?path=citylist');

    return $action;
}


// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>