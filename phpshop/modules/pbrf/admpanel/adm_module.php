<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pbrf.pbrf_system"));


// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $_POST['data_new'] = serialize($_POST['data']);

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=pbrf');
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI,$PHPShopOrm;

    //��������� ���������
    $data = $PHPShopOrm->select();

    $data_person = unserialize($data['data']);

    $Tab1 .= $PHPShopGUI->setField('���� API:',$PHPShopGUI->setInputText(false, 'key_new', $data['key'], '60%'),1,'�������� � pbrf.ru');

    $Tab1 .= $PHPShopGUI->setLine() . $PHPShopGUI->setField('���� ������ ��� ������:', 
        $PHPShopGUI->setInputText('�������&nbsp;&nbsp; ', 'data[surname]', $data_person['surname'], '60%', false , 'left') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('���&nbsp;&nbsp; ', 'data[name]', $data_person['name'], '60%', false , 'left') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('��������&nbsp;&nbsp; ', 'data[name2]', $data_person['name2'], '60%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('������&nbsp;&nbsp; ', 'data[country]', $data_person['country'], '20%' , false , 'left') .
        $PHPShopGUI->setInputText('�������, �����', 'data[region]', $data_person['region'], '20%', false , 'left') . 
        $PHPShopGUI->setInputText('�����&nbsp;&nbsp; ', 'data[city]', $data_person['city'], '20%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('�����&nbsp;&nbsp; ', 'data[street]', $data_person['street'], '20%' , false , 'left') . 
        $PHPShopGUI->setInputText('���&nbsp;&nbsp; ', 'data[build]', $data_person['build'], '20%' , false , 'left') . 
        $PHPShopGUI->setInputText('��������&nbsp;&nbsp; ', 'data[appartment]', $data_person['appartment'], '20%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('�������� ������&nbsp;&nbsp; ', 'data[zip]', $data_person['zip'], '40%') .
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('������� ��� sms&nbsp;&nbsp; +7', 'data[tel]', $data_person['tel'], '40%')
    , 'left', false, false, array('width' => '98%'));

    $Tab1 .= $PHPShopGUI->setLine() . $PHPShopGUI->setField('������������� ��������:', 
        $PHPShopGUI->setInputText('������������ ���������&nbsp;&nbsp; ', 'data[document]', $data_person['document'], '60%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('�����&nbsp;&nbsp; ', 'data[document_serial]', $data_person['document_serial'], '30%', false , 'left') . 
        $PHPShopGUI->setInputText('�&nbsp;&nbsp; ', 'data[document_number]', $data_person['document_number'], '30%') . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('�����&nbsp;&nbsp; ', 'data[document_day]', $data_person['document_day'], '40%' , false , 'left') . 
        $PHPShopGUI->setInputText('20&nbsp;&nbsp; ', 'data[document_year]', $data_person['document_year'], '20%',__('�.')) . 
        $PHPShopGUI->setLine(false, 10) .
        $PHPShopGUI->setInputText('������������ ���������� ��������� ��������&nbsp;&nbsp; ', 'data[document_issued_by]', $data_person['document_issued_by'], '60%' , false , 'left')
    , 'left', false, false, array('width' => '98%'));


    // ���������� �������� 3
    $Info = '<h4>���������� ������� pbrf.ru</h4>
    <p><b>��� ��������� ����� ����������:</b>
    <ul>
        <li>����������������� �� <a target="_blank" href="http://pbrf.ru/������������/�����">Pbrf.ru</a>.</li>
        <li>�������� ���� ������� � ������ �������� <i>(������� API)</i>.</li>
        <li>������ ���� ���� � ���� <kbd>���� API</kbd> � ��������� ������.</li>
    </ul>
    </p>
    <p class="alert alert-info">�������� ������ ��� �������� ����� ���������� ��������� ������� ������, ���� ���� ������� �������� � ��� �� ���������.<br>
    �������� � API ������� pbrf.ru �������� ������ �� ��������� ������� �������. ��������� <a target="_blank" href="http://pbrf.ru/������/�������-�����">��������</a> �� ����� ��������.</p>';
    $Tab3=$PHPShopGUI->setInfo($Info, 250, '95%');

    // ���������� �������� 4
    $Tab4=$PHPShopGUI->setPay(false,true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("���������",$Tab1,true), array("����������",$Tab3), array("� ������",$Tab4));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter=
            $PHPShopGUI->setInput("hidden","newsID",$id,"right",70,"","but").
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['newsID'], 'actionStart');

?>


