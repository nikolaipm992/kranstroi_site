<?php

include_once dirname(__FILE__) . '/../class/WbSeller.php';
$TitlePage = __('����� �� WB') . ' #' . $_GET['id'];
PHPShopObj::loadClass("product");

// ����
$WbSeller = new WbSeller();

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopInterface, $WbSeller;

    // ������ �� ������
    $orders = $WbSeller->getOrderList($_GET['date_start'], $_GET['date_end'], $_GET['status'])['orders'];

    if (is_array($orders)) {
        foreach ($orders as $order) {
            if ($order['id'] == $_GET['id']) {
                $order_info = $order;
            }
        }
    }

    $PHPShopGUI->field_col = 4;

    if (!empty($order_info['id']))
        $PHPShopGUI->action_button['��������� �����'] = array(
            'name' => '��������� �����',
            'locale' => true,
            'action' => 'saveID',
            'class' => 'btn  btn-default btn-sm navbar-btn' . $GLOBALS['isFrame'],
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-save'
        );


    $PHPShopGUI->setActionPanel(__('�����') . ' &#8470;' . $_GET['id'], false, array('��������� �����'));

    // ���� �����
    if ($PHPShopSystem->getDefaultValutaIso() == 'RUB' or $PHPShopSystem->getDefaultValutaIso() == 'RUR')
        $currency = ' <span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // ��������� � �������� ���
    ob_start();
    print_r($order_info);
    $log = ob_get_clean();

    $Tab3 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '100%', $height = '500');
    $Tab1 = $PHPShopGUI->setField("&#8470; ������", $PHPShopGUI->setText($order_info['id']));
    $Tab1 .= $PHPShopGUI->setField("���� �����������", $PHPShopGUI->setText($order_info['createdAt']));
    $Tab1 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['prioritySc'][0]), "left", false, false));
    $Tab1 .= $PHPShopGUI->setField("�����", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['offices'][0]), "left", false, false));

    $Tab1 .= $PHPShopGUI->setInput("hidden", "date_start", $_GET['date_start']);
    $Tab1 .= $PHPShopGUI->setInput("hidden", "date_end", $_GET['date_end']);

    $Tab1 = $PHPShopGUI->setCollapse('������', $Tab1);
    $Tab3 = $PHPShopGUI->setCollapse('JSON ������', $Tab3);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("������������", "50%"), array("����", "15%"), array("���-��", "10%"), array("�����", "15%", array('align' => 'right')));

    // ���� ����������
    if ($WbSeller->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    // ������ �� ������ � ��
    $prod = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) $order_info['article'] . '"']);

    // ������ �� ������ �� WB
    if (empty($prod)) {
        $product_info = $WbSeller->getProductList($order_info['skus'][0], 1)['cards'][0];
        $prod['pic_small'] = $product_info['photos'][0]['tm'];
        $prod['uid'] = PHPShopString::utf8_win1251($product_info['vendorCode']);
        $prod['name'] = PHPShopString::utf8_win1251($product_info['title']);

        $link = 'https://www.wildberries.ru/catalog/' . $product_info['nmID'] . '/detail.aspx';
    }
    else {
        $link = '?path=product&id=' . $prod['id'];
    }



    if (!empty($prod['pic_small']))
        $icon = '<img src="' . $prod['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
    else
        $icon = '<img class="media-object" src="./images/no_photo.gif">';

    $name = '
<div class="media">
  <div class="media-left">
    <a href="' . $link . '" target="_blank" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="' . $link . '" target="_blank">' . $prod['name'] . '</a></div>
    ' . $type_name . ': ' . $prod['uid'] . '
  </div>
</div>';

    $PHPShopInterface->setRow($name, number_format(round($order_info['price'] / 100), 0, '', ' '), array('name' => 1, 'align' => 'center'), array('name' => number_format($order_info['price'] / 100, 0, '', ' ') . $currency, 'align' => 'right'));


    $Tab2 = $PHPShopGUI->setCollapse("�������", '<table class="table table-hover cart-list">' . $PHPShopInterface->getContent() . '</table>');

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab1 . $Tab2, true, false, true), array('�������������', $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "status", $_GET['status'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.order.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

/**
 * ����� �������� ������
 */
function actionSave() {
    global $WbSeller;

    // ������ �� ������
    $orders = $WbSeller->getOrderList($_POST['date_start'], $_POST['date_end'],$_POST['status'])['orders'];

    if (is_array($orders)) {
        foreach ($orders as $order) {
            if ($order['id'] == $_POST['rowID']) {
                $order_info = $order;
            }
        }
    }
    

    
    $name = 'WB';
    $phone = null;
    $mail = null;
    $comment = null;

    // ������� �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $qty = $sum = $weight = 0;

    // ���� ����������
    if ($WbSeller->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }

    // ������ �� ������
    $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($order_info['article']) . '"']);

    if (empty($product) and ! empty($WbSeller->create_products)) {

        // �������� ������
        $product_id = $WbSeller->addProduct($order_info['skus'][0]);
        $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' . (int) $product_id]);
    }


    $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
    $order['Cart']['cart'][$product['id']]['uid'] = $product["uid"];
    $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
    $order['Cart']['cart'][$product['id']]['price'] = $order_info['price'] / 100;
    $order['Cart']['cart'][$product['id']]['num'] = 1;
    $order['Cart']['cart'][$product['id']]['weight'] = '';
    $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
    $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
    $order['Cart']['cart'][$product['id']]['parent'] = 0;
    $order['Cart']['cart'][$product['id']]['user'] = 0;
    $qty = 1;
    $sum = $order_info['price'] / 100;
    $weight += $product['weight'];


    $order['Cart']['num'] = $qty;
    $order['Cart']['sum'] = $sum;
    $order['Cart']['weight'] = $weight;
    $order['Cart']['dostavka'] = 0;

    $order['Person']['ouid'] = '';
    $order['Person']['data'] = time();
    $order['Person']['time'] = '';
    $order['Person']['mail'] = $mail;
    $order['Person']['name_person'] = $name;
    $order['Person']['org_name'] = '';
    $order['Person']['org_inn'] = '';
    $order['Person']['org_kpp'] = '';
    $order['Person']['tel_code'] = '';
    $order['Person']['tel_name'] = '';
    $order['Person']['adr_name'] = '';
    $order['Person']['dostavka_metod'] = '';
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = $comment;

    // ������ ��� ������ � ��
    $insert['datas_new'] = time();
    $insert['uid_new'] = $WbSeller->setOrderNum();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tel_new'] = $phone;
    $insert['city_new'] = PHPShopString::utf8_win1251($order_info['prioritySc'][0]);
    $insert['statusi_new'] = $WbSeller->status;
    $insert['status_new'] = serialize(array("maneger" => __('WB ����� &#8470;' . $_POST['rowID'])));
    $insert['sum_new'] = $order['Cart']['sum'];
    $insert['wbseller_order_data_new'] = $_POST['rowID'];

    // ������ � ����
    $orderId = $PHPShopOrm->insert($insert);

    // ���������� ������������ � ����� ������� � �������� �� ������
    if (!empty($insert['statusi_new'])) {
        PHPShopObj::loadClass("order");
        $PHPShopOrderFunction = new PHPShopOrderFunction($orderId);
        $PHPShopOrderFunction->changeStatus($insert['statusi_new'], 0);
    }

    header('Location: ?path=order&id=' . $orderId . '&return=' . $_GET['path']);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>