<?php

$TitlePage = __('�������������� �����������').' #' . $_GET['id'];
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);

function actionSave() {
    global $PHPShopGUI;

    // ���������� ������
    actionUpdate();

    //header('Location: ?path=' . $_GET['path']);
}

// ��������� ������������
function sendMailNotice($productID, $saveID, $email) {
    global $PHPShopSystem;

    if (PHPShopSecurity::true_email($email)) {

        $title = $PHPShopSystem->getName() . " - ".__('����������� � ������, ������')." �" . $saveID;
        PHPShopParser::set('title', $title);

        PHPShopObj::loadClass(array('array', 'valuta', 'product'));
        $PHPShopProduct = new PHPShopProduct($productID);

        $PHPShopMail = new PHPShopMail($email, $PHPShopSystem->getEmail(), $title, '', true, true);

        $text = "<p>".__('��������� �����������')." �" . $saveID . " ".__('� ��������-��������')." '" . $PHPShopSystem->getName() . "' ".__('��� ������������')." " . $email . "</p>
<p>
".__('�������').": " . $PHPShopProduct->getParam('uid') . "<br>
".__('C��������').": " . $PHPShopProduct->getPrice() . " " . $PHPShopSystem->getDefaultValutaCode() . "<br>
".__('���� ��������� ���������� �� ������').": " . PHPShopDate::get($PHPShopProduct->getParam('datas')) . "<br>
".__('������').": http://" . $_SERVER['SERVER_NAME'] . $GLOBALS['SysValue']['dir']['dir'] . "/shop/UID_" . $productID . ".html</p>";
        PHPShopParser::set('content', $text);
        PHPShopParser::set('logo', $_SERVER['SERVER_NAME'] . "/" . $GLOBALS['SysValue']['dir']['dir'] . $PHPShopSystem->getParam('logo'));
        $content = PHPShopParser::file('tpl/sendmail.mail.tpl', true, false);

        if ($PHPShopMail->sendMailNow($content))
            return true;
    }
}

// ������� ����������
function actionUpdate() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    if (!empty($_POST['saveID'])) {
        if (sendMailNotice($_POST['productID'], $_POST['rowID'], $_POST['email'])) {
            $_POST['enabled_new'] = 1;
        }
    }
    $action = $PHPShopOrm->update($_POST, array('id' => '=' . $_POST['rowID']));

    return array("success" => $action);
}

// ������� ��������������� �����������
function actionUpdateAuto() {
    global $PHPShopOrm, $PHPShopModules;

    $updateIds = array();

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);

    // ������� � �������
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);
    $PHPShopOrm->debug = false;
    $PHPShopOrm->sql = 'SELECT a.*, b.name, b.pic_small, c.login FROM ' . $GLOBALS['SysValue']['base']['notice'] . ' AS a 
        JOIN ' . $GLOBALS['SysValue']['base']['products'] . ' AS b ON a.product_id = b.id 
        JOIN ' . $GLOBALS['SysValue']['base']['shopusers'] . ' AS c ON a.user_id = c.id     
            limit 1000';

    $data = $PHPShopOrm->select();
    if (is_array($data))
        foreach ($data as $row) {

            if (empty($row['enabled']))
                if (sendMailNotice($row['product_id'], $row['id'], $row['login'])) {

                    $updateIds[] = $row['id'];
                }
        }

    $_POST['enabled_new'] = 1;

    if (is_array($updateIds)){
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['notice']);
        $action = $PHPShopOrm->update($_POST, array('id' => ' IN (' . implode(',', $updateIds).')'));
    }

    return array("success" => $action);
}

// ������� ��������
function actionDelete() {
    global $PHPShopOrm, $PHPShopModules;

    // �������� ������
    $PHPShopModules->setAdmHandler(__FILE__, __FUNCTION__, $_POST);


    $action = $PHPShopOrm->delete(array('id' => '=' . $_POST['rowID']));
    return $action;
}

// ��������� �������
$PHPShopGUI->getAction();
?>