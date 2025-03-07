<?php

function template_slider_hook($obj, $row, $rout) {

    if ($rout == 'END') {
        if(empty($obj->index)) {
            $obj->index = 0;
        }

        // Активный слайдер
        if ($obj->index === 0) {
            $obj->set('slideActive', 'active');
            $obj->set('slideIndicator', '<li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>', true);
        } else {
            $obj->set('slideActive', '');
            $obj->set('slideIndicator', '<li data-target="#carousel-example-generic" data-slide-to="' . $obj->index . '"></li>', true);
        }

        $obj->index++;

    }
}

$addHandler = array('index' => 'template_slider_hook');
?>
