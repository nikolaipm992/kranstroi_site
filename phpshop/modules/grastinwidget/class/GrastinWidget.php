<?php

include_once dirname(__FILE__) . '/Request.php';

class GrastinWidget {

    private $CREATE_GRASTIN_METHOD = 'newordercourier';
    private $CREATE_MAIL_METHOD = 'newordermail';
    private $CREATE_HERMES_METHOD = 'neworderhermes';
    private $CREATE_PARTNERS_METHOD = 'neworderpartner';
    private $CREATE_BOXBERRY_METHOD = 'neworderboxberry';
    private $CREATE_5POST_METHOD = 'neworder5post';
    private $FIND_BOXBERRY_POST_CODE_METHOD = 'boxberrypostcode';
    private $xml;
    public $option = array();
    private $orderId;

    function __construct() {
        $PHPShopOrm = new PHPShopOrm('phpshop_modules_grastinwidget_system');

        $this->option = $PHPShopOrm->select();

        $this->Request = new Request();
    }

    public function renderWidget($weight) {
        $frameBody = '<div id="gWidget"
                ' . $this->getCityParam('from_city', 'data-from-city') . '
                ' . $this->getCityParam('to_city', 'data-to-city') . '
                ' . $this->getCheckboxParam('city_from_hide', 'data-from-hide') . '
                ' . $this->getCheckboxParam('city_to_hide', 'data-to-hide') . '
                ' . $this->getCheckboxParam('duration_hide', 'data-no-duration') . '
                ' . $this->getCheckboxParam('weight_hide', 'data-no-weight') . '
                ' . $this->getFeeParam() . '
                ' . $this->getDeliveryAddParam() . '
                ' . $this->getWeightParam($weight) . '
                ' . $this->getNoPartnersParam() . '
                data-css=' . $this->getCssPath() . '
                style="height:500px;">';

        return '<div class="modal fade bs-example-modal" id="grastinwidgetModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" style="width:100%;max-width: 800px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                                <h4 class="modal-title">Доставка</h4>
                            </div>
                            <div class="modal-body" style="width:100%;">
                                ' . $frameBody . '
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success" data-dismiss="modal" disabled="" id="grastin-submit">Подтвердить</button>
                        </div>
                    </div>
                </div>
                <script src="//grastin.ru/widgets/delivery_widget/js/gWidget.js" async></script>
                <style>#gWidget {overflow-x: auto;-webkit-overflow-scrolling: touch;}#gWidget iframe {width:65rem!important}</style>';
    }

    private function getCityParam($city, $type) {
        if (!empty($this->option[$city]))
            $code = $type . '="' . PHPShopString::win_utf8($this->option[$city]) . '"';
        else
            $code = '';

        return $code;
    }

    private function getCheckboxParam($checkbox, $type) {
        if ($this->option[$checkbox] == 1)
            $checkbox = $type . '="1"';
        else
            $checkbox = '';

        return $checkbox;
    }

    private function getFeeParam() {
        if ($this->option['fee_type'] == 1)
            $fee_type = '%';
        else
            $fee_type = '';

        if ((!empty($this->option['fee']) and !empty($this->option['fee_type'])))
            $fee = 'data-add-cost="' . $this->option['fee'] . PHPShopString::win_utf8($fee_type) . '"';
        else
            $fee = '';

        return $fee;
    }

    private function getDeliveryAddParam() {
        if (!empty($this->option['delivery_add']))
            $delivery_add = 'data-add-duration="' . $this->option['delivery_add'] . '"';
        else
            $delivery_add = '';

        return $delivery_add;
    }

    private function getWeightParam($weight = 0) {
        if (empty($weight))
            $weight = $this->option['weight'];

        return 'data-weight-base="' . $weight . '"';
    }

    private function getNoPartnersParam() {
        if (is_array(unserialize($this->option['no_partners'])))
            return 'data-no-partners="' . implode(',', unserialize($this->option['no_partners'])) . '"';
    }

    public function setDataFromOrderEdit($data) {
        $order = unserialize($data['orders']);
        $grastinData = unserialize($data['grastin_order_data']);

        $createdOrder = $this->setOrder($grastinData, $data);

        // Прекращаем выполнение, если заполнены не все данные
        if (!$createdOrder) {
            return false;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<File>' . "\n";
        $xml .= '<API>' . $this->option['api'] . '</API>' . "\n";
        $xml .= '<Method>' . $this->setMethod($grastinData['partner_id']) . '</Method>' . "\n";
        $xml .= '<Orders>' . "\n";
        $xml .= $createdOrder . "\n";
        $xml .= $this->setProducts($order['Cart']['cart'], $order['Person']['discount']) . "\n";
        $xml .= $this->setDelivery($order['Cart']['dostavka']) . "\n";
        $xml .= '</Order>' . "\n";
        $xml .= '</Orders>' . "\n";
        $xml .= '</File>';

        $this->xml = $xml;

        $this->orderId = $data['uid'];
    }

    public function setDataFromDoneHook($obj, $data) {
        // fix order array
        $grastinData = $data['grastin_order_data_new'];
        $data['street'] = $data['street_new'];
        $data['house'] = $data['house_new'];
        $data['flat'] = $data['flat_new'];
        $data['index'] = $data['index_new'];
        $data['uid'] = $data['ouid'];
        $data['tel'] = $data['tel_new'];
        $data['sum'] = $data['sum_new'];
        $data['door_phone'] = $data['door_phone_new'];
        $data['country'] = $data['country_new'];
        $data['state'] = $data['state_new'];
        $data['orders'] = serialize(
                array(
                    'Person' => array(
                        'name_person' => $data['name_new'],
                        'mail' => $data['mail_new'],
                        'order_metod' => $data['order_metod']
                    )
                )
        );

        $createdOrder = $this->setOrder($grastinData, $data);

        // Прекращаем выполнение, если заполнены не все данные
        if (!$createdOrder) {
            return false;
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<File>' . "\n";
        $xml .= '<API>' . $this->option['api'] . '</API>' . "\n";
        $xml .= '<Method>' . $this->setMethod($grastinData['partner_id']) . '</Method>' . "\n";
        $xml .= '<Orders>' . "\n";
        $xml .= $createdOrder . "\n";
        $xml .= $this->setProducts($obj->PHPShopCart->getArray(), $obj->discount) . "\n";
        $xml .= $this->setDelivery($data('grastinSum')) . "\n";
        $xml .= '</Order>' . "\n";
        $xml .= '</Orders>' . "\n";
        $xml .= '</File>';

        $this->xml = $xml;
        $this->orderId = $data['uid'];
    }

    public function setProducts($products, $discount) {
        $xml = '';
        if (count($products) > 0) {
            foreach ($products as $product) {

                if ($discount > 0)
                    $price = $product['price'] - ($product['price'] * $discount / 100);
                else
                    $price = $product['price'];

                if (empty($product['uid']))
                    $product['uid'] = $product['id'];

                $xml .= '<good amount="' . $product['num'] . '" article="' . $product['uid'] . '" cost="' . $price . '" name="' . PHPShopString::win_utf8(str_replace('&', '', $product['name'])) . '"/>';
            }
        }

        return $xml;
    }

    public function setDelivery($delivery) {
        $xml = '<good amount="1" article="1004" cost="' . number_format($delivery, 2, '.', '') . '" name="' . PHPShopString::win_utf8('Доставка') . '"/>';

        return $xml;
    }

    public function send() {

        if (!empty($this->option['api'])) {
            $data = xml2array($this->Request->post($this->xml), false, false);

            if (is_array($data['Order']['Error'])) {
                foreach ($data['Order']['Error'] as $key => $Error) {
                    $data['Order']['Error'][$key] = PHPShopString::utf8_win1251($Error);
                }
                $this->log(
                        array('response' => $data, 'request' => $this->xml), $this->orderId, 'Ошибка передачи заказа', 'Передача заказа службе доставки Grastin', 'error'
                );
                return false;
            }

            $log = array();
            $log['response'] = $data;
            $log['request'] = $this->xml;
            $this->log(
                    $log, $this->orderId, 'Успешная передача заказа', 'Передача заказа службе доставки Grastin', 'success'
            );

            return true;
        }
        return false;
    }

    private function getAddress($city, $street, $house, $flat) {
        $city = str_replace(array('г.', 'город', 'Город'), '', $city);
        $street = str_replace(array('ул.', 'улица', 'Улица'), '', $street);
        $house = str_replace(array('д.', 'дом', 'Дом'), '', $house);
        $flat = str_replace(array('кв.', 'квартира', 'Квартира'), '', $flat);

        $address = '';
        if (!empty($city))
            $address .= 'г. ' . $city;
        if (!empty($street))
            $address .= ', ул. ' . $street;
        if (!empty($house))
            $address .= ', д. ' . $house;
        if (!empty($flat))
            $address .= ', кв. ' . $flat;

        return $address;
    }

    private function setMethod($partner) {
        switch ($partner) {
            case 'post':
                return $this->CREATE_MAIL_METHOD;
                break;
            case 'grastin':
                return $this->CREATE_GRASTIN_METHOD;
                break;
            case 'boxberry':
                return $this->CREATE_BOXBERRY_METHOD;
                break;
            case 'hermes':
                return $this->CREATE_HERMES_METHOD;
                break;
            case 'partner':
                return $this->CREATE_PARTNERS_METHOD;
                break;
            case '5post':
                return $this->CREATE_5POST_METHOD;
                break;
        }
    }

    private function setOrder($grastinData, $order) {
        switch ($grastinData['partner_id']) {
            case 'post':
                return $this->createOrderPost($order);
                break;
            case 'grastin':
                return $this->createOrderGrastin($grastinData, $order);
                break;
            case 'boxberry':
                return $this->createOrderBoxberry($grastinData, $order);
                break;
            case 'hermes':
                return $this->createOrderHermes($grastinData, $order);
                break;
            case 'partner':
                return $this->createOrderPartner($grastinData, $order);
                break;
            case '5post':
                return $this->createOrder5Post($grastinData, $order);
                break;
        }
    }

    private function createOrder5Post($grastinData, $order) {
        $person = unserialize($order['orders']);

        return '<Order number="' . PHPShopString::win_utf8(str_replace('-', '', $order['uid'])) . '" phone="' . str_replace(array('(', ')', ' ', '+', '-'), '', $order['tel']) . '" buyer="' . PHPShopString::win_utf8($this->setBuyer($order)) . '" summa="' . $order['sum'] . '" assessedsumma="' . $order['sum'] . '" sitename="' . $_SERVER['SERVER_NAME'] . '" pickup="' . $grastinData['pvz_id'] . '" email="' . $person['Person']['mail'] . '">' . "\n";
    }

    private function createOrderPost($order) {
        $person = unserialize($order['orders']);

        $shippingDate = new DateTime('+7 days'); //normal use $shippingDate->format('dmY')


        if (!$order['index']) {
            $this->log(array('error' => 'В заказе не заполнен индекс получателя'), $order['uid'], 'error', 'Создание заказа, Почта России');
        }

        return '<Order number="' . PHPShopString::win_utf8(str_replace('-', '', $order['uid'])) . '" address="' . PHPShopString::win_utf8($this->getAddress($order['city'], $order['street'], $order['house'], $order['flat'])) . '" buyer="' . PHPShopString::win_utf8($this->setBuyer($order)) . '" summa="' . $order['sum'] . '" ' . $this->setTest() . ' takewarehouse="' . PHPShopString::win_utf8($this->option['from_city']) . '" email="' . $person['Person']['mail'] . '"  phone="' . str_replace(array('(', ')', ' ', '+', '-'), '', $order['tel']) . '" service="13" assessedsumma="' . $order['sum'] . '" zipcode="' . $order['index'] . '" shippingdate="' . $shippingDate . '" cod="yes" city="' . PHPShopString::win_utf8($order['city']) . '" sitename="' . $_SERVER['SERVER_NAME'] . '">' . "\n";
    }

    private function createOrderGrastin($grastinData, $order) {
        $person = unserialize($order['orders']);

        $shippingDate = new DateTime('+7 days'); //normal use $shippingDate->format('dmY')


        if (empty($grastinData['pvz_id'])) {
            $service = 1;
            $address = PHPShopString::win_utf8($this->getAddress($order['city'], $order['street'], $order['house'], $order['flat']));
        } else {
            $service = 6;
            $address = PHPShopString::win_utf8($grastinData['pvz_id']);
        }

        foreach (unserialize($this->option['payment_service']) as $key => $payments) {
            if (in_array($person['Person']['order_metod'], $payments)) {
                $service = $key;
            }
        }

        return '<Order number="' . PHPShopString::win_utf8(str_replace('-', '', $order['uid'])) . '" address="' . $address . '" buyer="' . PHPShopString::win_utf8($this->setBuyer($order)) . '" summa="' . $order['sum'] . '" ' . $this->setTest() . ' takewarehouse="' . PHPShopString::win_utf8($this->option['from_city']) . '" email="' . $person['Person']['mail'] . '"  phone1="' . str_replace(array('(', ')', ' ', '+', '-'), '', $order['tel']) . '" service="' . $service . '" assessedsumma="' . $order['sum'] . '" shippingdate="' . $shippingDate->format('dmY') . '"  sitename="' . $_SERVER['SERVER_NAME'] . '">' . "\n";
    }

    private function createOrderBoxberry($grastinData, $order) {
        $person = unserialize($order['orders']);

        if (!empty($grastinData['pvz_id']))
            $pvzOrCity = 'pickup="' . $grastinData['pvz_id'] . '"';
        else
            $pvzOrCity = 'postcode="' . $this->getBoxberryCityCode($order['city'], $order['index']) . '"';

        if (!$pvzOrCity) {
            $this->log(array('error' => 'Не найден город доставки с индексом ' . $order['index']), $order['uid'], 'error', 'Получение кода города в системе Boxberry');
        }

        return '<Order number="' . PHPShopString::win_utf8(str_replace('-', '', $order['uid'])) . '" address="' . PHPShopString::win_utf8($this->getAddress($order['city'], $order['street'], $order['house'], $order['flat'])) . '" buyer="' . PHPShopString::win_utf8($this->setBuyer($order)) . '" summa="' . $order['sum'] . '" ' . $this->setTest() . ' takewarehouse="' . PHPShopString::win_utf8($this->option['from_city']) . '" email="' . $person['Person']['mail'] . '"  phone1="' . str_replace(array('(', ')', ' ', '+', '-'), '', $order['tel']) . '" service="2" ' . $pvzOrCity . ' assessedsumma="' . $order['sum'] . '" sitename="' . $_SERVER['SERVER_NAME'] . '">' . "\n";
    }

    private function createOrderHermes($grastinData, $order) {
        $person = unserialize($order['orders']);

        if (!empty($grastinData['pvz_id']))
            $pvz = 'pickup="' . $grastinData['pvz_id'] . '"';
        else
            $pvz = '';

        return '<Order number="' . PHPShopString::win_utf8(str_replace('-', '', $order['uid'])) . '" address="' . PHPShopString::win_utf8($this->getAddress($order['city'], $order['street'], $order['house'], $order['flat'])) . '" buyer="' . PHPShopString::win_utf8($this->setBuyer($order)) . '" summa="' . $order['sum'] . '" ' . $this->setTest() . ' takewarehouse="' . PHPShopString::win_utf8($this->option['from_city']) . '" email="' . $person['Person']['mail'] . '"  phone1="' . str_replace(array('(', ')', ' ', '+', '-'), '', $order['tel']) . '" sitename="' . $_SERVER['SERVER_NAME'] . '" assessedsumma="' . $order['sum'] . '" ' . $pvz . '>' . "\n";
    }

    private function createOrderPartner($grastinData, $order) {
        $person = unserialize($order['orders']);

        if (!empty($grastinData['pvz_id']))
            $pvz = 'pickup="' . $grastinData['pvz_id'] . '"';
        else
            $pvz = '';

        return '<Order number="' . PHPShopString::win_utf8(str_replace('-', '', $order['uid'])) . '" address="' . PHPShopString::win_utf8($this->getAddress($order['city'], $order['street'], $order['house'], $order['flat'])) . '" buyer="' . PHPShopString::win_utf8($this->setBuyer($order)) . '" summa="' . $order['sum'] . '" ' . $this->setTest() . ' takewarehouse="' . PHPShopString::win_utf8($this->option['from_city']) . '" email="' . $person['Person']['mail'] . '"  phone1="' . str_replace(array('(', ')', ' ', '+', '-'), '', $order['tel']) . '" sitename="' . $_SERVER['SERVER_NAME'] . '" assessedsumma="' . $order['sum'] . '" ' . $pvz . '>' . "\n";
    }

    private function setBuyer($order) {
        if (empty($order['fio'])) {
            $person = unserialize($order['orders']);
            return $person['Person']['name_person'];
        }

        return $order['fio'];
    }

    private function setTest() {
        if ($this->option['dev_mode'] == 1) {
            return 'test="yes"';
        }
    }

    private function getBoxberryCityCode($city, $index) {
        $city = trim(str_replace(array('г', 'г.', 'город'), '', $city));
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<File>' . "\n";
        $xml .= '<API>' . $this->option['api'] . '</API>' . "\n";
        $xml .= '<Method>' . $this->FIND_BOXBERRY_POST_CODE_METHOD . '</Method>' . "\n";
        $xml .= '<City>' . iconv("windows-1251", "utf-8", ucfirst(strtolower($city))) . '</City>' . "\n";
        $xml .= '</File>';

        $result = $this->Request->post($xml);

        // Ищем совпадение по индексу
        if (is_array($result['PostcodeBoxberry']) and strlen($index) == 6) {
            foreach ($result['PostcodeBoxberry'] as $item) {
                $name = explode(' ', $item['Name']);

                if ($name[0] == $index) {
                    return $item['Id'];
                }
            }
        }

        return false;
    }

    private function getCssPath()
    {
        if(!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }

        return $protocol . $_SERVER['HTTP_HOST'] . '/phpshop/modules/grastinwidget/templates/style.css';
    }

    public function log($message, $order_id, $status, $type, $status_code = 'succes') {

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['grastinwidget']['grastinwidget_log']);

        $log = array(
            'message_new' => serialize($message),
            'order_id_new' => $order_id,
            'status_new' => $status,
            'type_new' => $type,
            'date_new' => time(),
            'status_code_new' => $status_code
        );

        $PHPShopOrm->insert($log);
    }

    public static function getPartners($partners) {
        return array(
            array('Grastin самовывоз', 'grastinpikup', @in_array('grastinpikup', $partners) ? 'grastinpikup' : ''),
            array('Grastin курьер', 'grastincourier', @in_array('grastincourier', $partners) ? 'grastincourier' : ''),
            array('Boxberry самовывоз', 'boxberrypikup', @in_array('boxberrypikup', $partners) ? 'boxberrypikup' : ''),
            array('Boxberry курьер', 'boxberrycourier', @in_array('boxberrycourier', $partners) ? 'boxberrycourier' : ''),
            array('Hermes самовывоз', 'hermespikup', @in_array('hermespikup', $partners) ? 'hermespikup' : ''),
            array('DPD самовывоз', 'dpdpikup', @in_array('dpdpikup', $partners) ? 'dpdpikup' : ''),
            array('Партнерские ПВЗ', 'partnerpikup', @in_array('partnerpikup', $partners) ? 'partnerpikup' : ''),
            array('5post', '5postpikup', @in_array('5postpikup', $partners) ? '5postpikup' : ''),
            array('Почта России', 'post', @in_array('post', $partners) ? 'post' : ''),
            array('Почта РФ посылка online', 'postpackageonline', @in_array('postpackageonline', $partners) ? 'postpackageonline' : ''),
            array('CDEK самовывоз', 'cdekpikup', @in_array('cdekpikup', $partners) ? 'cdekpikup' : ''),
            array('CDEK курьер', 'cdekcourier', @in_array('cdekcourier', $partners) ? 'cdekcourier' : ''),
            array('CDEK постаматы', 'cdekpostamat', @in_array('cdekpostamat', $partners) ? 'cdekpostamat' : ''),
        );
    }

    public static function getFeeType($fee_type) {
        return array(
            array('%', 1, $fee_type),
            array('Руб.', 2, $fee_type)
        );
    }

    public static function getFromCity($city) {
        return array(
            array('Москва', 'Москва', $city),
            array('Санкт-Петербург', 'Санкт-Петербург', $city)
        );
    }

    public static function getServices() {
        return array(
            1 => 'Доставка без оплаты',
            3 => 'Доставка с кассовым обслуживанием',
            5 => 'Самовывоз без оплаты',
            7 => 'Самовывоз с кассовым обслуживанием',
            8 => 'Большой доставка без оплаты',
            13 => 'Почтовая доставка',
            19 => 'Доставка с оплатой картой'
        );
    }

}