<?php

function avangard($data) {
    global $PHPShopGUI;

    // �������� ������� ������
    $orders = unserialize($data['orders']);

    include_once '../modules/avangard/class/Avangard.php';

    if($orders['Person']['order_metod']  == Avangard::PAYMENT_METHOD){

        $Tab1 = '';
        // ������� ������ ��������, ���� ������� ��� �� ����������
        
        if(Avangard::isPaid($data['uid']) && !Avangard::isReverse($data['uid'])) {
            $Tab1 .= $PHPShopGUI->setInput("submit", "reverse", "�������� �����", "center", null, "", "btn-sm ", "actionReverseOrderAvangard");
            $Tab1 .= '<hr>';
        }

        $logArray = Avangard::getLogs($data['uid']);

        $PHPShopInterface = new PHPShopInterface();
        $PHPShopInterface->checkbox_action = false;

        $PHPShopInterface->setCaption(array("������ ��������", "50%"), array("����", "20%"), array("������", "30%"));

        if (is_array($logArray))
            foreach ($logArray as $row) {
                $PHPShopInterface->setRow(array('name' => $row['type'], 'link' => '?path=modules.dir.avangard&id=' . $row['id']), PHPShopDate::get($row['date'], true), $row['status']);
            }

        $Tab1 .= '<table class="table table-hover">'.$PHPShopInterface->getContent().'</table>';

        $PHPShopGUI->addTab(array("��������", $Tab1, true));
    }
}
function actionReverseOrderAvangard(){

    include_once '../modules/avangard/class/Avangard.php';

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);

    $order = $PHPShopOrm->select(array('uid'), array("`id`=" => "'$_GET[id]'"), false, array('limit' => 1));

    $Avangard = new Avangard();
    $Avangard->reverseOrder($order['uid']);
}

// ��������� �������
$PHPShopGUI->getAction();
$addHandler = array(
    'actionStart' => 'avangard',
    'actionDelete' => false,
    'actionUpdate' => false
);
