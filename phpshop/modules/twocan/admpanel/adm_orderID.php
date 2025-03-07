<?php

function twocan($data) {
    global $PHPShopGUI,$PHPShopModules;

    // �������� ������� ������
    $orders = unserialize($data['orders']);
    

    if($orders['Person']['order_metod']  == 10028){
        $Tab1 .= '';
        $PHPShopOrder = new PHPShopOrderFunction($data['id'], $data);
       

        // SQL
        $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_log"));        

        // ������� �����
        $log = $PHPShopOrm->getList(array('*'), array("order_id=" => "'$data[uid]'"), array('order' => 'date DESC'));

        // SQL �������
        $PHPShopOrmTwocanOrders = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));

        // ������� ������
        $order = $PHPShopOrmTwocanOrders->getOne(array('*'), array("id=" => "'$data[uid]'"));

        // ���� �����
        $currency = $PHPShopOrder->default_valuta_code;

        // ������������ ������ 
        $orderData = unserialize($data['orders']);
        $discount = $orderData['Person']['discount']/100;


        if(in_array($order['status'], ['authorized']))
            $Tab1 .= $PHPShopGUI->setInput("submit", "charge", "������� " . number_format($data['sum'], 2, '.', ' '). ' ' . $currency, "center", null, "", "btn-sm btn-success", "twocanActionCharge") . '&nbsp;';
       
        // ������ ������ ������ ������� ������
        $Tab1 .= $PHPShopGUI->setInput("submit", "check", "��������� ������", "center", null, "", "btn-sm", "twocanActionCheck") . '&nbsp;';

        $couldRefund =  (float)$order['charged'] - (float)$order['refunded'];

        // ������� ������ ��������, ���� ��� ����, ��� ����������
        if(in_array($order['status'], ['charged', 'refunded']) && ($couldRefund > 0))
            $Tab1 .= $PHPShopGUI->setInput("submit", "refund", "������ ������� (". number_format($couldRefund, 2, '.', ' '). ' ' . $currency .")", "center", null, "", "btn-sm btn-danger", "twocanActionRefund");

        $couldRefundPart =  (float)$order['charged'] - (float)$order['refunded'] - (float)$data['sum'] ;

        // ������� ������ ���������� ��������, ���� ����� ��������� � ����� ��������� ������� ������ ����� ������
        if(in_array($order['status'], ['charged', 'refunded']) && ($couldRefundPart > 0))
            $Tab1 .=  '&nbsp;'.$PHPShopGUI->setInput("submit", "refundpart", "��������� ������� (" . number_format($couldRefundPart, 2, '.', ' '). ' ' . $currency.")", "center", null, "", "btn-sm btn-warning", "twocanActionRefundpart");

        // ������� ������ ��� ������, ���� �������� ������������� ������, � �������� ��� ��� ������������
        if(in_array($order['status'], ['authorized']))
            $Tab1 .= $PHPShopGUI->setInput("submit", "reverse", "������ �����������", "center", null, "", "btn-sm btn-danger", "twocanActionReverse");
                
        $PHPShopInterfaceOrder = new PHPShopInterface();
        $PHPShopInterfaceOrder->checkbox_action = false;

        // ������� ���������� �� ������ ���������� �� iBox
        $PHPShopInterfaceOrder->setCaption(array("����� ������ 2can&ibox", "30%"),  array("������", "15%"), array("����� ������", "20%"), array("������������", "15%"), array("����������", "15%"));

        if (is_array($order))
           $PHPShopInterfaceOrder->setRow(array('name' => $order['twocanid']), $order['status'],number_format($order['amount'], 2, '.', ' '),number_format($order['charged'], 2, '.', ' '),number_format($order['refunded'], 2, '.', ' ') );    

        $Tab1 .= '<hr><table class="table table-hover">'.$PHPShopInterfaceOrder->getContent().'</table>';

        $PHPShopInterface = new PHPShopInterface();
        $PHPShopInterface->checkbox_action = false;

        // ������� ������ �������� �� ������
        $PHPShopInterface->setCaption(array("������ ��������", "30%"),array("����� ������ 2can&ibox", "30%"),  array("����", "15%"), array("������", "15%"));

        if (is_array($log))
            foreach ($log as $row) {
                $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.twocan&id=' . $row['id']), array('name' => $row['twocan_id']), PHPShopDate::get($row['date'], true), $row['status']);
            }

        $Tab1 .= '<hr><table class="table table-hover">'.$PHPShopInterface->getContent().'</table>';

        $PHPShopGUI->addTab(array("2can&ibox", $Tab1, true));
        
    }
}

/**
 * ������� ������� ���������� �� ������ � 2can&ibox
 *
 * @return void
 */
function twocanActionCheck(){
    global $PHPShopModules, $PHPShopSystem;

    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // ��������� ������
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // ������� ������
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));
    if($order){
    
        // ����� ���������� � ������ �����
        if ($option["dev_mode"] == 0)
            $url = $option["test_url"];
        else
            $url = $option["url"];
        
        // ��������� ���������� �� ������
        $result = $PHPShopTwocanArray->getOrder($order['twocanid'], $option);
        
        // ��������� ���������� �������
        if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message'])) {
            // ������ �������
            $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ������", '������ ��������� ������', $result['result']['orders'][0]['id']);
            $PHPShopTwocanArray->updateOrderStatus($orderData['uid'], $result['result']['orders'][0]['status']);

        }else{
            // ������������ ����� � ����������� �� �������
            switch ($result['status']) {
                case 'authorized': // ������ �������������
                    $ordersORM->update(array('statusi_new' => $option["status_auth"]), array('id=' => $orderData['id']));
                    $PHPShopTwocanArray->log($result, $orderData['uid'], "������� ���������� �������", '������ ��������� ������', $result['result']['orders'][0]['id']);
                    break;
                case 'charged': // ������ �������
                    if($orderData['statusi'] != '101'){
                        $ordersORM->query("UPDATE `phpshop_orders` SET `statusi`='101', `paid` = 1 WHERE `id`='$orderData[id]'");

                        // ��� �����
                        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
                        $paymentOrm->insert(array('uid_new' => str_replace('-', '', $orderData['uid']), 'name_new' => '2can&ibox',
                            'sum_new' => $result['result']['orders'][0]['amount'], 'datas_new' => time()));
                    }
                    $PHPShopTwocanArray->log($result, $orderData['uid'], '������ ��������', '������ ��������� ������', $result['result']['orders'][0]['id']);
                    
                    break;
                case 'refunded': // ���������� ������� ���� ��������� �������
                    $sts = ((float)$order['charged'] - (float)$order['refunded'] > 0)?"���������� ��������� �������":"���������� �������";
                    $PHPShopTwocanArray->log($result, $orderData['uid'], $sts , '������ ��������� ������', $result['result']['orders'][0]['id']);                    
                    $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));                
                    break;
                case 'reversed': // ����������� ������ �����������
                    $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ����������� ���������", '������ ��������� ������', $result['result']['orders'][0]['id']);
                    $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));
                    break;
                default: // �� ������� ���������� ������, ���� �������� �� ���������� ������� �� �������
                    $PHPShopTwocanArray->log($result, $orderData['uid'], "�� ����������", '������ ��������� ������', $result['result']['orders'][0]['id']);
                    break;

                
            }
            // ��������� ���������� �� ������ � ������ ������
           $PHPShopTwocanArray->setOrder($orderData['uid'],$result['result']['orders'][0]['id'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        }
    }
}

/**
 * ������� ���������� �������� �� ������
 *
 * @return void
 */
function twocanActionRefundpart(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // ��������� ������
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // ������� ������
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));
    
    // ������� �����, ������� ����� �������
    $couldRefund =  (float)$order['charged'] - (float)$orderData['sum'] - (float)$order['refunded'];

    if($couldRefund > 0){
        // ���� ���� ��� ����������, �� ���������� ������ �� ����
        $result = $PHPShopTwocanArray->refundOrder($order['twocanid'], $couldRefund,  $option);   

        // ��������� ���������� �������
        if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
           // ������ �������
            $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ��������", '��������� �������', $order['twocanid']);
        }elseif($result['status'] == 'refunded'){
            // ����� ���������, ��������� �������� � ��� � ��������� ���������� �� ������ � ������          
            $PHPShopTwocanArray->log($result, $orderData['uid'], "���������� ��������� �������", '��������� �������', $order['twocanid']);
            $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        }else{
            // �� ������� ���������� ������, ���� �������� �� ���������� ������� �� �������
            $PHPShopTwocanArray->log($result, $orderData['uid'], "�� ����������", '��������� �������', $order['twocanid']);
        }
    }else{
        // ���� ���������� ��� ������, ����� � ��� ������
        $PHPShopTwocanArray->log(['error'=> "����� �������� ������ ���� > 0"], $orderData['uid'], "������", '��������� �������', $order['twocanid']);
    }
}

/**
 * ������� ������� �������� �� ������
 *
 * @return void
 */
function twocanActionRefund(){

    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // ��������� ������
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // ������� ������
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));

    // ������� �����, ������� ����� �������
    $couldRefund =  (float)$order['charged'] - (float)$order['refunded'];
    
    if($couldRefund > 0){
        // ���� ���� ��� ����������, �� ���������� ������ �� ����

        $result = $PHPShopTwocanArray->refundOrder($order['twocanid'], $couldRefund,  $option);    
        

        if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
            // ������ �������
            $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ��������", '�������', $order['twocanid']);
        }elseif($result['status'] == 'refunded'){
            // ����� ���������, ���������� ������ ������ �� �����������, ��������� �������� � ��� � ��������� ���������� �� ������ � ������
            $PHPShopTwocanArray->log($result, $orderData['uid'], "������� ��������", '�������', $order['twocanid']);
            $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));
            $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        }else{
            // �� ������� ���������� ������, ���� �������� �� ���������� ������� �� �������
            $PHPShopTwocanArray->log($result, $orderData['uid'], "�� ����������", '�������', $order['twocanid']);
        }
    }else{
        // ���� ���������� ��� ������, ����� � ��� ������
        $PHPShopTwocanArray->log(['error'=> "����� �������� ������ ���� > 0"], $orderData['uid'], "������", '��������� �������', $order['twocanid']);
    }
}


/**
 * ������� �������� ������� �� ������ ����� ������������
 *
 * @return void
 */
function twocanActionCharge(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));
 
    // ��������� ������
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // ������� ������
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));    
    
    // ������ ������ �� ��������
    $result = $PHPShopTwocanArray->chargeOrder($order['twocanid'], number_format($orderData['sum'], 2, '.', ''),  $option);  

    if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
        $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ��������", '��������', $order['twocanid']);
    }elseif($result['status'] == 'charged'){
        $PHPShopTwocanArray->log($result, $orderData['uid'], "����� �������", '��������', $order['twocanid']);
        $ordersORM->query("UPDATE `phpshop_orders` SET `statusi`='101', `paid` = 1 WHERE `id`='$orderData[id]'");
        $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
        // ��� �����
        $paymentOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['payment']);
        $paymentOrm->insert(array('uid_new' => str_replace('-', '', $orderData['uid']), 'name_new' => '2can&ibox',
            'sum_new' => $result['result']['orders'][0]['amount'], 'datas_new' => time()));
    }else{
        $PHPShopTwocanArray->log($result, $orderData['uid'], "�� ����������", '��������', $order['twocanid']);
    }
}

function twocanActionReverse(){
    global $PHPShopModules, $PHPShopSystem;

    // SQL
    $PHPShopOrm = new PHPShopOrm($PHPShopModules->getParam("base.twocan.twocan_orders"));
    $ordersORM = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
    
    $orderData = $ordersORM->select(array('*'), array('id=' => intval($_GET['id'])));

    // ��������� ������
    include_once(dirname(__FILE__) . '/../hook/mod_option.hook.php');
    $PHPShopTwocanArray = new PHPShopTwocanArray();
    $option = $PHPShopTwocanArray->getArray();

    // ������� ������
    $order = $PHPShopOrm->getOne(array('*'), array("id=" => "'$orderData[uid]'"));
   
    $result = $PHPShopTwocanArray->reverseOrder($order['twocanid'], $option);   
    

    if($result['status'] == 'error' || isset($result['result']['orders'][0]['failure_message']) || isset($result['result']['failure_message'])) {
        $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ������ �����������", '������ �����������', $order['twocanid']);
    }elseif($result['status'] == 'reversed'){
        $PHPShopTwocanArray->log($result, $orderData['uid'], "������ ����������� ���������", '������ �����������', $order['twocanid']);
        $ordersORM->update(array('statusi_new' => 1), array('id=' => $orderData['id']));
        $PHPShopTwocanArray->setOrder($orderData['uid'],$order['twocanid'], $result['result']['orders'][0]['amount'],$result['result']['orders'][0]['status'],$result['result']['orders'][0]['amount_charged'],$result['result']['orders'][0]['amount_refunded']);
    }else{
        $PHPShopTwocanArray->log($result, $orderData['uid'], "�� ����������", '������ �����������', $order['twocanid']);
    }
}


$addHandler = array(
    'actionStart' => 'twocan',
    'actionDelete' => false,
    'actionEdit' => false,
);