<?php

$TitlePage = __("���������� - �����������");
PHPShopObj::loadClass('user');
PHPShopObj::loadClass('date');

function actionStart() {
    global $PHPShopInterface,$TitlePage;


    $PHPShopInterface->action_select['��������� ���������'] = array(
        'name' => '��������� ���������',
        'action' => 'send-user-select',
        'class' => 'disabled'
    );

    $PHPShopInterface->action_select['�������������� �����������'] = array(
        'name' => '�������������� �����������',
        'action' => 'send-user-all'
    );

    $PHPShopInterface->action_title['send-user'] = '���������';

    $PHPShopInterface->addJSFiles('./shopusers/gui/shopusers.gui.js');
    $PHPShopInterface->setActionPanel(__("�����������"), array('������� ���������','|','��������� ���������','�������������� �����������'), false);
    $PHPShopInterface->setCaption(array(null, "2%"), array("������", "7%", array('sort' => 'none')), array("��������", "40%"), array("ID", "7%"), array("������������", "20%"), array("������������", "12%"), array("", "10%"), array("����������", "10%", array('align' => 'right')));

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT a.*, b.name, b.pic_small, c.login FROM ' . $GLOBALS['SysValue']['base']['notice'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['products'] . ' AS b ON a.product_id = b.id 
        JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS c ON a.user_id = c.id order by a.id desc     
            limit 1000';

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (!empty($row['pic_small']))
                $icon = '<img src="' . $row['pic_small'] . '" onerror="imgerror(this)" class="media-object" lowsrc="./images/no_photo.gif">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';
            
            if(empty($row['enabled']))
                $class=null;
            else $class="text-muted";
                

            if (!empty($row['cumulative_discount_check']))
                $cumulative_discount_check = '<span class="glyphicon glyphicon-ok"></span>';
            else
                $cumulative_discount_check = '<span class="glyphicon glyphicon-remove"></span>';

            if ($row['datas'] < time())
                $date = '<span class="glyphicon glyphicon-eye-close"></span> ';
            else
                $date = '<span class="glyphicon glyphicon-eye-open"></span> ';


            $PHPShopInterface->setRow(
                    $row['id'], $icon, array('class'=>$class,'name' => $row['name'], 'link' => '?path=product&id=' . $row['product_id'] . '&return=' . $_GET['path']), $row['product_id'], array('class'=>$class,'name' => $row['login'], 'link' => '?path=shopusers&id=' . $row['user_id'] . '&return=' . $_GET['path']), $date . PHPShopDate::get($row['datas']), array('action' => array('delete','|', 'send-user', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('���', '��'))));
        }
    $PHPShopInterface->Compile();
}

?>