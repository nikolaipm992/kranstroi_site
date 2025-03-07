<?php

class Sberbank {

    const SBERBANK_PAYMENT_ID = 10010;

    public $options = array();
    public $tax = 0;

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_sberbankrf_system');
        $this->options = $PHPShopOrm->select();

        $PHPShopSystem = new PHPShopSystem();
        if ($PHPShopSystem->getParam('nds_enabled') == 1) {
            if ($PHPShopSystem->getParam('nds') == 0)
                $this->tax = 1;
            elseif ($PHPShopSystem->getParam('nds') == 10)
                $this->tax = 2;
            elseif ($PHPShopSystem->getParam('nds') == 18)
                $this->tax = 3;
            elseif ($PHPShopSystem->getParam('nds') == 20)
                $this->tax = 6;
        }
    }

    public function createPayment($items, $orderNumber, $email, $delivery = null) {
        // Если есть доставка - добавляем в общий массив товаров
        if (is_array($delivery)) {
            $delivery['positionId'] = count($items['items']) + 1;
            $delivery['itemCode'] = count($items['items']) + 1;
            $items['items'][] = $delivery;
            $total = $delivery['itemPrice'] + $items['total'];
        } else {
            $total = $items['total'];
        }

        $array = ["cartItems" => ["items" => $items['items']]];
        if (strpos($email, str_replace("www.", "", $_SERVER['SERVER_NAME'])) === false) {
            $array['customerDetails'] = ["email" => $email];
        }

        $orderBundle = json_encode($array);

        // Префикс
        $order_pref = $this->getSaltToOrderId($orderNumber);
        $orderNum = $orderNumber;

        if (!empty($order_pref))
            $orderNum = $orderNumber . '#' . $order_pref;

        // Регистрация заказа в платежном шлюзе
        $params = array(
            "orderNumber" => $orderNum,
            "amount" => $total,
            "returnUrl" => 'http://' . $_SERVER['HTTP_HOST'] . '/success/?module=sberbankrf&status=success&uid=' . $orderNumber,
            "failUrl" => 'http://' . $_SERVER['HTTP_HOST'] . '/success/?module=sberbankrf&status=fail&uid=' . $orderNumber,
            "orderBundle" => $orderBundle,
            "taxSystem" => (int) $this->options["taxationSystem"]
        );

        if (!empty($this->options['token'])) {
            $params['token'] = $this->options['token'];
        } else {
            $params['userName'] = $this->options['login'];
            $params['password'] = $this->options['password'];
        }

        $rbsCurl = curl_init();
        curl_setopt_array($rbsCurl, array(
            CURLOPT_URL => $this->getApiUrl() . 'register.do',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => http_build_query($params, '', '&')
        ));

        $result = json_decode(curl_exec($rbsCurl), true);

        curl_close($rbsCurl);

        // Запись лога
        if (isset($result["formUrl"])) {
            $this->log($result, $orderNumber, 'Заказ зарегистрирован', 'register');
        } else {
            $result['errorMessage'] = PHPShopString::utf8_win1251($result['errorMessage']);
            $this->log($result, $orderNumber, 'Ошибка регистрации заказа', 'register');
        }

        return $result;
    }

    public function prepareProducts($cart, $discount) {
        $items = array();
        $total = 0;
        $i = 1;
        foreach ($cart as $product) {
            // Скидка
            if ((float) $discount > 0 && empty($product['promo_price']))
                $price = ($product['price'] - ($product['price'] * (float) $discount / 100)) * 100;
            else
                $price = $product['price'] * 100;

            $price = round($this->applyCurrency($price));
            $amount = $price * (int) $product['num'];

            if (empty($product['ed_izm']))
                $product['ed_izm'] = 'шт.';

            $items[] = array(
                "positionId" => $i,
                "name" => PHPShopString::win_utf8($product['name']),
                "itemPrice" => $price,
                "quantity" => array("value" => $product['num'], "measure" => PHPShopString::win_utf8($product['ed_izm'])),
                "itemAmount" => $amount,
                "itemCode" => $product['id'],
                "tax" => array("taxType" => $this->tax),
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

        return array('items' => $items, 'total' => $total);
    }

    public function prepareDelivery($deliveryCost = 0, $deliveryNds = null) {
        if ($deliveryCost == 0) {
            return null;
        }
        if ($deliveryNds) {
            switch ($deliveryNds) {
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
                    $tax_delivery = 6;
                    break;
            }
        } else {
            $tax_delivery = $this->tax;
        }

        return array(
            "name" => PHPShopString::win_utf8('Доставка'),
            "itemPrice" => (int) $this->applyCurrency($deliveryCost * 100),
            "quantity" => array("value" => 1, "measure" => PHPShopString::win_utf8('ед.')),
            "itemAmount" => (int) $this->applyCurrency($deliveryCost * 100),
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
    }

    /**
     * Запись лога
     * @param string $message содержание запроса в ту или иную сторону
     * @param string $order_id номер заказа
     * @param string $status статус оплаты
     */
    public function log($message, $order_id, $status, $type) {

        $PHPShopOrm = new PHPShopOrm("phpshop_modules_sberbankrf_log");
        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time()
        );
        $PHPShopOrm->insert($log);
    }

    /**
     * Номер попытки создания ссылки
     * @param string $order_id
     * @return int
     */
    public function getSaltToOrderId($order_id) {
        $PHPShopOrm = new PHPShopOrm("phpshop_modules_sberbankrf_log");
        $result = $PHPShopOrm->select(array('id'), array('order_id' => '="' . $order_id . '"', 'type' => '="register"'), array('order' => 'id desc'), array('limit' => 1));
        if (is_array($result))
            return $result['id'] ++;
    }

    public function refund($orderId) {
        // SQL
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_sberbankrf_log');
        $ordersORM = new PHPShopOrm('phpshop_orders');
        $orderData = $ordersORM->select(array('*'), array('id=' => (int) $orderId, 'paid' => "='1'"));
        $log = $PHPShopOrm->getList(array('*'), array("order_id=" => "'$orderData[uid]'", 'type' => '="register"'));

        $orderRegistered = false;
        foreach ($log as $item) {
            $message = unserialize($item['message']);
            if (isset($message['orderId'])) {
                $orderId = $message['orderId'];
                $orderRegistered = true;
                break;
            }
        }

        if ($orderRegistered === false) {
            throw new \Exception('Заказ не найден или заказ не оплачен.');
        }

        $params = array(
            "userName" => $this->options["login"],
            "password" => $this->options["password"],
            "orderId" => $orderId,
            "amount" => floatval($orderData['sum'] * 100),
        );

        $rbsCurl = curl_init();
        curl_setopt_array($rbsCurl, array(
            CURLOPT_URL => $this->getApiUrl() . 'refund.do',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_POSTFIELDS => http_build_query($params, '', '&')
        ));

        $result = json_decode(curl_exec($rbsCurl), true);

        curl_close($rbsCurl);

        if ($result['errorCode'] == 0) {
            $this->log("Возврат денежных средств успешно выполнен", $orderData['uid'], 'Возврат денежных средств выполнен', 'refundTrue');
            $ordersORM->update(array('statusi_new' => 1, 'paid_new' => "1"), array('id=' => $orderData['id']));
        }

        $this->log($result, $orderData['uid'], 'Возврат денежных средств не выполнен', 'refundFalse');

        throw new Exception($result['errorMessage']);
    }

    public function getApiUrl() {
        if ((int) $this->options["dev_mode"] === 1) {
            return 'https://3dsec.sberbank.ru/payment/rest/';
        }

        return 'https://securepayments.sberbank.ru/payment/rest/';
    }

    public function isOrderPaid($orderNumber, $merchantId) {
        $params = array(
            "orderId" => $merchantId,
            "userName" => $this->options["login"],
            "password" => $this->options["password"]
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getApiUrl() . 'getOrderStatus.do' . "?" . http_build_query($params));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $r = json_decode(curl_exec($ch), true);
        curl_close($ch);

        // Ошибка запроса
        if ($r['ErrorCode'] != 0) {
            $r['errorMessage'] = PHPShopString::utf8_win1251($r['errorMessage']);
            $this->log($r, $orderNumber, 'Ошибка проведения платежа', 'Запрос состояния заказа');

            return false;
        }

        if ((int) $r['OrderStatus'] !== 2) {

            $code_description = PHPShopString::utf8_win1251($r['actionCodeDescription']);
            $this->log($r, $orderNumber, $code_description, 'Запрос состояния заказа');

            return false;
        }

        $this->log($r, $orderNumber, 'Платеж проведен', 'Запрос состояния заказа');

        return true;
    }

    private function applyCurrency($price) {
        $PHPShopSystem = new PHPShopSystem();
        $defaultIso = $PHPShopSystem->getDefaultValutaIso(true);

        if ($defaultIso === 'RUB' || $defaultIso === 'RUR') {
            return $price;
        }

        $orm = new PHPShopOrm($GLOBALS['SysValue']['base']['currency']);
        $rub = $orm->getOne(array('*'), array('iso' => "='RUB' or iso='RUR'"));

        if (!$rub) {
            $this->log(__('Не найдена валюта Российский рубль. Перейдите в Настройки\Валюты, создайте валюту Российский Рубль, ISO код RUB'), '#', 'Ошибка валюты', 'currency');

            return $price;
        }

        return round($price * $rub['kurs']);
    }

}
