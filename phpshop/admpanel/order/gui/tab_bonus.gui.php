<?php

/**
 * ������ ������� � ������
 * @param array $data ������ ������
 * @return string 
 */
function tab_bonus($data) {
    global $PHPShopGUI;

    $disp = $PHPShopGUI->setDiv('left', "<p>" . __('������� �������') . ": <span class='label label-default'>" . $data['bonus_minus'] . "</span></p>");
    $disp .= $PHPShopGUI->setDiv('left', "<p>" . __('��������� �������') . ": <span class='label label-default'>" . $data['bonus_plus'] . "</span></p>");

    return $disp;
}

?>