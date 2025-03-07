<?php

/**
 * ������� ���, ����������� ������ � ��������� ����� ����������, ������������� �� ��������� �����
 * @param object $obj ������ �������
 * @param array $value ������ � ������
 * @param string $rout ����� ��������� ����
 */
function send_alfabank_hook($obj, $value, $rout) {

    if ($rout === 'END' and (int) $value['order_metod'] === 10021) {
        global $PHPShopSystem;

        // ��������� ������
        include_once(dirname(__FILE__) . '/mod_option.hook.php');

        $PHPShopAlfabankArray = new PHPShopAlfabankArray();
        $option = $PHPShopAlfabankArray->getArray();

        $orders = unserialize($obj->order);

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

        // �������� ������ �� ������� ������
        if (empty($option['status'])) {

            // ���������� �������
            $i = 0;
            $total = 0;
            foreach ($orders['Cart']['cart'] as $key => $arItem) {

                // ������
                if ((float) $obj->discount > 0 && empty($arItem['promo_price']))
                    $price = ($arItem['price'] - ($arItem['price'] * (float) $obj->discount / 100)) * 100;
                else
                    $price = $arItem['price'] * 100;

                // ������
                if ($obj->bonus_minus > 0 and $i == 0) {
                    $price = $price - $obj->bonus_minus * 100;
                }

                $price = round($price);
                $amount = $price * (int) $arItem['num'];

                if (empty($arItem['ed_izm']))
                    $arItem['ed_izm'] = '��.';

                $aItem[] = array(
                    "positionId" => $i,
                    "name" => PHPShopString::win_utf8($arItem['name']),
                    "itemPrice" => $price,
                    "quantity" => array("value" => $arItem['num'], "measure" => PHPShopString::win_utf8($arItem['ed_izm'])),
                    "itemAmount" => $amount,
                    "itemCode" => $arItem['id'],
                    "tax" => array("taxType" => $tax),
                    "itemAttributes" => array(
                        "attributes" => array(
                            array(
                                "name" => "paymentMethod",
                                "value" => 1
                            ),
                            array(
                                "name" => "paymentObject",
                                "value" => 1
                            )
                        )
                    )
                );
                $i++;
                $total = $total + $amount;
            }

            // ��������
            if ($obj->delivery > 0) {

                switch ($obj->PHPShopDelivery->getParam('ofd_nds')) {
                    case 0:
                        $tax_delivery = 1;
                        break;
                    case 10:
                        $tax_delivery = 2;
                        break;
                    case 18:
                        $tax_delivery = 3;
                        break;
                    case 20:
                        $tax_delivery = 3;
                        break;
                    default: $tax_delivery = $tax;
                }

                $delivery_price = (int) $obj->delivery * 100;

                $aItem[] = array(
                    "positionId" => $i + 1,
                    "name" => PHPShopString::win_utf8('��������'),
                    "itemPrice" => $delivery_price,
                    "quantity" => array("value" => 1, "measure" => PHPShopString::win_utf8('��.')),
                    "itemAmount" => $delivery_price,
                    "itemCode" => $i + 1,
                    "tax" => array("taxType" => $tax_delivery),
                    "itemAttributes" => array(
                        "attributes" => array(
                            array(
                                "name" => "paymentMethod",
                                "value" => 1
                            ),
                            array(
                                "name" => "paymentObject",
                                "value" => 4
                            )
                        )
                    )
                );
                $total = $total + $delivery_price;
            }
            $array = array(
                "customerDetails" => array("email" => $_POST["mail"]),
                "cartItems" => array("items" => $aItem));

            $orderBundle = json_encode($array);

            // �������
            $order_pref = alfabank_log_check($value['ouid']);
            $orderNum = $value['ouid'];

            if (!empty($order_pref))
                $orderNum = $value['ouid'] . '#' . $order_pref;

            // ����������� ������ � ��������� �����
            $params = array(
                "userName" => $option["login"],
                "password" => $option["password"],
                "orderNumber" => $orderNum,
                "amount" => $total,
                "returnUrl" => 'https://' . $_SERVER['HTTP_HOST'] . '/success/?uid=' . $value['ouid'] . '&payment=alfabank',
                "failUrl" => 'https://' . $_SERVER['HTTP_HOST'] . '/fail/?uid=' . $value['ouid'] . '&payment=alfabank',
                "orderBundle" => $orderBundle,
                "taxSystem" => intval($option["taxationSystem"])
            );

            // ����� ���������� � ������ �����
            if ($option["dev_mode"] == "0")
                $url = $option["api_url"];
            else
                $url = $option["dev_mode"];

            $rbsCurl = curl_init();
            curl_setopt_array($rbsCurl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($params, '', '&')
            ));

            $result = json_decode(curl_exec($rbsCurl), true);
            $result['orderBundle'] = $array;
            unset($params['orderBundle']);
            $result['params'] = $params;
            $result['url'] = $url;

            curl_close($rbsCurl);

            // ������ ����
            if (isset($result["formUrl"]))
                $PHPShopAlfabankArray->log($result, $value['ouid'], '����� ���������������', 'register');
            else {
                $result['errorMessage'] = PHPShopString::utf8_win1251($result['errorMessage']);
                $PHPShopAlfabankArray->log([$url, $params, $result], $value['ouid'], '������ ����������� ������', 'register');
            }

            header('Location: ' . $result["formUrl"]);
        } else {

            $obj->set('mesageText', $option['title_sub']);

            $forma = ParseTemplateReturn($GLOBALS['SysValue']['templates']['order_forma_mesage']);

            $obj->set('orderMesage', $forma);
        }
    }
}

/**
 * ����� ������� �������� ������
 * @param string $order_id
 * @return int
 */
function alfabank_log_check($order_id) {
    $PHPShopOrm = new PHPShopOrm("phpshop_modules_alfabank_log");
    $result = $PHPShopOrm->select(array('id'), array('order_id' => '="' . $order_id . '"', 'type' => '="register"'), array('order' => 'id desc'), array('limit' => 1));
    if (is_array($result))
        return $result['id'] ++;
}

$addHandler = array('send_to_order' => 'send_alfabank_hook');
?>