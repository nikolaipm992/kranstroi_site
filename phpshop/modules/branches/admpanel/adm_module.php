<?php

include_once dirname(__DIR__) . '/class/include.php';

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_system'));

function actionUpdate() {
    global $PHPShopModules;

    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.branches.branches_system'));
    $PHPShopOrm->debug = false;

    $_POST['favorite_cities_new'] = serialize($_POST['favorite_cities']);

    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $Branches = new Branches();

    $Tab1 = $PHPShopGUI->setField('API ���� ������.����', $PHPShopGUI->setInputText(false, 'yandex_api_key_new', $data['yandex_api_key'], 300). $PHPShopGUI->setHelp('������������ ����� ��� ������ �������� ����� <a href="https://developer.tech.yandex.ru" target="_blank">������� ������������</a>'));
    $Tab1 .= $PHPShopGUI->setField('����� �� ���������', $PHPShopGUI->setSelect('default_city_id_new', $Branches->getDefaultCity($data['default_city_id']), 300, null, false, true, false, 1, false));
    $Tab1 .= $PHPShopGUI->setField('������ �������� �������', $PHPShopGUI->setSelect('favorite_cities[]', $Branches->getFavoriteCitiesForSelect(unserialize($data['favorite_cities'])), 300, false, false, true, false, 1, true));

    // �������� 
    $PHPShopGUI->setEditor('ace');
    $oFCKeditor = new Editor('conten1');
    $oFCKeditor->Value = '<div>
��� �����: <a href="#" class="geo-changecity">@geolocation_city@</a>
</div>';
    $oFCKeditor->Height = '50';
    
    $info .= '<h4>��������� ����</h4>
<p>��� ������� �������� � ������� ���� ����� ��� ��������� ��������� ������� ������ �� ������.����� ������� ������� ����� �������� � ������ "������ ������" � �������:<br> <kbd>��������</kbd> &rarr; <kbd>������� ���� �����</kbd> � �������: <code>../branches/branches</code></p>';
    
    $info .= '<h4>��������� ���� �������</h4>
<p>��� ��������� ���� ������� ������� ������� ������������ ���� <code>citylist_install.sql</code> � ������� <kbd>����</kbd> &rarr; <kbd>��������� �����������</kbd>
</p>';

    $info .= '<h4>��������� ����</h4>
        <p>��� ������� �������� ������ ������ ������� � ������ ������ �������� ��� � ����� �����:</p>
' . $oFCKeditor->AddGUI();

    $oFCKeditor = new Editor('content2');
    $oFCKeditor->Value = '@geolocationPopup@
<script src="phpshop/modules/branches/templates/jquery-ui.min.js"></script>
<script src="phpshop/modules/branches/templates/geolocation.js"></script>
<script>
  $(document).ready(function () {
    var GeolocationInstance = new GeolocationModule();
    GeolocationInstance.init();
  });
</script> ';
    $oFCKeditor->Height = '140';
    $info .= '<p>��� ������� �������� ������ ������ ������� � ������ ������ �������� ��� � ������ �����:</p>
' . $oFCKeditor->AddGUI();
    
   

    $Tab2 = $PHPShopGUI->setInfo($info);

    $Tab4 = $PHPShopGUI->setPay(false, false, $data['version'], false);

    // ����� ����� ��������
    $PHPShopGUI->setTab(['��������', $Tab1, true], ["����������", $Tab2], ['� ������', $Tab4]);

    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>