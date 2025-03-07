<?php

$TitlePage = __("��������");
PHPShopObj::loadClass('delivery');

function actionStart() {
    global $PHPShopInterface;

    $PHPShopCategoryArray = new PHPShopDeliveryArray(array('is_folder' => "='1'"));
    $CategoryArray = $PHPShopCategoryArray->getArray();

    if (!empty($_GET['cat']) and ! empty($CategoryArray[$_GET['cat']]['name']))
        $catname = " / " . $CategoryArray[$_GET['cat']]['name'];
    else
        $catname = null;

    $PHPShopInterface->action_select['������ � �������'] = [
        'name' => '������ � �������',
        'locale' => true,
        'url' => '?path=citylist'
    ];

    $PHPShopInterface->action_select['������������� �������'] = array(
        'name' => '������������� �������',
        'url' => '?path=' . $_GET['path'] . '&id=' . intval($_GET['cat'])
    );
    
        $PHPShopInterface->action_select['����� �������'] = array(
        'name' => '����� �������',
        'url' => '?path=' . $_GET['path'] . '&action=new&target=cat',
        'class' => 'enabled'
    );

    if (empty($_GET['cat']))
        $PHPShopInterface->action_select['������������� �������']['class'] = 'disabled';

    $PHPShopInterface->setActionPanel(__("��������" . $catname), array('����� �������','������ � �������', '������������� �������', '|', '������� ���������'), array('��������'));
    $PHPShopInterface->setCaption(
            array(null, "2%"), array("������", "10%", array('sort' => 'none')), array("��������", "35%"), array("���� ", "12%"), array("��������� ", "10%", array('tooltip' => '��������� �����')), array("", "7%"), array("������" . "", "7%", array('align' => 'right'))
    );

    $PHPShopInterface->addJSFiles('./js/jquery.treegrid.js', './delivery/gui/delivery.gui.js');


    $where = array('is_folder' => "!='1'");
    if (!empty($_GET['cat'])) {
        $where = array('PID' => '=' . intval($_GET['cat']));
    }


    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => 1000));
    if (is_array($data))
        foreach ($data as $row) {

            if (!empty($row['icon']))
                $icon = '<img src="' . $row['icon'] . '" onerror="imgerror(this)" class="media-object" lowsrc="./images/no_photo.gif">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            if ($row['is_mod'] == 2)
                $readonly = 'readonly';
            else
                $readonly = null;

            $PHPShopInterface->setRow(
                    $row['id'], array('name' => $icon, 'link' => '?path=delivery&id=' . $row['id'], 'align' => 'left'), array('name' => $row['city'], 'link' => '?path=delivery&id=' . $row['id'], 'align' => 'left'), array('name' => $row['price'], 'editable' => 'price_new'), array('name' => $row['price_null'], 'align' => 'center', 'editable' => 'price_null_new', 'readonly' => $readonly), array('action' => array('edit', '|','delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���')))
            );
        }

    // ����� ������� ������ ���������
    $CategoryArray[0]['name'] = '������';
    $tree_array = array();
    if (is_array($PHPShopCategoryArray->getKey('PID.id', true)))
        foreach ($PHPShopCategoryArray->getKey('PID.id', true) as $k => $v) {
            foreach ($v as $cat) {
                $tree_array[$k]['sub'][$cat] = @$CategoryArray[$cat]['city'];
            }
            $tree_array[$k]['name'] = @$CategoryArray[$k]['city'];
            $tree_array[$k]['id'] = $k;
        }

    $GLOBALS['tree_array'] = &$tree_array;

    $tree = '<table class="table table-hover">
         <tr class="treegrid-0">
           <td><a href="?path=' . $_GET['path'] . '">' . __('��� ��������') . '</a></td>
	</tr>';
    
    if (is_array($tree_array[0]['sub']))
        foreach ($tree_array[0]['sub'] as $k => $v) {
            $check = treegenerator(@$tree_array[$k], $k);
            if (empty($check))
                $tree .= '<tr class="treegrid-' . $k . ' data-tree">
		<td><a href="?path=delivery&cat=' . $k . '">' . $v . '</a></td>
	</tr>';
            else
                $tree .= '<tr class="treegrid-' . $k . ' data-tree">
		<td><a href="?path=delivery&cat=' . $k . '">' . $v . '</a></td>
	</tr>';
            $tree .= $check;
        }
    $tree .= '
        </table>';




    $sidebarleft[] = array('title' => '���������', 'content' => $tree, 'title-icon' => '<span class="glyphicon glyphicon-plus newcat" data-toggle="tooltip" data-placement="top" title="' . __('�������� �������') . '"></span>');

    $help = '<p class="text-muted">' . __('� ������� ���� �������� ����� ��������� ������������ � �������������� ���� ��� ���������� ������ � �������� ���������� ��������� <kbd>������ ������������</kbd>') . '</p>';

    $sidebarleft[] = array('title' => '���������', 'content' => $help);
    $PHPShopInterface->setSidebarLeft($sidebarleft, 3);

    $PHPShopInterface->Compile(3);
}

// ���������� ������ ���������
function treegenerator($array, $parent) {
    global $PHPShopInterface, $tree_array;
    $tree = $check = false;
    if (!empty($array['sub']) and is_array($array['sub'])) {
        foreach ($array['sub'] as $k => $v) {
            $check = treegenerator($tree_array[$k], $k);

            if (empty($check))
                $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="?path=delivery&cat=' . $k . '">' . $v . '</a></td>
	</tr>';
            else
                $tree .= '<tr class="treegrid-' . $k . ' treegrid-parent-' . $parent . ' data-tree">
		<td><a href="#" class="treegrid-parent" data-parent="treegrid-' . $k . '">' . $v . '</a></td>
	</tr>';

            $tree .= $check;
        }
    }
    return $tree;
}

?>