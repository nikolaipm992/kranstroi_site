<?php
/**
 * Печатная форма чека заказа для Order Agent
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.1
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

function DoZero($price) {
    if (empty($price))
        return 0;
    else
        return $price;
}

$sql = "select * from " . $SysValue['base']['table_name1'] . " where id='$orderID' and datas='$datas'";
$n = 1;
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);

if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {
        @$dis.="
  <tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . $val['name'] . "</td>
		<td align=right class=tablerow nowrap>" . $PHPShopOrder->returnSumma($val['price'], 0) . "</td>
		<td align=right class=tablerow>" . $val['num'] . "</td>
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
        <td align=right class=tablerow nowrap>" . DoZero($deliveryPrice) . "</td>
		<td align=right class=tablerow>1</td>
		<td class=tableright>" . DoZero($deliveryPrice) . "</td>
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



// Генерим номер товарного чека
$chek_num = substr(abs(crc32(uniqid(rand(), true))), 0, 5);
$LoadBanc = unserialize($LoadItems['System']['bank']);
?>
<head>
    <title>Товарный чек № <?php echo @$chek_num ?></title>
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

    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
            <TR>
                <TH scope=row align=middle width="50%" rowSpan=3><img src="<?php echo $PHPShopSystem->getLogo(); ?>" alt="" border="0"></TH>
                <TD align=right>
                    <BLOCKQUOTE>
                        <P>Товарный чек <SPAN class=style4><?php echo @$chek_num ?> от <?php echo PHPShopDate::dataV(date("U"), "update") ?></SPAN> </P></BLOCKQUOTE></TD></TR>
            <TR>
                <TD align=right>
                    <BLOCKQUOTE>
                        <P><SPAN class=style4><?php echo $LoadBanc['org_adres'] ?>, телефон <?php echo $LoadItems['System']['tel'] ?> </SPAN></P></BLOCKQUOTE></TD></TR>
            <TR>
                <TD align=right>
                    <BLOCKQUOTE>
                        <P class=style4>Поставщик: <?php echo $LoadItems['System']['company'] ?></P></BLOCKQUOTE></TD></TR></TBODY></TABLE>



    <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
            <TR>
                <TH scope=row align=middle width="50%">
        <P class=style4>Покупатель: <?php echo @$order['Person']['name_person'] . $row['fio'] ?></P></TH>
    <TH scope=row align=middle><b>Заказ №<?php echo $ouid ?> </b></TH></TR></TBODY></TABLE>



<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
        <TR>
            <TH class=style2 scope=row align=left>
    <BLOCKQUOTE>
        <P class=style4>Проверяйте комплектацию и внешний вид товара во время его получения!</P></BLOCKQUOTE></TH></TR>
<TR>
    <TH class=style4 scope=row align=left>
<BLOCKQUOTE>
    <P>Покупатель самостоятельно несет ответственность за внешний вид и комплектацию товара после приема его от продавца.</P></BLOCKQUOTE></TH></TR></TBODY></TABLE>

<p><br></p>
<table width=99% cellpadding=2 cellspacing=0 align=center>
    <tr class=tablerow>
        <td class=tablerow>№</td>
        <td width=50% class=tablerow>Наименование</td>
        <td class=tablerow>Цена</td>
        <td class=tablerow>Количество</td>
        <td class=tableright>Стоимость (<?php echo $PHPShopOrder->default_valuta_code; ?>)</td>
    </tr>
    <?php
    echo @$dis;
    $my_total = $PHPShopOrder->returnSumma($sum, $order['Person']['discount']) + $deliveryPrice;
    $my_nds = number_format($my_total * $LoadItems['System']['nds'] / (100 + $LoadItems['System']['nds']), "2", ".", "");
    ?>
    <tr>
        <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;">Скидка: <?php echo @$order['Person']['discount'] ?>%</td>
    </tr>
    <tr>
        <td colspan=5 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;">Итого:
            <?php echo $my_total;
            if ($LoadItems['System']['nds_enabled']) { 
                echo "в т.ч. НДС:  ".$my_nds; 
             } 
             ?>
        </td>
    </tr>

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


<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
        <TR>
            <TH scope=row align=middle width="50%">
    <P>&nbsp;</P>
    <P class=style4>Продавец: ________________ М.П. </P>
    <P>&nbsp;</P></TH>
<TD vAlign=center align=left><SPAN class=style5>Гарантийное обслуживание товаров осуществляется в авторизованном сервисном центре изготовителя. При отсутствии соответствующего сервисного центра гарантийное обслуживание осуществляется у продавца. </SPAN></TD></TR></TBODY></TABLE>
        <?php echo $LoadItems['System']['promotext'] ?>
</body>
</html>