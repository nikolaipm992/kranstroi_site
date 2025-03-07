<?php
/**
 * Печатная форма бланка заказа для Order Agent
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.2
 */
$_classPath = "../../../phpshop/";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");

$PHPShopSystem = new PHPShopSystem();
$LoadItems['System'] = $PHPShopSystem->getArray();

// Подключаем реквизиты
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);
$pathTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'];

if(!PHPShopSecurity::true_param($_GET['orderID'],$_GET['datas']))
exit('Error _GET');

$orderID = PHPShopSecurity::TotalClean($_GET['orderID'], 5);
$datas = PHPShopSecurity::TotalClean($_GET['datas'], 1);


$PHPShopOrder = new PHPShopOrderFunction($orderID);

$sql = "select * from " . $SysValue['base']['table_name1'] . " where id='$orderID' and datas='$datas'";
$n = 1;
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$dis = $this_nds_summa=$sum=$num=null;
if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {
        $dis.="
  <tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . $val['name'] . "</td>
		<td class=tablerow align=center>" . $val['ed_izm'] . "</td>
		<td align=right class=tablerow>" . $val['num'] . "</td>
		<td align=right class=tablerow nowrap>" . $PHPShopOrder->returnSumma($val['price'], 0) . "</td>
		<td class=tableright>" . $PHPShopOrder->returnSumma($val['price'] * $val['num'], 0) . "</td>
	</tr>
  ";

//Определение и суммирование веса
        $goodid = $val['id'];
        $goodnum = $val['num'];
        $wsql = 'select weight from ' . $SysValue['base']['table_name2'] . ' where id=\'' . $goodid . '\'';
        $wresult = mysqli_query($link_db,$wsql);
        $wrow = mysqli_fetch_array($wresult);
        $cweight = $wrow['weight'] * $goodnum;
        if (!$cweight) {
            $zeroweight = 1;
        } //Один из товаров имеет нулевой вес!
        $weight+=$cweight;


        @$sum+=$val['price'] * $val['num'];
        @$num+=$val['num'];
        $n++;
    }

//Обнуляем вес товаров, если хотя бы один товар был без веса
if ($zeroweight) {
    $weight = 0;
}

$PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
$deliveryPrice = $PHPShopDelivery->getPrice($sum, $weight);
@$dis.="
  <tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>Доставка - " . $PHPShopDelivery->getCity() . "</td>
		<td class=tablerow align=center>шт.&nbsp;</td>
		<td align=right class=tablerow>1</td>
		<td align=right class=tablerow nowrap>" . $deliveryPrice . "</td>
		<td class=tableright>" . $deliveryPrice . "</td>
	</tr>
  ";
if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    @$nds = number_format($sum * $nds / (100 + $nds), "2", ".", "");
}
@$sum = number_format($sum, "2", ".", "");

$summa_nds_dos = number_format($deliveryPrice * $nds / (100 + $nds), "2", ".", "");


$name_person = $order['Person']['name_person'];
$org_name = $order['Person']['org_name'];
$datas = PHPShopDate::dataV($datas, "false");

// время доставки под старый формат данных в заказе
if (!empty($order['Person']['dos_ot']) OR !empty($order['Person']['dos_do']))
    $dost_ot = " От: " . $order['Person']['dos_ot'] . ", до: " . $order['Person']['dos_do'];

// формируем адрес доставки с учётом старого формата данных в заказах
if ($row['country'])
    $adr_info .= ", страна: " . $row['country'];
if ($row['state'])
    $adr_info .= ", регион/штат: " . $row['state'];
if ($row['city'])
    $adr_info .= ", город: " . $row['city'];
if ($row['index'])
    $adr_info .= ", индекс: " . $row['index'];
if ($row['street'] OR $order['Person']['adr_name'])
    $adr_info .= ", улица: " . $row['street'] . $order['Person']['adr_name'];
if ($row['house'])
    $adr_info .= ", дом: " . $row['house'];
if ($row['porch'])
    $adr_info .= ", подъезд: " . $row['porch'];
if ($row['door_phone'])
    $adr_info .= ", код домофона: " . $row['door_phone'];
if ($row['flat'])
    $adr_info .= ", квартира: " . $row['flat'];

$adr_info = substr($adr_info, 2);
?>
<head>
    <title>Бланк Заказа №<?php echo $ouid ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link href="../style.css" type=text/css rel=stylesheet>
    <style media="screen" type="text/css">
        a.save{
            display: none;
        }

        * HTML a.save{ /* Только для браузера IE */
            display: inline;
        }
    </style>
    <style media="print" type="text/css">

        <!-- 
        .nonprint {
            display: none;
        }
        -->
    </style>
</head>
<body onload="window.focus()" bgcolor="#FFFFFF" text="#000000" marginwidth=5 leftmargin=5 style="padding: 2px;">
    <div align="right" class="nonprint">
        <button onclick="window.print()">
            Распечатать
        </button> 
    <hr>
    </div>
    <div align="center"><table align="center" width="100%">
            <tr>
                <td align="center"><img src="<?php echo $PHPShopSystem->getLogo(); ?>" alt="" border="0"></td>
                <td align="center"><h4 align=center>Заказ&nbsp;№&nbsp;<?php echo $ouid ?>&nbsp;от&nbsp;<?php echo $datas ?></h4></td>
            </tr>
        </table>
    </div>


    <br />
    <table width=99% cellpadding=2 cellspacing=0 align=center>
        <tr class=tablerow>
            <td class=tablerow width="150">Заказчик:</td>
            <td class=tableright><?php echo @$order['Person']['name_person'] . $row['fio'] ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>Компания:</td>
            <td class=tableright>&nbsp;<?php echo @$order['Person']['org_name'] . $row['org_name'] ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>Почта:</td>
            <td class=tableright><a href="mailto:<?php echo $order['Person']['mail'] ?>"><?php echo  $order['Person']['mail'] ?></a></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>ИНН:</td>
            <td class=tableright>&nbsp;<?php echo @$order['Person']['org_inn'] . $row['org_inn'] ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>КПП:</td>
            <td class=tableright>&nbsp;<?php echo @$order['Person']['org_kpp'] . $row['org_kpp'] ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>Тел:</td>
            <td class=tableright><?php echo @$order['Person']['tel_code'] . " " . @$order['Person']['tel_name'] . $row['tel'] ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>Адрес:</td>
            <td class=tableright><?php echo @$adr_info ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>Грузополучатель:</td>
            <td class=tableright><?php echo $PHPShopDelivery->getCity() ?></td>
        </tr>
        <tr class=tablerow>
            <td class=tablerow>Время доставки:</td>
            <td class=tableright><?php echo $dost_ot ?></td>
        </tr>
        <tr class=tablerow >
            <td class=tablerow>Тип оплаты:</td>
            <td class=tableright><?php echo $PHPShopOrder->getOplataMetodName() ?></td>
        </tr>
        <tr class=tablerow >
            <td class=tablerow style="border-bottom: 1px solid #000000;">Комментарии:</td>
            <td class=tableright style="border-bottom: 1px solid #000000;">&nbsp;<?php echo $status['maneger'] ?></td>
        </tr>
    </table>
    <p><br></p>
    <table width=99% cellpadding=2 cellspacing=0 align=center>
        <tr class=tablerow>
            <td class=tablerow>№</td>
            <td width=50% class=tablerow>Наименование</td>
            <td class=tablerow>Единица измерения&nbsp;</td>
            <td class=tablerow>Количество</td>
            <td class=tablerow>Цена</td>
            <td class=tableright>Сумма</td>
        </tr>
        <?php
        echo @$dis;
        $my_total = $PHPShopOrder->returnSumma($sum, $order['Person']['discount']) + $deliveryPrice;
        $my_nds = number_format($my_total * $LoadItems['System']['nds'] / (100 + $LoadItems['System']['nds']), "2", ".", "");
        ?>
        <tr>
            <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;">Скидка:</td>
            <td class=tableright nowrap><b><?php echo @$order['Person']['discount'] ?>%</b></td>
        </tr>
        <tr>
            <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;">Итого:</td>
            <td class=tableright nowrap><b><?php echo $my_total ?></b></td>
        </tr>
        <?php if ($LoadItems['System']['nds_enabled']) { ?>
            <tr>
                <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;">В т.ч. НДС: <?php echo $LoadItems['System']['nds'] ?>%</td>
                <td class=tableright nowrap><b><?php echo $my_nds ?></b></td>
            </tr>
        <?php } ?>
        <tr><td colspan=6 style="border: 0px; border-top: 1px solid #000000;">&nbsp;</td></tr>
    </table>
    <p><b>Всего наименований <?php echo ($num + 1) ?>, на сумму <?php echo ($PHPShopOrder->returnSumma($sum, $order['Person']['discount']) + $deliveryPrice) . " " . $PHPShopOrder->default_valuta_code; ?>
            <br />
            <?php
            $iw = new inwords;
            $s = $iw->get($PHPShopOrder->returnSumma($sum, $order['Person']['discount']) + $deliveryPrice);
            $v = $PHPShopOrder->default_valuta_code;
            if (preg_match("/руб/i", $v))
                echo $s;
            ?>
        </b></p><br>
    <p>Дата <u><?php echo date("d-m-y H:m a") ?></u></p>
<p>Руководитель<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
<p>Главный бухгалтер<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u></p>
<br>
<table>
    <tr>
        <td style="padding:50px;border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center">М.П.</td>
    </tr>
</table>


</body>
</html>