<?php

/**
 * Сжатие JS/CSS файлов
 * @author PHPShop Software
 * @version 1.0
 */
if (!empty($_GET['f'])) {

    session_start();
    include('../phpshop/class/security.class.php');

    $ext = PHPShopSecurity::getExt($_GET['f']);
    if (in_array($ext, ['js', 'css']) and ! stristr($_GET['f'], 'http') and ! stristr($_GET['f'], 'config') and ! empty($_SESSION['skin'])) {
        $file = $_SERVER['DOCUMENT_ROOT'] . $_GET['f'];

        if (file_exists($file)) {

            $content = file_get_contents($file);

            // Шрифты и иконки
            $content = str_replace(['fonts/', 'images/', 'css/'], ['/phpshop/templates/' . $_SESSION['skin'] . '/fonts/', '/phpshop/templates/' . $_SESSION['skin'] . '/images/', '/phpshop/templates/' . $_SESSION['skin'] . '/css/'], $content);

            // Комментарии
            $content = preg_replace('#// .*#', '', $content);
            //$content = preg_replace('#/\*(?:[^*]*(?:\*(?!/))*)*\*/#', '', $content);
            
            // Переводы строк
            $content = preg_replace('([\r\n\t])', '', $content);

            // 2 и более пробелов
            $content = preg_replace('/ {2,}/', '', $content);

            // Кеширование 30 дней
            header("Cache-Control: max-age=2592000");

            if ($ext == 'css')
                header("Content-Type: text/css");
            elseif ($ext == 'js')
                header("Content-Type: application/javascript");
            else
                $error = true;

            echo $content;
        } else
            $error = true;
    } else
        $error = true;
} else
    $error = true;

if (!empty($error)) {
    header("HTTP/1.0 404 Not Found");
    header("Status: 404 Not Found");
}