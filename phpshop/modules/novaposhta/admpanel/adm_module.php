<?php

include_once dirname(__FILE__) . '/../class/NovaPoshta.php';

PHPShopObj::loadClass('order');
PHPShopObj::loadClass('delivery');

$PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.novaposhta.novaposhta_system'));

function actionBaseUpdate() {
    global $PHPShopModules, $PHPShopOrm;
    $PHPShopOrm->clean();
    $option = $PHPShopOrm->select();
    $new_version = $PHPShopModules->getUpdate($option['version']);
    $PHPShopOrm->clean();
    $PHPShopOrm->update(array('version_new' => $new_version));
}

function actionUpdate() {
    global $PHPShopModules;

    $PHPShopModules->updateOption($_GET['id'], $_POST['servers']);

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam('base.novaposhta.novaposhta_system'));
    $PHPShopOrm->debug = false;

    $NovaPoshta = new NovaPoshta();
    // ���� �������� ����� ����������� - �������� ����� ��������
    if($NovaPoshta->option['sender_address'] !== $_POST['sender_address_new']) {
        $senderAddresses = $NovaPoshta->getSenderAddresses();
        foreach ($senderAddresses['data'] as $address) {
            if($_POST['sender_address_new'] == $address['Ref']) {
                $_POST['city_sender_new'] = $address['CityRef'];
            }
        }
    }

    // ���� �������� ���������� ���� - �������� ������� �����������
    if($NovaPoshta->option['sender_contact'] !== $_POST['sender_contact_new']) {
        $senderContacts = $NovaPoshta->getSenderContacts();
        foreach ($senderContacts['data'] as $contact) {
            if($_POST['sender_contact_new'] == $contact['Ref']) {
                $_POST['phone_new'] = $contact['Phones'];
            }
        }
    }

    $action = $PHPShopOrm->update($_POST);

    header('Location: ?path=modules&id=' . $_GET['id']);

    return $action;
}

function actionStart() {
    global $PHPShopGUI, $PHPShopOrm;

    $PHPShopGUI->addJSFiles('../modules/novaposhta/admpanel/gui/script.gui.js');
    $PHPShopGUI->addJSFiles('../modules/novaposhta/templates/js/jquery-ui.min.js');
    $PHPShopGUI->addCSSFiles('../modules/novaposhta/templates/css/jquery-ui.min.css');

    // �������
    $data = $PHPShopOrm->select();

    $NovaPoshta = new NovaPoshta();

    $status = NovaPoshta::getOrderStatuses($data['status']);
    $delivery = NovaPoshta::getDeliveries($data['delivery_id']);
    $senders = $NovaPoshta->getSenders();
    $senderContacts = $NovaPoshta->getSenderContacts();

    $senderArr = array();
    if(is_array($senders['data'])) {
        foreach ($senders['data'] as $sender) {
            $senderArr[] = array($sender['Description'], $sender['Ref'], $data['sender']);
        }
    }

    $senderAddresses = $NovaPoshta->getSenderAddresses();

    $senderAddressesArr = array();
    if(is_array($senderAddresses['data'])) {
        foreach ($senderAddresses['data'] as $address) {
            $senderAddressesArr[] = array($address['CityDescription'] . ', ' . $address['Description'], $address['Ref'], $data['sender_address']);
        }
    }

    $senderContactsArr = array();
    if(is_array($senderContacts['data'])) {
        foreach ($senderContacts['data'] as $contact) {
            $senderContactsArr[] = array($contact['Description'], $contact['Ref'], $data['sender_contact']);
        }
    }

    $citiesArr = $NovaPoshta->getCitiesArr($data['default_city']);

    if (empty($data['pvz_ref']))
        $buttonText = '������� ���';
    else
        $buttonText = '�������� ���';

    $Tab1 = $PHPShopGUI->setField('API ����', $PHPShopGUI->setInputText(false, 'api_key_new', $data['api_key'], 300));
    $Tab1.= $PHPShopGUI->setField('API ���� Google Map', $PHPShopGUI->setInputText(false, 'google_api_new', $data['google_api'], 300));

    if(empty($data['api_key'])) {
        $Tab1 .= '<div class="form-group form-group-sm "><div class="col-sm-12 text-info">'.__('��� ������� � �������������� ����������, ������� "API ����" � ������� "���������"').'.</div></div>';
    } else {
        $Tab1.= $PHPShopGUI->setField('����� �� ����� �� ���������', $PHPShopGUI->setSelect('default_city_new', $citiesArr, 300, null, false, true));
        $Tab1.= $PHPShopGUI->setField('�������� � ���������', $PHPShopGUI->setInputText(false, 'pvz_ref_new', $data['pvz_ref'], 300, '<a id="link-activate-novaposhta" onclick="novaPoshtaGetPVZ()" href="#">' . __($buttonText) . '</a>'));
        $Tab1.= $PHPShopGUI->setField('�����������', $PHPShopGUI->setSelect('sender_new', $senderArr, 300, null, false, false, false, 1, false));
        if(empty($data['sender'])) {
            $Tab1 .= '<div class="form-group form-group-sm "><div class="col-sm-12 text-info">'.__('�������� ����������� � ������� "���������" ��� ������� � ������� � ���������� ����� �����������').'.</div></div>';
        } else {
            $Tab1.= $PHPShopGUI->setField('����� �����������', $PHPShopGUI->setSelect('sender_address_new', $senderAddressesArr, 300, null, false, false, false, 1, false));
            $Tab1.= $PHPShopGUI->setField('���������� ���� �����������', $PHPShopGUI->setSelect('sender_contact_new', $senderContactsArr, 300, null, false, false, false, 1, false));
        }
    }

    $Tab1.= $PHPShopGUI->setField('������ ��� ��������', $PHPShopGUI->setSelect('status_new', $status, 300));
    $Tab1.= $PHPShopGUI->setField('�������� ���������', $PHPShopGUI->setSelect('delivery_id_new', $delivery, 300, null, false, false, false, 1, false));
    $Tab1 .= $PHPShopGUI->setField('��� �� ���������, ��.', '<input class="form-control input-sm " onkeypress="novaposhtaValidate(event)" type="number" step="1" min="1" value="' . $data['weight'] . '" name="weight_new" style="width:300px; ">');

    $Tab1 .= '
        <div class="modal fade bs-example-modal" id="novaposhtaModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">x</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">' . __('������� ���������') . '</h4>
            </div>
            <div class="modal-body" style="width:100%;">
               ' .
    $PHPShopGUI->setField('�����', $PHPShopGUI->setSelect('np-pvz-city', $citiesArr, 300, null, false, true))  .
    $PHPShopGUI->setField('���������', $PHPShopGUI->setSelect('np-pvz', [], 300, null, false, true)) . '
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="novaposhta-close">' . __('�������') . '</button>
            </div>
        </div>
    </div>
</div>';

    if(!empty($data['api_key'])) {
        $checkWh = $NovaPoshta->whBaseIsNotEmpty();
        if($checkWh) {
            $cities = $PHPShopGUI->setField('���������� ���������� �������<br>' . NovaPoshta::getCitiesStatus($data['last_cities_update']),
                $PHPShopGUI->setInput("submit", "updateCities", "��������", "center", '100px', "", "btn-sm btn-success", "actionGetCities"));
        } else {
            $cities = '<div class="form-group form-group-sm "><div class="col-sm-12 text-info">������� "��������" ����������� ���������, ��� ������� � ������������ ���������� �������.</div></div>';
        }


        $Tab1.= $PHPShopGUI->setCollapse('��������� ������������',
            $PHPShopGUI->setField('���������� ����� ���������<br>' . NovaPoshta::getWhTypesStatus($data['last_whtypes_update']),
                $PHPShopGUI->setInput("submit", "updateTypeWh", "��������", "center", '100px', "", "btn-sm btn-success", "actionGetWarehouseTypes")) .
            $PHPShopGUI->setField('���������� ���������<br>' . NovaPoshta::getWarehousesStatus($data['last_warehouses_update']),
                $PHPShopGUI->setInput("submit", "updateWh", "��������", "center", '100px', "", "btn-sm btn-success", "actionGetWarehouses")) .
            $cities
        );
    }

    $info = '<h4>��������� API �����</h4>
       <ol>
        <li>������������������ �� ����� <a href="https://novaposhta.ua/" target="_blank">���� �����</a>.</li>
        <li>� ������ �������� ������� "������������", "API 2.0".</li>
        <li>������ ������ "�������� ����� ����", ������� ������� ������������� ����������.</li>
        <li>��������������� ���� ������ � ���������� ������, ���� API ����.</li>
        </ol>
        
       <h4>��������� ������</h4>
        <ol>
        <li>������� ������ �������� ��� ������ ������.</li>
        <li>������ API ����. ������ "���������", ��������� ������ � �������������� ����������.</li>
        <li>������ API ���� ��� Google ����.</li>
        <li>������� ������ ��� �������� ������ � ������ ������� ����� �����.</li>
        <li>��������� ��� �� ���������. ����� �����������, ���� �� ����� � ���������� ������.</li>
        <li>������� �����������, ������ "���������".</li>
        <li>������� ��������� � ��������� "�������� � ���������" ��� ����� �����������.</li>
        <li>������� ���������� ���� �����������.</li>
        <li>��������� �������������� ���������� ����������� ���������� �������. �������� ������ � ������ "������", ����������� ���� phpshop/modules/novaposhta/cron/city.php</li>
        <li>��������� �������������� ���������� ����������� ���������. �������� ������ � ������ "������", ����������� ���� phpshop/modules/novaposhta/cron/warehouse.php</li>
        </ol>
        
       <h4>��������� ��������</h4>
        <ol>
        <li>� �������� �������������� �������� � �������� <kbd>��������� ��������� ��������</kbd> ��������� �������������� �������� ���������� ��������� �������� ��� ������. ����� "�� �������� ���������" ������ ���� �������.</li>
        </ol>';

    $Tab2 = $PHPShopGUI->setInfo($info);

    // ����� �����������
    $Tab4 = $PHPShopGUI->setPay($serial = false, false, $data['version'], true);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("��������", $Tab1, true), array("����������", $Tab2), array("� ������", $Tab4));

    // ����� ������ ��������� � ����� � �����
    $ContentFooter =
            $PHPShopGUI->setInput("hidden", "rowID", $data['id']) .
            $PHPShopGUI->setInput("submit", "saveID", "���������", "right", 80, "", "but", "actionUpdate.modules.edit");

    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

function actionGetCities() {
    $NovaPoshta = new NovaPoshta();
    $NovaPoshta->loader->getCities();

    header('Location: ?path=modules&id=' . $_GET['id']);
}

function actionGetWarehouseTypes() {
    $NovaPoshta = new NovaPoshta();
    $NovaPoshta->loader->getWarehouseTypes();

    header('Location: ?path=modules&id=' . $_GET['id']);
}

function actionGetWarehouses() {
    $NovaPoshta = new NovaPoshta();
    $NovaPoshta->loader->getWarehouses();

    header('Location: ?path=modules&id=' . $_GET['id']);
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setLoader($_POST['editID'], 'actionStart');
?>