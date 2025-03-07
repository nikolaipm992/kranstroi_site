<?php

include_once dirname(__FILE__) . '/../class/YandexMarket.php';
PHPShopObj::loadClass('category');
$TitlePage = __('������ �� ������.�������');

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name, $PHPShopModules;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("������", "5%"), array("��������", "40%"), array("���������", "25%"),array("�����", "15%"), array("������", "15%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $YandexMarket = new YandexMarket();

    // ��������� ��
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $PHPShopCategory= $PHPShopCategoryArray->getArray();
    
    if($_GET['status'] == 'NEW'){
        $_GET['status']='ALL';
        $new =true;
    }
    elseif(empty($_GET['status']))
        $_GET['status']='ALL';
    
    if(empty($_GET['limit']))
        $_GET['limit']=50;

    // ������
    $products = $YandexMarket->getProductList($_GET['status'],PHPShopString::utf8_win1251($_GET['offer_id']),PHPShopString::utf8_win1251($_GET['vendorName']),(int)$_GET['limit']);

    if ($YandexMarket->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($products['result']['offerMappings'])) {
        foreach ($products['result']['offerMappings'] as $products_list) {
            
            // �������� ������ � ��������� ����
            $PHPShopProduct = new PHPShopProduct(PHPShopString::utf8_win1251($products_list['offer']['offerId']), $type);
            if (!empty($PHPShopProduct->getName())) {
                
                // ����������
                if(!empty($new))
                    continue;
                
                $data[$products_list['offer']['offerId']]['id'] = $products_list['offer']['offerId'];
                $data[$products_list['offer']['offerId']] = $PHPShopProduct->getArray();
                $data[$products_list['offer']['offerId']]['name'] = $PHPShopProduct->getName();
                $data[$products_list['offer']['offerId']]['status'] = 'imported';
                $data[$products_list['offer']['offerId']]['vendor'] = PHPShopString::utf8_win1251($products_list['offer']['vendor']);
                $data[$products_list['offer']['offerId']]['offer_id'] = $products_list['offer']['offerId'];
                $data[$products_list['offer']['offerId']]['primary_image'] = $PHPShopProduct->getImage();
                $data[$products_list['offer']['offerId']]['link'] = '?path=product&id=' . $PHPShopProduct->getParam("id");
                
                $data[$products_list['offer']['offerId']]['category'] = $PHPShopCategory[$PHPShopProduct->getParam("category")]['name'];
                
            } else {
                
                $data[$products_list['offer']['offerId']]['id'] = $products_list['offer']['offerId'];
                $data[$products_list['offer']['offerId']]['name'] = PHPShopString::utf8_win1251($products_list['offer']['name']);
                $data[$products_list['offer']['offerId']]['vendor'] = PHPShopString::utf8_win1251($products_list['offer']['vendor']);
                $data[$products_list['offer']['offerId']]['primary_image'] =$products_list['offer']['pictures'][0];
                
                $data[$products_list['offer']['offerId']]['status'] = 'wait';
                $data[$products_list['offer']['offerId']]['link'] ='?path=modules.dir.yandexcart.import&id='.PHPShopString::utf8_win1251($products_list['offer']['offerId']);

                // ���������
                $data[$products_list['offer']['offerId']]['category'] = PHPShopString::utf8_win1251($products_list['offer']['category']);
            }
        }
    }

    $status = [
        'imported' => '<span class="text-mutted">' . __('��������') . '</span>',
        'wait' => '<span class="text-success">' . __('����� � ��������') . '</span>',
    ];
    

    if (is_array($data))
        foreach ($data as $row) {
        
            if(empty($row['name']))
                continue;

            if (!empty($row['primary_image']))
                $icon = '<img src="' . $row['primary_image'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
               $icon=null;

            // �������
            if (!empty($row['id']))
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($row['id']) . '</div>';
            else
                $uid = null;


            $PHPShopInterface->setRow($icon, array('name' => $row['name'], 'addon' => $uid, 'link' => $row['link']), $row['category'], $row['vendor'], $status[$row['status']]);
        }

        
    $status_value[] = ['��� ������, ����� ��������', 'ALL', $_GET['status']];
    $status_value[] = ['������ � ������', 'ARCHIVED', $_GET['status']];

    $searchforma = $PHPShopInterface->setSelect('status', $status_value, '100%',true);
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'offer_id', 'placeholder' => 'SKU', 'value' => $_GET['offer_id']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'vendorName', 'placeholder' => '�����', 'value' => $_GET['vendorName']));

    if(empty($_GET['limit']))
        $_GET['limit']=50;
    
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'limit', 'placeholder' => '����� �������', 'value' => $_GET['limit']));
    
    if (isset($_GET['offer_id']) or isset($_GET['product_id']))
        $searchforma .= $PHPShopInterface->setButton('�����', 'remove', 'btn-order-cancel pull-left',false, 'javascript:window.location.replace(\'?path=modules.dir.ozonseller.import\')');
    
    $searchforma .= $PHPShopInterface->setButton('��������', 'search', 'btn-order-search pull-right', false, 'document.avito_search.submit()');
    $searchforma .= $PHPShopInterface->setInput("hidden", "path", $_GET['path'], "right", 70, "", "but");

    $sidebarright[] = array('title' => '������', 'content' => $PHPShopInterface->setForm($searchforma, false, "avito_search", false, false, 'form-sidebar'));

    $PHPShopInterface->setSidebarRight($sidebarright, 2, 'hidden-xs');

    $PHPShopInterface->Compile(2);
}
