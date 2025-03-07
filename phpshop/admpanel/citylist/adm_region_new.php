<?php

$TitlePage = __('�������� �������');

/**
 * ����� �������� ���� ��������������
 */
function actionStart() {
    global $PHPShopGUI, $PHPShopModules;

    // ������ �������� ����
    $PHPShopGUI->field_col = 3;

    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_country']);
    $countries = array_map(function ($country) {
        return [
            $country['name'],
            $country['country_id'],
            0
        ];
    }, $orm->getList(['country_id', 'name']));

    // ��������� ������
    $PHPShopGUI->setActionPanel(__("�������� �������"), false, ['������� � �������������', '��������� � �������']);

    $PHPShopGUI->addJSFiles('./js/jquery.treegrid.js', './citylist/gui/citylist.gui.js');

    // ������������
    $Tab = $PHPShopGUI->setField("������������ �������", $PHPShopGUI->setInputText(false, 'name_new', null, '100%'));
    
    
    if(count($countries)>0)
    $Tab.= $PHPShopGUI->setField("������", $PHPShopGUI->setSelect('country_id_new', $countries, '100%', false, false, false));
    else $Tab .= $PHPShopGUI->setField("������", $PHPShopGUI->setInputText(false, 'country_name_new', null, '100%'));

    
    
    $collapse = $PHPShopGUI->setCollapse('����������', $Tab);

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, null);

    $PHPShopGUI->setTab(["��������", $collapse]);

    // ����� ������� ������ ���������
    $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_region']);
    $regions = $orm->getList(['region_id', 'name']);

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
    $action = $orm->insert($_POST);
    
    // ����� ������
    if(!empty($_POST['country_name_new'])){
       $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['citylist_country']);
       $_POST['name_new'] = $_POST['country_name_new'];
       $action = $orm->insert($_POST); 
    }
        
        

    if ($_POST['saveID'] == '������� � �������������')
        header('Location: ?path=' . $_GET['path'] . '&id=' . $action);
    else
        header('Location: ?path=citylist');

    return $action;
}


// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>