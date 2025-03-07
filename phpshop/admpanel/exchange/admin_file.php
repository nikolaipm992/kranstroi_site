<?php

$TitlePage = __("�������� �����������");

// ��������� ���
function actionStart() {
    global $PHPShopInterface, $PHPShopGUI, $TitlePage, $PHPShopModules, $PHPShopSystem;

    // �������� �����������
    $image_source = $PHPShopSystem->ifSerilizeParam('admoption.image_save_source');
    $PHPShopInterface->addJSFiles('./exchange/gui/exchange.gui.js');

    $PHPShopInterface->action_select['������� �����������'] = array(
        'name' => '������� �����������',
        'action' => 'image-clean',
        'class' => 'disabled'
    );

    $PHPShopInterface->setActionPanel($TitlePage, array('������� �����������'), false);

    $PHPShopInterface->setCaption(array(null, "3%"), array("������", "5%", array('sort' => 'none')), array("�������� ������", "35%"), array("�����", "10%", array('align' => 'center')), array("", "10%"), array("������������� �����", "35%", array('align' => 'right', array('sort' => 'none'))));

    if (empty($_GET['limit']))
        $_GET['limit'] = '0,10000';
    else
        $clean = true;

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;

    $PHPShopOrm->sql = 'SELECT a.id, a.uid, a.name, a.enabled, a.yml, b.name as img, b.id as img_id FROM ' . $GLOBALS['SysValue']['base']['products'] . ' AS a 
        RIGHT JOIN ' . $GLOBALS['SysValue']['base']['foto'] . ' AS b ON a.id = b.parent order by a.id desc 
            limit ' . $_GET['limit'];

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['name']))
                continue;

            $row['pic_big'] = $row['img'];
            $row['pic_small'] = str_replace(array('.jpg', '.png', '.JPG', '.PNG'), array('s.jpg', 's.png', 's.jpg', 's.png'), $row['img']);
            $row['pic_source'] = str_replace(array('.jpg', '.png', '.JPG', '.PNG'), array('_big.jpg', '_big.png', '_big.jpg', '_big.png'), $row['img']);

            if (!file_exists('../..' . $row['pic_small']) and ! strstr($row['pic_small'], 'http'))
                $error[] = $row['pic_small'];

            if (!file_exists('../..' . $row['pic_big']) and ! strstr($row['pic_big'], 'http'))
                $error[] = $row['pic_big'];

            if (!empty($image_source) and ! file_exists('../..' . $row['pic_source']) and ! strstr($row['pic_source'], 'http'))
                $error[] = $row['pic_source'];

            // �������� �� ������
            if (!empty($row['pic_big']) and strstr($row['pic_big'], 'http')) {
                $file_headers = @get_headers($row['pic_big']);
                if ($file_headers[0] == 'HTTP/1.1 404 Not Found') {
                    $error[] = $row['pic_big'];
                }
            }

            // �������� ����������
            if (!empty($row['pic_big']) and !in_array(pathinfo($row['pic_big'])['extension'], array('gif','jpg', 'jpeg','png', 'webp','GIF', 'JPG','JPEG', 'PNG','WEBP'))) {
                $error[] = $row['pic_big'];
            }

            if (!empty($error) and is_array($error)) {
                $file = null;
                foreach ($error as $img) {
                    $file .= '<a href="//' . $_SERVER['SERVER_NAME'] . $img . '" target="_blank" >' . $img . '</a><br>';
                }
            } else
                continue;

            $icon = '<img src="./images/no_photo.gif" class="media-object">';

            // �������
            if (!empty($row['uid']))
                $uid = '<div class="text-muted">' . __('���') . ' ' . $row['uid'] . '</div>';
            else
                $uid = '<div class="text-muted"></div>';

            // �����
            if (empty($row['enabled'])) {
                $enabled = '<span class="text-muted glyphicon glyphicon-eye-close" data-toggle="tooltip" data-placement="top" title="������"></span>';
                $enabled_css = 'text-muted';
            } else {
                $enabled = $enabled_css = null;
            }

            // YML
            if (!empty($row['yml']))
                $uid .= '<span class="label label-success" title="����� � ������.�������">�</span>';

            $PHPShopInterface->setRow($row['img_id'], array('name' => $icon, 'link' => '?path=product&return=' . $_GET['path'] . '&id=' . $row['id']), array('name' => $row['name'], 'link' => '?path=product&return=' . $_GET['path'] . '&id=' . $row['id'], 'addon' => $uid, 'class' => $enabled_css), array('name' => $enabled, 'align' => 'center'), array('action' => array('delete', 'id' => $row['img_id']), 'align' => 'center'), array('name' => $file, 'align' => 'right'));

            unset($error);
        }


    $option = $PHPShopGUI->setInputText($PHPShopGUI->setHelpIcon('������ � 1 �� 3000'), 'limit', $_GET['limit'], '100%');
    $option .= $PHPShopGUI->setButton('��������', 'search', 'btn-file-search pull-right');
    if (!empty($clean))
        $option .= $PHPShopGUI->setButton('�����', 'remove', 'btn-file-cancel pull-left');
    $option .= $PHPShopGUI->setInputArg(array('type' => 'hidden', 'name' => 'path', 'value' => $_GET['path']));

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);

    $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopInterface->loadLib('tab_menu_service', false, './exchange/'));
    $sidebarleft[] = array('title' => '����� �����', 'content' => $PHPShopInterface->setForm($option, false, "file_search", false, false, 'form-sidebar'));
    $PHPShopInterface->setSidebarLeft($sidebarleft, 2);

    // �����
    $PHPShopInterface->Compile(2);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>