<?php
session_start();
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base", "order", "system", "inwords", "delivery", "date", "valuta", "lang"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini");
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$PHPShopBase->checkMultibase("../../../../");
$LoadItems['System'] = $PHPShopSystem->getArray();
$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'admin'));

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);

function DoZero($price) {
    if (empty($price))
        return 0;
    else
        return $price;
}

// ���������� ���������
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);

$sql = "select * from " . $SysValue['base']['table_name1'] . " where id=" . intval($_GET['orderID']);
$n = 1;
$sum = $num = 0;
@$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array(@$result);
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$dis = $sum = $num = $weight = null;
if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {

        // ������
        if ($val['type'] == 2)
            continue;

        if (!empty($val['parent_uid']))
            $val['uid'] = $val['parent_uid'];

        if (!empty($val['uid']))
            $val['name'] .= ' (' . $val['uid'] . ')';

        $dis .= "<tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . $val['name'] . "</td>
                <td align=right class=tablerow>" . $val['uid'] . "</td>
		<td align=right class=tablerow nowrap>" . $PHPShopOrder->returnSumma($val['price'], 0) . "</td>
		<td align=right class=tablerow>" . $val['num'] . "</td>
		<td class=tableright>" . $PHPShopOrder->returnSumma($val['price'] * $val['num'], 0) . "</td>
	      </tr>";

        // ����������� � ������������ ����
        $goodid = $val['id'];
        $goodnum = $val['num'];
        $wsql = 'select weight from ' . $SysValue['base']['table_name2'] . ' where id=\'' . $goodid . '\'';
        $wresult = mysqli_query($link_db, $wsql);
        $wrow = mysqli_fetch_array($wresult);
        $cweight = $wrow['weight'] * $goodnum;
        if (!$cweight) {
            $zeroweight = 1;
        } //���� �� ������� ����� ������� ���!
        $weight += $cweight;


        $sum += $val['price'] * $val['num'];
        $num += $val['num'];
        $n++;
    }

//�������� ��� �������, ���� ���� �� ���� ����� ��� ��� ����
if ($zeroweight) {
    $weight = 0;
}

$PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
$PHPShopDelivery->checkMod($order['Cart']['dostavka']);
$deliveryPrice = $PHPShopDelivery->getPrice($sum, $weight);

$dis .= "<tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . __('��������') . " - " . $PHPShopDelivery->getCity() . "</td>
                <td class=tablerow></td>
                <td align=right class=tablerow nowrap>" . DoZero($deliveryPrice) . "</td>
		<td align=right class=tablerow>1</td>
		<td class=tableright>" . DoZero($deliveryPrice) . "</td>
	</tr>";

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * $nds / (100 + $nds), "2", ".", "");
}
$sum = number_format($sum + $deliveryPrice, "2", ".", "");

$PERSON = $order['Person'];
if ($PERSON['discount'] > 0) {
    $discount = $PERSON['discount'] . '%';
} else {
    $discount = (@$PERSON['tip_disc'] == 1 ? @$PERSON['discount_promo'] . '%' : @$PERSON['discount_promo']);
}

if (!empty($row['bonus_minus']))
    $discount = $row['bonus_minus'];

// ����� ��������� ����
$chek_num = $ouid;
$LoadBanc = unserialize($LoadItems['System']['bank']);
?>
<!doctype html>
<head>
    <title><? _e("�������� ���") . " &#8470;" . $chek_num ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link href="style.css" type=text/css rel=stylesheet>
    <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div align="right" class="nonprint">
        <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: '<? _e("�������� ���") ?> &#8470;<?php echo $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}});"><? _e("���������") ?></button> 
        <button onclick="window.print();"><? _e("�����������") ?></button> 
        <hr><br><br>
    </div>
    <div id="content">
        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
                <TR>
                    <TH scope=row align=middle width="50%" rowSpan=3><img src="<?php echo $PHPShopSystem->getLogo(true); ?>" alt="" border="0" style="max-width: 200px;height: auto;"></TH>
                    <TD align=right>
                        <BLOCKQUOTE>
                            <P><? _e("�������� ���") ?> <SPAN class=style4><?php echo @$chek_num ?> <? _e("��") ?> <?php echo PHPShopDate::dataV(date("U"), "update") ?></SPAN> </P></BLOCKQUOTE></TD></TR>
                <TR>
                    <TD align=right>
                        <BLOCKQUOTE>
                            <P><SPAN class=style4><?php echo $LoadBanc['org_adres'] ?>, <? _e("�������") ?> <?php echo $LoadItems['System']['tel'] ?> </SPAN></P></BLOCKQUOTE></TD></TR>
                <TR>
                    <TD align=right>
                        <BLOCKQUOTE>
                            <P class=style4><? _e("���������") ?>: <?php echo $LoadItems['System']['company'] ?></P></BLOCKQUOTE></TD></TR></TBODY></TABLE>



        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
                <TR>
                    <TH scope=row align=middle width="50%">
                        <P class=style4><? _e("����������") ?>: <?php if (!empty($row['fio'])) echo $row['fio'];
else echo @$order['Person']['name_person']; ?></P></TH>
                    <TH scope=row align=middle><b><? _e("�����") ?> &#8470;<?php echo $ouid ?> </b></TH></TR></TBODY></TABLE>



        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
                <TR>
                    <TH class=style2 scope=row align=left>
                        <BLOCKQUOTE>
                            <P class=style4><? _e("���������� ������������ � ������� ��� ������ �� ����� ��� ���������") ?></P></BLOCKQUOTE></TH></TR>
                <TR>
                    <TH class=style4 scope=row align=left>
                        <BLOCKQUOTE>
                            <P><? _e("���������� �������������� ����� ��������������� �� ������� ��� � ������������ ������ ����� ������ ��� �� ��������") ?>.</P></BLOCKQUOTE></TH></TR></TBODY></TABLE>

        <p><br></p>
        <table width=99% cellpadding=2 cellspacing=0 align=center>
            <tr class=tablerow>
                <td class=tablerow>&#8470;</td>
                <td width=35% class=tablerow><? _e("������������") ?></td>
                <td width=15% class=tablerow><? _e("�������") ?></td>
                <td class=tablerow><? _e("����") ?></td>
                <td class=tablerow><? _e("����������") ?></td>
                <td class=tableright><? _e("���������") ?> (<?php echo $PHPShopOrder->default_valuta_code; ?>)</td>
            </tr>
            <?php
            echo $dis;
            $my_total = $row['sum'];
            $my_nds = number_format($my_total * $LoadItems['System']['nds'] / (100 + $LoadItems['System']['nds']), "2", ".", "");
            ?>
            <tr>
                <td colspan=6 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;"><? _e("������") ?>: <?php echo $discount ?></td>
            </tr>
            <tr>
                <td colspan=6 align=right style="border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;"><? _e("�����") ?>:
                    <?php
                    echo $my_total . " ";
                    if ($LoadItems['System']['nds_enabled']) {
                        _e("� �.�. ���") . ": " . $my_nds;
                    }
                    ?>

                </td>
            </tr>

            <tr><td colspan=7 style="border: 0px; border-top: 1px solid #000000;">&nbsp;</td></tr>
        </table>
        <p><b><?php echo __("����� ������������") . " " . ($num + 1) . " " . __("�� �����") . " " . $my_total . " " . $PHPShopOrder->default_valuta_code; ?>
                <br />
                <?php
                $iw = new inwords;
                $s = $iw->get($row['sum']);
                $v = $PHPShopOrder->default_valuta_code;
                if (preg_match("/���/i", $v))
                    echo $s;
                ?>
            </b></p><br>
        <table>
            <tr>
                <td><b><?php _e("��������") ?>:</b></td>
                <td><?php
                    if (!empty($LoadBanc['org_sig']))
                        echo '<img src="' . $LoadBanc['org_sig'] . '">';
                    else
                        echo '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
                    ?></td>
                <td width="150"></td>
                <td >
<?php _e("����������� ������������ ������� �������������� � �������������� ��������� ������ ������������. ��� ���������� ���������������� ���������� ������ ����������� ������������ �������������� � ��������") ?>. 
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php
                    if (!empty($LoadBanc['org_stamp']))
                        echo '<img src="' . $LoadBanc['org_stamp'] . '">';
                    else
                        echo '<div style="padding:50px;border-bottom: 1px solid #000000;border-top: 1px solid #000000;border-left: 1px solid #000000;border-right: 1px solid #000000;" align="center">' . __('�.�.') . '</div>';
                    ?>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
<?php writeLangFile(); ?>