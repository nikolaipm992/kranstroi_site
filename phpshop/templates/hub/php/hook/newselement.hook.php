<?php


function index_newselement_hook($obj, $dataArray, $rout) {

    if ($rout == 'START') {
        $obj->limit = 2;
        $obj->disp_only_index=true;
    }
}

/**
 * ���������� � ������ ��������� ��������������� ������� � 3 ������, ����� 3
 */
$addHandler = array
    (
    'index' => 'index_newselement_hook'
);
?>