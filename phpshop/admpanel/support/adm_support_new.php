<?php

$TitlePage = __('����� ������ � ������������');

function actionStart() {
    global $PHPShopGUI, $TitlePage, $PHPShopSystem;

    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './support/gui/support.gui.js');

    $PHPShopGUI->action_button['��������� ���������'] = array(
        'name' => '��������� ���������',
        'locale' => true,
        'action' => 'saveID',
        'class' => 'btn  btn-default btn-sm navbar-btn' . $xs_class . $GLOBALS['isFrame'],
        'type' => 'submit',
        'icon' => 'glyphicon glyphicon-send'
    );

    $PHPShopGUI->setActionPanel($TitlePage, false, array('��������� ���������'));
    $PHPShopGUI->field_col = 2;

    $value[] = array('���������', 1, 1);
    $value[] = array('������ � �������', 2);
    $value[] = array('��������� 1� � ��������', 3);
    $value[] = array('���������������� PHPShop', 5);
    $value[] = array('����� �������', 6);
    $value[] = array('������� ���������� �����������', 8);
    $value[] = array('���������� �������', 15);
    $value[] = array('�������� �� �������� ��������', 18);
    $value[] = array('������� ������ � ������������', 19);
    $value[] = array('��������� �������', 20);

    $Tab1 .= $PHPShopGUI->setField("E-mail", $PHPShopGUI->setInput('email.required.6', "email", $PHPShopSystem->getEmail(), null, 400));
    $Tab1 .= $PHPShopGUI->setField("���", $PHPShopGUI->setInput('text.required.4', "name", null, null, 400));
    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setSelect('priority', array(array('������', 3), array('�������', 2), array('�������', 1)), 400, true));
    $Tab1 .= $PHPShopGUI->setField("���������", $PHPShopGUI->setSelect('category', $value, 400, true));
    $Tab1 .= $PHPShopGUI->setField("����", $PHPShopGUI->setInput('text.required.10', "subject", null));
    $Tab1 .= $PHPShopGUI->setField('���������', $PHPShopGUI->setTextarea('message.required.10', null, true, false, 300, false, __('����������, ������� ���� ��������. ��� ��������� ������� �������, ����� ������������ ������ ������� �� ����������� (�����, ������) ����� � FTP (��� �������, �����, ������)')));
    $Tab1 .= $PHPShopGUI->setField('����', $PHPShopGUI->setIcon(null, "attachment", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)));


    $PHPShopGUI->_CODE = $PHPShopGUI->setCollapse('����� ������', $Tab1, 'in', false);

    $ContentFooter = $PHPShopGUI->setInput("submit", "saveID", "��", "right", 70, "", "but", "actionInsert.system.edit");

    // �����
    $PHPShopGUI->setFooter($ContentFooter);

    return true;
}

// ������� ����������
function actionInsert() {

    $licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
    @$License = parse_ini_file_true("../../license/" . $licFile, 1);

    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'] . '&do=create&code='.$GLOBALS['PHPShopBase']->codBase;
    $ch = curl_init();
    
    if (!empty($_POST['attachment'])) {

        $fileAdd = fileAdd();
        if (!empty($fileAdd))
            $_POST['attachment'] = $fileAdd;

        $pathinfo = pathinfo($_POST['attachment']);
        $_POST['message'] .= '

<a href="http://' . $_SERVER['SERVER_NAME'] . $_POST['attachment'] . '" target="_blank"><span class="glyphicon glyphicon-paperclip"></span> ' . $pathinfo['basename'] . '</a>';
    }
    
    $_POST['subject']= str_replace('"','',$_POST['subject']);
    $_POST['name']= str_replace('"','',$_POST['name']);


    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_exec($ch);
    curl_close($ch);
    header('Location: ?path=' . $_GET['path']);
}

// ���������� �����
function fileAdd() {
    global $PHPShopSystem;

    // ����� ����������
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
        if (!empty($_FILES['file']['ext'])) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        } else
            $file = 'Error_PHP_ext';
    }

    if (empty($file))
        $file = '';

    return $file;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>