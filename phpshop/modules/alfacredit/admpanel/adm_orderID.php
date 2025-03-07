<?php

function addOptionAcreditStatus($data) {
    global $PHPShopGUI;

    // ����� ������
    $reference = date('dmy', $data['datas']) . str_replace('-', '', $data['uid']);
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['alfacredit']['alfacredit_log']);
    $PHPShopOrm->debug = false;
    $row = $PHPShopOrm->select(array('*'), array('reference' => "='" . $reference . "'"), false, array('limit' => 1));
    if (isset($row['id'])) {
        // ��������� ������
        include_once dirname(__FILE__) . '/../hook/mod_option.hook.php'; 
        $PHPShopAlfaCredit = new PHPShopAlfaCreditArray(false);
        
        $status = unserialize($row['status']);
        
        $fields = array (
            'lastName' => __('�������'),
            'firstName' => __('���'),
            'credTitle' => __('������������ ���������� ��������'),
            'credTerm' => __('���� �������')
        );
        
        $dis = '';
        foreach ($fields as $key => $field) {
            if (isset($status[$key]))
                $dis .= $field . ': ' . $status[$key] . '<br>';
        }
        $dis = substr($dis, 0, -4);
        $dis .= __('������ ������').': ' . $PHPShopAlfaCredit->statuses[$status['currentStatus']];
        
        $Tab3 = $PHPShopGUI->setPanel('���������� � ������', $dis);
        $PHPShopGUI->addTab(array("������", $Tab3, true));
    }
}

$addHandler = array(
    'actionStart' => 'addOptionAcreditStatus',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>