<?php


function index_newselement_hook($obj, $dataArray, $rout) {

    if ($rout == 'START') {
        $obj->limit = 3;
        $obj->disp_only_index=false;
    }
}

$addHandler = array
    (
    'index' => 'index_newselement_hook'
);
?>