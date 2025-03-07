<?php

include_once dirname(__FILE__) . '/../class/OzonSeller.php';
PHPShopObj::loadClass('category');
$TitlePage = __('������ �� Ozon');

function actionStart() {
    global $PHPShopInterface, $TitlePage, $select_name, $PHPShopModules;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setActionPanel($TitlePage, $select_name, false);
    $PHPShopInterface->setCaption(array("������", "5%"), array("��������", "40%"), array("���������", "30%"), array("������", "15%"));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
    $PHPShopOrm->debug = false;
    $OzonSeller = new OzonSeller();

    // ��������� Ozon
    $PHPShopOrmCat = new PHPShopOrm($PHPShopModules->getParam("base.ozonseller.ozonseller_categories"));
    
    // ��������� ��
    $PHPShopCategoryArray = new PHPShopCategoryArray();
    $PHPShopCategory= $PHPShopCategoryArray->getArray();
    
    if($_GET['status'] == 'NEW'){
        $_GET['status']='ALL';
        $new =true;
    }

    // ������
    $products = $OzonSeller->getProductList($_GET['status'],$_GET['offer_id'],$_GET['product_id'],$_GET['limit']);

    if ($OzonSeller->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    if (is_array($products['result']['items'])) {
        foreach ($products['result']['items'] as $products_list) {

            // �������� ������ � ��������� ����
            $PHPShopProduct = new PHPShopProduct(PHPShopString::utf8_win1251($products_list['offer_id']), $type);
            if (!empty($PHPShopProduct->getName())) {
                
                // ����������
                if(!empty($new))
                    continue;
                
                $data[$products_list['product_id']] = $PHPShopProduct->getArray();
                $data[$products_list['product_id']]['name'] = PHPShopString::win_utf8($PHPShopProduct->getName());
                $data[$products_list['product_id']]['status'] = 'imported';
                $data[$products_list['product_id']]['offer_id'] = $products_list['offer_id'];
                $data[$products_list['product_id']]['primary_image'] = $PHPShopProduct->getImage();
                $data[$products_list['product_id']]['link'] = '?path=product&id=' . $PHPShopProduct->getParam("id");
                
                $data[$products_list['product_id']]['category'] = $PHPShopCategory[$PHPShopProduct->getParam("category")]['name'];
                
            } else {
                
                $data[$products_list['product_id']] = $OzonSeller->getProduct($products_list['product_id'])['items'][0];
                
                $data[$products_list['product_id']]['status'] = 'wait';
                $data[$products_list['product_id']]['link'] ='?path=modules.dir.ozonseller.import&id='.$products_list['product_id'].'&type_id='.$data[$products_list['product_id']]['type_id'].'&sku='.$data[$products_list['product_id']]['sources'][0]['sku'];

                // ���������
                $category = $PHPShopOrmCat->getOne(['name'], ['id' => '=' . $data[$products_list['product_id']]['category_id']]);
                $data[$products_list['product_id']]['category'] = $category['name'];
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
                $icon = '<img src="' . $row['primary_image'][0] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
               $icon=null;

            // �������
            if (!empty($row['offer_id']))
                $uid = '<div class="text-muted">' . $type_name . ' ' . PHPShopString::utf8_win1251($row['offer_id']) . '</div>';
            else
                $uid = null;


            $PHPShopInterface->setRow($icon, array('name' => PHPShopString::utf8_win1251($row['name']), 'addon' => $uid, 'link' => $row['link']), $row['category'], $status[$row['status']]);
        }

        
    $status_value[] = ['��� ������, ����� ��������', 'ALL', $_GET['status']];
    $status_value[] = ['��� ������, ����� �����������', 'NEW', $_GET['status']];
    $status_value[] = ['������, ������� ����� �����������', 'VISIBLE', $_GET['status']];
    $status_value[] = ['������, ������� �� ����� �����������', 'INVISIBLE', $_GET['status']];
    $status_value[] = ['������ � ������', 'ARCHIVED', $_GET['status']];

    $searchforma = $PHPShopInterface->setSelect('status', $status_value, '100%',true);
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'offer_id', 'placeholder' => '�������', 'value' => $_GET['offer_id']));
    $searchforma .= $PHPShopInterface->setInputArg(array('type' => 'text', 'name' => 'product_id', 'placeholder' => 'OZON ID', 'value' => $_GET['product_id']));
    
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
