<?php

$TitlePage = __("��������� �������");

// ��������� ������� ��������
function actionStart() {
    global $PHPShopGUI;

    // SQL
    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges_log']);

    // �������
    $data = $PHPShopOrm->select(array('*'), array('id' => '=' . intval($_GET['id'])));
    $PHPShopGUI->setActionPanel(__('��������� ������� �� ') . PHPShopDate::get($data['date']), false, array('�������'));
    $PHPShopGUI->field_col = 5;

    $option = unserialize($data['option']);

    // ���������
    $PHPShopOrmExchanges = new PHPShopOrm($GLOBALS['SysValue']['base']['exchanges']);
    $data_exchanges = $PHPShopOrmExchanges->select(array('*'), array('id' => '=' . intval($option['exchanges']) . ' or id=' . intval($option['exchanges_new'])), false, array("limit" => 1));
    if (!is_array($data_exchanges)) {
        $data_exchanges['name'] = '-';
    }

    if (empty($option['export_imgpath']))
        $option['export_imgpath'] = __('����');
    else
        $option['export_imgpath'] = __('���');

    if (empty($option['export_imgproc']))
        $option['export_imgproc'] = __('����');
    else
        $option['export_imgproc'] = __('���');

    if (empty($option['export_imgload']))
        $option['export_imgload'] = __('����');
    else
        $option['export_imgload'] = __('���');

    if (empty($option['export_uniq']))
        $option['export_uniq'] = __('����');
    else
        $option['export_uniq'] = __('���');

    if (empty($option['export_imgsearch']))
        $option['export_imgsearch'] = __('����');
    else
        $option['export_imgsearch'] = __('���');
    
    if (empty($option['export_ai']))
        $option['export_ai'] = __('����');
    else
        $option['export_ai'] = __('���');


    $delim_value = array(';' => __('����� � �������'), ',' => __('�������'));
    $action_value = array('update' => __('����������'), 'insert' => __('��������'));
    $delim_sortvalue = array('#' => '#', '@' => '@', '$' => '$', '-' => __('�������'));
    $delim_sort = array('/' => '/', '\\' => '\\', '-' => '-', '&' => '&', ';' => ';', ',' => ',');
    $delim_imgvalue = array(',' => __('�������'), 0 => __('����'), ';' => __('����� � �������'), '#' => '#', ' ' => __('������'));
    $code_value = array('ansi' => 'ANSI', 'utf' => 'UTF-8');
    $extension_value = array('csv' => 'CSV', 'xls' => 'XLS', 'xlsx' => 'XLSX', 'yml' => 'YML');

    if (!empty($option['export_key']))
        $key_value = $option['export_key'];
    else
        $key_value = 'Id ��� �������';

    if (empty($data['status'])) {
        $status = "<span class='text-warning'>" . __('������') . "</span>";
        $text = null;
        $class = 'hide';
    } else {
        $status = __("�������");
        $info = unserialize($data['info']);



        $text = __('���������� ') . $info[0] . (' �����') . '.<br><a href="' . $info[3] . '" target="_blank">' . $info[1] . ' ' . $info[2] . __(' �������') . '</a>';
    }

    $path_name = [
        'exchange.import.catalog' => __('��������'),
        'exchange.import' => __('������'),
        'exchange.import.user' => __('������������'),
        'exchange.import.order' => __('������'),
    ];

    if (!empty($info[3]))
        $path_parts = pathinfo($info[3]);
    $result_file = './csv/' . $path_parts['basename'];
    
    if(stristr($data['file'],'http'))
            $file = $data['file'];
    else $file=pathinfo($data['file'])['basename'];
    

    // �������� 1
    $Tab1 = $PHPShopGUI->setField("����", $PHPShopGUI->setText($PHPShopGUI->setLink('./csv/'.$file, $file))) .
            $PHPShopGUI->setField("��� ������", $PHPShopGUI->setText($path_name[$option['path']], false, false, false)) .
            $PHPShopGUI->setField("���������", $PHPShopGUI->setText('<a href="?path=' . $option['path'] . '&exchanges=' . $data_exchanges['id'] . '">' . $data_exchanges['name'] . '</a>', false, false, false), false, false, $class) .
            $PHPShopGUI->setField("���������� �����", $PHPShopGUI->setText($info[0]), false, false, $class) .
            $PHPShopGUI->setField($info[1] . ' ' . __('�������'), $PHPShopGUI->setText($PHPShopGUI->setLink('./admin.php?path=catalog&import='.$data['import_id'], $info[2])), false, false, $class, 'control-label', false) .
            $Tab1 .= $PHPShopGUI->setField('��������� �����������', $PHPShopGUI->setText((int) $info[4]), false, false, $class)
            ;
    
    if(!empty($info[3])){
        $PHPShopGUI->setField('�����', $PHPShopGUI->setText('<a href="' . $result_file . '" target="_blank">CSV</a>'), false, false, $class) ;
    }
    
    $Tab1 .=
            $PHPShopGUI->setField('��������', $PHPShopGUI->setText($action_value[$option['export_action']], false, false, false)) .
            $PHPShopGUI->setField('CSV-�����������', $PHPShopGUI->setText($delim_value[$option['export_delim']], false, false, false)) .
            $PHPShopGUI->setField('����������� ��� �������������', $PHPShopGUI->setText($delim_sortvalue[$option['export_sortdelim']], false, false, false)) .
            $PHPShopGUI->setField('����������� �������� �������������', $PHPShopGUI->setText($delim_sort[$option['export_sortsdelim']], false, false, false)) .
            $PHPShopGUI->setField('�������� �������� � AI', $PHPShopGUI->setText($option['export_ai'], false, false, false),1,'�������� � ��������� �������� � ������� AI. ��������� �������� Yandex Cloud.') .
            $PHPShopGUI->setField('����� ����������� � ������', $PHPShopGUI->setText($option['export_imgsearch'], false, false, false),1,'����� ����������� � ������ �� ����� ������. ��������� �������� Yandex Cloud.') .
            $PHPShopGUI->setField('������ ���� ��� �����������', $PHPShopGUI->setText($option['export_imgpath'], false, false, false), 1, '��������� � ������������ ����� /UserFiles/Image/') .
            $PHPShopGUI->setField('��������� �����������', $PHPShopGUI->setText($option['export_imgproc'], false, false, false), 1, '�������� ��������� � ����������') .
            $PHPShopGUI->setField('�������� �����������', $PHPShopGUI->setText($option['export_imgload'], false, false, false), 1, '�������� ����������� �� ������ �� ������') .
            $PHPShopGUI->setField('����������� ��� �����������', $PHPShopGUI->setText($delim_imgvalue[$option['export_imgdelim']], false, false, false)) .
            
            $PHPShopGUI->setField('��������� ������', $PHPShopGUI->setText($code_value[$option['export_code']])) .
            $PHPShopGUI->setField('��� �����', $PHPShopGUI->setText($extension_value[$option['export_extension']])) .
            $PHPShopGUI->setField('���� ����������', $PHPShopGUI->setText($key_value)) .
            $PHPShopGUI->setField('�������� ������������', $PHPShopGUI->setText($option['export_uniq'], false, false, false), 1, '��������� ������������ ������ ��� ��������');

    $Tab1 = $PHPShopGUI->setCollapse('���������', $Tab1);

    $name_col = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    foreach ($option['select_action'] as $k => $p) {
        if (empty($p))
            $p = '-';
        $Tab2 .= $PHPShopGUI->setField('������� ' . $name_col[$k], $PHPShopGUI->setText($p));
    }

    $Tab2 = $PHPShopGUI->setCollapse('������������� �����', $Tab2);

    // ����� ����� ��������
    $PHPShopGUI->setTab(array("����������", $Tab1 . $Tab2, true, false, true));

    return true;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>