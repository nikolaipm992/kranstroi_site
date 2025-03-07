<?php

/**
 * ���������� Dialog Bot
 * @author PHPShop Software
 * @version 1.7
 * @package PHPShopClass
 */
class PHPShopBot {

    protected $bot = 'message';
    public $protocol = 'http://';

    /**
     * �����������
     */
    public function __construct() {

        $this->PHPShopSystem = new PHPShopSystem();

        $this->PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);

        // ���� ���������� ��������
        $this->image_dialog_path = $this->PHPShopSystem->getSerilizeParam('admoption.image_dialog_path');

        if (!empty($_SERVER['HTTPS']) && 'off' !== strtolower($_SERVER['HTTPS'])) {
            $this->protocol = 'https://';
        }
    }

    //  ����� ������ �� �����
    private function search($text) {

        $YandexSearch = new YandexSearch();
        $site = $_SERVER['SERVER_NAME'];
        //$site = 'myphpshop.ru';
        $result = $YandexSearch->search($text . ' site:' . $site);
        return PHPShopString::utf8_win1251($result[0]['title']) . ' - ' . $result[0]['url'] . ', ������ � ����� ������ �� ' . $result[0]['url'];
    }
    
    //  ����� ����������� �� �����
    private function search_img($text) {

        $YandexSearch = new YandexSearch();
        $site = $_SERVER['SERVER_NAME'];
        //$site = 'myphpshop.ru';
        $result = $YandexSearch->search_img($text, false, false, false, false, $site);
        return $result;
    }

    // AI
    public function ai($message) {

        // ����� ������
        $time = (int) date("H", time());
        $time_from = (int) $this->PHPShopSystem->getSerilizeParam('admoption.time_from_dialog');
        $time_until = (int) $this->PHPShopSystem->getSerilizeParam('admoption.time_until_dialog');
        $day_work = (int) $this->PHPShopSystem->getSerilizeParam('admoption.day_dialog');
        $day = date("D", time());

        $day_work_array[1] = array('Sunday', 'Saturday');
        $day_work_array[2] = array('Saturday');
        $day_work_array[3] = array();

        if (($time_from <= $time and $time < $time_until) and ! in_array($day, $day_work_array[$day_work])) {
            return false;
        }
        // AI
        elseif ($this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_chat_enabled') == 1) {
            
            PHPShopObj::loadClass("yandexcloud");
            include('./phpshop/lib/parsedown/Parsedown.php');

            $YandexGPT = new YandexGPT();
            $system = $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_chat_role');

            $search = $this->search($message['text']);

            $result = $YandexGPT->text(PHPShopString::utf8_win1251(strip_tags($message['text'])), $system . $search, 0.3, 200);
            $text = $YandexGPT->html($result['result']['alternatives'][0]['message']['text']);
            
            $search_img = $this->search_img($text);
            if(!empty($search_img[0]['url']))
                $attachments = $search_img[0]['url'];
            else $attachments=null;
            
            $insert = array(
                'user_id' => $message['user_id'],
                'chat' => array
                    (
                    'id' => $message['chat']['id'],
                    'first_name' => __('���-���'),
                    'last_name' => "",
                ),
                'date' => time(),
                'text' => $text,
                'staffid' => 0,
                'attachments' => $attachments,
                'bot' => 'message',
                'isview' => 0,
                'isview_user' => 0,
                'ai' => 1
            );

            $this->dialog($insert);
        }
    }

    public function dialog($message) {

        if (empty($message['attachments']))
            $message['attachments'] = null;

        if (empty($message['order_id']))
            $message['order_id'] = null;

        // ��������
        if (!empty($message['photo']) and is_array($message['photo'])) {
            $file = $message['photo'][count($message['photo']) - 1]['file_id'];

            if (empty($message['caption']))
                $message['caption'] = "��������";

            $message['text'] = $message['caption'];
            $message['attachments'] = $this->file($file);
        }

        // ����
        else if (!empty($message['document'])) {
            $file = $message['document']['file_id'];

            if (empty($message['caption']))
                $message['caption'] = "����";

            $message['text'] = $message['caption'];
            $message['attachments'] = $this->file($file);
        }

        $insert = array(
            'user_id' => $message['user_id'],
            'name' => $message['chat']['first_name'],
            'message' => PHPShopString::utf8_win1251(strip_tags($message['text'])),
            'chat_id' => $message['chat']['id'],
            'time' => $message['date'],
            'staffid' => $message['staffid'],
            'bot' => $this->bot,
            'attachments' => $message['attachments'],
            'isview' => $message['isview'],
            'isview_user' => $message['isview_user'],
            'order_id' => $message['order_id'],
            'ai' => $message['ai']
        );

        $this->PHPShopOrm->insert($insert, '');
    }

    public function downloadFile($url, $path) {

        $newfname = $path;
        $url = iconv("windows-1251", "utf-8//IGNORE", $url);

        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $file = @fopen($url, 'rb', false, stream_context_create($arrContextOptions));
        if ($file) {
            $newf = fopen($newfname, 'wb');
            if ($newf) {
                while (!feof($file)) {
                    fwrite($newf, fread($file, 1024 * 8), 1024 * 8);
                }
            }
        }
        if ($file) {
            fclose($file);
        }
        if ($newf) {
            fclose($newf);
            return true;
        }
    }

    // �������� ��������
    public function send_image($id, $message, $file) {
        return true;
    }

    // �������� �����
    public function send_file($id, $message, $file) {
        return true;
    }

    // �������� ������
    public function send($id, $message) {
        return true;
    }

    // ����������� ��������������
    public function notice($message, $bot = false) {

        // �������� ����
        if (empty($message['isview'])) {

            $message['from']['first_name'] = PHPShopString::win_utf8($message['chat']['first_name']);
            $message['text'] = strip_tags($message['text']);
            $msg = __('��������� � �������� �� ') . $message['chat']['first_name'];

            if ($this->PHPShopSystem->ifSerilizeParam('admoption.telegram_dialog')) {
                $PHPShopBot = new PHPShopTelegramBot();
                $PHPShopBot->notice_telegram($message, $bot);
            }

            if ($this->PHPShopSystem->ifSerilizeParam('admoption.vk_dialog')) {
                $PHPShopBot = new PHPShopVKBot();
                $PHPShopBot->notice_vk($message, $bot);
            }

            if ($this->PHPShopSystem->ifSerilizeParam('admoption.push_dialog')) {
                PHPShopObj::loadClass(array("push"));
                $PHPShopPush = new PHPShopPush();
                $PHPShopPush->send($msg);
            }

            if ($this->PHPShopSystem->ifSerilizeParam('admoption.mail_dialog', 1)) {
                PHPShopObj::loadClass(array("parser", "mail"));
                $adminmail = $this->PHPShopSystem->getEmail();
                if (empty($GLOBALS['_classPath']))
                    $GLOBALS['_classPath'] = '../phpshop/';
                $GLOBALS['PHPShopSystem'] = $this->PHPShopSystem;
                $PHPShopMail = new PHPShopMail($adminmail, $adminmail, $msg, '', true, true);
                $link = '<br><a href="' . $this->protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/admin.php?path=dialog&id=' . $message['user_id'] . '&bot=' . $bot . '&user=' . $message['user_id'] . '" target="_blank">' . __('��������') . '</a>';

                PHPShopParser::set('message', $msg . ': ' . PHPShopString::utf8_win1251($message['text']) . $link);
                $content = ParseTemplateReturn('./phpshop/lib/templates/order/blank.tpl', true);
                $PHPShopMail->sendMailNow($content);
            }
        }
    }

    public function find($user) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->getOne(array('chat_id'), array('user_id' => '="' . intval($user) . '"', 'bot' => '="' . $this->bot . '"'));
        return $data['chat_id'];
    }

    // �������
    public function log($data) {
        ob_start();
        print_r(unserialize($data));
        $log = ob_get_clean();
        return $log;
    }

}

/**
 * ���������� VK Bot
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopClass
 */
class PHPShopVKBot extends PHPShopBot {

    protected $bot = 'vk';
    protected $version = '5.199';

    /**
     * �����������
     */
    public function __construct() {

        $this->PHPShopSystem = new PHPShopSystem();

        // Chat
        $this->confirmation = $this->PHPShopSystem->getSerilizeParam('admoption.vk_confirmation');
        $this->secret = $this->PHPShopSystem->getSerilizeParam('admoption.vk_secret');
        $this->token = $this->PHPShopSystem->getSerilizeParam('admoption.vk_token');
        $this->enabled = $this->PHPShopSystem->getSerilizeParam('admoption.vk_enabled');
        $this->vk_admin = $this->PHPShopSystem->getSerilizeParam('admoption.vk_admin');

        if ($this->token == '')
            $this->enabled = 0;

        // ID
        $this->id_token = $this->PHPShopSystem->getSerilizeParam('admoption.vk_id_token');

        // Reviews
        $this->reviews_confirmation = $this->PHPShopSystem->getSerilizeParam('admoption.vk_reviews_confirmation');
        $this->reviews_secret = $this->PHPShopSystem->getSerilizeParam('admoption.vk_reviews_secret');
        $this->reviews_enabled = $this->PHPShopSystem->getSerilizeParam('admoption.vk_reviews_enabled');
        $this->reviews_token = $this->PHPShopSystem->getSerilizeParam('admoption.vk_reviews_token');

        if ($this->reviews_token == '')
            $this->reviews_enabled = 0;

        $this->PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
    }

    private function logtest($data) {

        ob_start();
        print_r($data);
        $log = ob_get_clean();

        $file = './log.txt';

        $content = '
==== ' . date('d-m-y H:i:s') . '=====
' . $log;

        $fp = fopen($file, "a+");
        if ($fp) {
            fputs($fp, $content);
            fclose($fp);
        }
    }

    public function add_reviews($chat) {
        $this->token = $this->reviews_token;
        $insert['name_new'] = PHPShopString::utf8_win1251($this->user($chat['object']['from_id']));
        $insert['otsiv_new'] = nl2br(PHPShopString::utf8_win1251(strip_tags($chat['object']['text'])));
        $insert['tema_new'] = __('����� �� ') . $insert['name_new'];
        $insert['datas_new'] = $chat['object']['date'];
        $insert['flag_new'] = 1;

        if (is_array($chat['object']['attachments']))
            foreach ($chat['object']['attachments'] as $message) {

                // ��������
                if (is_array($message['photo'])) {

                    $file = $message['photo']['orig_photo']['url'];
                    $image = '/UserFiles/Image/' . pathinfo(parse_url($file)['path'])['basename'];
                    $alt = strip_tags($message['photo']['text']);

                    // �������� ��������
                    if ($this->downloadFile($file, $_SERVER['DOCUMENT_ROOT'] . $image)) {
                        $insert['otsiv_new'] .= '<p><img src="' . $image . '" referrerpolicy="no-referrer" alt="' . $alt . '" class="img-responsive img-fluid"></p>';
                    }
                }

                // �����
                if (is_array($message['video'])) {

                    // �����
                    if (!empty($this->id_token)) {
                        $video = $this->video($message['video']['owner_id'], $message['video']['id']);
                        $insert['otsiv_new'] .= '<p class="embed-responsive embed-responsive-16by9"><iframe class="embed-responsive-item" src="' . $video . '"></iframe></p>';
                    }

                    // ��������
                    if (empty($video)) {

                        $file = $message['video']['photo_800'];
                        $image = '/UserFiles/Image/' . pathinfo(parse_url($file)['path'])['basename'];

                        // �������� ��������
                        if ($this->downloadFile($file, $_SERVER['DOCUMENT_ROOT'] . $image)) {
                            $insert['otsiv_new'] .= '<p><img src="' . $image . '" referrerpolicy="no-referrer" alt="" class="img-responsive img-fluid"></p>';
                        }
                    }
                }
            }

        // ������ � ����
        (new PHPShopOrm($GLOBALS['SysValue']['base']['gbook']))->insert($insert);
    }

    // ����� ����
    public function video($owner_id, $id) {

        $this->token = $this->id_token;
        $data = array(
            'videos' => $owner_id . '_' . $id,
            'v' => $this->version,
        );

        $out = $this->request('video.get', $data);
        return $out['response']['items'][0]['player'];
    }

    public function user($id) {

        $data = array(
            'user_ids' => $id,
            'v' => $this->version,
        );
        $out = $this->request('users.get', $data);

        $user_name = $out['response'][0]['first_name'] . ' ' . $out['response'][0]['last_name'];
        return $user_name;
    }

    public function init($message) {

        if ($this->enabled == 1) {

            $type = $message['type'];
            $chatId = $message['object']['user_id'];

            if ($type == 'message_new') {

                $token = $message['object']['ref'];

                // ����� ���
                if ($token) {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
                    $PHPShopOrm->debug = false;
                    $data = $PHPShopOrm->getOne(array('id'), array('bot' => '="' . $token . '"'));
                    $user = $data['id'];

                    if ($user !== null) {

                        $insert = array(
                            'user_id' => $user,
                            'chat' => array
                                (
                                'id' => $chatId,
                                'first_name' => "�������������",
                                'last_name' => "",
                            ),
                            'date' => time(),
                            'staffid' => 0,
                            'isview' => 1,
                            'isview_user' => 0,
                            'text' => '������������, ' . PHPShopString::utf8_win1251($this->user($chatId))
                        );
                        $this->dialog($insert);
                        $this->send($chatId, PHPShopString::win_utf8($insert['text']));
                    }
                }
                // �������� �� ����� ������
                elseif (strpos($message['object']['body'], '/chatid') !== false) {
                    $this->send($chatId, $chatId);
                }

                // ����������� ����
                else {
                    $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
                    $PHPShopOrm->debug = false;
                    $data = $PHPShopOrm->getOne(array('*'), array('chat_id' => '="' . intval($chatId) . '"'));
                    $chat_id = $data['chat_id'];
                    $message['user_id'] = $data['user_id'];
                    $message['staffid'] = 1;
                    $message['isview'] = 0;
                    $message['isview_user'] = 1;
                    $message['chat']['first_name'] = $this->user($chatId);
                    $message['date'] = time();
                    $message['chat']['id'] = $chatId;
                    $message['text'] = $message['object']['body'];

                    if (!empty($chat_id)) {
                        $this->dialog($message);
                        $this->notice($message, $this->bot);
                    }
                }
            }
        }
    }

    public function dialog($message) {

        // ��������
        if (is_array($message['object']['attachments'][0]['photo'])) {
            $file = $message['object']['attachments'][0]['photo']['photo_604'];

            if (empty($message['object']['body']))
                $message['object']['body'] = "��������";

            $message['text'] = $message['object']['body'];
            $message['attachments'] = $file;
        }

        // ����
        elseif (is_array($message['object']['attachments'][0]['doc'])) {
            $file = $message['object']['attachments'][0]['doc']['url'];

            if (empty($message['object']['body']))
                $message['object']['body'] = $message['object']['attachments'][0]['doc']['title'];

            $message['text'] = $message['object']['body'];
            $message['attachments'] = $file;
        }

        $insert = array(
            'user_id' => $message['user_id'],
            'name' => PHPShopString::utf8_win1251($message['chat']['first_name']),
            'message' => PHPShopString::utf8_win1251(strip_tags($message['text'])),
            'chat_id' => $message['chat']['id'],
            'time' => $message['date'],
            'staffid' => $message['staffid'],
            'bot' => $this->bot,
            'attachments' => $message['attachments'],
            'isview' => $message['isview'],
            'isview_user' => $message['isview_user'],
            'order_id' => $message['order_id']
        );

        $this->PHPShopOrm->insert($insert, '');
    }

    // �������� �����
    public function send_file($id, $message, $file) {

        $uploadServer = $this->request('docs.getMessagesUploadServer', array('type' => 'doc', 'peer_id' => $id));
        $upload_url = $uploadServer['response']['upload_url'];

        $data = array(
            'file' => new CURLfile($_SERVER['DOCUMENT_ROOT'] . $file)
        );

        // �������� �������� ����� � VK
        $upload_array = $this->request(null, $data, $upload_url);

        $upload_result = $this->request('docs.save', $upload_array);

        if (is_array($upload_result['response'])) {
            $doc = $upload_result['response'];
            $attachments = 'doc' . $doc['owner_id'] . '_' . $doc['id'];
        } else
            $message .= ' https://' . $_SERVER['SERVER_NAME'] . $file;

        $data = array(
            'peer_id' => $id,
            'message' => $message,
            'attachment' => $attachments
        );

        $out = $this->request('messages.send', $data);
        return $out;
    }

    // �������� ��������
    public function send_image($id, $message, $file) {

        $uploadServer = $this->request('photos.getMessagesUploadServer');
        $upload_url = $uploadServer['response']['upload_url'];

        $data = array(
            'photo' => new CURLfile($_SERVER['DOCUMENT_ROOT'] . $file)
        );

        $upload_array = $this->request(null, $data, $upload_url);

        $upload_result = $this->request('photos.saveMessagesPhoto', $upload_array);
        $photo = array_pop($upload_result['response']);
        $attachments = 'photo' . $photo['owner_id'] . '_' . $photo['id'];

        $data = array(
            'peer_id' => $id,
            'message' => $message,
            'attachment' => $attachments
        );

        $out = $this->request('messages.send', $data);
        return $out;
    }

    // �������� ���������
    public function send($id, $message, $keyboard = false) {

        if (strstr($id, ","))
            $chat_ids = explode(",", $id);
        else
            $chat_ids[] = $id;

        if (is_array($chat_ids))
            foreach ($chat_ids as $chat_id) {

                $data = array(
                    'peer_id' => trim($chat_id),
                    'message' => $message,
                    'keyboard' => json_encode($keyboard),
                );

                $out = $this->request('messages.send', $data);
            }
        return $out;
    }

    private function request($method, $data = array(), $path = 'https://api.vk.com/method/') {
        $curl = curl_init();
        $data['access_token'] = $this->token;
        $data['v'] = $this->version;
        curl_setopt($curl, CURLOPT_URL, $path . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $out = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $out;
    }

    // ����������� ��������������
    public function notice_vk($message, $bot = 'vk') {
        $chat_id = $this->PHPShopSystem->getSerilizeParam('admoption.vk_admin');
        $notice = $this->PHPShopSystem->getSerilizeParam('admoption.vk_dialog');

        $link = $this->protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/admin.php?path=dialog&id=' . $message['user_id'] . '&bot=' . $bot . '&user=' . $message['user_id'];

        if (empty($message['from']['last_name']))
            $message['from']['last_name'] = null;

        $buttons[][] = array(
            'action' => array(
                'type' => 'open_link',
                'link' => $link,
                'label' => $message['from']['first_name'] . ' ' . $message['from']['last_name']
            ),
        );


        if (!empty($chat_id) and ! empty($notice))
            $this->send($chat_id, PHPShopString::win_utf8('��������� � ��������: ') . $message['text'], array('buttons' => $buttons, 'one_time' => false, 'inline' => true));
    }

}

/**
 * ���������� Telegram Bot
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 */
class PHPShopTelegramBot extends PHPShopBot {

    protected $bot = 'telegram';

    /**
     * �����������
     */
    public function __construct() {

        $this->PHPShopSystem = new PHPShopSystem();
        $this->token = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_token');
        $this->enabled = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_enabled');
        if ($this->token == '')
            $this->enabled = 0;

        $this->news_enabled = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_news_enabled');
        $this->news_token = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_news_token');
        if ($this->news_token == '')
            $this->news_enabled = 0;
        $this->news_delim = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_news_delim');


        $this->PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
    }

    public function dialog($message) {

        // ��������
        if (is_array($message['photo'])) {
            $file = $message['photo'][count($message['photo']) - 1]['file_id'];

            if (empty($message['caption']))
                $message['caption'] = "��������";

            $message['text'] = $message['caption'];
            $message['attachments'] = $this->file($file);
        }

        // ����
        else if (!empty($message['document'])) {
            $file = $message['document']['file_id'];

            if (empty($message['caption']))
                $message['caption'] = "����";

            $message['text'] = $message['caption'];
            $message['attachments'] = $this->file($file);
        }

        $insert = array(
            'user_id' => $message['user_id'],
            'name' => PHPShopString::utf8_win1251($message['chat']['first_name'] . ' ' . $message['chat']['last_name']),
            'message' => PHPShopString::utf8_win1251(strip_tags($message['text'])),
            'chat_id' => $message['chat']['id'],
            'time' => $message['date'],
            'staffid' => $message['staffid'],
            'bot' => $this->bot,
            'attachments' => $message['attachments'],
            'isview' => $message['isview'],
            'isview_user' => $message['isview_user'],
            'order_id' => $message['order_id']
        );

        $this->PHPShopOrm->insert($insert, '');
    }

    // ����������� ��������������
    public function notice_telegram($message, $bot = 'telegram') {
        $chat_id = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_admin');

        $link = '(' . $this->protocol . $_SERVER['SERVER_NAME'] . '/phpshop/admpanel/admin.php?path=dialog&id=' . $message['user_id'] . '&bot=' . $bot . '&user=' . $message['user_id'] . '): ';

        if (!empty($chat_id))
            $this->send($chat_id, PHPShopString::win_utf8('��������� � �������� ��') . ' [' . $message['from']['first_name'] . ' ' . $message['from']['last_name'] . ']' . $link . $message['text']);
    }

    public function init($message) {

        if ($this->enabled == 1) {

            $text = $message['text'];

            // ����� ���
            if (strpos($text, '/start') !== false) {

                $textStrings = explode(' ', $text);

                if (isset($textStrings[1])) {
                    $token = $textStrings[1];
                    $chatId = $message['chat']['id'];

                    if ($token) {
                        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
                        $PHPShopOrm->debug = false;
                        $data = $PHPShopOrm->getOne(array('id'), array('bot' => '="' . $token . '"'));
                        $user = $data['id'];

                        if ($user !== null) {

                            $insert = array(
                                'user_id' => $user,
                                'chat' => array
                                    (
                                    'id' => $message['chat']['id'],
                                    'first_name' => "�������������",
                                    'last_name' => "",
                                ),
                                'date' => time(),
                                'staffid' => 0,
                                'isview' => 1,
                                'isview_user' => 0,
                                'text' => '������������, ' . PHPShopString::utf8_win1251($message['from']['first_name']) . ' ' . PHPShopString::utf8_win1251($message['from']['last_name'])
                            );

                            $this->dialog($insert);
                            $this->send($chatId, PHPShopString::win_utf8($insert['text']));
                        }
                    }
                }
            }
            // �������� �� ����� ������
            elseif (strpos($text, '/chatid') !== false) {
                $this->send($message['chat']['id'], $message['chat']['id']);
            }
            // ����������� ����
            elseif (!empty($message['chat']['id'])) {
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
                $PHPShopOrm->debug = false;
                $data = $PHPShopOrm->getOne(array('*'), array('chat_id' => '="' . intval($message['chat']['id']) . '"'));
                $chat_id = $data['chat_id'];
                $message['user_id'] = $data['user_id'];
                $message['staffid'] = 1;
                $message['isview'] = 0;
                $message['isview_user'] = 1;

                if (!empty($chat_id)) {
                    $this->dialog($message);
                    $this->notice($message, $this->bot);
                }
            }
        }
    }

    // �������� ���������
    public function send($id, $message) {

        if (strstr($id, ","))
            $chat_ids = explode(",", $id);
        else
            $chat_ids[] = $id;

        if (is_array($chat_ids))
            foreach ($chat_ids as $chat_id) {

                $data = array(
                    'chat_id' => trim($chat_id),
                    'text' => $message,
                    'parse_mode' => "markdown"
                );

                $out = $this->request('sendMessage', $data);
            }
        return $out;
    }

    // �������� ��������
    public function send_image($id, $message, $file) {
        $data = array(
            'chat_id' => $id,
            'caption' => $message,
            'photo' => curl_file_create($_SERVER['DOCUMENT_ROOT'] . $file)
        );

        $out = $this->request('sendPhoto', $data);
        return $out;
    }

    // �������� �����
    public function send_file($id, $message, $file) {
        $data = array(
            'chat_id' => $id,
            'caption' => $message,
            'document' => curl_file_create($_SERVER['DOCUMENT_ROOT'] . $file)
        );

        $out = $this->request('sendDocument', $data);
        return $out;
    }

    private function request($method, $data = array()) {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token . '/' . $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $out = json_decode(curl_exec($curl), true);
        curl_close($curl);
        return $out;
    }

    public function file($file_id) {
        $array = $this->request("getFile", ['file_id' => $file_id]);
        return 'https://api.telegram.org/file/bot' . $this->token . '/' . $array['result']['file_path'];
    }

    public function check_notification() {
        if ($_SERVER["PATH_INFO"] == '/' . md5($this->news_token))
            return true;
    }

    public function add_news($message) {

        $this->token = $this->news_token;

        if (empty($message['caption']))
            $message['caption'] = $message['text'];

        $PHPShopRSS = new PHPShopOrm($GLOBALS['SysValue']['base']['news']);
        $message['caption'] = PHPShopString::utf8_win1251($message['caption']);
        $insert['datas_new'] = PHPShopDate::get();
        $insert['datau_new'] = time();


        // ���������
        $title = explode(PHP_EOL, $message['caption'])[0];

        $insert['zag_new'] = $title;

        // ��������
        if (is_array($message['photo'])) {

            $big = $message['photo'][count($message['photo']) - 1]['file_id'];

            $file = $this->file($big);
            $image = '/UserFiles/Image/' . pathinfo($file)['basename'];

            // �������� ��������
            if ($this->downloadFile($file, $_SERVER['DOCUMENT_ROOT'] . $image)) {
                $insert['podrob_new'] = '<div><img src="' . $image . '" referrerpolicy="no-referrer" alt="" class="img-responsive img-fluid"></div>';
                $insert['icon_new'] = $image;
            }
        }

        // �����
        if (is_array($message['video'])) {

            $thumb = $message['video']['thumb']['file_id'];
            $video = $message['video']['file_id'];

            $video = $this->file($video);
            $mp4 = '/UserFiles/Image/' . pathinfo($video)['basename'];

            // �������� ����� �����
            if ($this->downloadFile($video, $_SERVER['DOCUMENT_ROOT'] . $mp4)) {
                $insert['podrob_new'] = '<div><video src="' . $mp4 . '" controls="controls"></video></div>';
            }

            $thumb = $this->file($thumb);
            $image = '/UserFiles/Image/' . pathinfo($thumb)['basename'];

            // �������� ��������
            if ($this->downloadFile($thumb, $_SERVER['DOCUMENT_ROOT'] . $image)) {
                $insert['icon_new'] = $image;
            }
        }

        if (!empty($this->news_delim))
            $insert['kratko_new'] = nl2br(substr($message['caption'], 0, (int) $this->news_delim) . '...');
        else
            $insert['kratko_new'] = nl2br($message['caption']);

        $insert['podrob_new'] .= nl2br($message['caption']);

        if (!empty($title))
            $PHPShopRSS->insert($insert);
    }

}
