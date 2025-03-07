<?php

class Shiptor
{
    const API_URL = 'https://api.shiptor.ru/shipping/v1';
    const ADD_PACKAGE_METHOD = 'addPackage';
    const STATUS_SENT = 'sent';
    const STATUS_NEW = 'new';

    public $options;

    public function __construct() {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_shiptor_system');
        $this->options = $PHPShopOrm->select();
    }

    public function isShiptorDeliveryMethod($deliveryId)
    {
        return (int) $deliveryId === (int) $this->options['delivery_id'];
    }

    public function getDimensions($cart)
    {
        return array(
            'length'  => (float) str_replace(',' , '.', $this->getMaxDimension($cart, 'length')),
            'width'   => (float) str_replace(',' , '.', $this->getMaxDimension($cart, 'width')),
            'height'  => (float) str_replace(',' , '.', $this->getMaxDimension($cart, 'height')),
        );
    }

    public static function getRoundVariants($current)
    {
        return array(
            array('Математическое округление', 'math', $current),
            array('Всегда в меньшую сторону', 'floor', $current),
            array('Всегда в большую сторону', 'ceil', $current)
        );
    }

    public static function getCompanyVariants($current)
    {
        $current = unserialize($current);
        if(!is_array($current)) {
            $current = array();
        }

        return array(
            array('Shiptor Today', 'shiptor_today', in_array('shiptor_today', $current) ? 'shiptor_today' : ''),
            array('Shiptor Курьер', 'shiptor_courier', in_array('shiptor_courier', $current) ? 'shiptor_courier' : ''),
            array('Shiptor Курьер за МКАД', 'shiptor_courier_za_mkad', in_array('shiptor_courier_za_mkad', $current) ? 'shiptor_courier_za_mkad' : ''),
            array('Shiptor Самовывоз', 'shiptor_pvz', in_array('shiptor_pvz', $current) ? 'shiptor_pvz' : ''),
            array('DPD Курьер (Авто)', 'dpd_auto', in_array('dpd_auto', $current) ? 'dpd_auto' : ''),
            array('DPD Курьер (Авиа)', 'dpd_avia', in_array('dpd_avia', $current) ? 'dpd_avia' : ''),
            array('DPD Самовывоз', 'dpd_pvz', in_array('dpd_pvz', $current) ? 'dpd_pvz' : ''),
            array('DPD Самовывоз (dpd_pvz_c)', 'dpd_pvz_c', in_array('dpd_pvz_c', $current) ? 'dpd_pvz_c' : ''),
            array('СДЭК Курьер', 'cdek_courier', in_array('cdek_courier', $current) ? 'cdek_courier' : ''),
            array('СДЭК Самовывоз', 'cdek_pvz', in_array('cdek_pvz', $current) ? 'cdek_pvz' : ''),
            array('CDEK ПВЗ Эконом', 'cdek_pvz_e', in_array('cdek_pvz_e', $current) ? 'cdek_pvz_e' : ''),
            array('IML Курьер', 'iml_courier', in_array('iml_courier', $current) ? 'iml_courier' : ''),
            array('IML Самовывоз', 'iml_pvz', in_array('iml_pvz', $current) ? 'iml_pvz' : ''),
            array('Pickpoint', 'pickpoint', in_array('pickpoint', $current) ? 'pickpoint' : ''),
            array('BoxBerry Курьер', 'boxberry_courier', in_array('boxberry_courier', $current) ? 'boxberry_courier' : ''),
            array('BoxBerry Самовывоз', 'boxberry_pvz', in_array('boxberry_pvz', $current) ? 'boxberry_pvz' : ''),
            array('Сквозной DPD Дверь-Дверь', 'dpd_courier_dd', in_array('dpd_courier_dd', $current) ? 'dpd_courier_dd' : ''),
            array('Сквозной DPD ПВЗ-Дверь', 'dpd_courier_td', in_array('dpd_courier_td', $current) ? 'dpd_courier_td' : ''),
            array('Сквозной DPD Дверь-ПВЗ', 'dpd_pvz_dt', in_array('dpd_pvz_dt', $current) ? 'dpd_pvz_dt' : ''),
            array('Сквозной DPD ПВЗ-ПВЗ', 'dpd_pvz_tt', in_array('dpd_pvz_tt', $current) ? 'dpd_pvz_tt' : ''),
            array('Сквозной СДЭК Дверь-Дверь', 'cdek_courier_dd', in_array('cdek_courier_dd', $current) ? 'cdek_courier_dd' : ''),
            array('Сквозной СДЭК ПВЗ-Дверь', 'cdek_courier_td', in_array('cdek_courier_td', $current) ? 'cdek_courier_td' : ''),
            array('Сквозной СДЭК Дверь-ПВЗ', 'cdek_pvz_dt', in_array('cdek_pvz_dt', $current) ? 'cdek_pvz_dt' : ''),
            array('Сквозной СДЭК ПВЗ-ПВЗ', 'cdek_pvz_tt', in_array('cdek_pvz_tt', $current) ? 'cdek_pvz_tt' : ''),
            array('Сберпосылка', 'sberlogistics_pvz', in_array('sberlogistics_pvz', $current) ? 'sberlogistics_pvz' : ''),
            array('Сберкурьер Дверь-Дверь', 'sberlogistics_dd', in_array('sberlogistics_dd', $current) ? 'sberlogistics_dd' : ''),
            array('Сберкурьер ПВЗ-Дверь', 'sberlogistics_td', in_array('sberlogistics_td', $current) ? 'sberlogistics_td' : ''),
            array('Сберпосылка Дверь-ПВЗ', 'sberlogistics_pvz_dp', in_array('sberlogistics_pvz_dp', $current) ? 'sberlogistics_pvz_dp' : ''),
            array('Сберкурьер', 'sber_courier', in_array('sber_courier', $current) ? 'sber_courier' : ''),
            array('Shiptor Почта', 'post', in_array('post', $current) ? 'post' : ''),
            array('Shiptor Почта (russian_post_main)', 'russian_post_main', in_array('russian_post_main', $current) ? 'russian_post_main' : ''),
            array('Почта «Курьер онлайн»', 'russian_post_courier_online', in_array('russian_post_courier_online', $current) ? 'russian_post_courier_online' : ''),
            array('Почта «Посылка онлайн»', 'russian_post_parcel_online', in_array('russian_post_parcel_online', $current) ? 'russian_post_parcel_online' : '')
        );
    }

    public function send($order)
    {
        $shiptorData = unserialize($order['shiptor_order_data']);
        $cart = unserialize($order['orders']);

        if(isset($shiptorData['status']) && $shiptorData['status'] === self::STATUS_SENT) {
            return;
        }

        $products = $this->getProducts($cart['Cart']['cart'], $cart['Person']['discount']);

        if(empty($order['fio']))
            $fio = $cart['Person']['name_person'];
        else
            $fio = $order['fio'];
        $fioArr = explode(' ', $fio);

        $address = array();
        if($shiptorData['type'] === 'courier') {
            if(!empty($order['index'])) {
                $address[] = $order['index'];
            }
            if(!empty($order['city'])) {
                $address[] = PHPShopString::win_utf8($order['city']);
            }
            if(!empty($order['street'])) {
                $address[] = PHPShopString::win_utf8($order['street']);
            }
            if(!empty($order['house'])) {
                $address[] = PHPShopString::win_utf8($order['house']);
            }
            if(!empty($order['flat'])) {
                $address[] = PHPShopString::win_utf8($order['flat']);
            }
        }

        $parameters = array(
            'id'      => 'JsonRpcClient.js',
            'jsonrpc' => '2.0',
            'method'  => self::ADD_PACKAGE_METHOD,
            'params' => array(
                'length'        => $this->getMaxDimension($cart['Cart']['cart'], 'length'),
                'width'         => $this->getMaxDimension($cart['Cart']['cart'], 'width'),
                'height'        => $this->getMaxDimension($cart['Cart']['cart'], 'height'),
                'weight'        => $products['weight'],
                'cod'           => (int) $order['paid'] === 1 ? 0 : $order['Cart']['dostavka'],
                'declared_cost' => (int) $order['paid'] === 0 ? $cart['Cart']['sum'] : ((int) $cart['Cart']['sum'] * $this->options['declared_percent']) / 100,
                'departure' => array(
                    'shipping_method' => (int) $shiptorData['method_id'],
                    'address' => array(
                        'country'        => 'RU',
                        'name'           => PHPShopString::win_utf8($fioArr[1]),
                        'surname'        => PHPShopString::win_utf8($fioArr[0]),
                        'patronymic'     => !empty($fioArr[2]) ? PHPShopString::win_utf8($fioArr[2]) : '',
                        'receiver'       => PHPShopString::win_utf8($fio),
                        'email'          => $cart['Person']['mail'],
                        'phone'          => str_replace(array('(', ')', ' ', '+', '-', '&#43;'), '', $order['tel']),
                        'address_line_1' => $shiptorData['type'] === 'pvz' ? PHPShopString::win_utf8('Доставка в ПВЗ.') : implode(', ', $address)
                    )
                ),
                'products' => $products['products']
            )
        );

        if($shiptorData['type'] === 'pvz') {
            $parameters['params']['departure']['delivery_point'] = $shiptorData['pvz_id'];
        }

        if(!empty($shiptorData['kladr'])) {
            $parameters['params']['departure']['address']['kladr_id'] = $shiptorData['kladr'];
        } else {
            $parameters['params']['departure']['address']['administrative_area'] = PHPShopString::win_utf8($order['state']);
            $parameters['params']['departure']['address']['settlement'] = PHPShopString::win_utf8($order['city']);
        }

        $result = $this->request($parameters);

        if(isset($result['result']['status']) && $result['result']['status'] === 'new') {
            $this->log(
                array('response' => $result, 'parameters' => $parameters),
                $order['id'],
                'Успешная передача заказа',
                'Передача заказа в Shiptor',
                'success'
            );
            $shiptorData['status'] = self::STATUS_SENT;

            $orm = new PHPShopOrm('phpshop_orders');
            $orm->update(array(
                'shiptor_order_data_new' => serialize($shiptorData),
                'tracking_new'           => $result['result']['tracking_number']
            ), array('id' => sprintf('="%s"', $order['id'])));
        } else {
            $this->log(
                array('response' => $result, 'parameters' => $parameters),
                $order['id'],
                'Ошибка передачи заказа',
                'Передача заказа в Shiptor',
                'fail'
            );
        }
    }

    private function request($parameters = array())
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'x-authorization-token: ' . $this->options['private_api_key'],
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($parameters))
        ));
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    private function getDimension($field, $productId, $parent = null)
    {
        $product = new PHPShopProduct((int) $productId);
        $param = $product->getParam($field);

        if(!empty($param)) {
            return $param;
        }

        if(is_null($parent) === false) {
            $product = new PHPShopProduct((int) $parent);
            $parentParam = $product->getParam($field);
            if(!empty($parentParam)) {
                return $product->getParam($field);
            }
        }

        return $this->options[$field];
    }

    private function getMaxDimension($cart, $side)
    {
        $maxDimension = 0;
        foreach ($cart as $cartItem) {
            $productDimension = $this->getDimension($side, $cartItem['id'], $cartItem['parent']);
            if($productDimension > $maxDimension) {
                $maxDimension = $productDimension;
            }
        }

        return $maxDimension;
    }

    public function getProducts($cart = array(), $discount = 0)
    {
        $products = array();
        $cartWeight = 0;
        foreach ($cart as $product) {
            if($discount > 0 && empty($product['promo_price']))
                $price = $product['price']  - ($product['price']  * $discount  / 100);
            else
                $price = $product['price'];

            if(empty($product['weight']))
                $weight = $this->options['weight'];
            else
                $weight = $product['weight'];

            $products[] = array(
                'name'        => PHPShopString::win_utf8($product['name']),
                'shopArticle' => !empty($product['uid']) ? PHPShopString::win_utf8($product['uid']) : $product['id'],
                'count'       => $product['num'],
                'price'       => $price
            );
            $cartWeight += $weight;
        }

        return array('items' => $products, 'weight' => $cartWeight);
    }

    private function log($message, $order_id, $status, $type, $status_code = 'succes') {

        $PHPShopOrm = new PHPShopOrm('phpshop_modules_shiptor_log');

        $log = array(
            'message_new'     => serialize($message),
            'order_id_new'    => $order_id,
            'status_new'      => $status,
            'type_new'        => $type,
            'date_new'        => time(),
            'status_code_new' => $status_code
        );

        $PHPShopOrm->insert($log);
    }
}