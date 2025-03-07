<?php

$TitlePage = __('�������� �����������');
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
PHPShopObj::loadClass('user');

// ��������� ���
function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $PHPShopSystem,$TitlePage;


    // ������ �������� ����
    $PHPShopGUI->field_col = 3;

    $PHPShopGUI->addJSFiles('./js/bootstrap-datetimepicker.min.js',  './shopusers/gui/shopusers.gui.js');
    $PHPShopGUI->addCSSFiles('./css/bootstrap-datetimepicker.min.css');

    $PHPShopGUI->setActionPanel(__("����������") . ' / ' .$TitlePage, null, array('��������� � �������'), false);

    $PHPShopGUI->setEditor($PHPShopSystem->getSerilizeParam("admoption.editor"));
    $oFCKeditor = new Editor('content_new');
    $oFCKeditor->Height = '300';
    $oFCKeditor->Value = null;
    
     $user='<div class="form-group form-group-sm ">
        <label class="col-sm-3 control-label">'.__('���').':</label><div class="col-sm-9">
        <input data-set="3" name="name_new" maxlength="50" class="search_user form-control input-sm" type="search" data-trigger="manual" data-container="body" data-toggle="popover" data-placement="bottom" data-html="true"  data-content="" placeholder="'.__('�����...').'" value="" required="">
        <input name="user_new" type="hidden">
     </div></div> ';
     
    $data['rate']=5;
    $rate_value[] = array(1, 1, $data['rate']);
    $rate_value[] = array(2, 2, $data['rate']);
    $rate_value[] = array(3, 3, $data['rate']);
    $rate_value[] = array(4, 4, $data['rate']);
    $rate_value[] = array(5, 5, $data['rate']);

    // ���������� �������� 1
    $Tab1 = $PHPShopGUI->setCollapse('����������', $user .
            $PHPShopGUI->setField("����", $PHPShopGUI->setInputDate("datas_new", PHPShopDate::get($data['datas']), 'width:200px')) .
            $PHPShopGUI->setField("�����", $PHPShopGUI->setInput('text.required', "product_name", null,null,false,false,false,false, false, '<a href="#" data-target="#product_name"  class="product-search"><span class="glyphicon glyphicon-search"></span> ' . __('�������') . '</a>')) .
            $PHPShopGUI->setField("������", $PHPShopGUI->setCheckbox("enabled_new", 1, null, 1)) .
            $PHPShopGUI->setField("�������", $PHPShopGUI->setSelect('rate_new', $rate_value, 50)) .
            $PHPShopGUI->setField("�����������", $oFCKeditor->AddGUI(). $PHPShopGUI->setAIHelpButton('content_new', 300, 'product_comment', 'product_name')) 
          
    );


    // ������ ������ �� ��������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $data);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = 
            $PHPShopGUI->setInput("hidden", "parent_id_new", null, "right", 70, "", "but") .
            $PHPShopGUI->setInput("hidden", "user_id_new", null, "right", 70, "", "but") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionInsert.shopusers.create");


    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}


// ������� ����������
function actionInsert() {
    global $PHPShopOrm, $PHPShopModules;
    
    if(empty($_POST['parent_id_new']) or empty($_POST['user_id_new']))
       header('Location: ?path=' . $_GET['path'].'&action=new');

    $_POST['datas_new'] = PHPShopDate::GetUnixTime($_POST['datas_new']);

           
    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    $action = $PHPShopOrm->insert($_POST);
    header('Location: ?path=' . $_GET['path']);
    return $action;
}

/**
 * �������� �������� ������
 */
function ratingUpdate() {

    if (empty($_POST['parentID'])) {
        return false;
    }

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['comment']);
    $PHPShopOrm->debug = false;

    $result = $PHPShopOrm->query("select avg(rate) as rate, count(id) as num from " . $GLOBALS['SysValue']['base']['comment'] . " WHERE parent_id=" . intval($_POST['parentID']) . " AND enabled='1' AND rate>0 group by parent_id LIMIT 1");
    if (mysqli_num_rows($result)) {
        $row = mysqli_fetch_array($result);
        $rate = round($row['rate'], 1);
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->update(array('rate_new' => $rate, 'rate_count_new' => $row['num']), array('id' => '=' . $_POST['parentID']));
    } else {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
        $PHPShopOrm->debug = false;
        $PHPShopOrm->update(array('rate_new' => 0, 'rate_count_new' => 0), array('id' => '=' . $_POST['parentID']));
    }
}


// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>