<?php

// Файл выгрузки прайса-листа из 1С 
$c_file = '../../UserFiles/Files/price.xls';

if (is_file($c_file)) {
    header('Location: ' . $c_file);
    exit();
} else {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
}
?>