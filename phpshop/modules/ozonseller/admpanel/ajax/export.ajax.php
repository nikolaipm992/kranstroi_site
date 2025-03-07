<?php

session_start();
$_classPath = "../../../../";
include($_classPath . "class/obj.class.php");
PHPShopObj::loadClass(array("base"));

$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
$PHPShopBase->chekAdmin();

$PHPShopSystem = new PHPShopSystem();

include_once '../../class/OzonSeller.php';
$OzonSeller = new OzonSeller();


// Всего товаров 
if (empty($_POST['end']))
    $end = $PHPShopBase->getNumRows('products', "where export_ozon='1' and export_ozon_task_status!=\"imported\"");
else
    $end = (int) $_POST['end'];

$start = (int) $_POST['start'];
$count = (int) $_POST['count'];

$PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['products']);

// Стоп
if (!empty($_POST['stop'])) {
    $PHPShopOrm->update(['export_ozon_task_status_new' => ''], ['export_ozon_id' => '=0']);
    $return = array("success" => 'done');
} else
    $data = $PHPShopOrm->getOne(array('*'), array('export_ozon' => "='1'", 'export_ozon_task_status' => '!="imported"'));

if (is_array($data)) {

    $OzonSeller = new OzonSeller();

    // Товар еще не выгружен
    if (empty($data['export_ozon_id'])) {

        // Выгрузка
        if (empty($data['export_ozon_task_id'])) {
            $result = $OzonSeller->sendProducts($data);
            $task_id = $result['result']['task_id'];
            $data['export_ozon_task_id'] = $result['result']['task_id'];
            $error = $result['message'];
        } else
            $task_id = $data['export_ozon_task_id'];

        if (!empty($task_id) and empty($error)) {

            // Проверка статуса выгрузки
            $info = $OzonSeller->sendProductsInfo($data)['result']['items'][0];

            // Товар выгрузился
            if (!empty($info['product_id'])) {
                $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status'], 'export_ozon_id_new' => $info['product_id']], ['id' => '=' . (int) $data['id']]);
                $OzonSeller->clean_log($data['id']);
                $count++;
            }
            // Ошибка
            elseif (is_array($info['errors']) and count($info['errors']) > 0) {

                if (empty($info['status']))
                    $info['status'] = 'error';

                foreach ($info['errors'] as $k => $er) {

                    // Ссылки
                    $er['description'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">[ссылка]</a>$3', $er['description']);
                    $error .= ($k + 1) . ' - ' . PHPShopString::utf8_win1251($er['description']) . '<br>';
                }

                $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status']], ['id' => '=' . (int) $data['id']]);
            }
            // В ожидании
            elseif ($info['status'] == 'pending') {
                PHPShopObj::loadClass("lang");
                $error = __('Товар поставлен в очередь на запись, сервис OZON временно занят. Требуется повторное отправление данных для завершения выгрузки.');
                $PHPShopOrm->update(['export_ozon_task_status_new' => $info['status'], 'export_ozon_task_id_new' => $task_id], ['id' => '=' . (int) $data['id']]);
            }

            if (!empty($error))
                $OzonSeller->export_log($error, $data['id'], $data['name'], $data['pic_small']);
        }
        // Ошибка 
        elseif (!empty($error)) {
            $OzonSeller->export_log($error, $data['id'], $data['name'], $data['pic_small']);
        }
    }

    $start++;
    $bar = round($start * 100 / $end);

    if ($bar < 100) {
        $return = array("success" => true, 'start' => (int) $start, 'end' => (int) $end, 'bar' => (int) $bar, 'count' => $count);
    } else {

        $return = array("success" => 'done', 'bar' => (int) $bar, 'count' => $count);
    }
} else
    $return = array("success" => 'done', 'bar' => 100, 'count' => $count);



if ($return) {
    header("Content-Type: application/json");
    echo json_encode($return);
}
