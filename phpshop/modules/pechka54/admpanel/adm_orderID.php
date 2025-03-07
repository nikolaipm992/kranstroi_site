<?php

function addAtolDocs($data) {
    global $PHPShopGUI, $PHPShopSystem,$_classPath;

    $PHPShopGUI->addJSFiles($_classPath.'modules/pechka54/admpanel/gui/pechka54.gui.js');


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
            
            if($data['ofd_status'] == 1){
               $Tab.='<span class="text-warning">�� �������� ������������� ������ ����. ���� ��� �� ������������ ����� 2 �����, �� ��������� ������ ������  � ��������� ���� ����� �� ��������� ������.</span>';
               $refundButton = '<button class="btn btn-default btn-sm" data-operation="registration" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> ��������� ������ ����</button>';
               
            }
            elseif ($data['ofd_type'] == 'registration' and $data['ofd_status'] == 2 ) {
                $Tab.='<span class="text-success">������</span>';
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="return" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> �������� ��� ��������</button>';
            } elseif($data['ofd_type'] == 'return' and $data['ofd_status'] == 2) {
                $Tab.='<span class="text-danger">�������</span>';
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="sell" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> �������� ��� �������</button>';
            }
            elseif($data['ofd_status'] == 3) {
                $Tab.='<span class="text-warning">������ ��������� ����. ��������� ������ ������ � ��������� �� ��������� ������.</span>';
                
                $refundButton = '<button class="btn btn-default btn-sm" data-operation="registration" id="atol" data-id="' . $data['id'] . '"><span class="glyphicon glyphicon-bookmark"></span> ��������� ������ ����</button>';
            }

            $Tab.='<input class="hidden-edit" type="hidden" value="'.$data['ofd_status'].'" name="ofd_status_new">
                <input class="hidden-edit" type="hidden" value="'.$data['ofd_type'].'" name="ofd_type_new">
                    </td>
            </tr>
            <tr>
            <td>��������� ����� ����������� ����������</td>
            <td><b>' . $ofd['payload']['fn_number'] . '</b></td>
            </tr>
            <tr>
            <td>��������������� ����� ���</td>
            <td>' . $ofd['payload']['ecr_registration_number'] . '</td>
            </tr>
            <tr>
            <td>����, �����</td>
            <td>' . $ofd['payload']['receipt_datetime'] . '</td>
            </tr>
            <tr>
            <td>����� ��������� ������� � ������ ������ � �������</td>
            <td><b>' . $ofd['payload']['total'] . '</b> ' . $currency . '</td>
            </tr>';
            
            if($data['ofd_status'] == 2)
            $Tab.='<tr>
            <td>�������� ���� �� �����</td>
            <td><a href="'.$ofd['payload']['OFDLink']. '" target="_blank"><span class="glyphicon glyphicon-qrcode" style="padding-right:5px"></span>������ �� ���</a></td>
            </tr>';
                
            $Tab.='<tr>
            <td>������ ������ �����</td>
            <td><a href="?path=modules.dir.pechka54&id=' . $ofd['log_id'] . '" class="text-muted"><span class="glyphicon glyphicon-list-alt" style="padding-right:5px"></span>Pechka54 API Log</a></td>
            </tr>
            </table>
            </div>
            ' . $refundButton;
            $PHPShopGUI->addTab(array("�����", $Tab, false, '7'));
        }
    }
    
}

$addHandler = array(
    'actionStart' => 'addAtolDocs',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>