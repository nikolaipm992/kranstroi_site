<?php

$TitlePage = __("�������� ������������ ��������� �������");

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

    $PHPShopInterface->setActionPanel($TitlePage, false, false);
    $PHPShopInterface->checkbox_action = false;

    $PHPShopInterface->setCaption(array("������", "5%", array('sort' => 'none')), array("�������� ������", "35%"),array("ID", "10%"),array("������� ���������", "35%", array('align' => 'right', array('sort' => 'none'))));


    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->mysql_error = false;

    $PHPShopOrm->sql = 'SELECT id, uid, name, enabled, pic_small, count(uid) from ' . $GLOBALS['SysValue']['base']['products'] . ' GROUP BY uid HAVING count(uid)>1';

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['name']) or empty($row['uid']))
                continue;
            
            // �����
            $list=$ids=null;
            $PHPShopOrmProduct = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            $product = $PHPShopOrmProduct->getList(['*'],['uid'=>'="'.$row['uid'].'"']);
            if(is_array($product)){
                foreach ($product as $val){
                   $ids.=$val['id'].'<br>';
                   $list.= $PHPShopGUI->setLink('?path=product&return=' . $_GET['path'] . '&id=' . $val['id'], $val['name']).'<br>';
                }
            }

            if (!empty($row['pic_small']))
                $icon = '<img src="' . $row['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            // �������
            if (!empty($row['uid']))
                $uid = '<div class="text-muted">' . __('���') . ' ' . $row['uid'] . '</div>';
            else
                $uid = '<div class="text-muted"></div>';

            // �����
            if (empty($row['enabled'])) {
                $enabled_css = 'text-muted';
            } else {
                $enabled_css = null;
            }


            $PHPShopInterface->setRow(array('name' => $icon, 'link' => '?path=product&return=' . $_GET['path'] . '&id=' . $row['id']), array('name' => $row['name'], 'link' => '?path=product&return=' . $_GET['path'] . '&id=' . $row['id'], 'addon' => $uid, 'class' => $enabled_css), $ids,  array('name' => $list, 'align' => 'right'));

        }

    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, false);

    $sidebarleft[] = array('title' => '���������', 'content' => $PHPShopInterface->loadLib('tab_menu_service', false, './exchange/'));
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