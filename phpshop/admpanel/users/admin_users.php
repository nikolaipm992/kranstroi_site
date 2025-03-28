<?php

$TitlePage = __("��������������");

function actionStart() {
    global $PHPShopInterface,$TitlePage;

    $PHPShopInterface->action_button['������ �����������'] = array(
        'name' => '������ �����������',
        'action' => 'users.jurnal',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'locale'=>true,
        'icon' => 'glyphicon glyphicon-eye-open'
    );

    $PHPShopInterface->action_button['������ ������'] = array(
        'name' => '������ ������',
        'action' => 'users.stoplist',
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel',
        'type' => 'button',
        'locale'=>true,
        'icon' => 'glyphicon glyphicon-fire'
    );

    $PHPShopInterface->action_select['������ ������'] = array(
        'name' => '������ ������',
        'locale'=>true,
        'url' => '?path=users.stoplist'
    );
    
   $PHPShopInterface->action_select['������ �����������'] = array(
        'name' => '������ �����������',
       'locale'=>true,
        'url' => '?path=users.jurnal'
    );

    $PHPShopInterface->action_button['�������� ��������������'] = array(
        'name' => '',
        'action' => 'addNew',
        'class' => 'btn btn-default btn-sm navbar-btn',
        'type' => 'button',
        'locale'=>true,
        'icon' => 'glyphicon glyphicon-plus',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="'.__('�������� ��������������').'"'
    );


    $PHPShopInterface->setActionPanel($TitlePage, array('������� ���������','������ ������','������ �����������'), array('�������� ��������������', '������ �����������'));
    $PHPShopInterface->setCaption(array(null, "2%"), array("�����", "20%"), array("���", "35%"), array("E-mail", "35%"), array("", "10%"), array("������", "10%", array('align' => 'right','locale'=>true)));

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['users']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'id DESC'), array('limit' => 300));
    if (is_array($data))
        foreach ($data as $row) {
        
            $PHPShopInterface->setRow(
                    $row['id'], array('name' => $row['login'], 'link' => '?path=users&id=' . $row['id'], 'align' => 'left'), $row['name'], array('name' => $row['mail'], 'link' => 'mailto:' . $row['mail']), array('action' => array('edit', '|', 'delete', 'id' => $row['id']), 'align' => 'center'), array('status' => array('enable' => $row['enabled'], 'align' => 'right', 'caption' => array('����', '���'))));
        }
    $PHPShopInterface->Compile();
}

?>