<?php

function addAtolDocs($data) {
    global $PHPShopGUI, $PHPShopSystem;

    $PHPShopGUI->addJSFiles('../modules/atol/admpanel/gui/atol.gui.js');


    if (!empty($data['ofd_status'])) {

        $ofd = unserialize($data['ofd']);
        if (is_array($ofd)) {


            $iso = $PHPShopSystem->getDefaultValutaIso();

            // ���� �����
            if ($iso == 'RUB' or $iso == 'RUR')
                $currency = ' <span class=rubznak>p</span>';
            else
                $currency = $iso;


            $Tab = '<div class="panel panel-default">
            <table class="table table-hover">
            <tr>
            <td>��� ��������</td>
            <td>';

            if ($ofd['operation'] == 'sell' and is_array($ofd['payload'])) {
                $Tab.='<span class="text-success">������</span>';
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="sell_refund" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> �������� ��� ��������</button>';
            } elseif(is_array($ofd['payload'])) {
                $Tab.='<span class="text-danger">�������</span>';
                $refundButton = null;
            }
            else {
                $Tab.='<span class="text-muted">������</span>';
                
                if(empty($data['operation']))
                    $data['operation']='sell';
                
                
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="' . $data['operation'] . '" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> ��������� ��������� ���</button>';
            }

            $Tab.='</td>
            </tr>
            <tr>
            <td>��������� ����� ����������� ����������</td>
            <td><b>' . $ofd['payload']['fn_number'] . '</b></td>
            </tr>
            <tr>
            <td>���������� ������� ���������</td>
            <td><b>' . $ofd['payload']['fiscal_document_attribute'] . '</b></td>
            </tr>
            <tr>
            <td>��������������� ����� ���</td>
            <td>' . $ofd['payload']['ecr_registration_number'] . '</td>
            </tr>
            <tr>
            <td>���������� ����� ����������� ���������</td>
            <td>' . $ofd['payload']['fiscal_document_number'] . '</td>
            </tr>
            <tr>
            <td>����, �����</td>
            <td>' . $ofd['payload']['receipt_datetime'] . '</td>
            </tr>
            <tr>
            <td>����� ��������� ������� � ������ ������ � �������</td>
            <td><b>' . $ofd['payload']['total'] . '</b> ' . $currency . '</td>
            </tr>
            <tr>
            <td>�������� ���� �� �����</td>
            <td><a href="https://lk.platformaofd.ru/web/noauth/cheque?fn=' . $ofd['payload']['fn_number'] . '&fp=' . $ofd['payload']['fiscal_document_attribute'] . '" target="_blank"><span class="glyphicon glyphicon-qrcode" style="padding-right:5px"></span>lk.platformaofd.ru</a></td>
            </tr>
            <tr>
            <td>������ ������ �����</td>
            <td><a href="?path=modules.dir.atol&id=' . $ofd['log_id'] . '" class="text-muted"><span class="glyphicon glyphicon-list-alt" style="padding-right:5px"></span>Atol API Log</a></td>
            </tr>
            </table>
            </div>
            ' . $refundButton;
            $PHPShopGUI->addTab(array("�����", $Tab, false, '117'));
        }
    }
    // �������� ����� �������� ���
    else {
        $Tab = '<button class="btn btn-default btn-md" data-operation="sell" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> �������� �������� ��� �������</button>';

        $PHPShopGUI->addTab(array("�����", $Tab, true, '117'));
    }
}

$addHandler = array(
    'actionStart' => 'addAtolDocs',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>