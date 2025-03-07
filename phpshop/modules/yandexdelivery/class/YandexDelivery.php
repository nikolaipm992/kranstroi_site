<?php

/**
 * ���������� ������ � ������ ��������� API 2.0
 * @author PHPShop Software
 * @version 1.2
 * @package PHPShopModules
 * @todo https://yandex.ru/support2/delivery-profile/ru/modules/widgets
 */
class YandexDelivery {

    const BASE_URL = 'https://b2b-authproxy.taxi.yandex.net';
    const STATUS_ORDER_PREPARED = 'prepared';
    const STATUS_ORDER_CREATED = 'created';

    public function __construct() {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_yandexdelivery_system');
        $this->options = $PHPShopOrm->select();
        $this->TOKEN = $this->options['token'];
        $this->STATION_ID = $this->options['warehouse_id'];
        $system = new PHPShopSystem();
        $this->format = (int) $system->getSerilizeParam('admoption.price_znak');
    }

    public function changeAddress($request) {
        $orm = new PHPShopOrm('phpshop_orders');
        $order = $this->getOrderById($request['orderId']);

        $cart = unserialize($order['orders']);
        $cart['Cart']['dostavka'] = (float) $request['cost'];
        $sum = $cart['Cart']['sum'] + $cart['Cart']['dostavka'];

        $yandex = unserialize($order['yadelivery_order_data']);
        $yandex['delivery_info'] = PHPShopString::utf8_win1251($request['info']);

        $status = unserialize($order['status']);
        $status['maneger'] = PHPShopString::utf8_win1251($request['info']);

        $orm->update(['orders_new' => serialize($cart), 'yadelivery_order_data_new' => serialize($yandex), 'status_new' => serialize($status), 'sum_new' => $sum], ['id' => "='" . $order['id'] . "'"]);
    }

    /**
     * @param $orderId
     * @throws Exception
     */
    public function getOrderById($orderId) {
        $orm = new PHPShopOrm('phpshop_orders');

        $order = $orm->getOne(array('*'), array('id' => "='" . (int) $orderId . "'"));
        if (!$order) {
            throw new \Exception('����� �� ������');
        }

        return $order;
    }

    /**
     * @param $deliveryId
     * @return bool
     */
    public function isYandexDeliveryMethod($deliveryId) {
        if ($deliveryId == $this->options['delivery_id'])
            return true;
    }

    public function buildOrderTab($order) {
        global $PHPShopGUI;

        $yandex = unserialize($order['yadelivery_order_data']);
        $disabledSettings = '';
        if ($yandex['status'] === self::STATUS_ORDER_CREATED or ! empty($order['tracking'])) {
            PHPShopParser::set('yadelivery_hide_actions', 'display: none;');
            $disabledSettings = 'disabled="disabled"';
            $yandex['status_text'] = '��������� � ��������';
        }

        if ($this->options['paid'] == 1)
            $order['paid'] = 1;

        $orderInfo = PHPShopText::tr(
                        __('������ ������'), '<span class="yandex-status">' . __($yandex['status_text']) . '</span>'
                ) .
                PHPShopText::tr(
                        __('����� �������� � �������'), '<span>' . $yandex['delivery_info'] . '</span>'
                ) .
                PHPShopText::tr(
                        __('������ ������'), $PHPShopGUI->setCheckbox("yandex_payment_status", 1, '����� �������', (int) $order['paid'], $disabledSettings)
        );

        PHPShopParser::set('yadelivery_order_info', PHPShopText::table($orderInfo, 3, 1, 'left', '100%', false, 0, 'yadelivery-table', 'list table table-striped table-bordered'));
        PHPShopParser::set('yadelivery_order_id', $order['id']);

        return ParseTemplateReturn(dirname(__DIR__) . '/templates/order.tpl', true);
    }

    /**
     * ������� ��� ��������
     * @return int
     */
    public function getAddPrice($request) {


        $fee = $this->option['fee'];

        if (empty($fee)) {
            return round($request['pricing_total'], $this->format);
        }

        if ((int) $this->option['fee_type'] == 1) {
            return round($request['pricing_total'] + ($request['pricing_total'] * $fee / 100), $this->format);
        }

        return round($request['pricing_total'] + $fee, $this->format);
    }

    public function getDefaultDimensions() {
        return [
            'weight' => $this->options['weight'], //�
            'length' => $this->options['length'], //��
            'height' => $this->options['height'],
            'width' => $this->options['width'],
        ];
    }

    /**
     * @param $url
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    private function request($url, $data = []) {
        $curl = curl_init();

        $data['access_token'] = $this->TOKEN;

        curl_setopt_array($curl, [
            CURLOPT_URL => static::BASE_URL . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $this->TOKEN,
                "content-type: application/json"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $result = json_decode($response, true);

        if ($err) {
            throw new Exception("cURL Error #:" . $err);
        } else {
            return $result;
        }
    }

    public static function getDeliveryStatuses($current) {
        $statusesObj = new PHPShopOrderStatusArray();
        $statuses = $statusesObj->getArray();

        $result = [['����� �����', 0, $current]];
        foreach ($statuses as $status) {
            $result[] = [$status['name'], $status['id'], $current];
        }

        return $result;
    }

    public static function getDeliveryVariants($current) {
        $deliveriesObj = new PHPShopDeliveryArray(['is_folder' => "!='1'", 'enabled' => "='1'"]);

        $deliveries = $deliveriesObj->getArray();
        $result = [];
        if (is_array($deliveries)) {
            foreach ($deliveries as $delivery) {

                if (strpos($delivery['city'], '.')) {
                    $name = explode(".", $delivery['city']);
                    $delivery['city'] = $name[0];
                }

                if (in_array($delivery['id'], @explode(",", $current)))
                    $delivery_id = $delivery['id'];
                else
                    $delivery_id = null;

                $result[] = [$delivery['city'], $delivery['id'], $delivery_id];
            }
        }

        return $result;
    }

    public function getApproxDeliveryPrice(YandexDeliveryOrderData $data) {

        if ($this->options['paid'] == 1) {
            $payment_method = 'already_paid';
        } else {
            $payment_method = 'card_on_receipt';
        }


        $response = $this->request('/api/b2b/platform/pricing-calculator', [
            'client_price' => 0,
            'destination' => [
                'platform_station_id' => $data->delivery_variant_id,
            ],
            'payment_method' => $payment_method,
            'source' => [
                'platform_station_id' => $this->STATION_ID,
            ],
            'tariff' => 'self_pickup',
            'total_assessed_price' => (int) ($data->total_price * 100),
            'total_weight' => (int) ($data->weight),
        ]);

        //$this->log($response, null, '/api/b2b/platform/pricing-calculator', null);

        $pricing_total = explode(' ', (int) $response['pricing_total'])[0];

        return ceil($pricing_total + $this->getAddPrice($pricing_total));
    }

    /**
     * @param YandexDeliveryOrderData $data
     * @throws Exception
     */
    public function order(YandexDeliveryOrderData $data) {
        $dimensions = $this->getDefaultDimensions();

        $dimensions['weight'] = $data->weight;

        $order_lines = [];
        foreach ($data->products as $product) {
            $order_lines[] = [
                'article' => $product['article'], //�������
                'billing_details' => [
                    'assessed_unit_price' => (int) ($product['price'] * 100),
                    'nds' => -1,
                    'unit_price' => (int) ($product['price'] * 100),
                ],
                'count' => (int) $product['quantity'],
                'name' => $product['name'],
                'place_barcode' => $data->order_id . '_1',
            ];
        }

        if ($this->options['paid'] == 1 or $data->paid == 1) {
            $billing_info = [
                'payment_method' => 'already_paid',
            ];
        } else {
            $billing_info = [
                'payment_method' => 'card_on_receipt',
                'delivery_cost' => $data->sum
            ];
        }

        $params = [
            'billing_info' => $billing_info,
            'destination' => [
                'type' => 'platform_station',
                'platform_station' => [
                    'platform_id' => $data->delivery_variant_id,
                ]
            ],
            'info' => [
                'operator_request_id' => $data->order_id . '_' . mt_rand(1000, 9999),
            ],
            'items' => $order_lines,
            'last_mile_policy' => 'self_pickup',
            'places' => [
                [
                    'barcode' => $data->order_id . '_1',
                    'physical_dims' => [
                        'dx' => (int) ($dimensions['length']), //cm
                        'dy' => (int) ($dimensions['height']),
                        'dz' => (int) ($dimensions['width']),
                        'weight_gross' => (int) $dimensions['weight'], //gram
                    ],
                ],
            ],
            'recipient_info' => [
                'email' => (string) $data->email,
                'first_name' => (string) $data->name,
                'phone' => (string) $data->phone,
            ],
            'source' => [
                'platform_station' => [
                    'platform_id' => $this->STATION_ID,
                ],
            ],
        ];

        $result = $this->request('/api/b2b/platform/offers/create?send_unix=true', $params);
        $offers = $result['offers']; //list delivery offers
        //�������� ����� �������� � ������������
        if (!empty($offers[0])) {
            $status = __('�������� �������� ������');
            $confirm = $this->confirmOrder($offers[0]['offer_id']);
            $this->updateStatus($data->order_id);
            $log['result'] = $offers[0];
        } else {
            $status = __('������ �������� ������');
            $log['result'] = $result;
        }

        $log['params'] = $params;

        $this->log($log, $data->order_id, '/api/b2b/platform/offers/create?send_unix=true', $status);

        return $confirm;
    }

    /*
     * ���������� ������� ��������
     */

    public function updateStatus($id) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
        $order = $PHPShopOrm->getOne(['*'], ['id' => '=' . (int) $id]);
        $yandex = unserialize($order['yadelivery_order_data']);
        $yandex['status'] = self::STATUS_ORDER_CREATED;
        $PHPShopOrm->update(['yadelivery_order_data_new' => serialize($yandex)], ['id' => '=' . (int) $id]);
    }

    public function log($message, $id, $type, $status) {


        $PHPShopOrm = new PHPShopOrm('phpshop_modules_yandexdelivery_log');

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $id,
            'type_new' => $type,
            'date_new' => time(),
            'status_new' => $status
        );

        $PHPShopOrm->insert($log);
    }

    public function confirmOrder($offer_id) {
        $result = $this->request('/api/b2b/platform/offers/confirm', [
            'offer_id' => $offer_id,
        ]);

        return $result['request_id'];
    }

    public function setDataFromOrderEdit($data) {

        $order = unserialize($data['orders']);
        $yandexlivery_data = unserialize($data['yadelivery_order_data']);

        $def_dimensions = $this->getDefaultDimensions();

        if (empty($order['Cart']['weight']))
            $weight = $def_dimensions['weight'];
        else
            $weight = $order['Cart']['weight'];
        if (empty($data['fio']))
            $name = $order['Person']['name_person'];
        else
            $name = $data['fio'];

        $delivery_data = new YandexDeliveryOrderData();
        $delivery_data->order_id = $this->order_id = $data['uid'];
        $delivery_data->name = PHPShopString::win_utf8($name);
        $delivery_data->phone = str_replace(array('(', ')', ' ', '+', '-'), '', $data['tel']);

        $matches = [];
        if (preg_match('#[7]?(\d{3})(\d{3})(\d{2})(\d{2})#', $delivery_data->phone, $matches)) {
            $delivery_data->phone = '+7' . '(' . $matches[1] . ')' . $matches[2] . '-' . $matches[3] . '-' . $matches[4];
        }

        $delivery_data->weight = $weight;
        $delivery_data->email = $order['Person']['mail'];
        $delivery_data->address = PHPShopString::win_utf8($yandexlivery_data['address']);
        $delivery_data->delivery_variant_id = $yandexlivery_data['pvz_id'];
        $delivery_data->paid = $order['paid'];
        $delivery_data->sum = (int) $order['sum'];

        foreach ($order['Cart']['cart'] as $product) {
            $delivery_data->products[] = [
                'name' => PHPShopString::win_utf8($product['name']),
                'quantity' => $product['num'],
                'price' => $product['price'],
                'article' => $product['uid'] ? $product['uid'] : $product['id'],
            ];
        }

        try {
            return $this->order($delivery_data);
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }

        return false;
    }

}

class YandexDeliveryOrderData {

    public $order_id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $delivery_variant_id;
    public $weight;
    public $total_price;

    /**
     * @var array
     * [
     *  [
     *      name,
     *      article,
     *      price,
     *      quantity
     *  ]
     * ]
     */
    public $products = [];

}
