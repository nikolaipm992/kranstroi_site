<?php

session_start();

// ���������
$enabled = false;

if (empty($_SERVER['DOCUMENT_ROOT'])) {
    $_classPath = realpath(dirname(__FILE__)) . "/../../../";
    $enabled = true;
} else
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
PHPShopObj::loadClass("valuta");

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopSystem = new PHPShopSystem();

// �����������
if ($_GET['s'] == md5($PHPShopBase->SysValue['connect']['host'] . $PHPShopBase->SysValue['connect']['dbase'] . $PHPShopBase->SysValue['connect']['user_db'] . $PHPShopBase->SysValue['connect']['pass_db']))
    $enabled = true;

if (empty($enabled))
    exit("������ �����������!");


// ��������� ������
PHPShopObj::loadClass("modules");
$PHPShopModules = new PHPShopModules($_classPath . "modules/");
$PHPShopValutaArray = new PHPShopValutaArray();

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
$PHPShopOrm->debug = false;
$productcomponents = $PHPShopOrm->getList(['*'], ['productcomponents_products' => ' != ""'], ['order' => 'datas desc'], ['limit' => 1000]);
$count = 0;
if (is_array($productcomponents)) {
    foreach ($productcomponents as $products) {
        $ids = explode(",", $products['productcomponents_products']);

        if (is_array($ids)) {

            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);
            foreach ($ids as $id) {
                $row[] = $PHPShopOrm->getOne(['*'], ['id=' => (int) $id]);
            }

            $price = $price2 = $price3 = $price4 = $price5 = 0;
            $enabled = 1;
            $items = 100;

            if (is_array($row)) {
                foreach ($row as $data) {

                    if ($data['baseinputvaluta'] != $products['baseinputvaluta']) {
                        $data['price'] = $data['price'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price2'] = $data['price2'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price3'] = $data['price3'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price4'] = $data['price4'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                        $data['price5'] = $data['price5'] / $PHPShopValutaArray->getArray()[$data['baseinputvaluta']]['kurs'];
                    }

                    $price += $data['price'];
                    $price2 += $data['price2'];
                    $price3 += $data['price3'];
                    $price4 += $data['price4'];
                    $price5 += $data['price5'];

                    if ($data['items'] < $items)
                        $items = $data['items'];

                    if (empty($data['items']) or empty($data['enabled'])) {
                        $items = 0;
                        $enabled = 0;
                    }
                }
            }

            // ������
            $price = $price - ($price * $products['productcomponents_discount'] / 100);
            $price2 = $price2 - ($price2 * $products['productcomponents_discount'] / 100);
            $price3 = $price3 - ($price3 * $products['productcomponents_discount'] / 100);
            $price4 = $price4 - ($price4 * $products['productcomponents_discount'] / 100);
            $price5 = $price5 - ($price5 * $products['productcomponents_discount'] / 100);

            // �������
            $price = $price + ($price * $products['productcomponents_markup'] / 100);
            $price2 = $price2 + ($price2 * $products['productcomponents_markup'] / 100);
            $price3 = $price3 + ($price3 * $products['productcomponents_markup'] / 100);
            $price4 = $price4 + ($price4 * $products['productcomponents_markup'] / 100);
            $price5 = $price5 + ($price5 * $products['productcomponents_markup'] / 100);

            $update['price_new'] = $price;
            $update['price2_new'] = $price2;
            $update['price3_new'] = $price3;
            $update['price4_new'] = $price4;
            $update['price5_new'] = $price5;
            $update['enabled_new'] = $enabled;
            $update['items_new'] = $items;

            if ($PHPShopOrm->update($update, ['id' => '=' . (int) $products['id']]))
                $count++;
        }
    }

    echo "���� � ������� ������� �������� ��� " . $count . " ������� �������";
}
?>