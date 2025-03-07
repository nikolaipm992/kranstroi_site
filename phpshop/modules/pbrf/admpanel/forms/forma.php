<?php
$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("inwords");
PHPShopObj::loadClass("delivery");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("valuta");
PHPShopObj::loadClass("modules");
PHPShopObj::loadClass("security");
include("../../class/pbrf.class.php");


$PHPShopBase = new PHPShopBase($_classPath."inc/config.ini");
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();
$LoadItems['System'] = $PHPShopSystem->getArray();

// ��������� ������
$PHPShopModules = new PHPShopModules($_classPath."modules/");

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);

// ���������� ���������
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);
$pathTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'];


$sql = "select * from " . $SysValue['base']['table_name1'] . " where id=" . intval($_GET['orderID']);
$n = 1;
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
$id = $row['id'];
//���������
if($_GET['datas']!=$row['datas']) {
  echo '�������� ������';
  exit();
}
$datas = $row['datas'];
$ouid = $row['uid'];
$order = unserialize($row['orders']);
$status = unserialize($row['status']);
$datas = PHPShopDate::dataV($datas, "false");
$user_id = $order['Person']['user_id'];

//select phshop_shopusers
$sql = "select * from " . $SysValue['base']['table_name27'] . " where id=" . intval($user_id);
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
//������
$data_adres = unserialize($row['data_adres']);
//������ �� �������
$country_new = $data_adres['list'][0]['country_new'];
$state_new = $data_adres['list'][0]['state_new'];
$city_new = $data_adres['list'][0]['city_new'];
$index_new = $data_adres['list'][0]['index_new'];
$fio_new = $data_adres['list'][0]['fio_new'];
$tel_new = $data_adres['list'][0]['tel_new'];
$street_new = $data_adres['list'][0]['street_new'];
$house_new = $data_adres['list'][0]['house_new'];
$porch_new = $data_adres['list'][0]['porch_new'];
$door_phone_new = $data_adres['list'][0]['door_phone_new'];
$flat_new = $data_adres['list'][0]['flat_new'];
$delivtime_new = $data_adres['list'][0]['delivtime_new'];
//������ �� Person
$mail_new = $order['Person']['mail'];

//������ ������
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbrf.pbrf_system"));
$pbrf_system = $PHPShopOrm->select();
$data_person = unserialize($pbrf_system['data']);

?>

<head>
    <title>����� ������ �<?php echo $ouid ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
    <link href="style.css" type=text/css rel=stylesheet>
    <style media="screen" type="text/css">
        a.save{
            display: none;
        }

        * HTML a.save{ /* ������ ��� �������� IE */
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
    <link rel="stylesheet" type="text/css" href="../../css/foundation.css" />
    <link rel="stylesheet" type="text/css" href="../../css/default.css" />
    <script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
    <script src="../../js/pbrf.js"></script>

</head>
<body onload="window.focus()" bgcolor="#FFFFFF" text="#000000" marginwidth=5 leftmargin=5 style="padding: 2px;">
    
    <?php
        //�������� ��� ������
        $type = $_POST['type_send'];
        //���������
        if( isset($type) ):
            //�����
            $blank = 'F7';
            //������ ������
            pbrf_get($_POST[ $blank ], $type, $blank);
        endif;

    ?>

    <div class="row blank" id="blank">
        <div align="center"><table align="center" width="100%">
            <tr>
                <td align="center"></td>
                <td align="right"><h4 align=center>�����&nbsp;�&nbsp;<?php echo $ouid ?>&nbsp;��&nbsp;<?php echo $datas ?></h4></td>
            </tr>
        </table>
    </div>
    <h2 class="blank_name">���������� ������ ������ �.7</h2>

        <div class="twelve columns">

<form class="twelve columns" method="post" action="" id="blanks">
  <fieldset> 
    <fieldset id="recipient" class="validationEngineContainer">
      <legend>������ ����������</legend>
      <div class="row">
        <div class="two columns">
          <label class="inline right">����</label>
        </div>
        <div class="four columns left">
          <input placeholder="�������" id="whom_surname" type="text" value="<?php echo $fio_new?>" rel="" name="F7[whom_surname]" class="twelve validate[required] capitalize" maxlength="30">
        </div>
        <div class="six columns">
          <label class="inline left">�������</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">&nbsp;</label>
        </div>
        <div class="six columns left">
          <input placeholder="��� ��������" id="whom_name" type="text" value="" rel="" name="F7[whom_name]" class="twelve" maxlength="38">
        </div>
        <div class="four columns">
          <label class="inline left">��� ��������</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="six columns end">
          <input placeholder="�������� ������" id="whom_city" type="text" name="F7[whom_city]" class="twelve validate[required]" value="<?php echo $city_new?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="three columns">
          <input placeholder="�������� �����" id="whom_street" type="text" name="F7[whom_street]" class="twelve" value="<?php echo $street_new?>">
        </div>
        <div class="one columns">
          <label class="inline right">���</label>
        </div>
        <div class="two columns">
          <input placeholder="�����" id="whom_build" type="text" name="F7[whom_build]" class="twelve" value="<?php echo $house_new?>" maxlength="6">
        </div>
        <div class="two columns">
          <label class="inline right">��������</label>
        </div>
        <div class="two columns">
          <input placeholder="�����" id="whom_appartment" type="text" name="F7[whom_appartment]" class="twelve validate[custom[numAfterLetter]]" value="<?php echo $flat_new?>" maxlength="7">
        </div>
      </div>      
      <div class="row">
        <div class="three columns">
          <label class="inline right">�������� ������</label>
        </div>
        <div class="three columns left end">
          <input placeholder="000000" id="whom_zip" type="text" value="<?php echo $index_new?>" rel="" name="F7[whom_zip]" class="twelve validate[required,custom[integer]]" maxlength="6">
        </div>
      </div>
    </fieldset>
    <fieldset id="sender" class="validationEngineContainer">
     <legend>������ �����������</legend>
     <div class="row">
        <div class="two columns">
          <label class="inline right">�� ����</label>
        </div>
        <div class="four columns left">
          <input placeholder="�������" id="from_surname" type="text" value="<?php echo $data_person['surname']?>" rel="" name="F7[from_surname]" class="twelve validate[required, ecustom[onlyLetterSp]] capitalize" maxlength="25">
        </div>
        <div class="six columns">
          <label class="inline left">�������</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">&nbsp;</label>
        </div>
        <div class="six columns left">
          <input placeholder="��� ��������" id="from_name" type="text" value="<?php echo $data_person['name'].' '.$data_person['name2']?>" rel="" name="F7[from_name]" class="twelve" maxlength="32">
        </div>
        <div class="four columns">
          <label class="inline left">��� ��������</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="six columns end">
          <input placeholder="�������� ������" id="from_city" type="text" name="F7[from_city]" class="twelve validate[required]" value="<?php echo $data_person['city']?>">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="three columns">
          <input placeholder="�������� �����" id="from_street" type="text" name="F7[from_street]" class="twelve" value="<<?php echo $data_person['street']?>">
        </div>
        <div class="one columns">
          <label class="inline right">���</label>
        </div>
        <div class="two columns">
          <input placeholder="�����" id="from_build" type="text" name="F7[from_build]" class="twelve" value="<?php echo $data_person['build']?>" maxlength="6">
        </div>
        <div class="two columns">
          <label class="inline right">��������</label>
        </div>
        <div class="two columns">
          <input placeholder="�����" id="from_appartment" type="text" name="F7[from_appartment]" class="twelve validate[custom[numAfterLetter]]" value="<?php echo $data_person['appartment']?>" maxlength="7">
        </div>
      </div>
      <div class="row">
        <div class="three columns">
          <label class="inline right">�������� ������</label>
        </div>
        <div class="three columns left end">
          <input placeholder="000000" id="from_zip" type="text" value="<?php echo $data_person['zip']?>" rel="" name="F7[from_zip]" class="eight validate[required,custom[integer]]" maxlength="6">
        </div>
      </div>

    </fieldset>
    <div id="other" class="validationEngineContainer">
    <fieldset>
      <div class="row">
        <div class="five columns">
          <label class="inline right">����� ����������� ��������</label>
        </div>
        <div class="three columns left end">
          <input placeholder="0.99" id="declared_value" type="text" name="F7[declared_value]" class="twelve validate[required,custom[number]] onlyNumber addPoint" value="">
        </div>
      </div>
      <div class="row">
        <div class="five columns">
        <label class="inline right">����� ����������� �������</label>
        </div>
        <div class="three columns left end">
          <input placeholder="0.99" id="COD_amount" type="text" name="F7[COD_amount]" class="twelve validate[custom[number]] onlyNumber addPoint" value="">
        </div>
      </div>
    </fieldset>
    </div><!--other-->
    <fieldset>
      <legend>��� ������ ������</legend>
      <div class="twelve columns">
        <label for="type_a"><input name="F7[type_blank]" type="radio" id="type_a" checked="" value="a"> ����������� 1 ������</label>
        <label for="type_b"><input name="F7[type_blank]" type="radio" id="type_b" value="b"> ���������</label>
        <label for="type_p"><input name="F7[type_blank]" type="radio" id="type_p" value="p"> �������</label>
      </div>
    </fieldset>
                 
        <div class="but-send-pbrf">
            <input type="hidden" name="type_send" id="type_send">
            <button class="blank_button" onclick="print_pbrf()">��� ������</button> 
            <button class="blank_button" onclick="pdf_pbrf()">������� PDF</button>
        </div>
      </fieldset><!--������ � ������� ���������� ������-->

    </form>
  </div>
    
  </div>
</body>
</html>