<?php
/**
 * ���������� 4-�� ������ � ���������������
 */
function specMain_hook($obj) {
    $obj->cell=4;
}
 
 
/**
 * ��������� ����� ������� � "������ ��������"
 * @param array $obj ������
 */
function nowBuy_hook($obj) {
    $obj->cell=4;
    $obj->limitpos = 4; // ���������� ��������� �������
    $obj->limitorders = 4; // ���������� ������������� �������
    $obj->enabled=2;
}

$addHandler = array
    (
    'nowBuy' => 'nowBuy_hook',
    'specMain'=>'specMain_hook'
);
?>