<?php

/**
 * Синхронизация с 1C
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.3
 */
$_classPath = '../../phpshop/';
include($_classPath . 'class/obj.class.php');
include($_classPath . "lib/phpass/passwordhash.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("xml");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("basexml");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("string");

/*
  $_POST['log'] = 'admin';
  $_POST['pas'] = '49O50O51O52O53O54OI10O';

  $_POST['sql'] = '<?xml version="1.0" encoding="windows-1251"?>
  <phpshop>
  <sql>
  <from>null</from>
  <method>ofd</method>
  <vars>
  <uid>'.rand(10000,20000).'</uid>
  <id>'.rand(10000,20000).'</id>
  <operation>sell</operation>
  <json><![CDATA[
  {
  "receipt": {
  "attributes": {
  "email": "dennion@yandex.ru",
  "phone": "89024562233"
  },
  "items": [
  {
  "name": "Товар 1",
  "price": 1499.99,
  "num": 2.00,
  "sum": 2999.98
  },
  {
  "name": "Товар 2",
  "price": 300.00,
  "num": 1.00,
  "sum": 300.00
  }
  ],
  "total": 3299.98,
  "payments": [
  {
  "type": 1,
  "sum": 3299.98
  }
  ]
  }
  }
  ]]></json>
  </vars>
  </sql>
  </phpshop>';

  $_POST['sql']='<?xml version="1.0" encoding="windows-1251"?>
  <phpshop>
  <sql>
  <from>table_name1</from>
  <method>update</method>
  <vars>
  <fio>Петя</fio>
  <tel>1050540</tel>
  <country>Россия</country>
  <city>Москва</city>
  <index>Москва</index>
  <house>55</house>
  <porch>4</porch>
  <street>Суздальская</street>
  <flat>22</flat>
  <org_name>ООО "ПХПШОП"</org_name>
  <org_inn>11111111111111111</org_inn>
  <org_kpp>2222222222222</org_kpp>
  <org_yur_adres>Щербаковская 4</org_yur_adres>
  <org_fakt_adres>Рязанский проспект</org_fakt_adres>
  <org_ras>333333333333</org_ras>
  <org_bank>Альфа Банк</org_bank>
  <org_kor>444444444444</org_kor>
  <org_bik>555555</org_bik>
  <org_bik>org_city</org_bik>
  <delivtime>org_city</delivtime>
  <statusi>1</statusi>
  <dop_info>1</dop_info>
  </vars>
  <where>id=4</where>
  </sql>
  </phpshop>';

  $_POST['sql']='<?xml version="1.0" encoding="windows-1251"?>
  <phpshop>
  <sql>
  <from>table_name1</from>
  <method>order</method>
  <vars>
  <art>87</art>
  <item>1</item>
  <price>4271.59</price>
  <currency>RUR</currency>
  </vars>
  <where>id=4</where>
  </sql>
  </phpshop>';

  $_POST['_sql']='<?xml version="1.0" encoding="windows-1251"?>
  <phpshop>
  <sql>
  <method>select</method>
  <from>table_name2</from>
  <vars>id,name</vars>
  <where>(name REGEXP "mp3" or description REGEXP "mp3" or id=0) and (category=1 and items>10)</where>
  <order>id</order>
  <limit>2</limit>
  </sql>
  </phpshop>';
 */

// Подключаем БД
$PHPShopBase = new PHPShopBase($_classPath . 'inc/config.ini', true, true);

// Настройки модулей
$PHPShopModules = new PHPShopModules($_classPath . "modules/");

// Валюты
$PHPShopValutaArray = new PHPShopValutaArray();

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

class PHPShop1C extends PHPShopBaseXml {

    function __construct() {
        global $PHPShopModules;

        $this->debug = false;
        $this->PHPShopModules = $PHPShopModules;
        $this->true_method = array('select', 'option', 'insert', 'update', 'delete', 'image', 'order', 'ofd');
        $this->true_from = array('table_name', 'table_name1', 'table_name2', 'table_name3', 'table_name24',
            'table_name5', 'table_name6', 'table_name7', 'table_name8', 'table_name11',
            'table_name14', 'table_name15', 'table_name17', 'table_name27', 'table_name29', 'table_name32',
            'table_name9', 'table_name48', 'table_name50', 'table_name51', 'table_name35', 'null');

        parent::__construct();
    }

    function ofd() {
        global $_classPath;

        $vars = readDatabase($this->sql, "vars", false);
        $data = json_fix_utf(json_decode(PHPShopString::win_utf8($vars[0]['json']), true));

        $ofd = 'atol';
        include_once($_classPath . 'modules/' . substr($ofd, 0, 15) . '/api.php');

        if (is_array($data)) {

            // № Чека
            $data['id'] = $vars[0]['id'];
            $data['uid'] = $vars[0]['uid'];

            $this->PHPShopModules->checkInstall('atol');
            $status = OFDStart($data, $vars[0]['operation'], true);
            echo json_encode(array('status' => $status['status'], 'data' => $status['data']));
        }
    }

    function order() {

        // Массив данных для вставки
        $vars = readDatabase($this->sql, "vars", false);
        $update = false;
        $PHPShopOrm = new PHPShopOrm($this->PHPShopBase->getParam('base.' . $this->xml['from']));
        $PHPShopOrm->debug = $this->debug;
        $order_data = $PHPShopOrm->select(array('orders'), $this->xml['where'], $this->xml['order'], $this->xml['limit']);
        $orders = unserialize($order_data["orders"]);

        if (is_array($orders['Cart']['cart']))
            foreach ($orders['Cart']['cart'] as $key => $product)
                if ($product['uid'] == $vars[0]['art']) {

                    $orders['Cart']['cart'][$key]['num'] = $vars[0]['item'];
                    $update = true;

                    // Удаление товара при нулевов кол-ве
                    if (empty($orders['Cart']['cart'][$key]['num']))
                        unset($orders['Cart']['cart'][$key]);
                }

        // Добавление нового товара
        if (empty($update) and !empty($vars[0]['item'])) {

            $PHPShopOrm = new PHPShopOrm($this->PHPShopBase->getParam('base.table_name2'));
            $PHPShopOrm->debug = $this->debug;
            $data = $PHPShopOrm->select(array('*'), array('uid' => '="' . $vars[0]['art'] . '"'), false, array('limit' => 1));

            if (is_array($data)) {

                // Если цена товара пришла из 1С
                if (PHPShopSecurity::true_param($vars[0]['price'], $vars[0]['currency'])) {
                    //$data['price'] = PHPShopProductFunction::GetPriceValuta($data['id'], $vars[0]['price'], $vars[0]['currency']);
                    $data['price'] = $vars[0]['price'];
                }
                $orders['Cart']['cart'][$data['id']] = array(
                    'id' => $data['id'],
                    'name' => $data['name'],
                    'price' => $data['price'],
                    'uid' => $vars[0]['art'],
                    'num' => $vars[0]['item'],
                    'weight' => $data['weight'],
                    'user' => $data['user']);
            }
            else
                exit("Error Art");
        }


        // Скидка
        if (PHPShopSecurity::true_param($vars[0]['discount'])) {
            $orders['Person']['discount'] = $vars[0]['discount'];
        }

        // Пересчет общих данных корзины
        $num = $sum = $weight = 0;
        if (is_array($orders['Cart']['cart'])) {
            foreach ($orders['Cart']['cart'] as $product) {
                $num+=$product['num'];
                $sum+=$product['num'] * $product['price'];
                $weight+=$product['num'] * $product['weight'];
            }
            $orders['Cart']['num'] = $num;
            $orders['Cart']['sum'] = $sum;
            $orders['Cart']['weight'] = $weight;
        }

        $order_data["orders_new"] = serialize($orders);

        // Итого
        $order_data['sum_new'] = $orders['Cart']['sum'] + $orders['Cart']['dostavka'];

        // Запись обновленного заказа
        $PHPShopOrm = new PHPShopOrm($this->PHPShopBase->getParam('base.' . $this->xml['from']));
        $PHPShopOrm->debug = $this->debug;
        $this->data = $PHPShopOrm->update($order_data, $this->xml['where']);
    }

    function decode($code) {
        $decode = substr($code, 0, strlen($code) - 4);
        $decode = str_replace("I", 11, $decode);
        $decode = explode("O", $decode);
        $disp_pass = "";
        for ($i = 0; $i < (count($decode) - 1); $i++)
            $disp_pass.=chr($decode[$i]);
        return $disp_pass;
    }

    function admin() {
        $hasher = new PasswordHash(8, false);

        $PHPShopOrm = new PHPShopOrm($this->PHPShopBase->getParam('base.table_name19'));
        $PHPShopOrm->debug = $this->debug;
        $data = $PHPShopOrm->select(array('login,password,status'), array('enabled' => "='1'"), false, array('limit' => 30));
        if (is_array($data)) {
            foreach ($data as $v)
                if ($_POST['log'] == $v['login'] and $hasher->CheckPassword($this->decode($_POST['pas']), $v['password'])) {
                    $this->user_status = unserialize($v['status']);
                    return true;
                }
        }
        return false;
    }

    // Проверка прав
    function status($from, $flag) {
        global $PHPShopModules;
        $path_core = explode("_", $this->PHPShopBase->getParam('base.' . $from));
        $path_mod = explode("_", $PHPShopModules->getParam('base.' . $from));
        $path = array_merge($path_core, $path_mod);

        // Корректировка статусов относительно имен БД
        $correct_path = array(
            'baners' => 'banner',
            'categories' => 'catalog',
            'modules' => 'catalog',
            'products' => 'catalog',
            '1c' => 'order',
            'orders' => 'order',
            'order' => 'order',
            'payment' => 'order',
            'valuta' => 'order',
            'foto' => 'catalog',
        );

        if ($correct_path[$path[1]])
            $path = $correct_path[$path[1]];
        else
            $path = $path[1];

        $array = explode("-", $this->user_status[$path]);
        if (!empty($array[$flag]))
            return true;
        else
            $this->data[] = array('phpshop_sql_user' => 'deny');
    }

    function update() {

        if ($this->status($this->xml['from'], 1))
            parent::update();

        $this->log();
    }

    function delete() {

        if ($this->status($this->xml['from'], 1))
            parent::delete();
        
        $this->log();
    }

    function insert() {

        if ($this->status($this->xml['from'], 2))
            parent::insert();
        
        $this->log();
    }

    function select() {

        if ($this->status($this->xml['from'], 0))
            parent::select();
    }

    function image() {
        $i = 1;
        $type_array = array('gif', 'jpg', 'png');
        $dir = '/UserFiles/Image/' . $this->xml['vars'][0];
        if ($dh = opendir('../..' . $dir . '/')) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {

                    if (is_file('../..' . $dir . '/' . $file)) {

                        $type = substr($file, -3);
                        if (in_array($type, $type_array)) {

                            $image[$i]['name'] = $file;
                            $image[$i]['type'] = $type;
                        }
                    } else {
                        $image[$i]['name'] = $file;
                        $image[$i]['type'] = 'dir';
                    }

                    $i++;
                }
            }
            closedir($dh);
        }
        $this->data = $image;
    }

    function log() {
        header("HTTP/1.1 200");
        header("Content-Type: application/json; charset=windows1251");
        echo json_encode(array('status' => $this->data));
    }

}

new PHPShop1C();
?>