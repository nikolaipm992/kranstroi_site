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
            $blank = 'F113F117';
            //������ ������
            pbrf_get($_POST['F113_F117'], $type, $blank);
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
    <h2 class="blank_name">���������� ������ �113/�117 ������� � ���������� ��������</h2>
    <div class="twelve columns">

    <form class="twelve columns" method="post" action="" id="blanks">
    <fieldset>  
      <fieldset id="sender" class="validationEngineContainer">
        <legend>������ �����������</legend>
        <div class="row">
          <div class="two columns">
            <label class="inline right">�� ����</label>
          </div>
          <div class="six columns left">
            <input placeholder="������� ���" id="from_surname" type="text" name="F113_F117[from_surname]" class="twelve validate[required] capitalize" value="<?=$data_person['surname']?> <?=$data_person['name']?>">
          </div>
          <div class="four columns">
            <label class="inline left">������� ���</label>
          </div>          
        </div>
        <div class="row">     
          <div class="two columns">
            <label class="inline right">&nbsp;</label>
          </div>
          <div class="four columns">
            <input placeholder="��������" id="from_patronymic" type="text" name="F113_F117[from_patronymic]" class="twelve validate[required] capitalize" value="<?=$data_person['name2']?>">
          </div>
          <div class="six columns">
            <label class="inline left">��������</label>
          </div>
        </div>
        <div class="row">
          <div class="two columns">
            <label class="inline right">�����</label>
          </div>
          <div class="six columns end">
            <input placeholder="�������� ������" id="from_city" type="text" name="F113_F117[from_city]" class="twelve validate[required]" value="<?=$data_person['city']?>">
          </div>
        </div>
        <div class="row">
          <div class="two columns">
            <label class="inline right">�����</label>
          </div>
          <div class="three columns">
            <input placeholder="�������� �����" id="from_street" type="text" name="F113_F117[from_street]" class="twelve validate[required]" value="<?=$data_person['street']?>">
          </div>
          <div class="one columns">
            <label class="inline right">���</label>
          </div>
          <div class="one columns">
            <input placeholder="�����" id="from_build" type="text" name="F113_F117[from_build]" class="twelve" value="<?=$data_person['build']?>">
          </div>

          <div class="one columns">
            <label class="inline right">������</label>
          </div>
          <div class="one columns">
            <input id="from_house" type="text" name="F113_F117[from_house]" class="twelve validate[custom[numAfterLetter],custom[onlyLetterNumber]]" value="">
          </div>
          <div class="two columns">
            <label class="inline right">��������</label>
          </div>
          <div class="one columns">
            <input placeholder="�����" id="from_appartment" type="text" name="F113_F117[from_appartment]" class="twelve validate[custom[numAfterLetter]]" value="<?=$data_person['appartment']?>">
          </div>
        </div>
        <div class="row">
          <div class="three columns">
            <label class="inline right">�������� ������</label>
          </div>
          <div class="three columns end">
            <input placeholder="000000" id="from_zip" type="text" name="F113_F117[from_zip]" class="twelve validate[required]" value="<?=$data_person['zip']?>" maxlength="6">
          </div>
        </div>
      </fieldset>
      <fieldset id="recipient" class="validationEngineContainer">
        <legend>������ ����������</legend>
        <div class="row">
          <div class="two columns">
            <label class="inline right">����</label>
          </div>
          <div class="six columns left">
            <input placeholder="������� ���" id="whom_surname" type="text" name="F113_F117[whom_surname]" class="twelve validate[required] capitalize" value="<?=$fio_new?>" maxlength="33">
          </div>
          <div class="four columns">
            <label class="inline left">������� ���</label>
          </div>          
        </div>
        <div class="row">
          <div class="two columns">
            <label class="inline right">&nbsp;</label>
          </div>
          <div class="four columns">
            <input placeholder="��������" id="whom_patronymic" type="text" name="F113_F117[whom_patronymic]" class="twelve validate[required] capitalize" value="">
          </div>
          <div class="six columns">
            <label class="inline left">��������</label>
          </div>
        </div>

        <div class="row">
          <div class="two columns">
            <label class="inline right">�����</label>
          </div>
          <div class="six columns end">
            <input placeholder="�������� ������" id="whom_city" type="text" name="F113_F117[whom_city]" class="twelve validate[required]" value="<?=$city_new?>">
          </div>
        </div>
        <div class="row">
          <div class="two columns">
            <label class="inline right">�����</label>
          </div>
          <div class="three columns">
            <input placeholder="�������� �����" id="whom_street" type="text" name="F113_F117[whom_street]" class="twelve validate[required]" value="<?=$street_new?>">
          </div>
          <div class="one columns">
            <label class="inline right">���</label>
          </div>
          <div class="one columns">
            <input placeholder="�����" id="whom_build" type="text" name="F113_F117[whom_build]" class="twelve" value="<?=$house_new?>">
          </div>

          <div class="one columns">
            <label class="inline right">������</label>
          </div>
          <div class="one columns">
            <input id="whom_house" type="text" name="F113_F117[whom_house]" class="twelve validate[custom[numAfterLetter],custom[onlyLetterNumber]]" value="">
          </div>
          <div class="two columns">
            <label class="inline right">��������</label>
          </div>
          <div class="one columns">
            <input placeholder="�����" id="whom_appartment" type="text" name="F113_F117[whom_appartment]" class="twelve validate[custom[numAfterLetter]]" value="<?=$flat_new?>">
          </div>
        </div>
        <div class="row">
          <div class="three columns">
            <label class="inline right">�������� ������</label>
          </div>
          <div class="three columns end">
            <input placeholder="000000" id="whom_zip" type="text" name="F113_F117[whom_zip]" class="twelve validate[required]" value="<?=$index_new?>" maxlength="6">
          </div>
        </div>
      </fieldset>
      <div id="other" class="validationEngineContainer">
      <fieldset>
        <div class="row">
          <div class="five columns">
            <label class="inline right">����� ����������� ��������</label>
          </div>
          <div class="seven columns left">
            <div class="row">
              <div class="seven columns">
                <input placeholder="0.99" id="declared_value_num" type="text" name="F113_F117[declared_value_num]" class="twelve validate[required,custom[number]] onlyNumber addPoint" value="">
              </div>
              <div class="five columns">
                <label class="inline left">�������</label>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="five columns">
            <label class="inline right">����� ����������� �������</label>
          </div>
          <div class="seven columns left">
            <div class="row">
              <div class="seven columns">
                <input placeholder="0.99" id="COD_amount_num" type="text" name="F113_F117[COD_amount_num]" class="twelve validate[required,custom[number]] onlyNumber addPoint" value="">
              </div>
              <div class="five columns">
                <label class="inline left">�������</label>
              </div>
            </div>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <legend>������������� ��������</legend>
        <div class="row">
          <div class="four columns">
            <label class="inline right">������������ ���������</label>
          </div>
          <div class="two columns left">
            <input placeholder="������������" id="document" class="twelve validate[required,custom[onlyLetterNumber]]" type="text" name="F113_F117[document]" maxlength="7" value="<?=$data_person['document']?>">
          </div>
          <div class="one columns">
            <label class="inline right">�����</label>
          </div>
          <div class="two columns left">
            <input placeholder="��" id="document_serial" class="twelve validate[required,custom[onlyLetterNumber]]" type="text" name="F113_F117[document_serial]" maxlength="4" value="<?=$data_person['document_serial']?>">
          </div>
          <div class="one columns">
            <label class="inline right">�</label>
          </div>
          <div class="two columns left">
            <input placeholder="1234567" id="document_number" class="twelve left validate[required,custom[integer]]" type="text" name="F113_F117[document_number]" maxlength="6" value="<?=$data_person['document_number']?>">
          </div>
        </div>
        <div class="row">
          <div class="four columns">
            <label class="inline right">�����</label>
          </div>
          <div class="two columns left">
            <input id="document_day" placeholder="00.00" type="text" class="twelve validate[required]" name="F113_F117[document_day]" value="<?=$data_person['document_day']?>">
          </div>
          <div class="one columns">
            <label class="inline right">20</label>
          </div>
          <div class="two columns left">
            <input placeholder="99" id="document_year" type="text" class="twelve validate[required,custom[integer]] " name="F113_F117[document_year]" maxlength="2" value="<?=$data_person['document_year']?>">
          </div>
          <div class="one columns end">
            <label class="inline left">�.</label>
          </div>
        </div>
        <div class="row">
          <div class="twelve columns">
            <label>������������ ���������� ��������� ��������</label>
            <input placeholder="������������ ���������� ��������� ��������" id="document_issued_by" type="text" name="F113_F117[document_issued_by]" class="twelve validate[required]" value="<?=$data_person['document_issued_by']?>" maxlength="250">
          </div>
        </div>
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