<?php

class Request {
    private $API_URL = 'http://api.grastin.ru/api.php';

    public function post($xml)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->API_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array(
            'XMLPackage' => $xml
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = json_decode(json_encode((array) simplexml_load_string(curl_exec($ch))), 1);
        array_walk_recursive($json, 'array2iconv');

        return $json;
    }
}