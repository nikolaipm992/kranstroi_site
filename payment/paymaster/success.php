<?php

if (empty($GLOBALS['SysValue']))
    exit(header("Location: /"));

// ����������� ��������� ������� �� $_GET['payment']
if (!empty($_REQUEST['payment']))
    if ($_REQUEST['payment'] == 'paymaster') {
        $order_metod = "Paymaster";
        $success_function = false; // ��������� ������� ���������� ������� ������, �������� ��� ��������� � result.php
        $my_crc = "NoN";
        $crc = "NoN";
        $inv_id = $_GET['LMI_PAYMENT_NO'];
    }
?>