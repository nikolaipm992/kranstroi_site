<?php

function cloudpayments($data) {
    global $PHPShopGUI,$PHPShopModules;

    // �������� ������� ������
    $orders = unserialize($data['orders']);

    if($orders['Person']['order_metod']  == 10014){

        // SQL
        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudpayments.cloudpayment_log"));

        // ������� �����
        $log = $PHPShopOrm->select(array('*'), array("order_id=" => "'$data[uid]'"), array('order' => 'date DESC'));

        // ������� ������ ��������, ���� ������� ��� �� ����������
        $refund = false;
        foreach ($log as $logItem){
            if($logItem['type'] == 'refundTrue')
                $refund = true;
        }
        if($refund == false)
            $Tab1 = $PHPShopGUI->setInput("submit", "cprefund", "������� �������� �������", "center", null, "", "btn-sm ", "actionCPRefund");
        else
            $Tab1 = '';

        $PHPShopInterface = new PHPShopInterface();
        $PHPShopInterface->checkbox_action = false;

        $PHPShopInterface->setCaption(array("������ ��������", "50%"), array("����", "20%"), array("������", "30%"));

        if (is_array($log))
            foreach ($log as $row) {
                $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.cloudpayments&id=' . $row['id']), PHPShopDate::get($row['date'], true), $row['status']);
            }

        $Tab1 .= '<hr><table class="table table-hover">'.$PHPShopInterface->getContent().'</table>';

        $PHPShopGUI->addTab(array("CloudPayments", $Tab1, true));
    }
}
function actionCPRefund(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.cloudpayments.cloudpayment_log"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // ��������� ������
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopcloudpaymentArray = new PHPShopcloudpaymentArray();
    $option = $PHPShopcloudpaymentArray->getArray();

    // ������� �����
    $log = $PHPShopOrm->select(array('*'), array("order_id=" => "'$orderData[uid]'"));
    foreach ($log as $item){
        $message = '';
        if($item['message'])
            $message = unserialize($item['message']);
        if(isset($message['TransactionId']) || isset($message['Amount'])){
            $amount = $message['Amount'];
            $transactionId = $message['TransactionId'];
            break;
        }
    }
    $url = 'https://api.cloudpayments.ru/payments/refund';

    // ���
    if ($PHPShopSystem->getParam('nds_enabled') == '')
        $tax = 0;
    else
        $tax = $PHPShopSystem->getParam('nds');

    $order = unserialize($orderData['orders']);

    foreach ($order['Cart']['cart'] as $orderItem){
        $amountItem = intval($orderItem['price']) * intval($orderItem['num']);

        $aItem[] = array(
            "label"     => PHPShopString::win_utf8($orderItem['name']),
            "price"     => $orderItem['price'],
            "quantity"  => $orderItem['num'],
            "amount"    => $amountItem,
            "vat"       => $tax
        );
    }
    $array = array(
        "cloudPayments" => array(
            "customerReceipt" => array (
                "Items" => $aItem,
                "taxationSystem" => $option["taxationSystem"],
                "email" => $order["Person"]["mail"],
                "phone" => $_POST["tel_new"]
            )
        )
    );
    $json = json_encode($array);
    $params = array(
        'TransactionId' => intval($transactionId),
        'Amount' => $amount,
        'JsonData' => $json
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "$option[publicId]:$option[api]");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
    $result = curl_exec($ch); // run the whole process
    curl_close($ch);

    if($result['Success'] == true){
        // ����� ���
        $PHPShopcloudpaymentArray->log("������� �������� ������� ������� ��������", $orderData['uid'], '������� �������� ������� ��������', 'refundTrue');
    }else{
        // ����� ��� ������, ������ ������ ������
        $PHPShopcloudpaymentArray->log("������� �������� ������� �� ��������", $orderData['uid'], '������� �������� ������� �� ��������', 'refundFalse');
        $ordersORM->update(array('statusi' => 1), array('id=' => $orderData['id']));
    }
}

// ��������� �������
$addHandler = array(
    'actionStart' => 'cloudpayments',
    'actionDelete' => false,
    'actionUpdate' => false
);
