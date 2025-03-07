<?php
PHPShopObj::loadClass('order');
include($_classPath . "modules/retailcrm/class/Tools.php");
$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.retailcrm.retailcrm_system"));

function actionUpdate() {
    global $PHPShopOrm;
    $post = Tools::clearArray($_POST);
    if ('/' != substr($post["siteurl"], strlen($post["siteurl"]) - 1, 1)) {
        $post["siteurl"] .= '/';
    }
    if ('/' != substr($post["url"], strlen($post["url"]) - 1, 1)) {
        $post["url"] .= '/';
    }
    $sql = "update phpshop_modules_retailcrm_system set value='" . serialize($post) . "' where code='options'";
    $action = $PHPShopOrm->query($sql);
    header('Location: ?path=modules&id=' . $_GET['id']);
    return $action;
}

// ���������� ������ ������
function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $data = $PHPShopOrm->select();

    $value = unserialize($data['value']);

    $help = '
    <h4>��� ������������ � RetailCRM?</h4>
        <ol>
        <li>������������������ �� ����� <a href="http://www.retailcrm.ru/?partner=RCM-6931" target="_blank">RetailCRM</a></li>
        <li>������� CMS PHPShop � ���������� RetailCRM</li>
        <li>� ���� <kbd>��������</kbd> ������� �������� ����� ��������, ��������� ��� ����������� �������� � RetailCRM</li>
        <li>� ���� <kbd>URL ����� ��������</kbd> ������� <code>http://' . $_SERVER['SERVER_NAME'] . '/</code></li>
        <li>� ���� <kbd>API URL</kbd> ������� URL ����� ������ �������� � RetailCRM <code>https://name.retailcrm.ru/</code></li>
        <li>� ���� <kbd>API KEY</kbd> ������� ���� �� ������� �������� RetailCRM, ��������� �  "����������������� / ���������� / ����� ������� � API"</li>
        <li>� ���� <kbd>�������� ��������</kbd> ������� �������� �������� �� ������� �������� RetailCRM</li>
        <li>������� �� ������� ����������� � �������� RetailCRM � ��������� ������������ �������� ��������, �������� ������, �������� ������ ��������-�������� � CRM.</li>
</li>
		</ol>';

    $tab1 = $PHPShopGUI->setField('�������� ��������', $PHPShopGUI->setInputText(false, 'shopname', $value["shopname"], 400));
    $tab1 .= $PHPShopGUI->setField('��������', $PHPShopGUI->setInputText(false, 'companyname', $value["companyname"], 400));
    $tab1 .= $PHPShopGUI->setField('URL ����� ��������', $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'siteurl', 'value' => $value["siteurl"], 'size' => 400, 'placeholder' => 'http://' . $_SERVER['SERVER_NAME'])));

    $tab1 .= $PHPShopGUI->setField('API URL', $PHPShopGUI->setInputArg(array('type' => 'text', 'name' => 'url', 'value' => $value["url"], 'size' => 400, 'placeholder' => 'https://name.retailcrm.ru/')));
    $tab1 .= $PHPShopGUI->setField('API KEY', $PHPShopGUI->setInputText(false, 'key', $value["key"], 400));

    $tab1 = $PHPShopGUI->setCollapse('���������', $tab1);

    if (isset($value["url"]) && isset($value["key"]) && $helper = new ApiHelper($value["url"], $value["key"])) {
        $field1 = "";
        $tab2 = "";
        // ������� ��������
        try {
            $response = $helper->api->deliveryTypesList();
        } catch (CurlException $e) {
            Tools::logger(array('error' => '������ ���������� � RetailCRM'), "connect", '������ ���������� � RetailCRM');
        }

        $deliveryTypes[] = array("�� �������", "", false);
        if ($response->isSuccessful()) {
            foreach ($response->deliveryTypes as $code => $params) {
                $deliveryTypes[] = array(Tools::iconvArray($params["name"], "UTF-8", "WINDOWS-1251"), $params["code"], false);
            }
        } else {
            Tools::logger(array('error' => '������ ���������� � RetailCRM'), "connect", '������ ���������� � RetailCRM');
        }

        $deliveryOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['delivery']);
        $delivery = $deliveryOrm->select(array('*'), array('is_folder' => "=''"));

        foreach ($delivery as $del) {
            $tmpDeliveryTypes = $deliveryTypes;
            if (isset($value["delivery"][$del["id"]])) {
                foreach ($tmpDeliveryTypes as $key => $val) {
                    if ($val[1] == $value["delivery"][$del["id"]]) {
                        $tmpDeliveryTypes[$key][2] = "selected";
                        break;
                    }
                }
            }

            $field1 .= $PHPShopGUI->setField($del["city"], $PHPShopGUI->setSelect('delivery[' . $del["id"] . ']', $tmpDeliveryTypes));
        }

        $tab2 .= $PHPShopGUI->setCollapse('������� ��������', $field1);

        // ������� ������
        try {
            $response = $helper->api->paymentTypesList();
        } catch (CurlException $e) {
            Tools::logger(array('error' => '������ ���������� � RetailCRM'), "connect", '������ ���������� � RetailCRM');
        }

        $paymentTypes[] = array("�� �������", "", false);
        if ($response->isSuccessful()) {
            foreach ($response->paymentTypes as $code => $params) {
                $paymentTypes[] = array(Tools::iconvArray($params["name"], "UTF-8", "WINDOWS-1251"), $params["code"], false);
            }
        } else {
            Tools::logger(array('error' => '������ ���������� � RetailCRM'), "connect", '������ ���������� � RetailCRM');
        }

        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment_systems']);
        $payment = $paymentOrm->select(array('*'));

        $field2 = "";
        foreach ($payment as $paymentValue) {
            $tmpPaymentTypes = $paymentTypes;
            if (isset($value["payment"][$paymentValue["id"]])) {
                foreach ($tmpPaymentTypes as $key => $val) {
                    if ($val[1] == $value["payment"][$paymentValue["id"]]) {
                        $tmpPaymentTypes[$key][2] = "selected";
                        break;
                    }
                }
            }

            $field2 .= $PHPShopGUI->setField($paymentValue["name"], $PHPShopGUI->setSelect('payment[' . $paymentValue["id"] . ']', $tmpPaymentTypes));
        }

        $tab2 .= $PHPShopGUI->setCollapse('������� ������', $field2);

        try {
            $response = $helper->api->statusesList();
        } catch (CurlException $e) {
            Tools::logger(array('error' => '������ ���������� � RetailCRM'), "connect", '������ ���������� � RetailCRM');
        }

        $statuses[] = array("�� �������", "", false);
        if ($response->isSuccessful()) {
            foreach ($response->statuses as $code => $params) {
                $statuses[] = array(Tools::iconvArray($params["name"], "UTF-8", "WINDOWS-1251"), $params["code"], false);
            }
        } else {
            Tools::logger(array('error' => '������ ���������� � RetailCRM'), "connect", '������ ���������� � RetailCRM');
        }

        $statusesOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['order_status']);
        $status = $statusesOrm->select(array('*'));

        $field3 = "";
        array_unshift($status, array("id" => "new", "name" => "����� �����"));
        foreach ($status as $statusValue) {
            $tmpStatuses = $statuses;
            if (isset($value["status"][$statusValue["id"]])) {
                foreach ($tmpStatuses as $key => $val) {
                    if ($val[1] == $value["status"][$statusValue["id"]]) {
                        $tmpStatuses[$key][2] = "selected";
                        break;
                    }
                }
            }

            $field3 .= $PHPShopGUI->setField($statusValue["name"], $PHPShopGUI->setSelect('status[' . $statusValue["id"] . ']', $tmpStatuses));
        }


        $tab2 .= $PHPShopGUI->setCollapse('�������', $field3);

        if (isset($GLOBALS['SysValue']['base']['oneclick'])) {
            $field3 = "";
            $status = array(
                array("id" => 1, "name" => "�����"),
                array("id" => 2, "name" => "������� �����������"),
                array("id" => 3, "name" => "����������"),
                array("id" => 4, "name" => "��������"),
            );
            foreach ($status as $statusValue) {
                $tmpStatuses = $statuses;
                if (isset($value["status-oneclick"][$statusValue["id"]])) {
                    foreach ($tmpStatuses as $key => $val) {
                        if ($val[1] == $value["status-oneclick"][$statusValue["id"]]) {
                            $tmpStatuses[$key][2] = "selected";
                            break;
                        }
                    }
                }

                $field3 .= $PHPShopGUI->setField($statusValue["name"], $PHPShopGUI->setSelect('status-oneclick[' . $statusValue["id"] . ']', $tmpStatuses));
            }

            $tab2 .= $PHPShopGUI->setCollapse('������� (����� � ���� ����)', $field3);
        }

        if (isset($GLOBALS['SysValue']['base']['returncall'])) {
            $field3 = "";
            $status = array(
                array("id" => 1, "name" => "�����"),
                array("id" => 2, "name" => "������� �����������"),
                array("id" => 3, "name" => "����������"),
                array("id" => 4, "name" => "��������"),
            );
            foreach ($status as $statusValue) {
                $tmpStatuses = $statuses;
                if (isset($value["status-oneclick"][$statusValue["id"]])) {
                    foreach ($tmpStatuses as $key => $val) {
                        if ($val[1] == $value["status-returncall"][$statusValue["id"]]) {
                            $tmpStatuses[$key][2] = "selected";
                            break;
                        }
                    }
                }

                $field3 .= $PHPShopGUI->setField($statusValue["name"], $PHPShopGUI->setSelect('status-returncall[' . $statusValue["id"] . ']', $tmpStatuses));
            }

            $tab2 .= $PHPShopGUI->setCollapse('������� (�������� ������)', $field3);
        }


        $PHPShopGUI->setTab(
                array("���������", $tab1, true), array("�����������", $tab2), array("����������", $PHPShopGUI->setInfo($help)), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], true)));
    } else {
        $PHPShopGUI->setTab(array("���������", $tab1, true), array("����������", $PHPShopGUI->setInfo($help)), array("� ������", $PHPShopGUI->setPay(false, false, $data['version'], true)));
    }

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