<?php

function tab_log() {

    $PHPShopInterface = new PHPShopInterface();
    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->setCaption(array("����", "15%"), array("����", "30%"), array("������", "10%"), array("�����", "25%", array('align' => 'right')));

    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);
    $PHPShopOrm->debug = false;
    $data = $PHPShopOrm->select(array('*'), false, array('order' => 'date desc'), array('limit' => 20));
    if (is_array($data)){
        foreach ($data as $row) {

            $date = PHPShopDate::get($row['date'], true);

            if (empty($row['status'])){
                $status = "<span class='text-warning'>" . __('������') . "</span>";
                $text = null;
            }
            else {
                $status = __("��������");
                $info = unserialize($row['info']);
                $text = __('���������� ') . $info[0] . __(' �����') . '. ' . $info[1] . ' ' . $info[2] . __(' �������');
            }

            $PHPShopInterface->setRow(array('name' => $date, 'link' => '?path=exchange.import&id=' . $row['id']), array('name' => pathinfo($row['file'])['basename']), $status, array('name' => $text, 'link'=>'?path=catalog&cat=0&import=' . $row['import_id'], 'align' => 'right'));
        }

    return '<table class="table table-hover">' . $PHPShopInterface->_CODE . '</table><a class="btn btn-default btn-sm pull-right" href="?path=exchange.log">'.__('�������� ��� ������').'</a>';
    }
}

?>