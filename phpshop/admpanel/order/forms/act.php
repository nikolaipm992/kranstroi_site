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

// ���������� ���������
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);

$sql = "select * from " . $SysValue['base']['orders'] . " where id=" . intval($_GET['orderID']);
$n = 1;
@$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$dis = $sum = $num = $adr_info = null;

// ����� ��������
if ($row['country'])
    $adr_info .= ", " . __("������") . ": " . $row['country'];
if ($row['state'])
    $adr_info .= ", " . __("������/����") . ": " . $row['state'];
if ($row['city'])
    $adr_info .= ", " . __("�����") . ": " . $row['city'];
if ($row['index'])
    $adr_info .= ", " . __("������") . ": " . $row['index'];
if ($row['street'] OR ! empty($order['Person']['adr_name']))
    $adr_info .= ", " . __("�����") . ": " . $row['street'] . @$order['Person']['adr_name'];
if ($row['house'])
    $adr_info .= ", " . __("���") . ": " . $row['house'];
if ($row['porch'])
    $adr_info .= ", " . __("�������") . ": " . $row['porch'];
if ($row['door_phone'])
    $adr_info .= ", " . __("��� ��������") . ": " . $row['door_phone'];
if ($row['flat'])
    $adr_info .= ", " . __("��������") . ": " . $row['flat'];

$adr_info = substr($adr_info, 2);

foreach ($order['Cart']['cart'] as $val) {

    // ������
    if ($val['type'] == 1 or $val['type'] == "")
        continue;

    $dis .= "<tr class=tablerow>
		<td class=tablerow>" . $n . "</td>
		<td class=tablerow>" . $val['name'] . "</td>
		<td align=right class=tablerow>" . $val['num'] . "</td>
                <td align=right class=tablerow>" . $val['price'] . "</td>
		<td class=tableright>".$val['total']."</td>
	</tr>";
    $sum += $val['price'] * $val['num'];
    $num += $val['num'];
    $n++;
}

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * $nds / (100 + $nds), "2", ".", "");
}
$sum = number_format($sum, "2", ".", "");

$name_person = @$order['Person']['name_person'];
$org_name = @$order['Person']['org_name'];
$datas = PHPShopDate::dataV($datas, false);

// ������� ����� ��������� ����
$chek_num = substr(abs(crc32(uniqid(rand(), true))), 0, 5);
$LoadBanc = unserialize($LoadItems['System']['bank']);
?>
<!doctype html>
<head>
    <title><?php echo __("��� ����������� �����") . " &#8470;" . $chek_num ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link href="style.css" type=text/css rel=stylesheet>
    <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
</head>
<body>
    <div align="right" class="nonprint">
        <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: '<?php echo __("���") . " &#8470;" . $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}});"><?php _e("���������") ?></button> 
        <button onclick="window.print();"><?php _e("�����������") ?></button> 
        <hr>
    </div>
    <div id="content">
        <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0><TBODY>
                <TR>
                    <TH scope=row align=middle width="30%" rowSpan=3><img src="<?php echo $PHPShopSystem->getLogo(true); ?>" alt="" border="0" style="max-width: 200px;height: auto;"></TH>
                    <TD align=right>
                        <BLOCKQUOTE>
                            <P><b><?php _e("��� ����������� �����") ?></b> <SPAN class=style4>&#8470;<?php echo @$chek_num ?> - <?php echo $datas ?></SPAN> </P></BLOCKQUOTE></TD></TR>
                <TR>
                    <TD align=right>
                        <BLOCKQUOTE>
                            <P><SPAN class=style4><?php echo $LoadBanc['org_adres'] ?>, <? _e("�������");
echo " " . $LoadItems['System']['tel'] ?> </SPAN></P></BLOCKQUOTE></TD></TR>
                <TR>
                    <TD align=right>
                        <BLOCKQUOTE>
                            <P class=style4><? _e("�����������") ?>: <?php echo $LoadItems['System']['company'] ?></P></BLOCKQUOTE></TD></TR></TBODY></TABLE>

        <p><br></p>
        <table width=99% cellpadding=2 cellspacing=0 align=center>
            <tr class=tablerow>
                <td class=tablerow>&#8470;</td>
                <td width=50% class=tablerow><?php _e("������������ �����") ?></td>
                <td class=tableright><?php _e("���-��") ?></td>
                <td class=tableright><?php _e("����") ?></td>
                <td class=tableright style="border-right: 1px solid #000000;"><?php _e("�����") ?></td>
            </tr>
<?php echo $dis; ?>
            <tr><td colspan=6 style="border: 0px; border-top: 1px solid #000000;">&nbsp;</td></tr>
        </table>


����� �������: <?php echo @$adr_info ?><br>
����������������� ������ ��������� ��������� � � ����. �������� ��������� �� ������, �������� � ������ �������� ����� �� �����. �������� �� ��������� ������ 12 �������".
<br><br>
        <table>
            <tr>
                <td><b><?php _e("�����������") ?>:</b></td>
                <td><?php
                    if (!empty($LoadBanc['org_sig']))
                        echo '<img src="' . $LoadBanc['org_sig'] . '">';
                    else
                        echo '<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
                    ?></td>
                <td width="500"></td>
                <td><b><?php _e("��������") ?>: ____________________________</b></td>
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