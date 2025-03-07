<?php
/**
 * �������� ����� ����-������� ��� Order Agent
 * @package PHPShopExchange
 * @author PHPShop Software
 * @version 1.1
 */

$_classPath="../../../phpshop/";
include($_classPath."class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("security");

$PHPShopBase = new PHPShopBase($_classPath."inc/config.ini");

$PHPShopSystem = new PHPShopSystem();
$LoadItems['System']=$PHPShopSystem->getArray();

// ���������� ���������
$SysValue['bank']=unserialize($LoadItems['System']['bank']);
$pathTemplate=$SysValue['dir']['templates'].chr(47).$_SESSION['skin'];

if(!PHPShopSecurity::true_param($_GET['orderID'],$_GET['datas']))
exit('Error _GET');

$orderID=PHPShopSecurity::TotalClean($_GET['orderID'],5);
$datas=PHPShopSecurity::TotalClean($_GET['datas'],1);

$PHPShopOrder = new PHPShopOrderFunction($orderID);

$sql="select * from ".$SysValue['base']['table_name1']." where id='$orderID' and datas='$datas'";
$n = 1;
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$nds = $LoadItems['System']['nds'];
$dis = $total_summa_nds = $total_summa=$sum=$num=null;

foreach ($order['Cart']['cart'] as $val) {
    $this_price = ($PHPShopOrder->returnSumma(number_format($val['price'], "2", ".", ""), $order['Person']['discount']));
    $this_nds = number_format($this_price * $nds / (100 + $nds), "2", ".", "");
    $this_price_bez_nds = ($this_price - $this_nds) * $val['num'];
    $this_price_c_nds = number_format($this_price * $val['num'], "2", ".", "");
    $this_nds_summa+=$this_nds * $val['num'];

    $dis.="
  <tr>
    <td >" . $val['name'] . "</td>
    <td align=\"center\">796</td>
    <td align=\"center\">" . $val['ed_izm'] . "</td>
    <td align=\"right\">" . $val['num'] . "</td>
    <td align=\"right\">" . $this_price . "</td>
    <td align=\"right\">" . $this_price_bez_nds . "</td>
    <td align=\"right\">--</td>
    <td align=\"right\">" . $LoadItems['System']['nds'] . "%</td>
    <td align=\"right\">" . $this_nds * $val['num'] . "</td>
    <td align=\"right\">" . $this_price_c_nds . "</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
  </tr>
  ";
    $total_summa_nds+=$summa_nds;
    $total_summa+=$PHPShopOrder->returnSumma(($val['price'] * $val['num']), $order['Person']['discount']);

//����������� � ������������ ����
    $goodid = $val['id'];
    $goodnum = $val['num'];
    $wsql = 'select weight from ' . $SysValue['base']['table_name2'] . ' where id=\'' . $goodid . '\'';
    $wresult = mysqli_query($link_db,$wsql);
    $wrow = mysqli_fetch_array($wresult);
    $cweight = $wrow['weight'] * $goodnum;
    if (!$cweight) {
        $zeroweight = 1;
    } //���� �� ������� ����� ������� ���!
    $weight+=$cweight;


    $sum+=$val['price'] * $val['num'];
    $num+=$val['num'];
    $n++;
}
//�������� ��� �������, ���� ���� �� ���� ����� ��� ��� ����
if ($zeroweight) {
    $weight = 0;
}


$PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
$deliveryPrice = $PHPShopDelivery->getPrice($sum, $weight);

$summa_nds_dos = number_format($deliveryPrice * $nds / (100 + $nds), "2", ".", "");

$dis.="
  <tr>
    <td >�������� " . $PHPShopDelivery->getCity() . "</td>
    <td align=\"right\">----</td>
    <td align=\"center\">----</td>
    <td align=\"right\">1</td>
    <td align=\"right\">" . $deliveryPrice . "</td>
    <td align=\"right\">" . $deliveryPrice . "</td>
    <td align=\"right\">--</td>
    <td align=\"right\">" . $LoadItems['System']['nds'] . "%</td>
    <td align=\"right\">" . $summa_nds_dos . "</td>
    <td align=\"right\">" . $deliveryPrice . "</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
    <td align=\"center\">---</td>
  </tr>
  ";

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * ($nds / (100 + $nds)), "2", ".", "");
}
$sum = number_format($sum, "2", ".", "");

$name_person = $order['Person']['name_person'];

if ($row['org_name'] or $order['Person']['org_name'])
    $org_name = $order['Person']['org_name'] . $row['org_name'];
else
    $org_name = $row['fio'];

$datas = PHPShopDate::dataV($datas, "false");

// ����� �������� ��� ������ ������ ������ � ������
if (!empty($order['Person']['dos_ot']) OR !empty($order['Person']['dos_do']))
    $dost_ot = " ��: " . $order['Person']['dos_ot'] . ", ��: " . $order['Person']['dos_do'];

// ��������� ����� �������� � ������ ������� ������� ������ � �������
if ($row['org_name'])
    $adr_info .= ", " . $row['org_name'];
elseif ($row['fio'] OR $order['Person']['name_person'])
    $adr_info .= ", " . $row['fio'] . $order['Person']['name_person'];
if ($row['country'])
    $adr_info .= ", ������: " . $row['country'];
if ($row['state'])
    $adr_info .= ", ������/����: " . $row['state'];
if ($row['city'])
    $adr_info .= ", �����: " . $row['city'];
if ($row['index'])
    $adr_info .= ", ������: " . $row['index'];
if ($row['street'] OR $order['Person']['adr_name'])
    $adr_info .= ", �����: " . $row['street'] . $order['Person']['adr_name'];
if ($row['house'])
    $adr_info .= ", ���: " . $row['house'];
if ($row['porch'])
    $adr_info .= ", �������: " . $row['porch'];
if ($row['door_phone'])
    $adr_info .= ", ��� ��������: " . $row['door_phone'];
if ($row['flat'])
    $adr_info .= ", ��������: " . $row['flat'];

$adr_info = substr($adr_info, 2);


// ������� ����� ��������� ����
$chek_num = substr(abs(crc32(uniqid(rand(), true))), 0, 5);
$LoadBanc = unserialize($LoadItems['System']['bank']);
?>
<head>
    <title>���� - ������� �<?php echo @$ouid ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <link href="../style.css" type=text/css rel=stylesheet>
    <style media="screen" type="text/css">
        .save{
            display: none;
        }

        * HTML .save{ /* ������ ��� �������� IE */
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
            �����������
        </button> 
        <hr> 
    </div>
   
    <table align="center" width="90%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td valign="top" align="right">
                <?php
                $GetIsoValutaOrder = $PHPShopOrder->default_valuta_code;
                if (preg_match("/���/", $GetIsoValutaOrder)) {
                    echo '
	<div id="d1">
���������� � 1<br>
� ������������� ������������� ���������� ���������<br>
�� 26 ������� 2011 �. � 1137 
 </div>
';
                }

                ?>
            </td>
        </tr>
        <tr>
            <td valign="top" id="d2">����-������� �<input title="��������" value="<?php echo @$ouid ?> �� <?php echo PHPShopDate::get($row['datas']) ?> �."><br>
                ����������� � --  �� --</td>
        </tr>
        <tr>
            <td valign="top" >��������: <?php echo $LoadItems['System']['company'] ?><br />					
                �����: <?php echo $LoadBanc['org_adres'] ?>, <?php echo $LoadItems['System']['tel'] ?> <br />							
                ����������������� ����� �������� (���) <?php echo $LoadBanc['org_inn'] ?>\<?php echo $LoadBanc['org_kpp'] ?> <br />							
                ���������������� � ��� �����: �� ��	<br />						
                ��������������� � ��� �����:  <?php echo @$adr_info ?>	<br />						
                � ��������-���������� ���������       <br />							
                ����������: <?php echo $org_name ?>	<br />						
                �����: <?php echo @$adr_info ?> <br />							
                ����������������� ����� ���������� (���) <?php echo @$order['Person']['org_inn'] . $row['org_inn'] ?>/ ��� <?php echo @$order['Person']['org_kpp'] . $row['org_kpp'] ?> <br />							
            </td>
        </tr>
        <tr>
            <td valign="top" >
                <table style="margin-top:10px;" bordercolor="#000000"  border="1" cellspacing="0" cellpadding="0">




                    <tr>
                        <td width="200" align="center" rowspan="2">������������ ������ (�������� ����������� 
                            �����, ��������� �����), �������������� �����</td>
                        <td  align="center" colspan="2">������� ���������</td>
                        <td  align="center" rowspan="2">����-
                            ������(�����)</td>
                        <td  align="center"  rowspan="2">���� (�����) �� ������� ���������</td>
                        <td  align="center"  rowspan="2">��������� ������� (�����, �����),
                            �������������
                            ���� ��� ������
                            �����</td>
                        <td  align="center" rowspan="2" >� ��� ����� ����� ������</td>
                        <td  align="center"  rowspan="2">��������� ������</td>
                        <td  align="center" rowspan="2" >����� ������, ������������� ����������</td>
                        <td  align="center"  rowspan="2">��������� ������� (�����, �����), ������������� ���� � ������� �����</td>
                        <td  align="center"  colspan="2">������<br>
                            ������������� ������</td>
                        <td  align="center"  rowspan="2">����� ���������� ����������</td>
        </tr>
        <tr>
                        <td align="center">���</td>
                        <td align="center">�������� �����������
                            (������������)</td>
                        <td align="center">�������� ���</td>
                        <td align="center">������� ������������</td>

                    </tr>
                    <tr>
                    <tr>
                        <td align="center">1</td>
                        <td align="center">2</td>
                        <td align="center">2�</td>
                        <td align="center">3</td>
                        <td align="center">4</td>
                        <td align="center">5</td>
                        <td align="center">6</td>
                        <td align="center">7</td>
                        <td align="center">8</td>
                        <td align="center">9</td>
                        <td align="center">10</td>
                        <td align="center">10a</td>
                        <td align="center">11</td>
                    </tr>
                    <?php echo $dis; ?>

                    <tr>
                        <td colspan="8"><b>����� � ������</b></td>

                        <td align="right"><?php echo $this_nds_summa + $summa_nds_dos; ?></td>
                        <td align="right"><?php echo $total_summa + $deliveryPrice; ?></td>
                        <td colspan="3">&nbsp;</td>

                    </tr>

                </table>
            </td>
        </tr>
        <tr>
            <td valign="top" >&nbsp;</td>


        </tr>
        <tr>
            <td valign="top" >&nbsp;</td>


        </tr>
        <tr>
            <td valign="top" ><table width="100%" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td width="50%">
                            <table  border="0" cellspacing="3" cellpadding="0">
                                <tr>
                                    <td>������������ �����������</td>                                
                                    <td>____________________</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>____________________</td>
                                </tr>
                                <tr>
                                    <td>��� ���� �������������� ����</td>                                
                                    <td id="center">(�������)</td>
                                    <td></td>
                                    <td id="center">(�.�.�.)</td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%" valign="top" align="right">
                            <table  border="0" cellspacing="3" cellpadding="0">
                                <tr>
                                    <td>������� ���������</td>                                
                                    <td>____________________</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>____________________</td>
                                </tr>
                                <tr>
                                    <td>��� ���� �������������� ����</td>                                
                                    <td id="center">(�������)</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td id="center" >(�.�.�.)</td>
                                </tr>
                            </table>
                        </td>
                                </tr>
                </table>
            </td>
                                </tr>
                                <tr>
            <td valign="top" >
                <p><br></p>
                <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                        <td width="70%">
                            <table  border="0" cellspacing="3" cellpadding="0">
                                <tr>
                                    <td>�������������� ���������������</td>                                
                                    <td>____________________</td>
                                    <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                    <td>____________________</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td id="center">(�������)</td>
                                    <td></td>
                                    <td id="center" >(�.�.�.)</td>
                                </tr>
                            </table>
                        </td>
                        <td width="10%"></td>
                        <td width="20%" valign="top" align="right">
                            <table  border="0" cellspacing="3" cellpadding="0">
                                <tr>
                                    <td>______________________________________________</td>
                                <tr>                             
                                    <td id="center">(��������� ������������� � ��������������� ����������� ��������������� ���������������)</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

</body>
</html>