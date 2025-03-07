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

$cart = $order['Cart']['cart'];

$k = 0;
$n=1;
if(isset($cart)) {
  foreach ($cart as $key => $value) {

    $html_predmet .= '<tr class="object" rel="'.$n.'">
                <td><input placeholder="�������" type="text" class="twelve validate[required]" name="CN23[object]['.$k.'][name]" value="'.substr($value['name'], 0, 26).'" maxlength="26"></td>
                <td><input placeholder="�����" type="text" class="twelve validate[required,custom[integer]] onlyNumber" name="CN23[object]['.$k.'][count]" value="'.$value['num'].'"></td>
                <td><input placeholder="�����" type="text" class="twelve validate[custom[number]] count onlyNumber addPoint" name="CN23[object]['.$k.'][brut]" value="'.$value['weight'].'"></td>
                <td><input placeholder="0.99" type="text" class="twelve validate[required,custom[number]] price onlyNumber addPoint" name="CN23[object]['.$k.'][price]" value="'.($value['price']*$value['num']).'"></td>
              </tr>';
    $n++;
    $k++;
    //����� ���-��
    $itNum = $itNum + $value['num'];
  }
}

for ($i=$n; $i<5 ; $i++) {
  $k++;

  $html_predmet .= '<tr class="object" rel="'.$i.'">
              <td><input placeholder="�������" type="text" class="twelve validate[required]" name="CN23[object]['.$k.'][name]" value="" maxlength="26"></td>
              <td><input placeholder="�����" type="text" class="twelve validate[required,custom[integer]] onlyNumber" name="CN23[object]['.$k.'][count]" value=""></td>
              <td><input placeholder="�����" type="text" class="twelve validate[custom[number]] count onlyNumber addPoint" name="CN23[object]['.$k.'][brut]" value=""></td>
              <td><input placeholder="0.99" type="text" class="twelve validate[required,custom[number]] price onlyNumber addPoint" name="CN23[object]['.$k.'][price]" value=""></td>
            </tr>';
}

?>

<head>
    <title>����� ������ �<?= $ouid ?></title>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=windows-1251">
    <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">

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
            //����� ��� � ��������, ������ ������
            if($_POST['CN23']['whom_build']!='' or $_POST['CN23']['whom_appartment']!='')
              $_POST['CN23']['whom_street'] = $_POST['CN23']['whom_street'].' �.'.$_POST['CN23']['whom_build'].' ��.'.$_POST['CN23']['whom_appartment'];

            if($_POST['CN23']['from_build']!='' or $_POST['CN23']['from_appartment']!='')
              $_POST['CN23']['from_street'] = $_POST['CN23']['from_street'].' �.'.$_POST['CN23']['from_build'].' ��.'.$_POST['CN23']['from_appartment'];
            
            //�����
            $blank = 'CN23';
            //������ ������
            pbrf_get($_POST['CN23'], $type, $blank);
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
    <h2 class="blank_name">���������� ������ ���������� ���������� CN23</h2>
        <div class="twelve columns">




<form class="twelve columns" method="post" action="" id="blanks">
  <fieldset>    
    <fieldset id="recipient" class="validationEngineContainer">
      <legend>������ ����������</legend>
      <div class="row">
        <div class="two columns">
          <label class="inline right">����</label>
        </div>
        <div class="six columns left">
          <input placeholder="������� ��� ��������" id="whom_surname" type="text" value="<?=$fio_new?>" rel="" name="CN23[whom_surname]" class="twelve validate[required] capitalize" maxlength="50">
        </div>
        <div class="four columns">
          <label class="inline left">������� ��� ��������</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">������</label>
        </div>
        <div class="four columns left end">
          <input placeholder="�������� ������" id="whom_country" type="text" value="<?=$country_new?>" rel="" name="CN23[whom_country]" class="twelve validate[required, custom[onlyLetterSp]] capitalize">
        </div>
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="four columns left end">
          <input placeholder="�������� ������" id="whom_city" type="text" value="<?=$city_new?>" rel="" name="CN23[whom_city]" class="twelve validate[required]" maxlength="28">
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="three columns left">  
          <input placeholder="�������� �����" id="whom_street" type="text" value="<?=$street_new?>" rel="" name="CN23[whom_street]" class="twelve validate[required]" maxlength="100">
        </div>
        <div class="one columns">
          <label class="inline right">���</label>
        </div>
        <div class="two columns left">
          <input placeholder="�����" id="whom_build" type="text" value="<?=$house_new?>" rel="" name="CN23[whom_build]" class="twelve validate[required]" maxlength="7">
        </div>
        <div class="two columns">
          <label class="inline right">��������</label>
        </div>
        <div class="two columns left">
          <input placeholder="�����" id="whom_appartment" type="text" value="<?=$flat_new?>" rel="" name="CN23[whom_appartment]" class="twelve" maxlength="7">
        </div>
      </div>
      <div class="row">
        <div class="three columns">
          <label class="inline right">�������� ������</label>
        </div>
        <div class="three columns left end">
          <input placeholder="000000" id="whom_zip" type="text" value="<?=$index_new?>" rel="" name="CN23[whom_zip]" class="twelve validate[required]" maxlength="10">
        </div>
      </div>
    </fieldset>
    <fieldset id="sender" class="validationEngineContainer">
     <legend>������ �����������</legend>
      <div class="row">
        <div class="two columns">
          <label class="inline right">�� ����</label>
        </div>
        <div class="six columns left">
          <input placeholder="������� ��� ��������" id="from_surname" type="text" value="<?=$data_person['surname']?> <?=$data_person['name']?> <?=$data_person['name2']?>" rel="" name="CN23[from_surname]" class="twelve validate[required] capitalize" maxlength="36">
        </div>
        <div class="four columns">
          <label class="inline left">������� ��� ��������</label>
        </div>
      </div>
      <div class="row">
        <div class="two columns">
          <label class="inline right">������</label>
        </div>
        <div class="four columns left">
          <input placeholder="�������� ������" id="from_country" type="text" value="<?=$data_person['country']?>" rel="" name="CN23[from_country]" class="twelve validate[required, custom[onlyLetterSp]] capitalize">
        </div>
        <div class="two columns">
          <label class="inline right">�����</label>
        </div>
        <div class="four columns left">
          <input placeholder="�������� ������" id="from_city" type="text" value="<?=$data_person['city']?>" rel="" name="CN23[from_city]" class="twelve validate[required]" maxlength="28">
        </div>
      </div>
      <div class="row">
        <div class="two columns"> 
          <label class="inline right">�����</label>
        </div>
        <div class="three columns left">
          <input placeholder="�������� �����" id="from_street" type="text" value="<?=$data_person['street']?>" rel="" name="CN23[from_street]" class="twelve validate[required]" maxlength="100">
        </div>
        <div class="one columns">
          <label class="inline right">���</label>
        </div>
        <div class="two columns left">
          <input placeholder="�����" id="from_build" type="text" value="<?=$data_person['build']?>" rel="" name="CN23[from_build]" class="twelve validate[required]" maxlength="7">
        </div>
        <div class="two columns">
          <label class="inline right">��������</label>
        </div>
        <div class="two columns left">
          <input placeholder="�����" id="from_appartment" type="text" value="<?=$data_person['appartment']?>" rel="" name="CN23[from_appartment]" class="twelve" maxlength="7">
        </div>
      </div>
      <div class="row">
        <div class="three columns">
          <label class="inline right">�������� ������</label>
        </div>
        <div class="three columns left end">
          <input placeholder="000000" id="from_zip" type="text" value="<?=$data_person['zip']?>" rel="" name="CN23[from_zip]" class="twelve validate[required]" maxlength="10">
        </div>
      </div>
    </fieldset>
    <div id="other" class="validationEngineContainer">
    <fieldset id="subject">
      <legend>������ ���������</legend>
      <table>
        <thead>
          <tr>
            <td class="text-center six">��������� �������� ��������</td>
            <td class="text-center two">����������</td>
            <td class="text-center two">��� �����<br>(� ��)</td>
            <td class="text-center two">���������</td>
          
        </tr></thead>
          <tbody>
          <?=$html_predmet?>
          </tbody>        
      </table>
    </fieldset>
    </div><!--other-->

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