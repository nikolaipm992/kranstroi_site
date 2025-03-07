<?php

$TitlePage = __("���������� - �����������");

function actionStart() {
    global $PHPShopInterface, $TitlePage;

    $PHPShopInterface->action_select['�������� ���������'] = array(
        'name' => '�������� ���������',
        'action' => 'on-comment-select',
        'class' => 'disabled'
    );
    
    $PHPShopInterface->action_button['����� �����������'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('�������� �����������') . '"'
    );

    $PHPShopInterface->action_title['comment-url'] = '���������� ��� ������';

    $PHPShopInterface->addJSFiles('./shopusers/gui/shopusers.gui.js');
    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������'), ['����� �����������'], false);
    $PHPShopInterface->setCaption(array(null, "2%"), array("������", "7%", array('sort' => 'none')), array("��������", "38%"), array("�������", "10%"), array("������������", "20%"), array("����", "10%"), array("", "10%"), array("������", "10%", array('align' => 'right')));

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT a.*, b.name, b.pic_small, c.login FROM ' . $GLOBALS['SysValue']['base']['comment'] . ' AS a 
        LEFT JOIN ' . $GLOBALS['SysValue']['base']['products'] . ' AS b ON a.parent_id = b.id 
        LEFT JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS c ON a.user_id = c.id order by a.id desc limit 1000';

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (!empty($row['pic_small']))
                $icon = '<img src="' . $row['pic_small'] . '" data-url="' . $row['parent_id'] . '" onerror="imgerror(this)" class="media-object" lowsrc="./images/no_photo.gif">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            if (!empty($row['cumulative_discount_check']))
                $cumulative_discount_check = '<span class="glyphicon glyphicon-ok"></span>';
            else
                $cumulative_discount_check = '<span class="glyphicon glyphicon-remove"></span>';


            $PHPShopInterface->setRow(
                    $row['id'], array('name' => $icon, 'link' => '?path=product&id=' . $row['parent_id'] . '&return=' . $_GET['path']), array('name' => $row['name'], 'link' => '?path=shopusers.comment&id=' . $row['id']), $row['rate'], array('name' => $row['login'], 'link' => '?path=shopusers&id=' . $row['user_id'] . '&return=' . $_GET['path']), PHPShopDate::get($row['datas']), array('action' => array('edit', 'comment-url', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
    $PHPShopInterface->Compile();
}

?>