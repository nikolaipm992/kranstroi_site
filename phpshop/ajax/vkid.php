<?php

/**
 * VK ID
 * @package PHPShopAjaxElements
 */
session_start();

$_classPath = "../";
include($_classPath . "class/obj.class.php");
include($_classPath . "inc/elements.inc.php");
PHPShopObj::loadClass("base");
$PHPShopBase = new PHPShopBase($_classPath . "inc/config.ini", true, true);
PHPShopObj::loadClass("orm");
PHPShopObj::loadClass("system");
PHPShopObj::loadClass("nav");
PHPShopObj::loadClass("lang");
PHPShopObj::loadClass("security");
PHPShopObj::loadClass("product");

// Системные настройки
$PHPShopSystem = new PHPShopSystem();

$PHPShopNav = new PHPShopNav();

$PHPShopRecaptchaElement = new PHPShopRecaptchaElement();

$PHPShopLang = new PHPShopLang(array('locale' => $_SESSION['lang'], 'path' => 'shop'));
$service_token = $PHPShopSystem->getSerilizeParam('admoption.vk_id_apikey');

if (!empty($_GET['payload']) and !empty($service_token)) {
    $payload = json_decode($_GET['payload'], true); 
    
    $api = 'https://api.vk.com/method/auth.exchangeSilentAuthToken?v=5.131&token='.$payload['token'].'&access_token='.$service_token.'&uuid='.$payload['uuid'];

    $ch = curl_init();
    $header = [
        'Content-Type: application/json',
    ];

    curl_setopt($ch, CURLOPT_URL, $api);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result, true)['response'];
    
    if (is_array($json) and ! empty($json['email'])) {

        // Создаём нового пользователя, или авторизуем старого
        PHPShopObj::importCore('users');
        $PHPShopUsers = new PHPShopUsers();
        $PHPShopUsers->stop_redirect = true;

        $userId = $PHPShopUsers->add_user_from_order($json['email'], PHPShopString::utf8_win1251($payload['user']['first_name'].' '. $payload['user']['last_name']), $json['phone']);

        if (!empty($userId)) {
            
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
            $data = $PHPShopOrm->getOne(['*'],['id'=>'='.(int) $userId]);

            setcookie("UserLogin", trim(trim($data['login'])), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserPassword", base64_decode($data['password']), time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);
            setcookie("UserChecked", 1, time() + 60 * 60 * 24 * 30, "/", $_SERVER['SERVER_NAME'], 0);

            header('Location: https://'.$_SERVER['SERVER_NAME'].urldecode($_GET['state']));
        }
    }
}