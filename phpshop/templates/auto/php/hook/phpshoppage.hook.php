<?php

/**
 * ����� ��������� ��� ������
 */
function template_odnotip($obj, $data, $rout) {

    if($rout == 'START'){
        $obj->odnotip_setka_num = 6;
    }
}

$addHandler = array
    (
    'odnotip' => 'template_odnotip'
);
?>
