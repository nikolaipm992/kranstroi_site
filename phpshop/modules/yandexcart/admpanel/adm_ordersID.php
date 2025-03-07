<?php

include_once dirname(__FILE__) . '/../class/YandexMarket.php';
$TitlePage = __('����� �� ������.������') . ' #' . $_GET['id'];
PHPShopObj::loadClass("product");

// ����
$YandexMarket = new YandexMarket();

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI, $PHPShopSystem, $PHPShopInterface, $YandexMarket;

    // ������ �� ������
    $order_info = $YandexMarket->getOrder($_GET['id'],$_GET['campaign_num'])['order'];


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
    
     if (!empty($_GET['campaign_num']))
            $model = $YandexMarket->options['model_' . $_GET['campaign_num']];
        else
            $model = $YandexMarket->options['model'];


    $Tab3 = $PHPShopGUI->setTextarea(null, PHPShopString::utf8_win1251($log), $float = "none", $width = '100%', $height = '500');
    $Tab1 .= $PHPShopGUI->setField("&#8470; ������", $PHPShopGUI->setText($order_info['id']));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setText($YandexMarket->getStatus($order_info['status'])));
    $Tab1 .= $PHPShopGUI->setField("������", $PHPShopGUI->setText($model));
    $Tab1 .= $PHPShopGUI->setField("���� �����������", $PHPShopGUI->setText($order_info['creationDate']));
    $Tab1 .= $PHPShopGUI->setField("���� ��������", $PHPShopGUI->setText($order_info['delivery']['dates']['fromDate']));
    $Tab1 .= $PHPShopGUI->setField("��������", $PHPShopGUI->setText(PHPShopString::utf8_win1251($order_info['delivery']['serviceName']), "left", false, false));

    $Tab1 = $PHPShopGUI->setCollapse('������', $Tab1);
    $Tab3 = $PHPShopGUI->setCollapse('JSON ������', $Tab3);

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("������������", "50%"), array("����", "15%"), array("���-��", "10%"), array("�����", "15%", array('align' => 'right')));

    // ���� ����������
    if ($YandexMarket->type == 2) {
        $type_name = __('���');
        $type = 'uid';
    } else {
        $type_name = 'ID';
        $type = 'id';
    }


    if (is_array($order_info['items']))
        foreach ($order_info['items'] as $row) {

            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['offerId']) . '"']);

            if (empty($product)) {
                $product_info = $YandexMarket->getProductList($visibility = "ALL", $row['offerId'], null, $limit = 1)['result']['offerMappings'][0]['offer'];
                $image = $product_info['pictures'][0];
                $link = 'https://partner.market.yandex.ru/shop/' . $product_info['campaigns'][0]['campaignId'] . '/assortment/offer-card?article=' . $product_info['offerId'];
            } else {
                $image = $product['pic_small'];
                $link = '?path=product&id=' . $row['id'] . '&return=modules.dir.yandexcart';
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
    <div class="media-heading"><a href="' . $link . '" target="_blank">' . PHPShopString::utf8_win1251($row['offerName']) . '</a></div>
    ' . $type_name . ': ' . $row['offerId'] . '
  </div>
</div>';

            $PHPShopInterface->setRow($name, (1 * $row['price']), array('name' => $row['count'], 'align' => 'center'), array('name' => number_format($row['price'] * $row['count'], 0, '', ' ') . $currency, 'align' => 'right'));
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
    global $YandexMarket;

    // ������ �� ������ ����
    $order_info = $YandexMarket->getOrder($_POST['rowID'],$_POST['campaign_num'])['order'];

    $name = '������.������';
    $phone = null;
    $mail = null;
    $comment = null;

    // ������� �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    $qty = $sum = $weight = 0;

    // ���� ����������
    if ($YandexMarket->type == 2)
        $type = 'uid';
    else
        $type = 'id';

    $data = $order_info['items'];

    if (is_array($data))
        foreach ($data as $row) {

            // ������ �� ������
            $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], [$type => '="' . (string) PHPShopString::utf8_win1251($row['offerId']) . '"']);


            if (empty($product) and ! empty($YandexMarket->create_products)) {

                // �������� ������
                $product_id = $YandexMarket->addProduct($row['offerId']);
                $product = (new PHPShopOrm($GLOBALS['SysValue']['base']['products']))->getOne(['id,uid,name,pic_small'], ['id' => '=' . (int) $product_id]);
            }


            if (empty($product))
                continue;

            $order['Cart']['cart'][$product['id']]['id'] = $product['id'];
            $order['Cart']['cart'][$product['id']]['uid'] = $product['uid'];
            $order['Cart']['cart'][$product['id']]['name'] = $product['name'];
            $order['Cart']['cart'][$product['id']]['price'] = $row['price'];
            $order['Cart']['cart'][$product['id']]['num'] = $row['count'];
            $order['Cart']['cart'][$product['id']]['weight'] = $product['weight'];
            $order['Cart']['cart'][$product['id']]['ed_izm'] = '';
            $order['Cart']['cart'][$product['id']]['pic_small'] = $product['pic_small'];
            $order['Cart']['cart'][$product['id']]['parent'] = 0;
            $order['Cart']['cart'][$product['id']]['user'] = 0;
            $qty += $row['count'];
            $sum += $row['price'] * $row['count'];
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
    $order['Person']['dostavka_metod'] = (int) $YandexMarket->options['delivery_id'];
    $order['Person']['discount'] = 0;
    $order['Person']['user_id'] = '';
    $order['Person']['dos_ot'] = '';
    $order['Person']['dos_do'] = '';
    $order['Person']['order_metod'] = '';
    $insert['dop_info_new'] = $comment;

    // ������ ��� ������ � ��
    $insert['datas_new'] = time();
    $insert['uid_new'] = $YandexMarket->setOrderNum();
    $insert['orders_new'] = serialize($order);
    $insert['fio_new'] = $name;
    $insert['tel_new'] = $phone;
    $insert['statusi_new'] = unserialize($YandexMarket->options['options'])['statuses']['processing_started'];
    $insert['status_new'] = serialize(array("maneger" => __('������.������ �����') . ' &#8470;' . $_POST['rowID']));
    $insert['sum_new'] = $order['Cart']['sum'];
    $insert['yandex_order_id_new'] = $_POST['rowID'];

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