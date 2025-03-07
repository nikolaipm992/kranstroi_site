<?php

include_once dirname(__FILE__) . '/../class/Avito.php';
$TitlePage = __('����� �� Avito') . ' #' . $_GET['id'];
PHPShopObj::loadClass("product");

// ����
$Avito = new Avito();

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopInterface, $Avito;

    // ������ �� ������
    $order_info = $Avito->getOrder($_GET['id'])['orders'][0];


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
        $currency = '<span class="rubznak hidden-xs">p</span>';
    else
        $currency = $PHPShopSystem->getDefaultValutaCode();

    // ��������� � �������� ���
    ob_start();
    print_r($order_info);
    $log = ob_get_clean();
    
    $Tab3 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '100%', $height = '500');
    $Tab1 .= $PHPShopGUI->setField("&#8470; ������", $PHPShopGUI->setText($order_info['id']));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setText($Avito->getStatus($order_info['status'])));
    $Tab1 .= $PHPShopGUI->setField("���� �����������", $PHPShopGUI->setText(str_replace(['T', 'Z'], ' ', $order_info['createdAt'])));
    $Tab1 .= $PHPShopGUI->setField("����� ������������", $PHPShopGUI->setText($order_info['delivery']['trackingNumber']));
    $Tab1 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['delivery']['serviceName']), "left", false, false));

    $Tab1 = $PHPShopGUI->setCollapse('������', $Tab1);
    $Tab3 = $PHPShopGUI->setCollapse('JSON ������', $Tab3);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("������������", "50%"), array("����", "15%"), array("���-��", "10%"), array("�����", "15%", array('align' => 'right')));

    // ���� ����������
    if ($Avito->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }


    if (is_array($order_info['items']))
        foreach ($order_info['items'] as $row) {

            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['id']) . '"']);

            if (empty($product)) {
                $product_info = $Avito->getProductList($visibility = "ALL", $row['id'], null, $limit = 1)['resources'][0];
                $image = null;
                $link =  $product_info['url'];
            } else {
                $image = $product['pic_small'];
                $link = '?path=product&id=' . $row['id'] . '&return=modules.dir.avito';
            }

            if (!empty($image))
                $icon = '<img src="' . $image . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
            else
                $icon = '<img class="media-object" src="./images/no_photo.gif">';

            $name = '
<div class="media">
  <div class="media-left">
    <a href="' . $link . '" target="_blank">
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="' . $link . '" target="_blank">' . PHPShopString::utf8_win1251($row['title']) . '</a></div>
    ' . $type_name . ': ' . $row['id'] . '
  </div>
</div>';

            $PHPShopInterface->setRow($name, (1 * $row['prices']['total']), array('name' => $row['count'], 'align' => 'center'), array('name' => number_format($row['prices']['total'] * $row['count'], 0, '', ' ') . $currency, 'align' => 'right'));
        }

    $Tab2 = $PHPShopGUI->setCollapse("�������", '<table class="table table-hover cart-list">' . $PHPShopInterface->getContent() . '</table>');

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab1 . $Tab2, true, false, true), array('�������������', $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id'], "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "campaign_num", $_GET['campaign_num'], "right", 70, "", "but").
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionSave.order.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

/**
 * ����� �������� ������
 */
function actionSave() {
    global $Avito;

    // ������ �� ������ ����
    $order_info = $Avito->getOrder($_POST['rowID'])['orders'][0];

    $name = 'Avito';
    $phone = $order_info['delivery']['buyerInfo']['phoneNumber'];
    $mail = null;
    $comment = null;

    // ������� �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $qty = $sum = $weight = 0;

    // ���� ����������
    if ($Avito->type == 2)
        $type = 'uid';
    else
        $type = 'id';

    if (is_array($order_info['items']))
        foreach ($order_info['items'] as $row) {

            // ������ �� ������
            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['id']) . '"']);


            if (empty($product) and ! empty($Avito->create_products)) {

                // �������� ������
                $product_id = $Avito->addProduct($row);
                $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' . (int) $product_id]);
            }


            if (empty($product))
                continue;

            $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
            $order['Cart']['cart'][$product['id']]['uid'] = $product['uid'];
            $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
            $order['Cart']['cart'][$product['id']]['price'] = $row['prices']['total'];
            $order['Cart']['cart'][$product['id']]['num'] = $row['count'];
            $order['Cart']['cart'][$product['id']]['weight'] = $product['weight'];
            $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
            $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
            $order['Cart']['cart'][$product['id']]['parent'] = 0;
            $order['Cart']['cart'][$product['id']]['user'] = 0;
            $qty += $row['count'];
            $sum += $row['prices']['total'] * $row['count'];
            $weight += $product['weight'];
        }

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
    $order['Person']['dostavka_metod'] = (int) $Avito->options['delivery_id'];
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['dos_ot'] = '';
    $order['Person']['dos_do'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = $comment;

    // ������ ��� ������ � ��
    $insert['datas_new'] = time();
    $insert['uid_new'] = $Avito->setOrderNum();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tracking_new'] = $order_info['delivery']['trackingNumber'];
    $insert['tel_new'] = $phone;
    $insert['statusi_new'] = (int)$Avito->options['status'];
    $insert['status_new'] = serialize(array("maneger" => __('Avito �����') . ' &#8470;' . $_POST['rowID']));
    $insert['sum_new'] = $order['Cart']['sum'];
    $insert['avito_order_id_new'] = $_POST['rowID'];

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