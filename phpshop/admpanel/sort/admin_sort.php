<?php

PHPShopObj::loadClass('sort');
$TitlePage = __("��������������");

$PHPShopSortCategoryArray = new PHPShopSortCategoryArray(array('category' => '=0'));
$SortCategoryArray = $PHPShopSortCategoryArray->getArray();

/**
 * ����� �������
 */
function actionStart() {
    global $PHPShopInterface, $TitlePage, $SortCategoryArray, $help;
    
    if(empty($_GET['cat']))
        $_GET['cat']=null;

    $PHPShopInterface->action_button['�������� ��������������'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="'.__('�������� ��������������').'" data-cat="' . $_GET['cat'] . '"'
    );

    $PHPShopInterface->action_select['�������� ������'] = array(
        'name' => '�������� ������',
        'action' => 'enabled',
        'url' => '?path=' . $_GET['path'] . '&action=new&type=sub'
    );

    $PHPShopInterface->action_select['�������� ���'] = array(
        'name' => '�������� ��� �������',
        'action' => 'ResetCache'
    );
    
    $PHPShopInterface->action_select['������� ��������������'] = array(
        'name' => '������� ��������������',
        'action' => 'CleanSort'
    );

    if (!empty($_GET['cat']))
        $PHPShopInterface->action_select['������������� ������'] = array(
            'name' => '������������� ������',
            'action' => 'enabled',
            'url' => '?path=' . $_GET['path'] . '&type=sub&id=' . intval($_GET['cat'])
        );

    if (!empty($_GET['cat']))
        $TitlePage.=': ' . $SortCategoryArray[$_GET['cat']]['name'];

    $PHPShopInterface->setActionPanel($TitlePage, array('������������� ������', '�������� ������', '|','�������� ���', '������� ��������������', '|', '������� ���������'), array('�������� ��������������'));
    $PHPShopInterface->setCaption(array(null, "1%"), array("��������", "40%"), array("", "8%"), array("�������" . "", "10%", array('align' => 'center')), array("�����" . "", "10%", array('align' => 'center')), array("�����" . "", "10%", array('align' => 'center')), array("������" . "", "10%", array('align' => 'center')));

    $where = array('category' => '!=0');
    if (!empty($_GET['cat'])) {
        $where = array('category' => '=' . intval($_GET['cat']));
    }

    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './sort/gui/sort.gui.js');

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
    //$PHPShopOrm->Option['where'] = ' or ';
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'num, id desc'), array('limit' => 3000));
    if (is_array($data))
        foreach ($data as $row) {

            // ������
            $filtr=array('checkbox' => array('val' => $row['filtr'],'name'=>'filtr'), 'align' => 'center');

            // �����
            $goodoption=array('checkbox' => array('val' => $row['goodoption'],'name'=>'goodoption'), 'align' => 'center');

            // �����
            $brand=array('checkbox' => array('val' => $row['brand'],'name'=>'brand'), 'align' => 'center');

            // ����������� �������
            $virtual=array('checkbox' => array('val' => $row['virtual'],'name'=>'virtual'), 'align' => 'center');

            // ��������
            if (!empty($row['description']))
                $help='<div class="text-muted">'.$row['description'].'</div>';
            else $help=null;

            $PHPShopInterface->path = 'sort';
            $PHPShopInterface->setRow($row['id'], array('name' => $row['name'], 'link' => '?path=sort&id=' . $row['id'], 'align' => 'left','addon' => $help), array('action' => array('edit', 'copy', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), $virtual, $brand, $goodoption, $filtr);
        }

    $sidebarleft[] = array('title' => '������', 'content' => $PHPShopInterface->loadLib('tab_menu_sort', false, './sort/'), 'title-icon' => '<span class="glyphicon glyphicon-plus newsub" data-toggle="tooltip" data-placement="top" title="' . __('�������� ������') . '"></span>');
    $sidebarleft[] = array('title' => '���������', 'content' => $help, 'class' => 'hidden-xs');
    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

?>