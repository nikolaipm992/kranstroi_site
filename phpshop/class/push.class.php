<?php

/**
 * Библиотека отправки WEB PUSH
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopClass
 */
class PHPShopPush {

    // Общий акаунт
    var $API_KEY = 'AAAAYW3WqtA:APA91bH7s1xdQwl3Ge8TqU2dKKZ3Z24_jDjoqoY850sj04ruPwF32sbw4WD0tiNnVFI6Phz5_Z9XPUR4XWkIWO6cy4Yw3kTMsfsnVg4nLPQJjZMf1amFKoVI9ITNjeW8UigLj__4ciIS';
    var $API_ID = '418454612688';

    public function __construct() {
        global $PHPShopSystem;

        if ($PHPShopSystem->ifSerilizeParam('admoption.push_token'))
            $this->API_KEY = $PHPShopSystem->getSerilizeParam('admoption.push_token');
        
        if ($PHPShopSystem->ifSerilizeParam('admoption.push_id'))
            $this->API_ID = $PHPShopSystem->getSerilizeParam('admoption.push_id');

        $this->enabled = $PHPShopSystem->getSerilizeParam('admoption.push_enabled');

        $this->PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['push']);
    }
    
    public function init(){
        if ($this->enabled == 1) {
            echo '<script>var messagingSenderId = \''.$this->API_ID.'\';</script>'
            . '<script src="./js/firebase.js" data-rocketoptimized="false" data-cfasync="false"></script>'
            . '<script src="./js/push.js" data-rocketoptimized="false" data-cfasync="false"></script>';
        }
    }

    // Запись токена в базу
    public function add($token) {

        if ($this->enabled == 1) {
            $this->PHPShopOrm->insert(array('token_new' => $token));
        }
    }
    
    // Удаление токенов
    public function clean(){
         $this->PHPShopOrm->delete(array('token'=>'!=""'));
    }

    // Отправка PUSH
    public function send($push_text) {

        $data = $this->PHPShopOrm->select(array('token'), false, false, array('limit' => 15));

        if (is_array($data)) {

            foreach ($data as $row) {
                $registration_ids[] = $row['token'];
            }
        }

        $request_body = array(
            'notification' => array(
                'title' => iconv("windows-1251", "utf-8", $_SERVER['SERVER_NAME']),
                'body' => iconv("windows-1251", "utf-8", $push_text),
                'click_action' => 'https://' . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/?path=order',
            ),
            'registration_ids' => $registration_ids,
        );


        $fields = json_encode($request_body);

        $request_headers = array(
            'Content-Type: application/json',
            'Authorization: key=' . $this->API_KEY,
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }

}

?>