<?php

/**
 * ������� ���, ����� ������ ������ � �� � ����������� ����������� ������ � ������
 * @param object $obj ������ �������
 * @param array $PHPShopOrderFunction ������ � ������
 */
function userorderpaymentlink_mod_dolyame_hook($obj, $PHPShopOrderFunction) {
    global $PHPShopSystem;
    
    require_once "./phpshop/modules/dolyame/class/Dolyame.php";
    $Dolyame = new Dolyame();

    // ������
    if ($_REQUEST["paynow"] == "Y") {

        // ����� ������
        $uid = $PHPShopOrderFunction->objRow['uid'];
        $id = $PHPShopOrderFunction->objRow['id'];

        // ���
        if ($PHPShopSystem->getParam('nds_enabled') == 1) {
            if ($PHPShopSystem->getParam('nds') == 0)
                $tax = 1;
            elseif ($PHPShopSystem->getParam('nds') == 10)
                $tax = 2;
            elseif ($PHPShopSystem->getParam('nds') == 18)
                $tax = 3;
            elseif ($PHPShopSystem->getParam('nds') == 20)
                $tax = 3;
        } else
            $tax = 0;

        $order = $PHPShopOrderFunction->unserializeParam('orders');

        $cart = $order['Cart']['cart'];
        foreach ($cart as $v) {

            $products[] = [
                'name' => iconv("windows-1251", "utf-8", htmlspecialchars($v['name'], ENT_COMPAT, 'cp1251', true)),
                'quantity' => $v['num'],
                'price' => number_format($v['price'], 2, '.', ''),
                'sku' => $v['uid'],
            ];
        }

        $client_info = [
            'first_name' => iconv("windows-1251", "utf-8", htmlspecialchars($_SESSION['UsersName'], ENT_COMPAT, 'cp1251', true)),
            //'phone' => $_SESSION['UsersTel'],
            'email' => $_SESSION['UsersMail'],
        ];

        // ����� ������
        $result = $Dolyame->create($products, $client_info, $id, $uid);

        if (!empty($result['link'])) {
            header('Location: ' . $result['link']);
        }
    }

    // �������� ������ �� ������� ������
    if ($PHPShopOrderFunction->order_metod_id == 10025)
        if ($PHPShopOrderFunction->getParam('statusi') == $Dolyame->order_status or $Dolyame->order_status == 0) {

            $order_uid = $PHPShopOrderFunction->objRow['uid'];

            $return = PHPShopText::a("/users/order.html?order_info=$order_uid&paynow=Y#Order", '�������� ������', '�������� ������', false, false, '_blank', 'btn btn-success pull-right');
        } elseif ($PHPShopOrderFunction->getSerilizeParam('orders.Person.order_metod') == 10025)
            $return = $PHPShopOrderFunction->getOplataMetodName().', ����� �������������� ����������';

    return $return;
}

$addHandler = array('userorderpaymentlink' => 'userorderpaymentlink_mod_dolyame_hook');
?>