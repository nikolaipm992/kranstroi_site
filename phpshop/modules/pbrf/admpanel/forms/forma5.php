<?
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

$PHPShopOrder = new PHPShopOrderFunction($_GET['orderID']);
// ��������� ������
$PHPShopModules = new PHPShopModules($_classPath."modules/");


// ���������� ���������
$SysValue['bank'] = unserialize($LoadItems['System']['bank']);
$pathTemplate = $SysValue['dir']['templates'] . chr(47) . $_SESSION['skin'];


$sql = "select * from " . $SysValue['base']['table_name1'] . " where id=" . intval($_GET['orderID']);
$n = 1;
@$result = mysqli_query($link_db,$sql);
$row = mysqli_fetch_array(@$result);
//���������
if($_GET['datas']!=$row['datas']) {
  echo '�������� ������';
  exit();
}
$id = $row['id'];
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

//���������
$bank = $SysValue['bank'];
//������
$org_name = $bank['org_name'];
$org_ur_adres = $bank['org_ur_adres'];
$org_adres = $bank['org_adres'];
$org_inn = $bank['org_inn'];
$org_kpp = $bank['org_kpp'];
$org_schet = $bank['org_schet'];
$org_bank = $bank['org_bank'];
$org_bic = $bank['org_bic'];
$org_bank_schet = $bank['org_bank_schet'];
//������ ������
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbrf.pbrf_system"));
$pbrf_system = $PHPShopOrm->select();
$data_person = unserialize($pbrf_system['data']);


?>

<head>
    <title>����� ������ �<?= $ouid ?></title>
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
    
    <?
        //�������� ��� ������
        $type = $_POST['type_send'];
        //���������
        if( isset($type) ):
            //�����
            $blank = 'F113';
            //������ ������
            pbrf_get($_POST['F113en'], $type, $blank);
        endif;
    ?>

    <div class="row blank" id="blank">
        <div align="center"><table align="center" width="100%">
            <tr>
                <td align="center"></td>
                <td align="right"><h4 align=center>�����&nbsp;�&nbsp;<?= $ouid ?>&nbsp;��&nbsp;<?= $datas ?></h4></td>
            </tr>
        </table>
    </div>
    
    <h2 class="blank_name">���������� ������ ����������� ������� �. 113\��</h2>
        <div class="twelve columns">
    

<form class="twelve columns" method="post" action="" id="blanks">
<fieldset>
  <fieldset id="recipient" class="validationEngineContainer">
    <legend>������ ���������� �������� �������</legend>
    <div class="row">
      <div class="two columns">
        <label class="inline right">����</label>
      </div>
      <div class="six columns left">
        <input placeholder="������� ��� ��������" id="whom_name" class="twelve validate[required] capitalize" type="text" name="F113en[whom_name]" value="<?=$data_person['surname']?> <?=$data_person['name']?> <?=$data_person['name2']?>" maxlength="50">
      </div>
      <div class="four columns">
        <label class="inline left">������� ��� ��������</label>
      </div>
    </div>

    <div class="row">
      <div class="two columns">
        <label class="inline right">�����</label>
      </div>
      <div class="six columns end">
        <input placeholder="�������� ������" id="whom_city" type="text" name="F113en[whom_city]" class="twelve validate[required]" value="<?=$data_person['city']?>">
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">�����</label>
      </div>
      <div class="six columns end">
        <input placeholder="�������� �����" id="whom_street" type="text" name="F113en[whom_street]" class="twelve" value="<?=$data_person['street']?>">
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">���</label>
      </div>
      <div class="two columns">
        <input placeholder="�����" id="whom_build" type="text" name="F113en[whom_build]" class="twelve" value="<?=$data_person['build']?>" maxlength="6">
      </div>
      <div class="two columns">
        <label class="inline right">������</label>
      </div>
      <div class="two columns">
        <input id="whom_house" type="text" name="F113en[whom_house]" class="twelve" value="<?=$data_person['house']?>" maxlength="4">
      </div>
      <div class="two columns">
        <label class="inline right">��������</label>
      </div>
      <div class="two columns">
        <input placeholder="�����" id="whom_appartment" type="text" name="F113en[whom_appartment]" class="twelve" value="<?=$data_person['appartment']?>">
      </div>
    </div>
    <div class="row">
      <div class="three columns">
        <label class="inline right">�������� ������</label>
      </div>
      <div class="three columns end">
        <input placeholder="000000" id="whom_zip" type="text" name="F113en[whom_zip]" class="twelve validate[required]" value="<?=$data_person['zip']?>" maxlength="6">
      </div>
    </div>
  </fieldset>
  <fieldset id="sender" class="validationEngineContainer">
    <legend>������ ����������� �������� �������</legend>
    <div class="row">
      <div class="two columns">
        <label class="inline right">�� ����</label>
      </div>
      <div class="four columns">
        <input placeholder="�������" id="from_surname" class="twelve capitalize" type="text" name="F113en[from_surname]" value="<?=$fio_new?>" maxlength="25">
      </div>
      <div class="six columns">
        <label class="inline left">�������</label>
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">&nbsp;</label>
      </div>
      <div class="six columns"> 
        <input placeholder="��� ��������" id="from_name" class="twelve capitalize" type="text" name="F113en[from_name]" value="" maxlength="60">
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
        <input placeholder="�������� ������" id="from_city" type="text" name="F113en[from_city]" class="twelve" value="<?=$city_new?>">
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">�����</label>
      </div>
      <div class="six columns end">
        <input placeholder="�������� �����" id="from_street" type="text" name="F113en[from_street]" class="twelve" value="<?=$street_new?>">
      </div>
    </div>
    <div class="row">
      <div class="two columns">
        <label class="inline right">���</label>
      </div>
      <div class="two columns">
        <input placeholder="�����" id="from_build" type="text" name="F113en[from_build]" class="twelve" value="<?=$house_new?>" maxlength="6">
      </div>
      <div class="two columns">
        <label class="inline right">������</label>
      </div>
      <div class="two columns">
        <input id="from_house" type="text" name="F113en[from_house]" class="twelve" value="" maxlength="�����">
      </div>
      <div class="two columns">
        <label class="inline right">��������</label>
      </div>
      <div class="two columns">
        <input placeholder="�����" id="from_appartment" type="text" name="F113en[from_appartment]" class="twelve" value="<?=$flat_new?>">
      </div>
    </div>
    <div class="row">
      <div class="three columns">
        <label class="inline right">�������� ������</label>
      </div>
      <div class="three columns end">
        <input placeholder="000000" id="from_zip" type="text" name="F113en[from_zip]" class="twelve validate[required]" value="<?=$index_new?>">
      </div>
    </div>
  </fieldset>
  <div id="other" class="validationEngineContainer">
  <fieldset>
    <legend>�������� �������</legend>
    <div class="row">
      <div class="two columns">
        <label class="inline right">�� �����</label>
      </div>
      <div class="four columns left">
        <input placeholder="0.99" id="sum_num" class="twelve onlyNumber addPoint" type="text" name="F113en[sum_num]" value="">
      </div>
      <div class="six columns">
        <label class="inline left">������� (���.)</label>
      </div>
    </div>
  </fieldset>
  <fieldset>
    <legend>����������� ��� ������ �������� � ����� ������������ ����</legend>
    <div class="row">
      <div class="four columns">
        <label class="inline right">���</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="123456789012" id="inn" class="twelve validate[custom[number]] onlyNumber" type="text" name="F113en[inn]" maxlength="12" value="<?=$org_inn?>">
          </div>
        </div>
      </div>
      <div class="four columns">
        <label class="inline right">���/����</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="12345678901234567890" id="kor_account" class="twelve validate[custom[number]]" type="text" name="F113en[kor_account]" maxlength="20" value="<?=$org_schet?>">
          </div>
        </div>
      </div>
      <div class="four columns">
        <label class="inline right">������������ �����</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="������������ �����" id="bank_name" class="twelve" type="text" name="F113en[bank_name]" value='<?=$org_bank?>' maxlength="70">
          </div>
        </div>
      </div>
      <div class="four columns">
        <label class="inline right">���/����</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="12345678901234567890" id="current_account" class="twelve validate[custom[number]]" type="text" name="F113en[current_account]" maxlength="20" value="<?=$org_bank_schet?>">
          </div>
        </div>
      </div>
      <div class="four columns">
        <label class="inline right">���</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="123456789" id="bik" class="twelve validate[custom[number]]" type="text" name="F113en[bik]" maxlength="9" value="<?=$org_bic?>">
          </div>
        </div>
      </div>
    </div>
  </fieldset>
    <fieldset>
    <legend>K�� �������������� ������������ ����������</legend>
    <div class="row">
      <div class="four columns">
        <label class="inline right">��������� ���</label>
      </div>
      <div class="eight columns left">
        <div class="row">
          <div class="seven columns">
            <input placeholder="000000" id="barcode" class="twelve validate[custom[number]]" type="text" name="F113en[barcode]" maxlength="6" value="">
          </div>
        </div>
      </div>
    </div>
  </fieldset>
    </div><!--other-->
          
      <div class="but-send-pbrf">
            <input type="hidden" name="type_send" id="type_send">
            <button class="blank_button" onclick="pdf_pbrf()">������� PDF</button>
        </div>   
      </fieldset><!--������ � ������� ���������� ������-->
    </form>
  </div>
    
  </div>
    
</body>
</html>