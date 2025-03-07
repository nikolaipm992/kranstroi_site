<?php

/**
 * Синхронизация с PHPShop Monitor
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.8
 */
$_classPath = "../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("array");
PHPShopObj::loadClass("product");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("security");

// Подключение к БД
$PHPShopBase = new PHPShopBase("../phpshop/inc/config.ini", true, true);

// Системные настройки
$PHPShopSystem = new PHPShopSystem();


// Проверка пользователя
require("lib/user.lib.php");
header('Content-type: text/html; charset=windows-1251');

// Заказы
function OrdersArray($p1, $p2, $words, $list) {
    global $link_db;

    $words = MyStripSlashes(base64_decode($words));

    if (empty($p1))
        $p1 = date("U") - 432000;
    else
        $p1 = PHPShopDate::GetUnixTime($p1) - 432000;
    if (empty($p2))
        $p2 = date("U");
    else
        $p2 = PHPShopDate::GetUnixTime($p2) + 432000;


    if ($list == "all" or !$list)
        $sort = "";
    elseif ($list == "new")
        $sort = "and statusi=0";
    else
        $sort = "and statusi=" . $list;


    if (!empty($words)) {
        if (is_int($words))
            $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " where uid=" . $words;
        else
            $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " where orders REGEXP '" . $words . "'";
    }
    else {
        $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " where datas<'$p2' and datas>'$p1' $sort order by id desc";
    }
    $result = mysqli_query($link_db, $sql);
    $i = mysqli_num_rows($result);
    while ($row = mysqli_fetch_array($result)) {
        $id = $row['id'];
        $datas = $row['datas'];
        $uid = $row['uid'];
        $order = unserialize($row['orders']);
        $status = unserialize($row['status']);

        if (empty($row['statusi']))
            $statusi = 0;
        else
            $statusi = $row['statusi'];

        if (empty($status['time']))
            $time = "-";
        else
            $time = $status['time'];

        $array = array(
            "id" => $id,
            "uid" => $uid,
            "datas" => $datas,
            "cart" => $order['Cart'],
            "order" => $order['Person'],
            "time" => $time,
            "row" => $row,
            "statusi" => $statusi
        );
        $i--;
        $OrdersArray[$id] = $array;
    }
    return $OrdersArray;
}

// перекодировка unicode UTF-8 -> win1251 
function MyStripSlashes($s) {
    $out = "";
    $c1 = "";
    $byte2 = false;
    for ($c = 0; $c < strlen($s); $c++) {
        $i = ord($s[$c]);
        if ($i <= 127)
            $out.=$s[$c];
        if ($byte2) {
            $new_c2 = ($c1 & 3) * 64 + ($i & 63);
            $new_c1 = ($c1 >> 2) & 5;
            $new_i = $new_c1 * 256 + $new_c2;
            if ($new_i == 1025) {
                $out_i = 168;
            } else {
                if ($new_i == 1105) {
                    $out_i = 184;
                } else {
                    $out_i = $new_i - 848;
                }
            }
            $out.=chr($out_i);
            $byte2 = false;
        }
        if (($i >> 5) == 6) {
            $c1 = $i;
            $byte2 = true;
        }
    }
    return str_replace("\"", "*", $out);
}

// Вывод доставки
function GetDelivery($deliveryID, $name) {
    global $link_db;
    $sql = "select * from " . $GLOBALS['SysValue']['base']['delivery'] . " where id=" . intval($deliveryID);
    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);
    return PHPShopSecurity::TotalClean(strip_tags($row[$name]));
}

// Расчёт цены доставки
function GetDeliveryPrice($deliveryID, $sum, $weight = 0) {
    global $SysValue, $link_db;

    $sql = "select * from " . $SysValue['base']['delivery'] . " where id='$deliveryID'";
    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);

    if ($row['price_null_enabled'] == 1 and $sum >= $row['price_null']) {
        return 0;
    } else {
        if ($row['taxa'] > 0) {
            $addweight = $weight - 500;
            if ($addweight < 0) {
                $addweight = 0;
            }
            $addweight = ceil($addweight / 500) * $row['taxa'];
            $endprice = $row['price'] + $addweight;
            return $at . $endprice;
        } else {
            return $row['price'];
        }
    }
}

// Статус заказа
function GetOrderStatusArray() {
    global $link_db;
    $sql = "select * from " . $GLOBALS['SysValue']['base']['order_status'];
    $result = mysqli_query($link_db, $sql);
    while (@$row = mysqli_fetch_array(@$result)) {
        $array = array(
            "id" => $row['id'],
            "name" => $row['name'],
            "color" => $row['color'],
            "sklad" => $row['sklad_action']
        );
        $Status[$row['id']] = $array;
    }
    return $Status;
}

// Тип оплаты
function GetOplataMetodArray() {
    global $link_db;
    $sql = "select * from " . $GLOBALS['SysValue']['base']['payment_systems'] . " where enabled='1' order by num";
    $result = mysqli_query($link_db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $array = array(
            "id" => $row['id'],
            "name" => $row['name']
        );
        $Status[$row['id']] = $array;
    }
    return $Status;
}

// Тип доставки
function GetDeliveryMetodArray() {
    global $link_db;
    $sql = "select * from " . $GLOBALS['SysValue']['base']['delivery'] . " where enabled='1' and is_folder!='1' order by city";
    $result = mysqli_query($link_db, $sql);
    while ($row = mysqli_fetch_array($result)) {
        $array = array(
            "id" => $row['id'],
            "name" => $row['city']
        );
        $Status[$row['id']] = $array;
    }
    return $Status;
}

$GetOrderStatusArray = GetOrderStatusArray();
$GetOrderStatusArray[0]['name'] = "Новый заказ";
$GetOrderStatusArray[0]['color'] = "C0D2EC";
$GetOrderStatusArray[0]['id'] = 0;
$GetOplataMetodArray = GetOplataMetodArray();
$GetDeliveryMetodArray = GetDeliveryMetodArray();

// Форматирование строки
function Clean($s) {
    return PHPShopSecurity::TotalClean($s);
}

/**
 * Обновление данных по заказу
 */
function OrderUpdateXml() {
    global $GetOrderStatusArray, $link_db;


    $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " where id='" . intval($_REQUEST['id']) . "'";
    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);
    $status = unserialize($row['status']);
    $order = unserialize($row['orders']);
    $old_status = $row['statusi'];

    // Время изменения
    $Status = array(
        "maneger" => MyStripSlashes($_REQUEST['manager']),
        "time" => date("d-m-y H:i")
    );

    sendUserMail($row);

    // Изменения под новую структуру таблицы заказов.
//    $order['Person']['name_person'] = MyStripSlashes($_REQUEST['name_person']);
//    $order['Person']['adr_name'] = MyStripSlashes($_REQUEST['adr_name']);
//    $order['Person']['dos_ot'] = MyStripSlashes($_REQUEST['dos_ot']);
//    $order['Person']['dos_do'] = MyStripSlashes($_REQUEST['dos_do']);
//    $order['Person']['tel_code'] = MyStripSlashes($_REQUEST['tel_code']);
//    $order['Person']['tel_name'] = MyStripSlashes($_REQUEST['tel_name']);
//    $order['Person']['org_name'] = MyStripSlashes($_REQUEST['org_name']);
    $order['Person']['order_metod'] = MyStripSlashes($_REQUEST['metod_id']);


    // Корзина
    $cart = $order['Cart']['cart'];

    // Если новый статус Аннулирован, а был статус не Новый заказ, то мы не списываем, а добавляем обратно
    if ($old_status != 0 && $_REQUEST['statusi'] == 0) {
        if (is_array($cart))
            foreach ($cart as $val) {
                $sql = "select items from " . $GLOBALS['SysValue']['base']['products'] . " where id='" . intval($val['id']) . "'";
                mysqli_query($link_db, $sql);
                $row = mysqli_fetch_array($result);
                $items = $row['items'];
                $items_update = $items + $val['num'];
                $sklad_update = "";
                if ($items_update > 0)
                    $sklad_update = " ,sklad='0' ";
                $sql = "UPDATE " . $GLOBALS['SysValue']['base']['products'] . "
				SET
				items='$items_update' " . $sklad_update . "
				where id='" . $val['id'] . "'";
                mysqli_query($link_db, $sql);
            }
    }
    // Списываем со склада
    else if ($GetOrderStatusArray[$_REQUEST['statusi']]['sklad'] == 1) {
        if (is_array($cart))
            foreach ($cart as $val) {
                $sql = "select items from " . $GLOBALS['SysValue']['base']['products'] . " where id='" . intval($val['id']) . "'";
                $result = mysqli_query($link_db, $sql);
                $row = mysqli_fetch_array($result);
                $items = $row['items'];
                $items_update = $items - $val['num'];
                $sklad_update = "";
                if ($items_update == 0)
                    $sklad_update = " ,sklad='1' ";
                $sql = "UPDATE " . $GLOBALS['SysValue']['base']['products'] . "
         SET
         items='$items_update' " . $sklad_update . "
         where id='" . $val['id'] . "'";
                mysqli_query($link_db, $sql);
            }
    }

    // Обновляем данные по заказу
    $sql = "UPDATE " . $GLOBALS['SysValue']['base']['orders'] . "
    SET
    orders='" . serialize($order) . "',
    status='" . serialize($Status) . "',
    fio='" . MyStripSlashes($_REQUEST['name_person']) . "',
    tel='" . MyStripSlashes($_REQUEST['tel_name']) . "',
    org_name='" . MyStripSlashes($_REQUEST['org_name']) . "',
    dop_info='" . MyStripSlashes($_REQUEST['manager']) . "',
    statusi='" . $_REQUEST['statusi'] . "'
    where id='" . $_REQUEST['id'] . "'";
    mysqli_query($link_db, $sql);
}

/**
 * Оповещение пользователя о новом статусе
 * @param array $data массив данных заказа
 */
function sendUserMail($data) {
    global $GetOrderStatusArray, $PHPShopSystem;


    if ($data['statusi'] != $_REQUEST['statusi']) {

        PHPShopObj::loadClass("parser");
        PHPShopObj::loadClass("mail");

        PHPShopParser::set('ouid', $data['uid']);
        PHPShopParser::set('date', PHPShopDate::dataV($data['datas']));

        PHPShopParser::set('status', $GetOrderStatusArray[$_REQUEST['statusi']][name]);
        PHPShopParser::set('user', $data['user']);
        PHPShopParser::set('company', $PHPShopSystem->getParam('name'));

        if ($PHPShopSystem->ifSerilizeParam('1c_option.1c_load_status_email')) {
            $title = 'Cтатус заказа ' . $data['uid'] . ' изменен';
            $order = unserialize($data['orders']);

            PHPShopParser::set('mail', $order['Person']['mail']);
            PHPShopParser::set('user_name', $order['Person']['name_person']);

            $PHPShopMail = new PHPShopMail($order['Person']['mail'], $PHPShopSystem->getValue('adminmail2'), $title, '', true, true);
            $content = PHPShopParser::file('../phpshop/lib/templates/order/status.tpl', true);
            if (!empty($content)) {
                $PHPShopMail->sendMailNow($content);
            }
        }
    }
}

// Данные по заказу
function OrdersReturn($id) {
    global $link_db;

    $sql = "select * from " . $GLOBALS['SysValue']['base']['orders'] . " where id=" . intval($id);
    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);
    $id = $row['id'];
    $order = unserialize($row['orders']);
    $status = unserialize($row['status']);
    $datas = $row['datas'];

    if (empty($row['statusi']))
        $statusi = 0;
    else
        $statusi = $row['statusi'];

    if (empty($status['time']))
        $time = "-";
    else
        $time = $status['time'];

    $array = array(
        "id" => $id,
        "cart" => $order['Cart'],
        "order" => $order['Person'],
        "time" => $time,
        "datas" => $datas,
        "dos_ot" => Clean($status['dos_ot']),
        "dos_do" => Clean($status['dos_do']),
        "manager" => Clean($status['maneger']),
        "row" => $row,
        "statusi" => $statusi,
        "ofd" => $row['ofd_status']
    );
    return $array;
}

// Пролучаем тип оплаты
function OplataMetod($id) {
    global $link_db;
    $order_metod = Clean($id);
    $sql = "select name from " . $GLOBALS['SysValue']['base']['payment_systems'] . " where id=" . intval($order_metod);
    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);
    return $row['name'];
}

// Изображение товара
function ReturnPic($id) {
    global $link_db;
    $sql = "select pic_big from " . $GLOBALS['SysValue']['base']['products'] . " where id=" . intval($id);
    $result = mysqli_query($link_db, $sql);
    $row = mysqli_fetch_array($result);
    $pic_big = $row['pic_big'];
    if (empty($pic_big))
        $pic_big = "none";
    return $pic_big;
}

function ReturnSumma($sum, $id, $disc) {
    $PHPShopProduct = new PHPShopProduct($id);
    $getValutaID = $PHPShopProduct->getValutaID();

    if (empty($getValutaID)) {
        $System = new PHPShopSystem();
        $getValutaID = $System->getDefaultValutaId();
    }

    $PHPShopValuta = new PHPShopValuta($getValutaID);
    $kurs = $PHPShopValuta->getKurs();
    $sum*=$kurs;
    $sum = $sum - ($sum * $disc / 100);
    return number_format($sum, "2", ".", "");
}

function ReturnCarrency($id) {
    $PHPShopProduct = new PHPShopProduct($id);
    $getValutaID = $PHPShopProduct->getValutaID();

    if (empty($getValutaID)) {
        $System = new PHPShopSystem();
        $getValutaID = $System->getDefaultValutaId();
    }

    $PHPShopValuta = new PHPShopValuta($getValutaID);
    return $PHPShopValuta->getIso();
}

// Обработка комманд
switch ($_REQUEST['command']) {

    case ("loadListOrder"):

        $OrdersArray = OrdersArray($_REQUEST['p1'], $_REQUEST['p2'], $_REQUEST['words'], $_REQUEST['list']);
        $XML = '<?xml version="1.0" encoding="windows-1251"?>
<orderdb>';

        if (is_array($OrdersArray))
            foreach ($OrdersArray as $val) {
                $XML.='<order>
	      <data>' . PHPShopDate::dataV($val['order']['data']) . '</data>
		  <datas>' . $val['datas'] . '</datas>
		  <uid>' . $val['uid'] . '</uid>
		  <id>' . $val['id'] . '</id>
		  <name>' . Clean($val['row']['fio'] . " (" . $val['order']['mail'] . ")") . '</name>
		  <mail>' . Clean($val['order']['mail']) . '</mail>
		  <tel>' . Clean($val['order']['tel_code']) . ' ' . Clean($val['order']['tel_name']) . '</tel>
		  <adres>' . Clean($val['order']['adr_name']) . '</adres>
		  <place>' . GetDelivery($val['order']['dostavka_metod'], "city") . '</place>
		  <metod>' . $val['order']['order_metod'] . '</metod>
		  <status>' . $GetOrderStatusArray[$val['statusi']]['name'] . '</status>
		  <color>' . $GetOrderStatusArray[$val['statusi']]['color'] . '</color>
		  <time>' . $val['time'] . '</time>
		  <summa>' . (ReturnSumma($val['cart']['sum'], $val['id'], $val['order']['discount']) + GetDeliveryPrice($val['order']['dostavka_metod'], $val['cart']['sum'], $val['cart']['weight'])) . '</summa>
		  <num>' . ($val['cart']['num'] + 1) . '</num>
		  <kurs>' . $val['cart']['kurs'] . '</kurs>';
                $XML.='</order>
';
            }
        $XML.='</orderdb>';
        echo $XML;
        break;

    // Количество новых заказов
    case("loadNumNew"):
        $sql = "select id from " . $GLOBALS['SysValue']['base']['orders'] . " where statusi=0";
        $result = mysqli_query($link_db, $sql);
        $num = mysqli_num_rows($result);
        if ($num == 0)
            echo "";
        else
            echo $num;
        break;

    // Данные по заказу
    case ("loadIdOrder"):
        $XMLS = null;
        $XMLM = null;
        $XMLD = null;

        if (!empty($_REQUEST['id'])) {
            $OrdersReturn = OrdersReturn($_REQUEST['id']);
            $XML = '<?xml version="1.0" encoding="windows-1251"?>
                    <orderdb>';

            if (is_array($GetOrderStatusArray))
                foreach ($GetOrderStatusArray as $status)
                    $XMLS.='
                    <status>
                      <sid>' . $status['id'] . '</sid>
                          <sname>' . $status['name'] . '</sname>
                   </status>
                   ';

            if (is_array($GetOplataMetodArray))
                foreach ($GetOplataMetodArray as $metod)
                    $XMLM.='
                    <pay>
                      <pid>' . $metod['id'] . '</pid>
                          <pname>' . $metod['name'] . '</pname>
                   </pay>
                   ';

            if (is_array($GetDeliveryMetodArray))
                foreach ($GetDeliveryMetodArray as $metod)
                    $XMLD.='
                    <deliv>
                      <did>' . $metod['id'] . '</did>
                          <dname>' . $metod['name'] . '</dname>
                   </deliv>
                   ';


            // выводим сгрупппированные данные пользователя
            if ($OrdersReturn['row']['fio'] OR $OrdersReturn['order']['name_person'])
                $adr_info .= Clean(", ФИО: " . $OrdersReturn['row']['fio']);
            if ($OrdersReturn['row']['tel'] or $_POST['person']['tel_code'] or $_POST['person']['tel_name'])
                $adr_info .= Clean(", тел.: " . $OrdersReturn['row']['tel'] . $_POST['person']['tel_code'] . $_POST['person']['tel_name']);
            if ($OrdersReturn['row']['country'])
                $adr_info .= Clean(", страна: " . $OrdersReturn['row']['country']);
            if ($OrdersReturn['row']['state'])
                $adr_info .= Clean(", регион/штат: " . $OrdersReturn['row']['state']);
            if ($OrdersReturn['row']['city'])
                $adr_info .= Clean(", город: " . $OrdersReturn['row']['city']);
            if ($OrdersReturn['row']['index'])
                $adr_info .= Clean(", индекс: " . $OrdersReturn['row']['index']);
            if ($OrdersReturn['row']['street'] OR $OrdersReturn['order']['adr_name'])
                $adr_info .= Clean(", улица: " . $OrdersReturn['row']['street'] . $OrdersReturn['order']['adr_name']);
            if ($OrdersReturn['row']['house'])
                $adr_info .= Clean(", дом: " . $OrdersReturn['row']['house']);
            if ($OrdersReturn['row']['porch'])
                $adr_info .= Clean(", подъезд: " . $OrdersReturn['row']['porch']);
            if ($OrdersReturn['row']['door_phone'])
                $adr_info .= Clean(", код домофона: " . $OrdersReturn['row']['door_phone']);
            if ($OrdersReturn['row']['flat'])
                $adr_info .= Clean(", квартира: " . $OrdersReturn['row']['flat']);

            // Данные по заказу
            $XML.='<order>
	      <data>' . PHPShopDate::dataV($OrdersReturn['order']['data']) . '</data>
          <datas>' . $OrdersReturn['datas'] . '</datas>
		  <uid>' . $OrdersReturn['row']['uid'] . '</uid>
		  <name>' . Clean($OrdersReturn['row']['fio']) . '</name>
		  <mail>' . Clean($OrdersReturn['order']['mail']) . '</mail>
		  <tel_code>' . Clean($OrdersReturn['order']['tel_code']) . '</tel_code>
		  <tel_name>' . Clean($OrdersReturn['order']['tel_name'] . $OrdersReturn['row']['tel']) . '</tel_name>
		  <adres>' . Clean($OrdersReturn['order']['adr_name'] . $adr_info) . '</adres>
		  <country>' . Clean($OrdersReturn['order']['country'] . $OrdersReturn['row']['country']) . '</country>
		  <state>' . Clean($OrdersReturn['order']['state'] . $OrdersReturn['row']['state']) . '</state>
		  <city>' . Clean($OrdersReturn['order']['city'] . $OrdersReturn['row']['city']) . '</city>
		  <index>' . Clean($OrdersReturn['order']['index'] . $OrdersReturn['row']['index']) . '</index>
		  <street>' . Clean($OrdersReturn['order']['street'] . $OrdersReturn['row']['street']) . '</street>
		  <house>' . Clean($OrdersReturn['order']['house'] . $OrdersReturn['row']['house']) . '</house>
		  <porch>' . Clean($OrdersReturn['order']['country'] . $OrdersReturn['row']['porch']) . '</porch>
		  <door_phone>' . Clean($OrdersReturn['order']['door_phone'] . $OrdersReturn['row']['door_phone']) . '</door_phone>
		  <flat>' . Clean($OrdersReturn['order']['flat'] . $OrdersReturn['row']['flat']) . '</flat>
		  <org_name>' . Clean($OrdersReturn['order']['org_name'] . $OrdersReturn['row']['org_name']) . '</org_name>
		  <org_inn>' . Clean($OrdersReturn['order']['org_inn'] . $OrdersReturn['row']['org_inn']) . '</org_inn>
		  <org_kpp>' . Clean($OrdersReturn['order']['org_kpp'] . $OrdersReturn['row']['org_kpp']) . '</org_kpp>
		  <org_yur_adres>' . Clean($OrdersReturn['order']['org_yur_adres'] . $OrdersReturn['row']['org_yur_adres']) . '</org_yur_adres>
		  <org_fakt_adres>' . Clean($OrdersReturn['order']['org_fakt_adres'] . $OrdersReturn['row']['org_fakt_adres']) . '</org_fakt_adres>
		  <org_ras>' . Clean($OrdersReturn['order']['org_ras'] . $OrdersReturn['row']['org_ras']) . '</org_ras>
		  <org_bank>' . Clean($OrdersReturn['order']['org_bank'] . $OrdersReturn['row']['org_bank']) . '</org_bank>
		  <org_kor>' . Clean($OrdersReturn['order']['org_kor'] . $OrdersReturn['row']['org_kor']) . '</org_kor>
		  <org_bik>' . Clean($OrdersReturn['order']['org_bik'] . $OrdersReturn['row']['org_bik']) . '</org_bik>
		  <org_city>' . Clean($OrdersReturn['order']['org_city'] . $OrdersReturn['row']['org_city']) . '</org_city>
                  <tracking>' . Clean($OrdersReturn['order']['tracking'] . $OrdersReturn['row']['tracking']) . '</tracking>
		  <dop_info>' . Clean($OrdersReturn['order']['dop_info'] . $OrdersReturn['row']['dop_info']) . '</dop_info>
		  <dos_ot>' . Clean($OrdersReturn['row']['delivtime']) . '</dos_ot>
		  <dos_do>' . Clean($OrdersReturn['order']['dos_do']) . '</dos_do>
		  <discount>' . (Clean($OrdersReturn['order']['discount']) + 0) . '</discount>
		  <manager>' . Clean($OrdersReturn['row']['dop_info']) . '</manager>
		  <place>' . GetDelivery($OrdersReturn['order']['dostavka_metod'], "city") . '</place>
		  <place_price>' . GetDeliveryPrice($OrdersReturn['order']['dostavka_metod'], $OrdersReturn['cart']['sum'], $OrdersReturn['cart']['weight']) . '</place_price>
		  <metod>' . OplataMetod($OrdersReturn['order']['order_metod']) . '</metod>
		  <metod_id>' . $OrdersReturn['order']['order_metod'] . '</metod_id>
		  <statusi>' . $OrdersReturn['statusi'] . '</statusi>
		  <status>' . $GetOrderStatusArray[$OrdersReturn['statusi']]['name'] . '</status>
                  <ofd>' . $OrdersReturn['ofd'] . '</ofd>   
		  <time>' . $OrdersReturn['time'] . '</time>
		  <statuslist2>
		  ' . $XMLS . '
		  </statuslist2>
		  <paylist>
		  ' . $XMLM . '
		  </paylist>
		  <delivlist>
		  ' . $XMLD . '
		  </delivlist>
		  </order>
		  <productlist>
		  ';

            // Содержание корзины
            if (is_array($OrdersReturn['cart']['cart']))
                foreach ($OrdersReturn['cart']['cart'] as $vals)
                    $XML.='
  <product>
    <id>' . $vals['id'] . '</id>
	<art>#' . PHPShopSecurity::TotalClean($vals['uid']) . '</art>
	<p_name>' . PHPShopSecurity::TotalClean($vals['name']) . '</p_name>
	<pic>' . ReturnPic($vals['id']) . '</pic>
	<price>' . ReturnSumma($vals['price'], $vals['id'], $OrdersReturn['order']['discount']) . '</price>
	<currency>' . ReturnCarrency($vals['id']) . '</currency>
	<num>' . $vals['num'] . '</num>
 </product>
 ';
            $XML.='
</productlist>
</orderdb>';
            echo $XML;
        }
        break;

    // Описание товара из корзины
    case("loadIdOrderProduct"):

        if (!empty($_REQUEST['id'])) {
            $OrdersReturn = OrdersReturn($_REQUEST['id']);
            $XML = '<?xml version="1.0" encoding="windows-1251"?>
<orderdb>';
            if (is_array($OrdersReturn['cart']['cart']))
                foreach ($OrdersReturn['cart']['cart'] as $vals)
                    $XML.='
<product>
    <id>' . $vals['id'] . '</id>
	<art>' . $vals['uid'] . '</art>
	<p_name>' . $vals['name'] . '</p_name>
	<pic>' . ReturnPic($vals['id']) . '</pic>
	<price>' . ReturnSumma($vals['price'], $vals['id'], $OrdersReturn['order']['discount']) . '</price>
	<num>' . $vals['num'] . '</num>
</product>';
            echo $XML . '</orderdb>';
        }
        break;

    // Обновление данных заказа
    case("orderUpdate"):
        OrderUpdateXml();
        break;
}
?>