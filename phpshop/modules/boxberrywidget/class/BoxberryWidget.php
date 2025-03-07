<?php

class BoxberryWidget {

    const GET_API_KEY_METHOD = 'GetKeyIntegration';

    public $option;
    public $parameters;
    public $format;

    public function __construct() {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['boxberrywidget']['boxberrywidget_system']);

        /* Опции модуля */
        $this->option = $PHPShopOrm->select();

        /* Данные для передачи */
        $this->parameters = array();

        $system = new PHPShopSystem();
        $this->format = (int) $system->getSerilizeParam('admoption.price_znak');
    }

        /**
     * @param $orderId
     * @throws Exception
     */
    public function getOrderById($orderId)
    {
        $orm = new PHPShopOrm('phpshop_orders');

        $order = $orm->getOne(array('*'), array('id' => "='" . (int) $orderId . "'"));
        if(!$order) {
            throw new \Exception('Заказ не найден');
        }

        return $order;
    }
    
    /**
     * @param array $request
     * @throws Exception
     */
    public function changeAddress($request) {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $this->getOrderById($request['orderId']);

        $cart = unserialize($order['orders']);
        $cart['Cart']['dostavka'] = (float) $request['cost'];
        $sum = $cart['Cart']['sum'] + $cart['Cart']['dostavka'];
        
        $status = unserialize($order['status']);
        $status['maneger']=PHPShopString::utf8_win1251($request['info']);

        $orm->update(['boxberry_pvz_id_new' => $request['pvz'], 'orders_new' => serialize($cart), 'status_new'=>serialize($status), 'sum_new' => $sum], ['id' => "='" . $order['id'] . "'"]);
    }

    public function isPvzDelivery($deliveryId) {
        return in_array($deliveryId, explode(",", $this->option['delivery_id']));
    }

    public function isCourierDeliveryId($deliveryId) {
        return in_array($deliveryId, explode(",", $this->option['express_delivery_id']));
    }

    public function isBoxberryDeliveryMethod($deliveryId) {
        return $this->isPvzDelivery($deliveryId) || $this->isCourierDeliveryId($deliveryId);
    }

    public function request($method, $orderId) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->option['api_url'] . '/json.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'token' => $this->option['token'],
            'method' => $method,
            'sdata' => json_encode($this->parameters)
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), 1);
        if ($data['err']) {
            $this->log(
                    array('error' => $data['err'], 'parameters' => $this->parameters), $orderId, 'Ошибка передачи заказа', 'Передача заказа службе доставки Boxberry', 'error'
            );

            return false;
        }

        if (isset($data['track']))
            $tracking = $data['track'];

        $data['parameters'] = $this->parameters;
        $this->log(
                $data, $orderId, 'Успешная передача заказа', 'Передача заказа службе доставки Boxberry', 'success', $tracking
        );

        return true;
    }

    public function requestGet($method, $data) {

        $data['token'] = $this->option['token'];
        $data['method'] = $method;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->option['api_url'] . '/json.php?' . http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = json_decode(curl_exec($ch), 1);

        return $data;
    }

    public function setData($data, $vid, $discount) {
        $order = unserialize($data['orders']);

        if ($order['Cart']['weight'] < 5)
            $weight = 5;
        else
            $weight = $order['Cart']['weight'];
        
        if($this->option['paid'] == 1)
            $data['paid']==1;

        $this->parameters = array(
            'order_id' => $data['uid'],
            'price' => $data['sum'],
            'delivery_sum' => $order['Cart']['dostavka'],
            'payment_sum' => (int) $data['paid'] === 1 ? 0 : $order['Cart']['dostavka'],
            'vid' => $vid,
            'shop' => array(
                'name' => $data['boxberry_pvz_id'],
                'name1' => $this->option['pvz_id']
            ),
            'customer' => array(
                'fio' => PHPShopString::win_utf8($data['fio']),
                'phone' => PHPShopString::win_utf8($data['tel']),
                'email' => $order['Person']['mail'],
            ),
            'weights' => array(
                'weight' => $weight,
            )
        );

        if ($vid == 2) {
            if (!empty($data['street']))
                $street = ', ' . $data['street'];
            else
                $street = '';
            if (!empty($data['house']))
                $house = ', ' . $data['house'];
            else
                $house = '';
            if (!empty($data['flat']))
                $flat = ', ' . $data['flat'];
            else
                $flat = '';

            $this->parameters['kurdost'] = array(
                'index' => $data['index'],
                'citi' => PHPShopString::win_utf8($data['city']),
                'addressp' => PHPShopString::win_utf8($data['index'] . ', ' . $data['city'] . ', ' . $street . $house . $flat)
            );
        }

        if (!empty($data['org_inn'])) {
            $this->parameters['customer']['name'] = PHPShopString::win_utf8($data['org_name']);
            $this->parameters['customer']['address'] = PHPShopString::win_utf8($data['org_yur_adres']);
            $this->parameters['customer']['inn'] = $data['org_inn'];
            $this->parameters['customer']['kpp'] = PHPShopString::win_utf8($data['org_kpp']);
            $this->parameters['customer']['r_s'] = PHPShopString::win_utf8($data['org_ras']);
            $this->parameters['customer']['bank'] = PHPShopString::win_utf8($data['org_bank']);
            $this->parameters['customer']['kor_s'] = PHPShopString::win_utf8($data['org_kor']);
            $this->parameters['customer']['bik'] = PHPShopString::win_utf8($data['org_bik']);
        }

        $this->setProducts($order['Cart']['cart'], $discount, (int) $data['paid']);
    }

    public function setProducts($cart = array(), $discount = 0, $paid = 0) {
        global $PHPShopSystem;

        if ($PHPShopSystem->getParam('nds_enabled') == '')
            $nds = $nds_delivery = 0;
        else
            $nds = $nds_delivery = $PHPShopSystem->getParam('nds');

        $total = 0;
        if (count($cart) > 0) {
            foreach ($cart as $product) {

                if ($discount > 0 && empty($product['promo_price']))
                    $price = number_format($product['price'] - ($product['price'] * $discount / 100), 2, '.', '');
                else
                    $price = number_format($product['price'], 2, '.', '');

                if (empty($product['ed_izm']))
                    $ed_izm = 'шт.';
                else
                    $ed_izm = $product['ed_izm'];

                $this->parameters['items'][] = array(
                    'id' => $product['id'],
                    'name' => PHPShopString::win_utf8($product['name']),
                    'UnitName' => PHPShopString::win_utf8($ed_izm),
                    'nds' => (int) $nds,
                    'price' => floatval($price),
                    'quantity' => $product['num']
                );
                $total += $price * $product['num'];
            }
            if ($paid !== 1) {
                $this->parameters['payment_sum'] += $total;
            }
        }
    }

    /**
     * @return float
     * @throws Exception
     */
    public function getCourierPrice($zip, $weight, $depth, $height, $width) {
        if ($zip === null || strlen($zip) !== 6) {
            throw new \Exception(__('Неверно введен индекс получателя!'));
        }

        $this->checkZip($zip);

        if (empty($weight))
            $weight = $this->option['weight'];

        $request = $this->requestGet('DeliveryCosts', array(
            'weight' => $weight,
            'depth' => $depth,
            'height' => $height,
            'width' => $width,
            'targetstart' => $this->option['pvz_id'],
            'zip' => $zip
                )
        );

        $fee = $this->option['fee'];

        if (empty($fee)) {
            return round($request['price'], $this->format);
        }

        if ((int) $this->option['fee_type'] == 1) {
            return round($request['price'] + ($request['price'] * $fee / 100), $this->format);
        }

        return round($request['price'] + $fee, $this->format);
    }

    /**
     * Проверка возможности доставки по заданному индексу
     * @param string $zip индекс
     */
    public function checkZip($zip) {
        $data['Zip'] = $zip;
        $request = $this->requestGet('ZipCheck', $data);

        if ((int) $request[0]['ExpressDelivery'] !== 1) {
            throw new \Exception(__('Доставка по данному индексу не возможна.'));
        }
    }

    /**
     * Запись лога
     * @param array $message содержание запроса в ту или иную сторону
     * @param string $order_id id заказа
     * @param string $status статус отправки
     * @param string $type request
     */
    public function log($message, $order_id, $status, $type, $status_code = 'succes', $traking = '') {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['boxberrywidget']['boxberrywidget_log']);

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time(),
            'status_code_new' => $status_code,
            'tracking_new' => $traking
        );
        $PHPShopOrm->insert($log);
    }

}
