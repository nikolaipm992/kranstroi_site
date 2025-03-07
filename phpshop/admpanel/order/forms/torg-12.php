<?php
$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini",true,true);
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$LoadItems['System'] = $PHPShopSystem->getArray();
$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);

// ����������� ����
$company = $PHPShopOrder->getParam('company');
$PHPShopSystem->setCompany($company);

$blank_org_name = $PHPShopSystem->getSerilizeParam('bank.org_name');
$blank_org_inn = $PHPShopSystem->getSerilizeParam('bank.org_inn');
$blank_org_kpp = $PHPShopSystem->getSerilizeParam('bank.org_kpp');
$blank_org_ur_adres = $PHPShopSystem->getSerilizeParam('bank.org_ur_adres');
$blank_org_adres = $PHPShopSystem->getSerilizeParam('bank.org_adres');

$LoadBanc = unserialize($LoadItems['System']['bank']);
$LoadBanc['org_sig'] = $PHPShopSystem->getSerilizeParam('bank.org_sig');
$LoadBanc['org_sig_buh'] = $PHPShopSystem->getSerilizeParam('bank.org_sig_buh');
$LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');
$LoadBanc['org_stamp'] = $PHPShopSystem->getSerilizeParam('bank.org_stamp');
$LoadBanc['org_adres']=$PHPShopSystem->getSerilizeParam('bank.org_adres');
$LoadBanc['org_inn']=$PHPShopSystem->getSerilizeParam('bank.org_inn');
$LoadBanc['org_kpp']=$PHPShopSystem->getSerilizeParam('bank.org_kpp');
$LoadItems['System']['company']=$PHPShopSystem->getParam('company');

$fio = $PHPShopOrder->getParam('fio');
if(!empty($fio))
    $blank_person_user = $PHPShopOrder->getParam('fio');
else
    $blank_person_user = $PHPShopOrder->getSerilizeParam('orders.Person.name_person');

$orgData = $PHPShopOrder->getSerilizeParam('orders.Person.org_name');
if(empty($orgData)) {
    $orgData = $PHPShopOrder->getParam('org_name');
}
$inn = $PHPShopOrder->getSerilizeParam('orders.Person.org_inn');
if(empty($inn)) {
    $inn = $PHPShopOrder->getParam('org_inn');
}
if(!empty($inn)) {
    $orgData .= ' ��� ' . $inn;
}

$kpp = $PHPShopOrder->getSerilizeParam('orders.Person.org_kpp');
if(empty($kpp)) {
    $kpp =$PHPShopOrder->getParam('org_kpp');
}
if(!empty($kpp)) {
    $orgData .= ' ��� ' . $kpp;
}

if(!empty($PHPShopOrder->getParam('org_yur_adres'))) {
    $orgData .= ' ��. ����� ' .  $PHPShopOrder->getParam('org_yur_adres');
}

// ���������� ���������
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);
$pathTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'];


$sql = "select * from " . $SysValue['base']['table_name1'] . " where id=" . intval($_GET['orderID']);
$n = 1;
@$result = mysqli_query($link_db, $sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$nds = $LoadItems['System']['nds'];
$dis = $weight = $adr_info = null ;
$sum=$num=$this_nds_summa=$total_summa_nds=$total_summa_nds_taxe=$total_summa=0;
if (is_array($order['Cart']['cart']))
    foreach ($order['Cart']['cart'] as $val) {
    
        // ������
        if($val['type'] == 2)
            continue;
    
        $this_price = ($PHPShopOrder->returnSumma(number_format($val['price'], "2", ".", ""), (int)$order['Person']['discount']));
        $this_nds = number_format($this_price * $nds / (100 + $nds), "2", ".", "");
        $this_price_bez_nds = ($this_price - $this_nds) * $val['num'];
        $this_price_c_nds = number_format($this_price * $val['num'], "2", ".", "");
        $this_nds_summa+=$this_nds * $val['num'];

        $dis.='<tr class="�������113">   
        <td style="text-align:center;width:0.924cm; " class="�������1_A34"><p class="P41" style="text-align:center;">' . $n . '</p></td>
        <td colspan="4" style="text-align:left;width:1.513cm; " class="�������1_A34"><p class="P42">' . $val['name'] . '</p></td>
        <td style="text-align:left;width:0.716cm; " class="�������1_A34"><p class="P40"></p></td>
        <td style="text-align:left;width:0.93cm; " class="�������1_A34"><p class="P40">' . $val['ed_izm'] . '</p></td>
        <td style="text-align:left;width:0.877cm; " class="�������1_A34"><p class="P40">�</p></td>
        <td colspan="2" style="text-align:left;width:0.665cm; " class="�������1_A34"><p class="P42">�</p></td>
        <td colspan="2" style="text-align:left;width:0.162cm; " class="�������1_A34"><p class="P41">�</p></td>
        <td style="text-align:left;width:0.903cm; " class="�������1_A34"><p class="P42">�</p></td>
        <td style="text-align:left;width:0.903cm; " class="�������1_A34"><p class="P42">�</p></td>
        <td colspan="2" style="text-align:left;width:0.24cm; " class="�������1_A34"><p class="P41">' . $val['num'] . '</p></td>
        <td colspan="3" style="text-align:left;width:1.619cm; " class="�������1_A34"><p class="P41">' . $this_price . '</p></td>
        <td colspan="4" style="text-align:left;width:0.22cm; " class="�������1_A34"><p class="P41">' . $this_price_bez_nds . '</p></td>
        <td colspan="2" style="text-align:left;width:1.171cm; " class="�������1_A34"><p class="P40">' . $nds . '</p></td>
        <td colspan="4" style="text-align:left;width:0.459cm; " class="�������1_A34"><p class="P47">' . $this_nds_summa . '</p></td>
        <td colspan="2" style="text-align:left;width:0.132cm; " class="�������1_A34"><p class="P47">' . $this_price_c_nds . '</p></td>
      </tr>';

        $total_summa_nds+=$this_price_bez_nds;
        $total_summa_nds_taxe+=$this_nds_summa;
        $total_summa+=$PHPShopOrder->returnSumma(($val['price'] * $val['num']), (int)$order['Person']['discount']);

        //����������� � ������������ ����
        $goodid = $val['id'];
        $goodnum = $val['num'];
        $wsql = 'select weight from ' . $SysValue['base']['table_name2'] . ' where id=\'' . $goodid . '\'';
        $wresult = mysqli_query($link_db, $wsql);
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

$total_summa_nds = number_format($sum, "2", ".", "");
$total_summa = $total_summa_nds;

$PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);
$PHPShopDelivery->checkMod($order['Cart']['dostavka']);
$deliveryPrice = $PHPShopDelivery->getPrice($sum, $weight);

$summa_nds_dos = number_format($deliveryPrice * $nds / (100 + $nds), "2", ".", "");

//$sum = $row['sum'];

if ($LoadItems['System']['nds_enabled']) {
    $nds = $LoadItems['System']['nds'];
    $nds = number_format($sum * ($nds / (100 + $nds)), "2", ".", "");
}



if ($row['org_name'] or !empty($order['Person']['org_name']))
    $org_name = $order['Person']['org_name'] . $row['org_name'];
else
    $org_name = $row['fio'];

$datas = PHPShopDate::dataV($datas, "false");

// ����� �������� ��� ������ ������ ������ � ������
if (!empty($order['Person']['dos_ot']) OR !empty($order['Person']['dos_do']))
    $dost_ot = " ��: " . $order['Person']['dos_ot'] . ", ��: " . $order['Person']['dos_do'];

if(!empty($row['fio']))
    $user = $row['fio'];
else
    $user = $order['Person']['name_person'];

// ��������� ����� �������� � ������ ������� ������� ������ � �������
if ($row['org_name'])
    $adr_info .= ", " . $row['org_name'];
elseif ($row['fio'] OR $order['Person']['name_person'])
    $adr_info .= ", " . $user;
if ($row['country'])
    $adr_info .= ", ������: " . $row['country'];
if ($row['state'])
    $adr_info .= ", ������/����: " . $row['state'];
if ($row['city'])
    $adr_info .= ", �����: " . $row['city'];
if ($row['index'])
    $adr_info .= ", ������: " . $row['index'];
if ($row['street'] OR $order['Person']['adr_name'])
    $adr_info .= ", �����: " . $row['street'] . @$order['Person']['adr_name'];
if ($row['house'])
    $adr_info .= ", ���: " . $row['house'];
if ($row['porch'])
    $adr_info .= ", �������: " . $row['porch'];
if ($row['door_phone'])
    $adr_info .= ", ��� ��������: " . $row['door_phone'];
if ($row['flat'])
    $adr_info .= ", ��������: " . $row['flat'];

$adr_info = substr($adr_info, 2);
?>

<!doctype html>
<html>
    <head>
    <title>��������������� ����� ����-12 �<?php echo @$ouid ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
        <link href="style.css" type=text/css rel=stylesheet>
            <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
            <link href="style.css" type=text/css rel=stylesheet>
                <style type="text/css">
                    @page {  }
                    table { border-collapse:collapse; border-spacing:0; empty-cells:show }
                    td, th { vertical-align:top; font-size:12pt;}
                    h1, h2, h3, h4, h5, h6 { clear:both }
                    ol, ul { margin:0; padding:0;}
                    li { list-style: none; margin:0; padding:0;}
                    <!-- "li span.odfLiEnd" - IE 7 issue-->
                    li span. { clear: both; line-height:0; width:0; height:0; margin:0; padding:0; }
                    span.footnodeNumber { padding-right:1em; }
                    span.annotation_style_by_filter { font-size:95%; font-family:Arial; background-color:#fff000;  margin:0; border:0; padding:0;  }
                    * { margin:0;}
                    .P1 { font-size:10pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P10 { font-size:4.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P11 { font-size:2.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P12 { font-size:5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P13 { font-size:5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P14 { font-size:3.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P15 { font-size:3.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P16 { font-size:8.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P17 { font-size:7.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P18 { font-size:10pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P19 { font-size:10pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P2 { font-size:9.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P20 { font-size:4pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P21 { font-size:5.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P22 { font-size:3pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P23 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P24 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P25 { font-size:10pt; line-height:0.467cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P26 { font-size:6pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P27 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P28 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P29 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P3 { font-size:2pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P30 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P31 { font-size:10pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P32 { font-size:10pt; line-height:0.467cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P33 { font-size:7pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P34 { font-size:9pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P35 { font-size:12pt; line-height:0.467cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Arial; vertical-align:top; writing-mode:lr-tb; color:#000000; font-weight:bold; }
                    .P36 { font-size:11pt; line-height:0.328cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.053cm; text-align:center ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
                    .P37 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P38 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P39 { font-size:10pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P4 { font-size:2pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P40 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P41 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P42 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P43 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P44 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P45 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P46 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P47 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P48 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P49 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P5 { font-size:1.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P50 { font-size:10pt; line-height:0.277cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; letter-spacing:-0.126cm; }
                    .P51 { font-size:10pt; line-height:0.268cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; letter-spacing:-0.126cm; }
                    .P52 { font-size:6pt; line-height:0.259cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P53 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P54 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P55 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P56 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P57 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P58 { font-size:11pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.051cm; text-align:center ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
                    .P59 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P6 { font-size:9pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P60 { font-size:10pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P61 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P62 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:right ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P63 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P64 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P65 { font-size:10pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P66 { font-size:10pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P67 { font-size:5pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:center ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P68 { font-size:1pt; line-height:0.217cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Arial; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P69 { font-size:11pt; line-height:0.318cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.025cm; text-align:left ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
                    .P7 { font-size:1pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P70 { font-size:10pt; line-height:0.293cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P71 { font-size:10pt; line-height:0.367cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P72 { font-size:10pt; line-height:0.367cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Verdana; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P73 { font-size:10pt; line-height:0.367cm; margin:100%; margin-bottom:0.055cm; margin-left:0.126cm; margin-right:0.055cm; margin-top:0.078cm; text-align:left ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; color:#000000; }
                    .P74 { font-size:11pt; line-height:115%; margin:100%; margin-bottom:0.353cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:left ! important; text-indent:0.055cm; font-family:Calibri; vertical-align:top; writing-mode:lr-tb; }
                    .P8 { font-size:1pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .P9 { font-size:4.5pt; line-height:100%; margin:100%; margin-bottom:0.055cm; margin-left:0.055cm; margin-right:0.055cm; margin-top:0.055cm; text-align:center ! important; text-indent:0.055cm; font-family:Tahoma; vertical-align:top; writing-mode:lr-tb; }
                    .�������1 { width:100%; writing-mode:lr-tb; }
                    .�������2 { width:100%; writing-mode:lr-tb; margin-top: 50px;}
                    .�������1_A1 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
                    .�������1_A34 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.035cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.035cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������1_A43 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
                    .�������1_A5 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
                    .�������1_A7 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
                    .�������1_A8 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm;}
                    .�������1_A9 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������1_B33 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-width:0.0333cm; border-top-style:solid; border-top-color:#000000; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������1_D15 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.0333cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
                    .�������1_M43 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.035cm; border-style:solid; border-color:#000000; }
                    .�������1_Y4 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
                    .�������1_b21 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; }
                    .�������1_e3 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; border-style:solid; border-color:#000000; }
                    .�������1_e4 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.0333cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������1_e6 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.0333cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������1_e7 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.0333cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������2_A1 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; border-style:solid; border-color:#000000; }
                    .�������2_A2 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.0333cm; border-style:solid; border-color:#000000; }
                    .�������2_A4 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.035cm; border-left-style:solid; border-left-color:#000000; border-right-width:0.035cm; border-right-style:solid; border-right-color:#000000; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������2_A6 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
                    .�������2_A7 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
                    .�������2_A8 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
                    .�������2_C24 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-style:none; }
                    .�������2_E3 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-width:0.0333cm; border-right-style:solid; border-right-color:#000000; border-top-width:0.0333cm; border-top-style:solid; border-top-color:#000000; border-bottom-width:0.0333cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������2_J28 { vertical-align:bottom; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������2_J37 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-width:0.035cm; border-top-style:solid; border-top-color:#000000; border-bottom-style:none; }
                    .�������2_N19 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }
                    .�������2_a17 { padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-width:0.035cm; border-left-style:solid; border-left-color:#000000; border-right-style:none; border-top-style:none; border-bottom-style:none; }
                    .�������2_a6 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-width:0.035cm; border-style:solid; border-color:#000000; }
                    .�������2_f11 { vertical-align:middle; padding-left:0.126cm; padding-right:0.126cm; padding-top:0.055cm; padding-bottom:0.055cm; border-left-style:none; border-right-style:none; border-top-style:none; border-bottom-width:0.035cm; border-bottom-style:solid; border-bottom-color:#000000; }

                    .T2 { color:#000000; font-family:Verdana; font-size:10pt; }
                    <!-- ODF styles with no properties representable as CSS -->
                    .�������1.1 .�������1.10 .�������1.11 .�������1.13 .�������1.14 .�������1.16 .�������1.2 .�������1.21 .�������1.26 .�������1.31 .�������1.32 .�������1.5 .�������1.6 .�������1.7 .�������1.8 .�������1.9 .�������2.1 .�������2.10 .�������2.12 .�������2.16 .�������2.17 .�������2.18 .�������2.19 .�������2.2 .�������2.20 .�������2.22 .�������2.23 .�������2.24 .�������2.27 .�������2.29 .�������2.3 .�������2.30 .�������2.36 .�������2.39 .�������2.4 .�������2.8 .�������2.9 { }
                </style>
                <script src="../../../lib/templates/print/js/html2pdf.bundle.min.js"></script>
                </head>
                <body style="margin:10px">

                    <div align="right" class="nonprint">
                        <button onclick="html2pdf(document.getElementById('content'), {margin: 1, filename: '����-12 �<?php echo $ouid ?>.pdf', html2canvas: {dpi: 192, letterRendering: true}, jsPDF: {orientation: 'landscape'}});">���������</button> 
                        <button onclick="window.print();">�����������</button> 
                        <br><br><hr><br><br>
                                            </div>
                                            <div id="content">
                                                <table border="0" cellspacing="0" cellpadding="0" class="�������1">
                                                    <colgroup>
                                                        <col width="40"/>
                                                        <col width="66"/>
                                                        <col width="9"/>
                                                        <col width="213"/>
                                                        <col width="94"/>
                                                        <col width="31"/>
                                                        <col width="41"/>
                                                        <col width="38"/>
                                                        <col width="29"/>
                                                        <col width="6"/>
                                                        <col width="7"/>
                                                        <col width="38"/>
                                                        <col width="39"/>
                                                        <col width="39"/>
                                                        <col width="10"/>
                                                        <col width="59"/>
                                                        <col width="71"/>
                                                        <col width="9"/>
                                                        <col width="18"/>
                                                        <col width="10"/>
                                                        <col width="57"/>
                                                        <col width="7"/>
                                                        <col width="25"/>
                                                        <col width="51"/>
                                                        <col width="8"/>
                                                        <col width="20"/>
                                                        <col width="5"/>
                                                        <col width="14"/>
                                                        <col width="58"/>
                                                        <col width="6"/>
                                                        <col width="93"/>
                                                    </colgroup>
                                                    <tr class="�������11">
                                                        <td colspan="17" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P1">�</p></td>
                                                        <td colspan="14" style="text-align:left;width:0.213cm; " class="�������1_A1"><p class="P26">��������������� ����� � ����-12<br/>���������� �������������� ����������� ������ �� 25.12.98 � 132</p></td>
                                                    </tr>
                                                    <tr class="�������12">  <td colspan="17" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P3">�</p></td>
                                                        <td colspan="14" style="text-align:left;width:0.213cm; " class="�������1_A1"><p class="P23">�</p></td>
                                                    </tr>
                                                    <tr class="�������11">    <td colspan="17" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P2">�</p></td>
                                                        <td colspan="13" style="text-align:left;width:0.213cm; " class="�������1_A1"><p class="P23">�</p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e3"><p class="P27">���</p></td>
                                                    </tr>
                                                    <tr class="�������11">    <td colspan="17" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P2">�</p></td>
                                                        <td colspan="7" style="text-align:left;width:0.213cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td colspan="6" style="text-align:left;width:0.185cm; " class="�������1_Y4"><p class="P28">����� �� ���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P27">0310001</p></td>
                                                    </tr>
                                                    <tr class="�������15">    <td colspan="25" style="text-align:left;width:0.924cm; " class="�������1_A5"><p class="P30"><?php echo $blank_org_name ?>, &nbsp;���&nbsp;<?php echo$blank_org_inn ?>, ���&nbsp;<?php echo $blank_org_kpp ?>, ��. �����:&nbsp;<?php echo $blank_org_ur_adres ?>, �������� �����:&nbsp;<?php echo $blank_org_adres ?></p></td>
                                                        <td colspan="5" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28">�� ���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>
                                                    <tr class="�������17">    
                                                        <td colspan="25" style="text-align:left;width:0.924cm; " class="�������1_A7"><p class="P52">(�����������-����������������, �����, �������, ����, ���������� ���������)</p></td>
                                                        <td colspan="3" style="text-align:left;width:0.459cm; " class="�������1_A1"><p class="P9">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:1.326cm; " class="�������1_A1"><p class="P37">�</p></td>
                                                        <td rowspan="2" style="text-align:left;width:2.124cm; " class="�������1_e7"><p class="P39">�</p></td>
                                                    </tr>
                                                    <tr class="�������19">
                                                        <td colspan="30" style="text-align:left;width:0.924cm; " class="�������1_A9"><p class="P6">�</p></td>
                                                    </tr>
                                                    <tr class="�������110">   <td colspan="3" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P37">�</p></td>
                                                        <td colspan="18" style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P52">����������� �������������</p></td>
                                                        <td style="text-align:left;width:0.159cm; " class="�������1_A1"><p class="P37">�</p></td>
                                                        <td colspan="8" style="text-align:left;width:0.582cm; " class="�������1_A5"><p class="P28">��� ������������ �� ���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e7"><p class="P31">�</p></td>
                                                    </tr>
                                                    <tr class="�������113">   <td colspan="3" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P29">���������������</p></td>
                                                        <td colspan="22" style="text-align:left;width:4.884cm; " class="�������1_A5"><p class="P29"><?php if(empty($orgData)) echo $blank_person_user; else echo $orgData; ?></p></td>
                                                        <td style="text-align:left;width:0.319cm; " class="�������1_A1"><p class="P24"></p></td>
                                                        <td colspan="4" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28">�� ���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>

                                                    <tr class="�������110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td colspan="22" style="text-align:left;width:4.884cm; " class="�������1_D15"><p class="P52">(�����������, �����, �������, ����, ���������� ���������)</p></td>
                                                        <td style="text-align:left;width:0.319cm; " class="�������1_A1"><p class="P12">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28">�� ���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>

                                                    <tr class="�������110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1" valign="top"><p class="P29">���������</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td colspan="22" style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P29"><?php echo $blank_org_name ?>, &nbsp;���&nbsp;<?php echo$blank_org_inn ?>, ���&nbsp;<?php echo $blank_org_kpp ?>, ��. �����:&nbsp;<?php echo $blank_org_ur_adres ?>, �������� �����:&nbsp;<?php echo $blank_org_adres ?></p></td>
                                                        <td style="text-align:left;width:0.319cm; " class="�������1_A1"><p class="P12">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28"></p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>

                                                    <tr class="�������110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P13">�</p></td>
                                                        <td colspan="22" style="text-align:left;width:4.884cm; " class="�������1_D15"><p class="P52">(�����������, �����, �������, ����, ���������� ���������)</p></td>
                                                        <td style="text-align:left;width:0.319cm; " class="�������1_A1"><p class="P12">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28">�� ���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>

                                                    <tr class="�������110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P29">����������</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P13">�</p></td>
                                                        <td colspan="22" style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P29"><?php if(empty($orgData)) echo $blank_person_user; else echo $orgData; ?></p></td>
                                                        <td style="text-align:left;width:0.319cm; " class="�������1_A1"><p class="P12">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28"></p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>


                                                    <tr class="�������110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P13">�</p></td>
                                                        <td colspan="22" style="text-align:left;width:4.884cm; " class="�������1_D15"><p class="P52">(�����������, �����, �������, ����, ���������� ���������)</p></td>
                                                        <td colspan="2" style="text-align:left;width:1.326cm; " class="�������1_Y4"><p class="P28"></p></td>
                                                        <td colspan="3" style="text-align:left;width:0.319cm; " class="�������1_e3"><p class="P28">����� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>
                                                    <tr class="�������113">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P29">���������</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P16">�</p></td>
                                                        <td colspan="24" style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P16">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:0.319cm; " class="�������1_e3"><p class="P28">���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>
                                                    <tr class="�������110">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P13">�</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P13">�</p></td>
                                                        <td colspan="24" style="text-align:left;width:4.884cm; " class="�������1_D15"><p class="P52">(�������, �����-�����)</p></td>
                                                        <td colspan="3" style="text-align:left;width:0.319cm; " class="�������1_e3"><p class="P28">����� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>
                                                    <tr class="�������126">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:0.873cm; " class="�������1_e3"><p class="P36"><span class="T2">����� ���������</span></p></td>
                                                        <td colspan="4" style="text-align:left;width:1.353cm; " class="�������1_e3"><p class="P36"><span class="T2">���� �����������</span></p></td>
                                                        <td colspan="1" style="text-align:left;width:0.423cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:1.295cm; " class="�������1_Y4"><p class="P28">������������ ��������� </p></td>
                                                        <td style="text-align:left;width:0.125cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:0.319cm; " class="�������1_e3"><p class="P28">���� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e4"><p class="P31">�</p></td>
                                                    </tr>
                                                    <tr class="�������12">
                                                        <td colspan="6" style="text-align:left;width:2.148cm; " class="�������1_Y4"><p class="P35">�������� ���������</p></td>
                                                        <td colspan="5" style="text-align:left;width:0.873cm; " class="�������1_e3">
                                                            <p class="P32"><input title="��������" style="font-size: 14px;font-weight: normal;" value="<?php echo  $ouid;?>"></p></td>
                                                        <td colspan="4" style="text-align:left;width:1.353cm; " class="�������1_e3">
                                                            <p class="P32"><input title="��������" style="font-size: 14px;font-weight: normal;" value="<?php echo PHPShopDate::get($row['datas'],false, false,'.', false) ?>"></p>
                                                        </td>
                                                        <td colspan="9" style="text-align:left;width:0.423cm; " class="�������1_A1"><p class="P4">�</p></td>
                                                        <td colspan="1" style="text-align:left;width:1.295cm; " class="�������1_A1"><p class="P4">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:0.459cm; " class="�������1_Y4"><p class="P28">��� �������� </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_e7"><p class="P34">�</p></td>
                                                    </tr>
                                                    <tr class="�������126">   <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:2.148cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.131cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.873cm; " class="�������1_b21"><p class="P17">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.353cm; " class="�������1_b21"><p class="P17">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.423cm; " class="�������1_A1"><p class="P17">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:1.295cm; " class="�������1_A1"><p class="P25">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:0.459cm; " class="�������1_A1"><p class="P25"> </p></td>
                                                        <td style="text-align:left;width:2.124cm; " class="�������1_A1"><p class="P34">�</p></td>
                                                    </tr>
                                                    <tr class="�������17">    <td colspan="2" style="text-align:left;width:0.924cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:0.212cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:4.884cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:2.148cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.131cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td colspan="7" style="text-align:left;width:0.873cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.423cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:1.295cm; " class="�������1_A1"><p class="P10">�</p></td>
                                                        <td colspan="6" style="text-align:left;width:0.459cm; " class="�������1_A1"><p class="P24">�</p></td>
                                                    </tr>
                                                    <tr class="�������131">   
                                                        <td rowspan="2" style="text-align:left;width:0.924cm; " class="�������1_e3"><p class="P29">���-<br/> ���<br/> ��<br/> ��-<br/> ���-<br/> ��</p></td>
                                                        <td colspan="5" style="text-align:left;width:1.513cm; " class="�������1_e3"><p class="P27">�����</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.93cm; " class="�������1_e3"><p class="P36"><span class="T2">��. ���.</span></p></td>
                                                        <td colspan="2" rowspan="2" style="text-align:left;width:0.665cm; " class="�������1_e3"><p class="P36"><span class="T2">���<br/>���-<br/>���-<br/>��</span></p></td>
                                                        <td colspan="3" style="text-align:left;width:0.162cm; " class="�������1_e3"><p class="P27">����������</p></td>
                                                        <td rowspan="2" style="text-align:left;width:0.903cm; " class="�������1_e3"><p class="P36"><span class="T2">���-<br/>��<br/>����-<br/>��</span></p></td>
                                                        <td rowspan="2" colspan="2" style="text-align:left;width:0.24cm; " class="�������1_e3"><p class="P36"><span class="T2">����-<br/>������<br/>(�����<br/>�����)</span></p></td>
                                                        <td rowspan="2" colspan="3" style="text-align:left;width:1.619cm; " class="�������1_e3"><p class="P36"><span class="T2">���� ���.<br/>���.</span></p></td>
                                                        <td rowspan="2" colspan="4" style="text-align:left;width:0.22cm; " class="�������1_e3"><p class="P36"><span class="T2">����� ���<br/>����� ���<br/>���. ���.</span></p></td>
                                                        <td colspan="6" style="text-align:left;width:1.171cm; " class="�������1_e3"><p class="P27">���</p></td>
                                                        <td rowspan="2" colspan="2" style="text-align:left;width:0.132cm; " class="�������1_e3"><p class="P36"><span class="T2">����� �<br/>������ ���,<br/>���. ���.</span></p></td>
                                                    </tr>
                                                    <tr class="�������132">   
                                                        <td colspan="4" style="text-align:left;width:1.513cm; " class="�������1_e3"><p class="P36"><span class="T2">������������, ��������������,<br/>����, ������� ������</span></p></td>
                                                        <td style="text-align:left;width:0.716cm; " class="�������1_e3"><p class="P27">���</p></td>
                                                        <td style="text-align:left;width:0.93cm; " class="�������1_e3"><p class="P36"><span class="T2">�����-<br/>����-<br/>���</span></p></td>
                                                        <td style="text-align:left;width:0.877cm; " class="�������1_e3"><p class="P36"><span class="T2">���<br/>��<br/>����</span></p></td>
                                                        <td colspan="2" style="text-align:left;width:0.162cm; " class="�������1_e3"><p class="P36"><span class="T2">�<br/>�����<br/>�����</span></p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_e3"><p class="P36"><span class="T2">����,<br/>����</span></p></td>
                                                        <td colspan="2" style="text-align:left;width:1.171cm; " class="�������1_e3"><p class="P36"><span class="T2">����-<br/>��, %</span></p></td>
                                                        <td colspan="4" style="text-align:left;width:0.459cm; " class="�������1_e3"><p class="P36"><span class="T2">����� ���.<br/>���.</span></p></td>
                                                    </tr>
                                                    <tr class="�������126">   <td style="text-align:left;width:0.924cm; " class="�������1_e3"><p class="P46">1</p></td>
                                                        <td colspan="4" style="text-align:left;width:1.513cm; " class="�������1_B33"><p class="P46">2</p></td>
                                                        <td style="text-align:left;width:0.716cm; " class="�������1_B33"><p class="P50">3</p></td>
                                                        <td style="text-align:left;width:0.93cm; " class="�������1_B33"><p class="P46">4</p></td>
                                                        <td style="text-align:left;width:0.877cm; " class="�������1_B33"><p class="P46">5</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.665cm; " class="�������1_B33"><p class="P46">6</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.162cm; " class="�������1_B33"><p class="P46">7</p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_B33"><p class="P46">8</p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_B33"><p class="P46">9</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.24cm; " class="�������1_B33"><p class="P46">10</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.619cm; " class="�������1_B33"><p class="P46">11</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.22cm; " class="�������1_B33"><p class="P46">12</p></td>
                                                        <td colspan="2" style="text-align:left;width:1.171cm; " class="�������1_B33"><p class="P46">13</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.459cm; " class="�������1_B33"><p class="P46">14</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.132cm; " class="�������1_B33"><p class="P46">15</p></td>
                                                    </tr>
<?php echo $dis ?>
                                                    <tr class="�������131">   <td colspan="12" style="text-align:left;width:0.924cm; " class="�������1_A43"><p class="P28">����� </p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_M43"><p class="P31">�</p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_M43"><p class="P31">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.24cm; " class="�������1_M43"><p class="P28">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.619cm; " class="�������1_M43"><p class="P27">X</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.22cm; " class="�������1_M43"><p class="P28"><?php echo $total_summa_nds ?></p></td>
                                                        <td colspan="2" style="text-align:left;width:1.171cm; " class="�������1_M43"><p class="P27">X</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.459cm; " class="�������1_M43"><p class="P28"><?php echo $total_summa_nds_taxe ?></p></td>
                                                        <td colspan="2" style="text-align:left;width:0.132cm; " class="�������1_M43"><p class="P28"><?php echo $total_summa ?></p></td>
                                                    </tr>
                                                    <tr class="�������131">   <td colspan="12" style="text-align:left;width:0.924cm; "><p class="P28">����� �� ��������� </p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_M43"><p class="P31">�</p></td>
                                                        <td style="text-align:left;width:0.903cm; " class="�������1_M43"><p class="P31">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.24cm; " class="�������1_M43"><p class="P28">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.619cm; " class="�������1_M43"><p class="P27">X</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.22cm; " class="�������1_M43"><p class="P28"><?php echo $total_summa_nds ?></p></td>
                                                        <td colspan="2" style="text-align:left;width:1.171cm; " class="�������1_M43"><p class="P27">X</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.459cm; " class="�������1_M43"><p class="P28"><?php echo $total_summa_nds_taxe ?></p></td>
                                                        <td colspan="2" style="text-align:left;width:0.132cm; " class="�������1_M43"><p class="P28"><?php echo $total_summa ?></p></td>
                                                    </tr>
                                                </table>
                                                <table border="0" cellspacing="0" cellpadding="0" class="�������2">
                                                    <colgroup>
                                                        <col width="4"/>
                                                        <col width="15"/>
                                                        <col width="20"/>
                                                        <col width="2"/>
                                                        <col width="35"/>
                                                        <col width="30"/>
                                                        <col width="4"/>
                                                        <col width="52"/>
                                                        <col width="4"/>
                                                        <col width="125"/>
                                                        <col width="8"/>
                                                        <col width="48"/>
                                                        <col width="4"/>
                                                        <col width="43"/>
                                                        <col width="8"/>
                                                        <col width="25"/>
                                                        <col width="31"/>
                                                        <col width="29"/>
                                                        <col width="11"/>
                                                        <col width="4"/>
                                                        <col width="28"/>
                                                        <col width="7"/>
                                                        <col width="35"/>
                                                        <col width="11"/>
                                                        <col width="4"/>
                                                        <col width="32"/>
                                                        <col width="4"/>
                                                        <col width="13"/>
                                                        <col width="6"/>
                                                        <col width="9"/>
                                                        <col width="3"/>
                                                        <col width="6"/>
                                                        <col width="10"/>
                                                        <col width="29"/>
                                                        <col width="14"/>
                                                        <col width="6"/>
                                                        <col width="50"/>
                                                        <col width="9"/>
                                                        <col width="7"/>
                                                        <col width="56"/>
                                                        <col width="27"/>
                                                        <col width="78"/>
                                                        <col width="4"/>
                                                        <col width="18"/>
                                                        <col width="2"/>
                                                        <col width="4"/>
                                                        <col width="7"/>
                                                        <col width="12"/>
                                                        <col width="14"/>
                                                        <col width="7"/>
                                                        <col width="4"/>
                                                        <col width="12"/>
                                                        <col width="99"/>
                                                        <col width="3"/>
                                                        <col width="35"/>
                                                        <col width="15"/>
                                                        <col width="15"/>
                                                        <col width="30"/>
                                                    </colgroup>
                                                    <tr class="�������29">    <td colspan="58" style="text-align:left;width:0.088cm; " class="�������2_A7"><p class="P61">�������� ��������� ����� ���������� �� ______________________ ������� � �������� �________________________________ ���������� ������� �������</p></td>
                                                    </tr>
                                                    <tr class="�������210">   <td colspan="58" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                    </tr>
                                                    <tr class="�������29">    <td colspan="20" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="9" style="text-align:left;width:0.639cm; " class="�������2_A7"><p class="P69"><span class="T2">����� ����� (�����)</span></p></td>
                                                        <td colspan="2" style="text-align:left;width:0.213cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="17" style="text-align:left;width:0.132cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.319cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                    </tr>
                                                    <tr class="�������212">   <td colspan="20" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P14">�</p></td>
                                                        <td colspan="9" style="text-align:left;width:0.639cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.213cm; " class="�������2_A8"><p class="P14">�</p></td>
                                                        <td colspan="17" style="text-align:left;width:0.132cm; " class="�������2_A7"><p class="P67">��������</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.319cm; " class="�������2_A8"><p class="P15">�</p></td>
                                                    </tr>
                                                    <tr class="�������29">
                                                        <td colspan="6" style="text-align:left;width:0.452cm; " class="�������2_A7"><p class="P69"><span class="T2">����� ����</span></p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="11" style="text-align:left;width:1.194cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.263cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.639cm; " class="�������2_A7"><p class="P69"><span class="T2">����� ����� (������)</span></p></td>
                                                        <td style="text-align:left;width:0.079cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="17" style="text-align:left;width:0.132cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.319cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                    </tr>
                                                    <tr class="�������212">   <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P14">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P14">�</p></td>
                                                        <td colspan="11" style="text-align:left;width:1.194cm; " class="�������2_A7"><p class="P67">��������</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.263cm; " class="�������2_A8"><p class="P15">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.639cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.079cm; " class="�������2_A8"><p class="P14">�</p></td>
                                                        <td colspan="17" style="text-align:left;width:0.132cm; " class="�������2_A7"><p class="P67">��������</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.319cm; " class="�������2_A8"><p class="P15">�</p></td>
                                                    </tr>
                                                    <tr class="�������217">   <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="11" style="text-align:left;width:1.194cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.263cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="6" style="text-align:left;width:0.639cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P68">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.079cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="17" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.319cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                    </tr>
                                                    <tr class="�������218">
                                                        <td colspan="12" style="text-align:left;width:0.452cm; " class="�������2_A7"><p class="P69"><span class="T2">���������� (��������, �����������, � �. �.) ��</span></p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="8" style="text-align:left;width:0.982cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.157cm; " class="�������2_A7"><p class="P62">������</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P59">�</p></td>

                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P20">�</p></td>
                                                        <td colspan="13" style="text-align:right;width:0.132cm; " class="�������2_A7"><p class="P69"><span class="T2">�� ������������ �</span></p></td>
                                                        <td colspan="8" style="text-align:left;width:1.274cm; " class="�������2_f11"><p class="P65">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.423cm; " class="�������2_A7"><p class="P63">��</p></td>
                                                        <td style="text-align:left;width:0.159cm; " class="�������2_A7"><p class="P63">"</p></td>
                                                        <td style="text-align:left;width:0.159cm; " class="�������2_A7"><p class="P63">"</p></td>
                                                        <td colspan="5" style="text-align:left;width:0.266cm; " class="�������2_f11"><p class="P65">�</p></td>
                                                        <td style="text-align:left;width:0.344cm; " class="�������2_A7"><p class="P62">�.</p></td>
                                                    </tr>
                                                    <tr class="�������218">   <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="8" style="text-align:left;width:0.982cm; " class="�������2_A7"><p class="P67">��������</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.157cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.161cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.274cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.423cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.159cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.265cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.159cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:0.266cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.797cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td style="text-align:left;width:0.344cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                    </tr>
                                                    <tr class="�������222"> 
                                                        <td colspan="12" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="8" style="text-align:left;width:0.982cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.157cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P11">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.161cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.274cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.423cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.159cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                        <td colspan="5" style="text-align:left;width:0.266cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P11">�</p></td>
                                                    </tr>
                                                    <tr class="�������29">    <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        <td colspan="23" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P70">����� �������� �� �����:</p</td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P21">�</p></td>
                                                        <td colspan="8" style="text-align:left;width:0.132cm; " class="�������2_A7"><p class="P61">��������</p></td>
                                                        <td style="text-align:left;width:0.134cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="22" style="text-align:left;width:1.141cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                    </tr>
                                                    <tr class="�������218">   <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="23" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="7" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td style="text-align:left;width:0.134cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="20" style="text-align:left;width:1.141cm; " class="�������2_A7"><p class="P67">���, ���� (�����������, ���������, �������, �. �.)</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P8">�</p></td>
                                                    </tr>
                                                    <tr class="�������230">
                                                        <td colspan="8" style="text-align:left;width:0.452cm; " class="�������2_A7"><p class="P62">������ ��������</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td style="text-align:left;width:2.866cm; " class="�������2_N19"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A7"><p class="P64">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P10">�</p></td>
                                                        <td colspan="9" style="text-align:left;width:0.132cm; " class="�������2_A7"><p class="P61">���� ������</p></td>
                                                        <td colspan="22" style="text-align:left;width:1.141cm; " class="�������2_f11"><p class="P61">�</p></td>
                                                    </tr>
                                                    <tr class="�������218">   <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="6" style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:2.866cm; " class="�������2_A7"><p class="P67">���������</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_A7"><p class="P67">�������</p></td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A6"><p class="P67">����������� �������</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="7" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:0.134cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td colspan="20" style="text-align:left;width:1.141cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                    </tr>
                                                    <tr class="�������29">
                                                        <td style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td colspan="9" style="text-align:left;width:0.053cm; " class="�������2_A7"><p class="P71">������� (�������) ���������</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_f11">
                                                            <?php if (!empty($LoadBanc['org_sig']))
                                                                echo '<img src="' . $LoadBanc['org_sig_buh'] . '">'; 
                                                            else echo '<p class="P72">�</p>';
                                                            ?>
                                                            
                                                        </td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A7"><p class="P72">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P21">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        <td colspan="28" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P21">�</p></td>
                                                    </tr>
                                                    <tr class="�������236">   <td colspan="2" style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P3">�</p></td>
                                                        <td style="text-align:left;width:0.452cm; " class="�������2_A8"><p class="P3">�</p></td>
                                                        <td colspan="6" style="text-align:left;width:0.053cm; " class="�������2_A8"><p class="P73">�</p></td>
                                                        <td style="text-align:left;width:2.866cm; " class="�������2_A6"><p class="P67">���������</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P4">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_A7"><p class="P67">�������</p></td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P4">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A6"><p class="P67">����������� �������</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P4">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P4">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P4">�</p></td>
                                                        <td colspan="28" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P4">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P4">�</p></td>
                                                    </tr>

                                                    <tr class="�������239">
                                                        <td colspan="9" style="text-align:left;width:0.346cm; " class="�������2_A7"><p class="P62">������ ����� ��������</p></td>
                                                        <td style="text-align:left;width:2.866cm; " class="�������2_f11"><p class="P64">�</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_f11">
                                                         <?php if (!empty($LoadBanc['org_sig']))
                                                                echo '<img src="' . $LoadBanc['org_sig'] . '">'; 
                                                            else echo '<p class="P64">�</p>';
                                                            ?>
                                                        
                                                        </td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A7"><p class="P64">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P7">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P7">�</p></td>
                                                        <td colspan="19" style="text-align:left;width:0.132cm; " class="�������2_A7"><p class="P61">���� ������� ���������������</p></td>
                                                        <td colspan="13" style="text-align:left;width:0.61cm; " class="�������2_f11"><p class="P61">�</p></td>
                                                    </tr>
                                                    <tr class="�������219">   <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td colspan="7" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td style="text-align:left;width:2.866cm; " class="�������2_A7"><p class="P67">���������</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_A7"><p class="P67">�������</p></td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A6"><p class="P67">����������� �������</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P20">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                        <td colspan="28" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P20">�</p></td>
                                                    </tr>
                                                    <tr class="�������229">   <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td colspan="7" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td style="text-align:left;width:2.866cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.187cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td colspan="3" style="text-align:left;width:1.088cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.185cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td colspan="10" style="text-align:left;width:0.582cm; " class="�������2_A8"><p class="P60">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P22">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td colspan="28" style="text-align:left;width:0.132cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                        <td colspan="2" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P22">�</p></td>
                                                    </tr>
                                                    <tr class="�������230">   <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td colspan="4" style="text-align:left;width:0.346cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td colspan="19" style="text-align:left;width:0.691cm; " class="�������2_A7"><p class="P61">
                                                              <?php
                                                               if (!empty($LoadBanc['org_stamp']))
                        echo '<img src="' . $LoadBanc['org_stamp'] . '" align="left">';
                    else echo " �. �. � ";
                                                              ?></p>
                                                           <p class="P61" style="padding-top:70px;padding-left:200px">�<?php echo PHPShopDate::dataV(false, false, false,' ', true); ?> ����</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_A8"><p class="P59">�</p></td>
                                                        <td style="text-align:left;width:0.743cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:0.088cm; " class="�������2_a17"><p class="P10">�</p></td>
                                                        <td style="text-align:left;width:0.293cm; " class="�������2_A8"><p class="P10">�</p></td>
                                                        <td colspan="30" style="text-align:left;width:0.663cm; " class="�������2_A7"><p class="P61">�. �. � � �  � � � � � � � � �" � � � � �" _______________ 20____ ����</p></td>
                                                    </tr>
                                                </table>
                                            </div>
                                            </body>
                                            </html>