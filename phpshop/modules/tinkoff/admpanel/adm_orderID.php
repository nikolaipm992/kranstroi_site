<?php

include_once dirname(__DIR__) . '/class/tinkoff.class.php';

function tbankTab($data) {
    global $PHPShopGUI, $PHPShopModules,$PHPShopSystem;

    // Проверка способа оплаты
    $orders = unserialize($data['orders']);

    if ((int) $orders['Person']['order_metod'] === Tinkoff::PAYMENT_ID and $data['paid'] != 1) {

        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.tinkoff.tinkoff_log"));
        $log = $PHPShopOrm->getList(array('*'), array("order_id=" => "'$data[uid]'"), array('order' => 'date DESC'));
        if (count($log) > 0) {
            $Tab1 = '';
            if ((int) $data['paid'] === 1) {
                $Tab1 = '<hr>';
                $Tab1 .= $PHPShopGUI->setInput("submit", "refund", "Возврат денежных средств", "center", null, "", "btn-sm tinkoff-refund");
                $Tab1 .= $PHPShopGUI->setInput('hidden', 'tinkoff_order_id', $data['id']);
            }

            $PHPShopInterface = new PHPShopInterface();
            $PHPShopInterface->checkbox_action = false;

            $PHPShopInterface->setCaption(array("Журнал операций", "50%"), array("Дата", "20%"), array("Статус", "30%"));

            foreach ($log as $row) {
                $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.tinkoff&id=' . $row['id']), PHPShopDate::get($row['date'], true), $row['status']);
            }

            


            // Ссылка на оплату
            $tinkoff = new Tinkoff();
            $PHPShopOrderFunction  = new PHPShopOrderFunction(false);
            $PHPShopOrderFunction->import($data);
            $email['mail'] = $PHPShopOrderFunction->getMail();
            
            
            class tinkoff_data{
                function __construct() {
                }
            }
            
            $obj = new tinkoff_data();
            
            $obj->ouid = $PHPShopOrderFunction->objRow['uid'];
            $obj->tinkoff_total = floatval(number_format($PHPShopOrderFunction->getTotal(), 2, '.', '')) * 100;
            $order = $PHPShopOrderFunction->unserializeParam('orders');
            $obj->tinkoff_cart = $order['Cart']['cart'];
            $obj->discount = $order['Person']['discount'];

            // Доставка
            if (!empty($order['Cart']['dostavka'])) {

                PHPShopObj::loadClass('delivery');
                $PHPShopDelivery = new PHPShopDelivery($order['Person']['dostavka_metod']);

                if ($ofd_nds = $PHPShopDelivery->getParam('ofd_nds'))
                    $tax = $PHPShopDelivery->getParam('ofd_nds');
                else
                    $tax = $PHPShopSystem->getParam('nds');

                $obj->tinkoff_delivery_nds = $tax;

                $obj->delivery = floatval(number_format($order['Cart']['dostavka'], 2, '.', ''));
            }

            $link = $tinkoff->getPaymentUrl($obj, $email);
            
            $Tab1 .= '<hr>'.$PHPShopGUI->setField('Ссылка на оплату', $PHPShopGUI->setInputText(null, 'tinkoff', $link['url'] , 300));
            $Tab1 .= '<hr><table class="table table-hover">' . $PHPShopInterface->getContent() . '</table>';

            $PHPShopGUI->addTab(array("Т-Банк", $Tab1, false));
        }
    }
}

// Обработка событий
$addHandler = array(
    'actionStart' => 'tbankTab',
    'actionDelete' => false,
    'actionUpdate' => false
);
