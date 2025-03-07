<?php

function addCloudpaymentsDocs($data) {
    global $PHPShopGUI, $PHPShopSystem;

    $PHPShopGUI->addJSFiles('../modules/cloudkassir/admpanel/gui/cloudpayments.gui.js');


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
                $Tab.='<span id="operation-status" class="text-success">������</span>';
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="sell_refund" id="cloudpayments" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> �������� ��� ��������</button><span class="text-success" id="refund_alert"></span>';
            } elseif(is_array($ofd['payload'])) {
                $Tab.='<span class="text-danger">�������</span>';
                $refundButton = null;
            }
            else {
                $Tab.='<span class="text-muted">������</span>';
                
                if(empty($data['operation']))
                    $data['operation']='sell';
                
                
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="' . $data['operation'] . '" id="cloudpayments" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> ��������� ��������� ���</button>';
            }

            $Tab.='</td>
            </tr>
            <tr>
            <td>��������� ����� ����������� ����������</td>
            <td><b>' . $ofd['payload']['DeviceNumber'] . '</b></td>
            </tr>
            <tr>
            <td>���������� ������� ���������</td>
            <td><b>' . $ofd['payload']['FiscalSign'] . '</b></td>
            </tr>
            <tr>
            <td>��������������� ����� ���</td>
            <td>' . $ofd['payload']['RegNumber'] . '</td>
            </tr>
            <tr>
            <td>����� ����</td>
            <td>' . $ofd['payload']['DocumentNumber'] . '</td>
            </tr>
            <tr>
            <td>����, �����</td>
            <td>' . $ofd['payload']['DateTime'] . '</td>
            </tr>
            <tr>
            <td>����� ��������� ������� � ������ ������ � �������</td>
            <td><b>' . $ofd['payload']['Amount'] . '</b> ' . $currency . '</td>
            </tr>
            <tr>
            <td>�������� ���� �� �����</td>
            <td><a href="'. $ofd['payload']['Url'] . '" target="_blank"><span class="glyphicon glyphicon-qrcode" style="padding-right:5px"></span>��� �� �����</a></td>
            </tr>
            <tr>
            <td>������ ������ �����</td>
            <td><a href="?path=modules.dir.cloudkassir&id=' . $ofd['log_id'] . '" class="text-muted"><span class="glyphicon glyphicon-list-alt" style="padding-right:5px"></span>CloudKassir API Log</a></td>
            </tr>
            </table>
            </div>
            ' . $refundButton;
            $PHPShopGUI->addTab(array("����� CloudKassir", $Tab, false, '7'));
        }
    }
    // �������� ����� �������� ���
    else {
        $Tab = '<button class="btn btn-default btn-md" data-operation="sell" id="cloudpayments" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> �������� �������� ��� �������</button>';
        $PHPShopGUI->addTab(array("����� CloudKassir", $Tab, true, '7'));
    }
}

$addHandler = array(
    'actionStart' => 'addCloudpaymentsDocs',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>