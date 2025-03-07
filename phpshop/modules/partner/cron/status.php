<?php

/**
 * ���������� �������� ������� �� csv �����
 * ��� ��������� ��������� �������� enabled �� true
 */
// ���������
$enabled = false;
$csv_file = 'status.csv';
session_start();

$_classPath = "../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass("base");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("date");
PHPShopObj::loadClass("order");
PHPShopObj::loadClass("cart");
PHPShopObj::loadClass("parser");
PHPShopObj::loadClass("text");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("file");
PHPShopObj::loadClass("order");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();
$PHPShopOrderStatusArray = new PHPShopOrderStatusArray();

// �����������
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("������ �����������!");


// ��������� ������
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopModules->checkInstall('partner');

include_once($_classPath . 'modules/partner/class/partner.class.php');
$PHPShopPartnerOrder = new PHPShopPartnerOrder();
$PHPShopPartnerOrder->option = $PHPShopPartnerOrder->option();

// ������� �������
$GetOrderStatusArray = $PHPShopOrderStatusArray->getKey('name.id', true);
$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
$csv_load_count = 0;

// ��������� ������ CSV
function csv_update($row) {
    static $n;
    global $PHPShopPartnerOrder, $GetOrderStatusArray, $PHPShopOrm, $csv_load_count;

    if ($n > 0) {

        // ���� ����� ��������, ������� ��� ������ � ��� ��������, ��������� % ��������
        if ($row[1] == $PHPShopPartnerOrder->option['order_status']) {

            // ����� ������
            $data = $PHPShopOrm->getOne(['id', 'sum', 'statusi'], ['id' => '=' . (int) $row[0]]);

            // ��������� ����� ��������
            if (!empty($data['id'])) {
                $PHPShopPartnerOrder->addBonus($data['id'], $data['sum']);

                // ����� ������� ������ ��������
                $PHPShopPartnerOrder->updateLog($data['id']);
            }
        }

        // ����� ������� ������
        if (!empty($row[1]))
            $PHPShopOrm->update(['statusi_new' => $row[1]], ['id' => '=' . (int) $row[0]]);

        $csv_load_count++;
    }
    $n++;
}

// ������ �������
$result = PHPShopFile::readCsv($csv_file, 'csv_update');
if ($result)
    echo '���������� ' . $GLOBALS['csv_load_count'] . ' �����';
?>