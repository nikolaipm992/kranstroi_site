<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.deliverycalc.deliverycalc_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    // �������
    $data = $PHPShopOrm->select();
    
    if(empty($data['code']))
        $data['code']='<script src="https://alliance-catalog.ru/site/delivery-iframe/script.js"></script>
<div>'.__('<a href="https://alliance-catalog.ru/deliverycalc/" id="link" >* ��������������</a> ��������������� ��������� ��������, �������� ��������� ������������ ����� ������ ����� �� ��������� ��������').'</div>';

    // ���������
    $Tab1.=$PHPShopGUI->setField("���������:", $PHPShopGUI->setInput("text", "target_new", $data['target']) .
            $PHPShopGUI->setHelp('* ������: /,/page/,/page/addres.html. ����� ������� ��������� ������� ����� �������.'));

    $Tab1.=$PHPShopGUI->setField('��� �������', $PHPShopGUI->setTextarea('code_new', $data['code'], false, false, 150).
            $PHPShopGUI->setHelp('* ������ ���� ������� ����������� �� <a href="https://alliance-catalog.ru/ourdelcalc/" target="_blank">�������� ������������</a>.'));


    $Tab3 = $PHPShopGUI->setPay();
    $Info = '<h4>��������� ������</h4>
        <ol>
        <li> � ���� "���������" ����� ������� ������ ������� ��� ������ �� ��� ������� ������������ ��������� ��������. ������ ��������� � ����� ���������� ��������.
        <li> ��� ���������� ���������� ������ ��������� �� ����� �������� ����� ������� ���������� <kbd>@deliverycalc @</kbd> � ����� ����� ���������� ��������.
        <li> ��� ��������� ���� ������� ��������� ������ ��������� � ����������� ���� "��� �������".
        <li> ����������� ��������� �������������� �������� ������������� ������� <a href="https://alliance-catalog.ru/ourdelcalc/" target="_blank">Alliance-catalog.ru</a>

</ol>';
    $Tab2 = $PHPShopGUI->setInfo($Info);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��� �������", $Tab1, true), array("��������", $Tab2), array("� ������", $Tab3));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>