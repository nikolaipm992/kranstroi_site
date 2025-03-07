<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';

// ��������� ������� ��������
function actionStart() {
    global $PHPShopInterface, $PHPShopModules, $TitlePage, $select_name, $PHPShopSystem;

    $PHPShopInterface->checkbox_action = true;
    $PHPShopInterface->path = 'modules.dir.ozonseller.action';
    $PHPShopInterface->addJSFiles('../modules/ozonseller/admpanel/gui/order.gui.js');
    
    $PHPShopInterface->action_select['������ �� ����� ���������'] = array(
        'name' => '������ �� ����� ���������',
        'action' => 'select-deactivation',
        'class' => 'disabled'
    );
    
    $PHPShopInterface->setActionPanel(__('����� ').' [ID '.$_GET['id'].'] - '.$TitlePage, array('������ �� ����� ���������'), false);
    $PHPShopInterface->setCaption(array(null, "2%"),array("������", "7%"), array("��������", "40%"), array("��������� � ���", "20%"),array("����", "10%",array('align' => 'center')),array("������" . "", "7%", array('align' => 'right')));
    
      // ��������� Ozon
    $PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_categories"));
    $PHPShopOrmType = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_type"));
    
    $OzonSeller = new OzonSeller();
    $data = $OzonSeller->getActionsProduct($_GET['id'])['result']['products'];
    
    if ($OzonSeller->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($data)) {
        foreach ($data as $row) {
            $ids[] = $row['id'];
        }

        // ������
        $products = $OzonSeller->getProductInfoList(null, $ids)['items'];

    }

        // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();
    
    if (is_array($products))
        foreach ($products as $row) {

            if (!empty($row['primary_image']))
                $icon = '<img src="' . $row['primary_image'][0] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = mull;
            
            // ���������
            $category = $PHPShopOrmCat->getOne(['name'], ['id' => '=' . $row['description_category_id']])['name'];
            $type =  $PHPShopOrmType->getOne(['name'], ['id' => '=' . $row['type_id']])['name'];
            
            // �������
            if (!empty($row['offer_id']))
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($row['offer_id']) . '</div>';
            else
                $uid = null;
            
            $sku = $row['sources'][0]['sku'];

            // ����
            $price = (float)$row['marketing_price'].$currency.'<br><strike>'.(float)$row['price'].'</strike>';

            $PHPShopInterface->setRow($_GET['id'].'-'.$row['id'],$icon, array('name' => PHPShopString::utf8_win1251($row['name']), 'addon' => $uid, 'link' => 'https://www.ozon.ru/product/' . $sku,'target'=>'_blank'), $category.' - '.$type, ['name'=>$price,'align' => 'center'],array('status' => array('enable' => 1, 'align' => 'right', 'caption' => array('����', '���'))));
            
           
        }
    $PHPShopInterface->Compile(2);
}

// ������� ����������
function actionUpdate() {
    
    $OzonSeller = new OzonSeller();
    $id = explode("-",$_POST['rowID']);
    $result = $OzonSeller->deactivationActionsProduct($id[0],[$id[1]]);
    
    if($result['result']['product_ids'][0] == $id[1])
        $action = true;

    return array("success" => $action);
}


// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>