<?php

function uid_productlastview_hook($obj, $row, $rout) {

    if ($rout == 'END') {

        $GLOBALS['ProductLastView']->add($row['id']);

        // ������
        $GLOBALS['ProductLastView']->clean_memory();

        // ������ � ��
        $GLOBALS['ProductLastView']->add_memory();

        // ���� ������ � ����
        $GLOBALS['ProductLastView']->add_cookie();
    }
}

$addHandler = array
    (
    'UID' => 'uid_productlastview_hook'
);
?>