<?php

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.formgenerator.formgenerator_system"));

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm,$PHPShopModules;
    
    // ��������� �������
    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);
    
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&id=' . $_GET['id']);
    
    return $action;
}

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI;


    $Info = '��� ���������� �����  � ������ ������ �������� ��������� ��� � ���������� �������� ��� ���������� �����:
        <p>
        <code>@php<br>
        $PHPShopFormgeneratorElement = new PHPShopFormgeneratorElement();<br>
        echo $PHPShopFormgeneratorElement->forma("������ �����");<br>
        php@</code>
         </p>
         <p>
         ��� ���������� ����� ����� ����������� � ������������ ������� ����� ����� � ��������� formgenerator_, ��������:<code><br>
         &lt;input  type="text" <b>name="formgenerator_����"</b>&gt; </code>  
         </p>
         <p>
         ��� ��������� ���� � ������ ������������� ���������� �������� ���� ��������� � ��� ����, ��������:<code><br>
         &lt;input  type="text" name="formgenerator_<b>*����</b>"&gt;  </code>
         </p>
         <p>
         ��� ����������� ������ ���� � ������ ����������� ����������� ��� ��������� ���������� ����� ������������ �������� ��������
         ���� � ������� ���� � ����� �� �������, ������� ������, ��������:<code><br>
         &lt;input  type="text" name="formgenerator_����" <b>value="@formamemory3@</b>"&gt;</code>
         </p>
         <p>
         ��� ��������� �������� ������ ����������� <kbd>@formgenerator_captcha@</kbd>
         </p>
         <p>
         ��� �������� ������ �� ������ � ��������� �������������� (������������, �������, ��, ������) �������� ���������� <kbd>?product_id=XXX</kbd> �� �������� ������ �����, ��� (��� - ID ������). ������: <code>http://'.$_SERVER['SERVER_NAME'].'/formgenerator/example/?product_id=101</code>
         </p>';

    $Tab2 = $PHPShopGUI->setInfo($Info, 250, '97%');


    // ���������� �������� 2
    $Tab3 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab2), array("� ������", $Tab3),array("�����", null,'?path=modules.dir.formgenerator'));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", 1) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['saveID'], 'actionStart');
?>