<?php

function moyskladSend($data) {
    global $_classPath;

    include_once($_classPath . 'modules/moysklad/class/MoySklad.php');
    $MoySklad = new MoySklad($data);

    // �������� ������ �� ������� ������
    if ($MoySklad->option['status'] == $_POST['statusi_new']) {
        $MoySklad->init();
    }
}

$addHandler = array(
    'actionStart' => false,
    'actionDelete' => false,
    'actionUpdate' => 'moyskladSend',
);
?>