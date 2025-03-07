<?php

include_once dirname(__DIR__) . '/class/Sberbank.php';

function sberbank($data) {
    global $PHPShopGUI,$PHPShopModules;

    // Проверка способа оплаты
    $orders = unserialize($data['orders']);

    if((int) $orders['Person']['order_metod'] === Sberbank::SBERBANK_PAYMENT_ID){

        $PHPShopGUI->addJSFiles('../modules/sberbankrf/admpanel/gui/script.gui.js?v=1.3');
        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.sberbankrf.sberbankrf_log"));
        $log = $PHPShopOrm->getList(array('*'), array("order_id=" => "'$data[uid]'"), array('order' => 'date DESC'));
        if(count($log) > 0) {
            $Tab1 = '';
            if((int) $data['paid'] === 1) {
                $Tab1 = '<hr>';
                $Tab1 .= $PHPShopGUI->setInput("submit", "refund", "Возврат денежных средств", "center", null, "", "btn-sm sberbank-refund");
                $Tab1 .= $PHPShopGUI->setInput('hidden', 'sberbank_order_id', $data['id']);
            }

            $PHPShopInterface = new PHPShopInterface();
            $PHPShopInterface->checkbox_action = false;

            $PHPShopInterface->setCaption(array("Журнал операций", "50%"), array("Дата", "20%"), array("Статус", "30%"));

            foreach ($log as $row) {
                $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.sberbankrf&id=' . $row['id']), PHPShopDate::get($row['date'], true), $row['status']);
            }

            $Tab1 .= '<hr><table class="table table-hover">'.$PHPShopInterface->getContent().'</table>';

            $PHPShopGUI->addTab(array("Сбербанк России", $Tab1, false));
        }
    }
}

// Обработка событий
$addHandler = array(
    'actionStart' => 'sberbank',
    'actionDelete' => false,
    'actionUpdate' => false
);
