<?php

include_once dirname(__DIR__) . '/class/UnisenderApi.class.php';

// SQL
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.unisender.unisender_system"));

function actionBase() {
    global $_classPath;

    @set_time_limit(10000);

    $apikey = $_POST['key_new'];

    // ����� �������������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $data = $PHPShopOrm->select(array('*'), array('subscribe' => "='1'"), array('order'=>'id desc'), array('limit' => 500));
    if (is_array($data))
        foreach ($data as $user) {

            // ����� ����������
            $key[] = $user['id'];
            $new_emails[] = $user['login'];
            $new_names[] = $user['name'];
            $new_phone[] = $user['tel'];
        }


    if (!empty($new_emails)) {

        // ������ POST-������
        $query_array = array(
            'api_key' => $apikey,
            'field_names[0]' => 'email',
            'field_names[1]' => 'Name',
            'field_names[2]' => 'phone',
            'field_names[21]' => 'email_list_ids',
            'platform' => 'phpshop',
            'format' => 'json'
        );
        for ($i = 0; $i < count($new_emails); $i++) {
            $query_array['data[' . $i . '][0]'] = $new_emails[$i];
            $query_array['data[' . $i . '][1]'] = $new_names[$i];
            $query_array['data[' . $i . '][2]'] = $new_phone[$i];
        }

        $Unisender = new \Unisender\ApiWrapper\UnisenderApi($apikey, $GLOBALS['PHPShopLang']->charset, 4, null, false, 'phpshop');
        $result = $Unisender->importContacts($query_array);

        if ($result) {
            // ����������� ����� API-�������
            $jsonObj = json_decode($result);

            if (null === $jsonObj) {

                // ������ � ���������� ������
                echo '<div class="alert alert-danger" id="rules-message"  role="alert">Invalid JSON</div>';
            } elseif (!empty($jsonObj->error)) {

                // ������ �������
                echo '<div class="alert alert-danger" id="rules-message"  role="alert">An error occured: ' . $jsonObj->error . '(code: ' . $jsonObj->code . ')</div>';
            } else {

                // ��������� ������������� �� �������������
                $PHPShopOrm->clean();
                $PHPShopOrm->debug = false;
                $id_list = implode(',', $key);
                if (!empty($id_list))
                    $PHPShopOrm->update(array('subscribe_new' => 2), array('id' => ' IN (' . $id_list . ')'));

                // ����� ���������� ������� ���������
                echo '<div class="alert alert-success" id="rules-message"  role="alert">���������. ��������� ' . $jsonObj->result->new_emails . ' ����� e-mail �������, ��������� ' . $jsonObj->result->updated . ' e-mail �������.</div>';
            }
        } else {
            // ������ ���������� � API-��������
            echo '<div class="alert alert-danger" id="rules-message"  role="alert">������ API</div>';
        }
    }
    else
        echo '<div class="alert alert-info" id="rules-message"  role="alert">��� ����� ��������� ��� ��������</div>';
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm;

    $PHPShopOrm->debug = false;
    $action = $PHPShopOrm->update($_POST);
    header('Location: ?path=modules&install=check');
    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm, $PHPShopModules, $TitlePage, $select_name;

    // ����� ������������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
    $data_user = $PHPShopOrm->select(array('*'), array('subscribe' => "='1'"), array('order'=>'id desc'), array('limit' => 500));
    $num_new_user = count($data_user);
    if ($num_new_user > 0)
        $new_user = '<span class=badge>' . $num_new_user . '</span>';
    else
        $new_user = false;


    if ($new_user) {
        $PHPShopGUI->action_button['�������������'] = array(
            'name' => __('��������� �������������').' ' . $new_user,
            'action' => 'loadBase',
            'class' => 'btn  btn-info btn-sm navbar-btn',
            'type' => 'submit',
            'icon' => 'glyphicon glyphicon-open'
        );
    }

    $PHPShopGUI->setActionPanel($TitlePage, $select_name, array('�������������', '��������� � �������'));

    // �������
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.unisender.unisender_system"));
    $data = $PHPShopOrm->select();

    $Tab1.=$PHPShopGUI->setField('���� ������� � API ', $PHPShopGUI->setInput('text.required', "key_new", $data['key'], false, 300));

    $Tab2 = $PHPShopGUI->setInfo('<p>������ ��������� ������������� ��������� ������ ����������� �� ��������-�������� � ������ ���������� ��������� ������ ����� ����� �������� <a href="https://unisender.com/?a=phpshop" target="_blank">UniSender.com</a>.</p>
<p>    
���� ������� � API ��� ������������� �������� ������ ����� �������� � ���������� �������� Unisender � �������� <kbd>���������� � API</kbd>.<br>����� <code>������� ������ API</code> ������ ���� � ������ <kbd>�������</kbd>.</p>');

    // ����� �����������
    $Tab3 = $PHPShopGUI->setPay();

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab3,));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "loadBase", "���������", "right", 80, "", "but", "actionBase.modules.edit") .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>