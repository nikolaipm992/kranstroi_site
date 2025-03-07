<?php
include($_classPath."modules/pozvonim/class/pcurl.php");
include($_classPath."modules/pozvonim/class/pozvonim.php");
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.pozvonim.pozvonim_system"));

// ������� �����������
function actionRegister()
{
    global $PHPShopOrm;
    $p = new Pozvonim();
    $data = array();
    foreach (array('email', 'phone', 'host', 'code', 'reset', 'token', 'restore') as $field) {
        if (isset($_POST[$field])) {
            $data[$field] = $_POST[$field];
        }
    }
    $oldData = $PHPShopOrm->select();
    if ($oldData['appId'] > 1) {
        echo '������ ��� ���������������';
        return false;
    }
    if (empty($data['token']) && $oldData['token']) {
        $data['token'] = $oldData['token'];
    }
    if ($result = $p->update($data)) {
        if ($PHPShopOrm->update($result, false, '')) {
            echo 'ok';
        } else {
            echo '������';
        }
    } else {
        echo $p->errorMessage ? $p->errorMessage : '������ ����������';
    }
    exit();
    return false;
}

// ������� ��������� ����
function actionCode()
{
    global $PHPShopOrm;
    $p = new Pozvonim();
    if (!isset($_POST['code'])) {
        echo '���������� ������� ��� �������';
        return false;
    }
    $code = $_POST['code'];

    $data = $PHPShopOrm->select();
    $data['code'] = $code;

    if ($data = $p->update($data)) {
        if ($PHPShopOrm->update($data, false, '')) {
            echo 'ok';
        } else {
            echo '������ ��������� ����';
        }
    } else {
        echo $p->errorMessage;
    }
    exit();
    return false;
}

// ������� ��������� ����
function actionRestore()
{
    $p = new Pozvonim();
    if (!isset($_POST['email'])) {
        echo '���������� ������� email';
        return false;
    }
    if ($p->restoreTokenToEmail($_POST['email'])) {
        echo '��������� ��� ��������� �� ' . htmlspecialchars($_POST['email']);
    } else {
        echo $p->errorMessage;
    }
    exit();
    return false;
}

function actionStart()
{
    global $PHPShopGUI, $PHPShopOrm,$PHPShopSystem;
    
    $PHPShopGUI->addJSFiles('../modules/pozvonim/gui/pozvonim.gui.js');

    //�������
    $data = $PHPShopOrm->select();
    $isRegistered = $data['appId'] > 0;

    $Tab1 = '<fieldset ' . ($isRegistered ? 'disabled="disabled"' : '') . ' >';
    $Tab1 .= $PHPShopGUI->setField('Email', $PHPShopGUI->setInputText(false, 'email', $data['email'] ? $data['email'] : $PHPShopSystem->getEmail()));
    $Tab1 .= $PHPShopGUI->setField('�������', $PHPShopGUI->setInputText(false, 'phone', $data['phone'] ? $data['phone'] : $PHPShopSystem->getParam('tel')));
    $Tab1 .= $PHPShopGUI->setField('�����', $PHPShopGUI->setInputText(false, 'host', $data['host'] ? $data['host'] : $_SERVER['HTTP_HOST']));
    $Tab1 .= $PHPShopGUI->setField('��������� ���',
        $PHPShopGUI->setInputText(false, 'token', $data['token'] ? $data['token'] : md5(uniqid('', true)))
    );
    if (!$isRegistered) {
        $Tab1 .= $PHPShopGUI->setField(false,$PHPShopGUI->setButton('���������������� ������', 'download-alt', 'btn-success pozvonim-register').$PHPShopGUI->setButton('������������ ��������� ���', 'refresh', 'pozvonim-restore'));
    } else {
        $Tab1 .= '<br/>';
        $link = $PHPShopGUI->setLink(
            'http://appspozvonim.com/phpshop/login?id=' . $data['appId'] . '&token=' . md5($data['appId'] . $data['token']),
            '������� ������ ������� my.pozvonim.com'
        );
        $Tab1 .= $PHPShopGUI->setField(false,$PHPShopGUI->setInfo('������ ��������������� � �������� � �������� <b>' . $data['email'] . '</b><br/>' .$link, false, '400px'));
    }
    $Tab1 .= '</fieldset>';

    $Tab12 = '<fieldset>' . $PHPShopGUI->setField('��� �������', $PHPShopGUI->setTextarea('code', $data['code']));;
    if ($data['code']) {
        $Tab12 .= $PHPShopGUI->setField(false,$PHPShopGUI->setInfo('<span class="text-success">������ ���������� ��������� ��� �������</span>', false, '300px'));
    }
    $Tab12 .= $PHPShopGUI->setField(false,$PHPShopGUI->setButton($data['code'] ? '���������' : '����������', false, 'pozvonim-save'));
    $Tab12 .= '</fieldset>';

    $Tab2 = $PHPShopGUI->setInfo('
           <b>��� ������ ������� ���������� ���������������� ���</b> ����� ����� ����������� �� ������� "�����������".<br/>
           <br/>
           � ������ <b>���� �� �������������� ������, �� �������� ���� ��������� ���</b> (��������� ������������� ����� ������������),
           �� ������ ������������ ��������� ��� �������� ���� email � ����� ������ "������������ ��������� ���".<br/>
           <br/>
           ���� �� ��� ���������������� � ������� <a href="http://pozvonim.com/?i=513468710" target="_blank">pozvonim.com</a> � <b>������ ������������ ������������ ��� �������</b>.
           �� ������ ������� ��� ������� � �������������� �������.
             <br/> <br/>
           �� ��������� ������ ��������� � ���������� <kbd>@leftMenu@</kbd>.
           ���� � ������� ��� ���������� <kbd>@leftMenu@</kbd> �� ���������� �������� ���������� <kbd>@pozvonim@</kbd> � �������� ������.<br/>
           ��������� � ������� ��� ������� ������������� � ������ �������� pozvonim.com.<br/>
           � ������ ������� ����� ������� �� ������ ������������ ����� ����������� �������.<br/>
           <p>��� ���������� ������ � ���������� <kbd>@leftMenu@</kbd> ��������������� 46 ������ � <mark>phpshop/modules/pozvonim/inc/pozvonim.inc.php</mark> </p>
   ');
    
        // ����� �����������
    $Tab3=$PHPShopGUI->setPay();

    // ����� ����� ��������
    if (isset($data['code']) && $data['code'] != '') {
        $PHPShopGUI->setTab(array("��� �������", $Tab12, true), array("�����������", $Tab1, true), array("����������", $Tab2),array("� ������",$Tab3));
    } else {
        $PHPShopGUI->setTab(array("�����������", $Tab1, true), array("��� �������", $Tab12, true), array("����������", $Tab2),array("� ������",$Tab3));
    }

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
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>