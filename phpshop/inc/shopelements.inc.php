<?php

/**
 * ������ ����
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopDialogElement extends PHPShopElements {

    /**
     * �����������
     */
    public function __construct() {

        parent::__construct();


        // AI
        if ($this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_chat_enabled') == 1) {

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
                $fix_avatar = false;
            } else {
                //$this->PHPShopSystem->setSerilizeParam('admoption.avatar_dialog', $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_avatar_dialog'));
                $this->PHPShopSystem->setSerilizeParam('admoption.title_dialog', $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_title_dialog'));
                $fix_avatar = true;
            }
        } else {
            $fix_avatar = false;
        }

        // ������
        $icon = $this->PHPShopSystem->getSerilizeParam('admoption.avatar_dialog');
        if (empty($icon))
            $icon = '/phpshop/lib/templates/chat/avatar.png';

        $this->avatar = $icon;
        $this->avatar_bot = $this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_avatar_dialog');

        if ($fix_avatar)
            $this->set('icon_dialog', str_replace('/phpshop', 'phpshop/', $this->avatar_bot));
        else
            $this->set('icon_dialog', str_replace('/phpshop', 'phpshop/', $this->avatar));

        // ����
        $color = $this->PHPShopSystem->getSerilizeParam('admoption.color_dialog');
        if (empty($color))
            $color = '#42a5f5';
        $this->set('color_dialog', $color);
        $this->color_dialog = $color;

        // ������
        $margin = $this->PHPShopSystem->getSerilizeParam('admoption.margin_dialog');
        if (empty($margin))
            $margin = 0;
        $this->set('margin_dialog', ($margin + 10));
        $this->set('margin_button_dialog', $margin);

        // ������ PC
        $size = (int) $this->PHPShopSystem->getSerilizeParam('admoption.size_dialog');
        if (empty($size))
            $size = 56;

        // ������ ���������
        $sizem = (int) $this->PHPShopSystem->getSerilizeParam('admoption.sizem_dialog');
        if (empty($sizem))
            $sizem = 56;

        if (PHPShopString::is_mobile()) {
            $size = $sizem;
        }

        $chat_right = $size + 30;
        $this->set('right_dialog', $chat_right);
        $this->set('size_dialog', $size);

        if ($size >= 80)
            $icon_size = 3;
        else
            $icon_size = 2;

        $this->set('icon_size_dialog', $icon_size);
    }

    public function dialog() {
        $dialog = null;

        if ($this->PHPShopSystem->ifSerilizeParam("admoption.chat_dialog", 1) and $this->PHPShopNav->objNav['truepath'] != '/users/message.html') {

            if (empty($_SESSION['UsersId'])) {
                $this->set('dialogContent', 'disabled');
            }

            // ���������
            $title = $this->PHPShopSystem->getSerilizeParam('admoption.title_dialog');
            if (empty($title))
                $title = __('�����������');
            $this->set('title_dialog', $title);

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
                $this->set('status_dialog', __('��������') . ' online');
                $this->set('status_dialog_style', 'online');
                $time_off = false;
            }
            // AI
            elseif ($this->PHPShopSystem->getSerilizeParam('ai.yandexgpt_chat_enabled') == 1) {
                $this->set('status_dialog', __('���-���') . ' online');
                $this->set('status_dialog_style', 'online');
                $time_off = false;
            } else {
                $this->set('status_dialog', __('������ ����������'));
                $this->set('status_dialog_style', 'offline');
                $time_off = true;
            }

            // ����������� �������
            $dialog = ParseTemplateReturn('phpshop/lib/templates/chat/chat.tpl', true);
        }

        // ���������� � ��������� �����
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.time_off_dialog', 1) and ! empty($time_off))
            $dialog = null;

        $this->set('editor', $dialog, true);
    }

    /**
     * �������� ������ ������������
     */
    public function add_user($mail, $name, $pas, $tel) {

        if (!class_exists('PHPShopUsers'))
            PHPShopObj::importCore('users');

        if (PHPShopSecurity::true_email($mail)) {
            $PHPShopUsers = new PHPShopUsers();

            // �������� �����
            $check = $PHPShopUsers->user_check_by_email($mail);

            // ����� ������������
            if (!$check) {

                $PHPShopUsers->stop_redirect = true;
                $_SESSION['UsersId'] = $PHPShopUsers->add_user_from_order($mail, PHPShopSecurity::TotalClean($name), PHPShopSecurity::TotalClean($tel));
                $message = __('������������') . ', ' . $name . '.' . $this->messenger_button();
                $status = 1;
            }
            // �����������
            else if ($check and ! empty($pas)) {

                $_POST['login'] = $mail;
                $_POST['password'] = $pas;
                $_POST['tel'] = $tel;
                $_POST['safe_users'] = 1;
                $PHPShopUserElement = new PHPShopUserElement();
                if ($PHPShopUserElement->autorization()) {
                    $message = __('������������') . ', ' . $name . '.' . $this->messenger_button();
                    $status = 1;
                } else {
                    $message = '<form class="message_form">' . __('������ �����������, ������� ���������� ������') . ':<input type="password" name="password" class="form-control" placeholder="' . __('������') . '" required=""><button class="send-message" type="button">' . __('���������') . '</button></form>';
                    $status = 0;
                }
            }
            // ������ ������������
            else {
                $message = '<form class="message_form">' . __('������������ � ����� email ��� ����������, ������� ������') . ':<input type="password" name="password" class="form-control" placeholder="' . __('������') . '" required=""><button class="send-message" type="button">' . __('���������') . '</button></form>';
                $status = 0;
            }
        } else {
            $message = __('������ ����� ������');
        }

        $data[] = array(
            'user_id' => 0,
            'date' => time(),
            'name' => __('�������������'),
            'message' => $message,
            'staffid' => 0,
            'isview' => 1,
            'isview_user' => 1,
            'date' => false
        );

        if (!empty($_SESSION['UsersBot']))
            $UsersBot = $_SESSION['UsersBot'];
        else
            $UsersBot = null;

        $result = array('message' => $this->viewMessage($data), 'count' => 1, 'status' => $status, 'bot' => $UsersBot);
        return $result;
    }

    /**
     *  ���������� ������
     */
    public function messenger_button() {

        $messenger = null;
        if ($this->PHPShopSystem->ifSerilizeParam('admoption.telegram_enabled', 1)) {
            $bot = $this->PHPShopSystem->getSerilizeParam('admoption.telegram_bot');
            $messenger .= '<li class="messenger-button" data-url="telegram.me/' . $bot . '?start=' . $_SESSION['UsersBot'] . '">' . __('Telegram') . '</li>';
        }

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.vk_enabled', 1)) {
            $bot = $this->PHPShopSystem->getSerilizeParam('admoption.vk_bot');
            $messenger .= '<li class="messenger-button" data-url="vk.me/' . $bot . '?ref=' . $_SESSION['UsersBot'] . '">' . __('���������') . '</li>';
        }

        if (!empty($messenger))
            $messenger = __('<br>�� ������ ������� ��� �:') . '<ul class="chat_category">' . $messenger . '</ul>';

        return $messenger;
    }

    /**
     *  ��������� ������
     */
    public function answer_button($id = false) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog_answer']);
        $PHPShopOrm->debug = false;
        $answer = null;
        $data = $PHPShopOrm->select(array('*'), array('view' => "='1'", 'enabled' => "='1'"), array('order' => 'id DESC'), array('limit' => 15));
        if (is_array($data)) {
            $answer = '<ul class="chat_category">';
            foreach ($data as $row) {

                if ($id == $row['id'])
                    $active = 'active';
                else
                    $active = '';

                $answer .= '<li class="dialog-answer ' . $active . '" data-answer="' . $row['id'] . '">' . $row['name'] . '</li>';
            }
            $answer .= '<li class="dialog-answer" data-answer="0">' . __('������ ���') . '</li></ul>';
        }

        return $answer;
    }

    /**
     *  ���������
     */
    public function answer($id) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog_answer']);
        $PHPShopOrm->debug = false;

        // ���������
        $answer = $this->answer_button($id);

        $row = $PHPShopOrm->getOne(array('*'), array('view' => "='1'", 'id' => '=' . intval($id)));



        if (is_array($row)) {
            $row['message'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">$1://$2</a>$3', $row['message']);
            $data[] = array(
                'user_id' => 0,
                'date' => time(),
                'name' => __('�������������'),
                'message' => $row['message'] . $answer,
                'staffid' => 0,
                'isview' => 1,
                'isview_user' => 1,
                'date' => false
            );

            $result['message'] = $this->viewMessage($data, 'chat', false);
        } else {
            $result = $this->message(0, false, true);
        }

        $result['count'] = 1;
        $result['animation'] = $GLOBALS['animation'];

        return $result;
    }

    /**
     * ���������
     * @return string
     */
    public function message($user, $new = false, $skip_welcom = false, $path = 'chat') {


        if (empty($new))
            $where = array('bot' => '="message"', 'chat_id' => '=' . intval($user), 'isview_user' => "='1'");
        else
            $where = array('bot' => '="message"', 'chat_id' => '=' . intval($user), 'isview_user' => "='0'");

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['dialog']);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('*'), $where, array('order' => 'id'), array('limit' => 500));

        if (!is_array($data) and empty($new) and empty($user)) {

            $text_dialog = $this->PHPShopSystem->getSerilizeParam('admoption.text_dialog');

            if (!empty($text_dialog))
                $welcom = $this->PHPShopSystem->getSerilizeParam('admoption.text_dialog');
            else
                $welcom = __('������������');

            // ���������
            $answer = $this->answer_button();

            if (empty($skip_welcom))
                $data[] = array(
                    'user_id' => 0,
                    'date' => time(),
                    'name' => __('�������������'),
                    'message' => $welcom . $answer,
                    'staffid' => 0,
                    'isview' => 1,
                    'isview_user' => 1,
                    'date' => false
                );


            // �������
            if ($this->PHPShopSystem->ifSerilizeParam('admoption.tel_dialog', 1)) {
                $tel = '<span class="dialog-reg-tel"><input type="tel" name="tel" autocomplete="off" class="form-control" placeholder="' . __('�������') . '" required=""></span>';
            } else
                $tel = null;

            if (empty($answer) or ! empty($skip_welcom))
                $data[] = array(
                    'user_id' => 0,
                    'date' => time(),
                    'name' => __('�������������'),
                    'message' => __('��� ������ ������� ��������� ���������� ��� ����') . ':<form class="message_form"><span class="dialog-reg-name"><input type="text" autocomplete="off" name="name" class="form-control" placeholder="' . __('���') . '" required=""></span><span class="dialog-reg-mail"><input type="email" autocomplete="off" name="mail" class="form-control" placeholder="Email" required=""></span>' . $tel . '<div class="dialog-reg-rule"><input type="checkbox" value="on" name="rule" checked="checked"> ' . __('� ��������') . ' <a href="/page/soglasie_na_obrabotku_personalnyh_dannyh.html" target="_blank" title="' . __('�������� �� ��������� ������������ ������') . '">' . __('�� ��������� ���� ������������ ������') . '</a></div><button class="send-message" type="button">' . __('���������') . '</button></form>',
                    'staffid' => 0,
                    'isview' => 1,
                    'isview_user' => 1,
                    'date' => false
                );
        } else if (!is_array($data) and empty($new) and ! empty($_SESSION['UsersId'])) {
            $data[] = array(
                'user_id' => $_SESSION['UsersId'],
                'date' => time(),
                'name' => __('�������������'),
                'message' => __('������������') . ', ' . $_SESSION['UsersName'] . '.' . $this->messenger_button(),
                'staffid' => 0,
                'isview' => 1,
                'isview_user' => 1,
                'date' => false
            );
        }

        $result['message'] = $this->viewMessage($data, $path);

        if (!empty($result['message']) and ! empty($new)) {
            if (!empty($GLOBALS['chat_ids']) and is_array($GLOBALS['chat_ids']))
                $PHPShopOrm->update(array('isview_user_new' => 1), array('id' => ' IN (' . implode(',', $GLOBALS['chat_ids']) . ')'));
        }

        if (!empty($result['message'])) {
            $result['count'] = count($data);
        }

        $result['animation'] = $GLOBALS['animation'];

        return $result;
    }

    /**
     * ������ ���������
     */
    private function viewMessage($data, $path = 'chat', $url = true) {
        global $chat_ids, $animation;

        $message = null;

        if (is_array($data)) {
            foreach ($data as $row) {

                if (empty($row['message']) and empty($row['attachments']))
                    continue;

                if (!empty($row['id']))
                    $chat_ids[] = $row['id'];

                if (empty($row['staffid']))
                    $animation = 1;
                else
                    $animation = 0;

                // ������
                if (!empty($url))
                    $row['message'] = preg_replace("~(http|https|ftp|ftps)://(.*?)(\s|\n|[,.?!](\s|\n)|$)~", '<a href="$1://$2" target="_blank">$1://$2</a>$3', $row['message']);

                // �����
                if (!empty($row['attachments'])) {

                    if (in_array(PHPShopSecurity::getExt($row['attachments']), array('gif', 'png', 'jpg', 'jpeg'))) {
                        $flist = '
                             <a href="' . $row['attachments'] . '" class="thumbnail" target="_blank" title="' . $row['attachments'] . '"><img src="' . $row['attachments'] . '" alt="" class="img-responsive img-fluid"></a>';
                    } else {
                        $pathinfo = pathinfo($row['attachments']);
                        $flist = '<a title="" target="_blank" href="' . $row['attachments'] . '"><span class="glyphicon glyphicon-paperclip"></span> ' . $pathinfo['basename'] . '</a>';
                    }
                } else
                    $flist = null;

                // ���
                if ($path == 'chat') {

                    if (!empty($row['staffid'])) {
                        $message .= '
               <div class="chat_msg_item chat_msg_item_user" style="background: ' . $this->color_dialog . '">
                ' . nl2br($row['message']) . '
               </div>
               ';
                    } else {

                        // ����
                        if (!isset($row['date'])) {
                            $status = '<div class="status">' . PHPShopDate::get($row['time'], true) . '</div>';
                            $style_adm = null;
                        } else {
                            $status = null;
                            $style_adm = 'chat_form';
                        }

                        if (empty($row['ai']))
                            $avatar = $this->avatar;
                        else
                            $avatar = $this->avatar_bot;

                        $message .= '
                <div class="chat_msg_item chat_msg_item_admin ' . $style_adm . '">
                  <div class="chat_avatar">
                    <img src="' . $avatar . '" alt="" title="' . $row['name'] . '">
                  </div>' . nl2br($row['message']) . '
                  <div class="file">' . $flist . '</div>
                  ' . $status . '
               </div> 
               ';
                    }
                }
                // �������
                else {

                    if (!empty($row['staffid'])) {
                        $message .= '
             <div class="incoming_msg">
              <div class="received_msg">
                <div class="received_withd_msg">
                   <span class="time_date">' . PHPShopDate::get($row['time'], true) . '</span>
                    <p>' . nl2br($row['message']) . '</p>
                    <span class="time_date"><div class="row">' . $flist . '</div></span>
                 </div>
              </div>
            </div>';
                    } else {
                        $message .= '
            <div class="outgoing_msg">
              <div class="sent_msg">
                <span class="time_date text-right">' . $row['name'] . ': ' . PHPShopDate::get($row['time'], true) . '</span>
                <p>' . nl2br($row['message']) . '</p>
                <span class="time_date"><div class="row">' . $flist . '</div></span>
               </div>
            </div>';
                    }
                }
            }
        }
        return $message;
    }

}

/**
 * ������� ������� �� �������
 * @author PHPShop Software
 * @version 1.4
 * @package PHPShopElements
 */
class PHPShopBrandsElement extends PHPShopElements {

    /**
     * @var int  ���-�� �������
     */
    public $limitOnLine = 5;
    public $firstClassName = 'span-first-child';
    public $debug = false;
    // �������� ������� � �������� � ���, ��� �� �� ������ ������ �������� ��� ������������� � ������.
    private static $brands = [];
    private static $brandValues = [];

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * �����
     * @return string
     */
    function index() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $i = 0;
        foreach (self::getBrandsValues() as $v) {
            if ($i % $this->limitOnLine == 0) {
                $this->set('brandFirstClass', $this->firstClassName);
            } else {
                $this->set('brandFirstClass', '');
            }
            $i++;

            $this->set('brandIcon', null);
            foreach ($v as $val) {
                if (!empty($val['icon']))
                    $this->set('brandIcon', $val['icon']);
            }
            $this->set('brandName', $v[0]['name']);
            $this->set('brandPageLink', PHPShopBrandsElement::getBrandLink($v));
            $this->set('brandsList', ParseTemplateReturn('brands/top_brands_one.tpl'), true);

            // ��� ���������� ����
            $this->set('brandsListMobile', PHPShopText::li($this->get('brandName'), $this->get('brandPageLink'), false), true);
        }

        if ($this->get('brandsList'))
            return ParseTemplateReturn('brands/top_brands_main.tpl');
    }

    public static function getCategoryBrands($categoryId) {
        global $PHPShopShopCatalogElement;

        PHPShopParser::set('categoryBrandsList', null);

        if (is_array($PHPShopShopCatalogElement->CategoryArray)) {
            $category = $PHPShopShopCatalogElement->CategoryArray[(int) $categoryId];
        } else {
            $category = (new PHPShopCategory((int) $categoryId))->objRow;
        }

        $brands = self::getBrands();
        $categoryBrands = [];

        $sorts = unserialize($category['sort']);
        if (is_array($sorts)) {
            foreach ($sorts as $sort) {
                if (isset($brands[$sort])) {
                    $categoryBrands[] = (int) $sort;
                }
            }
        }

        foreach (self::getBrandsValues($categoryBrands) as $brandValues) {
            PHPShopParser::set('categoryBrandIcon', null);
            foreach ($brandValues as $brandValue) {
                if (!empty($brandValue['icon']))
                    PHPShopParser::set('categoryBrandIcon', $brandValue['icon']);
            }
            PHPShopParser::set('categoryBrandName', $brandValues[0]['name']);
            PHPShopParser::set('categoryBrandPageLink', self::getBrandLink($brandValues));
            PHPShopParser::set('categoryBrandsList', ParseTemplateReturn('brands/category_brands_one.tpl'), true);
        }

        if (!empty(PHPShopParser::get('categoryBrandsList'))) {
            return ParseTemplateReturn('brands/category_brands.tpl');
        }
    }

    private static function getBrands() {
        if (count(self::$brands) > 0) {
            return self::$brands;
        }

        // ����������
        $where = ['brand' => '="1"'];
        if (defined("HostID"))
            $where['brand'] .= " and servers REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['brand'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        // ������ ���� �������������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort_categories']);
        self::$brands = array_column($PHPShopOrm->getList(['*'], $where, ['order' => 'num']), null, 'id');

        return self::$brands;
    }

    public static function getBrandsValues($categories = null) {
        if (count(self::$brandValues) === 0) {
            $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['sort']);
            $PHPShopOrm->mysql_error = false;

            $brands = array_keys(self::getBrands());
            if (is_array($brands) && count($brands) > 0) {
                $result = $PHPShopOrm->query('select * from ' . $GLOBALS['SysValue']['base']['sort'] . ' where category IN (' . implode(',', $brands) . ') order by num,name');
                while ($row = mysqli_fetch_assoc($result)) {
                    self::$brandValues[$row['name']][] = ['name' => $row['name'], 'id' => $row['id'], 'category' => $row['category'], 'icon' => $row['icon'], 'seo' => $row['sort_seo_name']];
                }
            }
        }

        // �������� ��� ������ ���������
        if (is_array($categories)) {
            $result = [];
            foreach (self::$brandValues as $brandName => $brandValue) {
                foreach ($brandValue as $value) {
                    if (in_array((int) $value['category'], $categories)) {
                        $result[$brandName][] = $value;
                    }
                }
            }

            return $result;
        }


        return self::$brandValues;
    }

    public static function getBrandLink($values) {
        // ���� ������ SEOURLPRO
        if (!empty($GLOBALS['SysValue']['base']['seourlpro']['seourlpro_system'])) {
            if (is_null($GLOBALS['PHPShopSeoPro'])) {
                include_once dirname(__DIR__) . '/modules/seourlpro/inc/option.inc.php';
                $GLOBALS['PHPShopSeoPro'] = new PHPShopSeoPro();
            }
            $seourlpro = $GLOBALS['PHPShopSeoPro']->getSettings();
        }

        $link = null;
        $isSeoNameIdentical = true;
        foreach ($values as $key => $val) {
            $link .= 'v[' . $val['category'] . ']=' . $val['id'] . '&';

            if ($key > 0 && $val['seo'] !== $values[$key - 1]['seo']) {
                $isSeoNameIdentical = false;
            }
        }

        if ((int) $seourlpro['seo_brands_enabled'] === 2 && $isSeoNameIdentical) {
            return $GLOBALS['SysValue']['dir']['dir'] . '/brand/' . $values[0]['seo'] . '.html';
        }

        return $GLOBALS['SysValue']['dir']['dir'] . '/selection/?' . substr($link, 0, strlen($link) - 1);
    }

}

/**
 * ������� ������������� �������
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopSortElement extends PHPShopElements {

    /**
     * �����������
     */
    function __construct() {
        parent::__construct();
    }

    /**
     * ����� ������ �������������� ��� ������
     * @param string $var ��� ���������� � �������������
     * @param int $n �� �������������� ��� ������ ��������
     * @param string $title ��������� �����
     * @param string $target ���� ����� [/selection/  |  /selectioncat/]
     */
    function brand($var, $n, $title, $target = '/selection/') {

        // �� �������������� ��� ������ ��������
        $this->n = $n;

        // ���������� ����������
        PHPShopObj::loadClass('sort');

        $PHPShopSort = new PHPShopSort();
        $value = $PHPShopSort->value($n, $title);
        $forma = PHPShopText::p(PHPShopText::form($value . PHPShopText::button('OK', 'SortSelect.submit()'), 'SortSelect', 'get', $target, false, 'ok'));
        $this->set('leftMenuContent', $forma);
        $this->set('leftMenuName', $title);

        // ���������� ������
        $dis = $this->parseTemplate($this->getValue('templates.left_menu'));

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__, $value);

        // ��������� ���������� �������
        $this->set($var, $dis);
    }

}

/**
 * ������� ���������� ������ ������� � �������
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopElements
 */
class PHPShopProductIconElements extends PHPShopProductElements {

    /**
     * �������
     * @var bool
     */
    var $debug = false;

    /**
     * ������ �������
     * @var bool
     */
    var $memory = true;

    /**
     * ������ ������
     * @var string 
     */
    var $template = 'main_spec_forma_icon';

    /**
     * ����������� �� �����
     * @var string 
     */
    var $limitspec;

    /**
     * ����� ������ [1-5]
     * @var int 
     */
    var $cell;

    /**
     * �����������
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        $this->template_debug = true;
        parent::__construct();

        // HTML ����� �������
        $this->setHtmlOption(__CLASS__);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * ������� "���������������-�������" ��� ���� �������
     * @param bool $force �������� ����������� ��� ���������� �������� ������
     * @param int $category �� ��������� ��� �������
     * @param int $cell ����� ������ [1-5]
     * @param int $limit ����������� �� �����
     * @return string
     */
    function specMainIcon($force = false, $category = null, $cell = null, $limit = null, $line = false) {

        $this->limitspec = $limit;

        if (!empty($cell))
            $this->cell = $cell;

        elseif (empty($this->cell))
            $this->cell = 1;

        // ������� ������ 
        $this->new_enabled = $this->PHPShopSystem->getSerilizeParam("admoption.new_enabled");

        switch ($GLOBALS['SysValue']['nav']['nav']) {

            // ������ ������ �������
            case "CID":

                if (!empty($category))
                    $where['category'] = '=' . $category;

                elseif (PHPShopSecurity::true_num($this->PHPShopNav->getId())) {

                    $category = $this->PHPShopNav->getId();
                    if (!$this->memory_get('product_enabled.' . $category, true))
                        $where['category'] = '=' . $category;
                }
                break;

            // ������ ���������� ��������
            case "UID":

                if (!empty($category))
                    $where['category'] = '=' . $category;

                $where['id'] = '!=' . $this->PHPShopNav->getId();

                break;
        }

        // ��������� SeoUrlPro
        if ($GLOBALS['PHPShopNav']->objNav['name'] == 'UID') {
            $where['id'] = '!=' . $GLOBALS['PHPShopNav']->objNav['id'];
        }

        // ���-�� ������� �� ��������
        if (empty($this->limitspec))
            $this->limitspec = $this->PHPShopSystem->getParam('new_num');

        if (!$this->limitspec)
            $this->limitspec = $this->num_row;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        // ���������� ���� �������� �����
        if (empty($this->limitspec))
            return false;

        // ��������� ������� ����� ������ � �������� � �������
        $where['newtip'] = "='1'";
        $where['enabled'] = "='1'";
        $where['parent_enabled'] = "='0'";

        // �������� �� ��������� �������
        if ($limit == 1 || $this->limitspec == 1) {
            $array_pop = true;
            $limit++;
            $this->limitspec++;
        }

        // ������ ������ ������� ������� �� ���������
        //$memory_spec = $this->memory_get('product_spec.' . $category);
        // ����������
        $queryMultibase = $this->queryMultibase();
        if (!empty($queryMultibase))
            $where['enabled'] .= ' ' . $queryMultibase;
        else {
            // �������� ������ ��� ������� ���
            $where['id'] = $this->setramdom($limit);
        }

        // ������� �������
        //if ($memory_spec != 2 and $memory_spec != 3)
        $this->dataArray = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limitspec), __FUNCTION__);

        // �������� �� ��������� �������
        if (!empty($array_pop) and is_array($this->dataArray)) {
            array_pop($this->dataArray);
        }

        // ������ ������� �������, ����������� RAND ��������
        if (is_array($this->dataArray))
            $count = count($this->dataArray);
        else
            $count = 0;

        if ($count < $this->limitspec) {
            unset($where['id']);
            $this->dataArray = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limitspec), __FUNCTION__);
        }

        if (is_array($this->dataArray)) {
            $this->product_grid($this->dataArray, $this->cell, $this->template, $line);
            $this->set('specMainTitle', $this->lang('newprod'));

            // ������� � ������
            //$this->memory_set('product_spec.' . $category, 1);
        }
        // ��������������� ���� ��� �������
        elseif ($this->new_enabled == 1) {

            // ������� ���������������
            unset($where['newtip']);
            $where['spec'] = "='1'";

            //if ($memory_spec != 1 and $memory_spec != 3)
            $this->dataArray = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limitspec), __FUNCTION__);

            // �������� �� ��������� �������
            if (!empty($array_pop) and is_array($this->dataArray)) {
                array_pop($this->dataArray);
            }

            if (!empty($this->dataArray) and is_array($this->dataArray)) {
                $this->product_grid($this->dataArray, $this->cell, $this->template, $line);
                $this->set('specMainTitle', $this->lang('specprod'));

                // ������� � ������
                //$this->memory_set('product_spec.' . $category, 2);
            }
        }
        // ��������� ���������� ������ ���� ��� �������
        elseif ($this->new_enabled == 2) {

            // ������� ��������� ����������� �������
            unset($where['id']);
            unset($where['spec']);
            unset($where['newtip']);
            $this->dataArray = $this->select(array('*'), $where, array('order' => 'id DESC'), array('limit' => $this->limitspec), __FUNCTION__);

            // �������� �� ��������� �������
            if (!empty($array_pop) and is_array($this->dataArray)) {
                array_pop($this->dataArray);
            }

            if (is_array($this->dataArray)) {
                $this->product_grid($this->dataArray, $this->cell, $this->template, $line);
                $this->set('specMainTitle', $this->lang('newprod'));

                // ������� � ������
                //$this->memory_set('product_spec.' . $category, 3);
            }
        }

        // �������� � ���������� ������� � ��������
        return $this->compile();
    }

    /**
     * ������� ������� ����� ������ ������� (���������)
     * @param array $row ������ ������ �������
     * @param int $cell ����������� ����� [1|2|3|4|5]
     * @param string $template ������ ������
     * @param bool $line ������� ����������� ����� �������
     * @return string
     */
    function seamply_forma($row, $cell = false, $template = 'main_spec_forma_icon', $line = false, $mod = false) {

        // ���������� ����� ��� ������ ������
        if (empty($cell))
            $this->cell = $this->PHPShopSystem->getParam('num_vitrina');
        else
            $this->cell = $cell;

        $this->set('productInfo', $this->lang('productInfo'));

        // ��������� � ������ ������ � ��������
        $this->product_grid($row, $this->cell, $template, $line, $mod);

        // �������� � ���������� ������� � ��������
        return $this->compile();
    }

    /**
     * ����� ����� � ��������
     * @return string
     */
    function setCell($d1, $d2 = null, $d3 = null, $d4 = null, $d5 = null, $d6 = null, $d7 = null) {

        // �������� ������, ��������� � ������ ������� ������ ��� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $Arg = func_get_args();
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $Arg);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        return parent::setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
    }

    /**
     * ���� ������ �� ������� � �������
     * @return string
     */
    function compile() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook) {
            return $hook;
        }

        return parent::compile();
    }

}

/**
 * ������� ���������� ������ �������
 * @author PHPShop Software
 * @version 1.6
 * @package PHPShopElements
 */
class PHPShopProductIndexElements extends PHPShopProductElements {

    /**
     * �������
     * @var bool
     */
    var $debug = false;

    /**
     * ����� ������
     * @var int
     */
    var $cell;

    /**
     * ������ �������
     * @var bool
     */
    var $memory = true;

    /**
     * ������ ������
     * @var string 
     */
    var $template = '';
    var $check_index = false;

    /**
     * �����������
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct();

        // HTML ����� �������
        $this->setHtmlOption(__CLASS__);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * ������� "������ ��������" ��� ������� ��������
     * @return string
     */
    function nowBuy() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        // �������� ������� ������� ��������
        if ($this->PHPShopNav->index($this->check_index)) {
            $i = 1;

            if (!$this->limitpos)
                $this->limitpos = 10; // ���������� ��������� �������

            if (!$this->limitorders)
                $this->limitorders = 10; // ���������� ������������� �������
            $disp = $li = null;

            if (empty($this->enabled))
                $this->enabled = $this->PHPShopSystem->getSerilizeParam('admoption.nowbuy_enabled');

            $sort = null;

            // ���������� �����
            if (empty($this->cell))
                $this->cell = $this->PHPShopSystem->getValue('num_vitrina');

            if (!empty($this->enabled)) {

                $where['statusi'] = " !=1";

                // ����������
                if (defined("HostID"))
                    $where['servers'] = "=" . HostID;
                elseif (defined("HostMain"))
                    $where['servers'] .= '=0';

                // ��������� ������
                $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['orders']);
                $PHPShopOrm->debug = $this->debug;
                $data = $PHPShopOrm->select(array('orders'), $where, array('order' => 'id desc'), array('limit' => $this->limitorders));

                if (is_array($data)) {
                    foreach ($data as $row) {
                        $order = unserialize($row['orders']);
                        $cart = $order['Cart']['cart'];
                        if (is_array($cart))
                            foreach ($cart as $good) {
                                if ($i > $this->limitpos)
                                    break;
                                // �������� ������������ ������
                                if (!empty($good['parent']))
                                    $good['id'] = $good['parent'];

                                $sort .= ' id=' . intval($good['id']) . ' OR';
                            }
                    }
                    $sort = substr($sort, 0, strlen($sort) - 2);

                    // ���� ���� ������
                    if (!empty($sort)) {
                        $PHPShopOrm = new PHPShopOrm();
                        $PHPShopOrm->debug = $this->debug;

                        // ����������
                        $queryMultibase = $this->queryMultibase();

                        $PHPShopOrm->sql = "select * from " . $this->objBase . " where (" . $sort . ") and enabled='1' and sklad != '1' " . $queryMultibase . " LIMIT 0," . $this->limitpos;
                        $PHPShopOrm->comment = __CLASS__ . '.' . __FUNCTION__;
                        $dataArray = $PHPShopOrm->select();

                        if (is_array($dataArray)) {

                            // ������ ��������
                            if ($this->enabled == 1) {

                                // ���������� ����� ��� ������ ������
                                if (empty($this->cell))
                                    $this->cell = $this->PHPShopSystem->getParam('num_vitrina');
                                $this->set('productInfo', $this->lang('productInfo'));

                                // ��������� � ������ ������ � ��������
                                $this->product_grid($dataArray, $this->cell, $this->template);

                                // �������� � ���������� ������� � ��������
                                $disp = $this->compile();
                            }

                            // �������� ������
                            $this->setHook(__CLASS__, __FUNCTION__, $dataArray, 'END');

                            if (!empty($disp)) {
                                $this->set('now_buying', $this->lang('now_buying'));
                                return $disp;
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * ������� "���������������" �� ������� ��������
     * @return string
     */
    function specMain() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook)
            return $hook;

        // �������� ������� ������� ��������
        if ($this->PHPShopNav->index($this->check_index)) {


            // ���������� ����� ��� ������ ������
            $this->cell = $this->PHPShopSystem->getParam('num_vitrina');

            // ���-�� ������� �� ��������
            $this->limit = $this->PHPShopSystem->getParam('spec_num');

            if (!$this->limit)
                $this->limit = $this->num_row;

            // ���������� ���� �������� �����
            if ($this->limit < 1)
                return false;

            $this->set('productInfo', $this->lang('productInfo'));

            // ��������� ������� ����� ������ � ��������������� � �������
            $where['spec'] = "='1'";
            $where['enabled'] = "='1'";
            $where['parent_enabled'] = "='0'";

            // ����������
            $queryMultibase = $this->queryMultibase();
            if (!empty($queryMultibase))
                $where['enabled'] .= ' ' . $queryMultibase;
            else {
                // ��������� ������
                $where['id'] = $this->setramdom($this->limit);
            }

            // �������
            if ($this->limit > 1)
                $this->dataArray = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limit), __FUNCTION__);
            else
                $this->dataArray[] = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limit), __FUNCTION__);

            // ������ ������� ������� ���������������, ����������� RAND ��������
            if (is_array($this->dataArray))
                $count = count($this->dataArray);
            else
                $count = 0;

            if ($count < $this->limit) {
                unset($where['id']);
                $this->dataArray = $this->select(array('*'), $where, array('order' => 'RAND()'), array('limit' => $this->limit), __FUNCTION__);
            }

            // ��������� � ������ ������ � ��������
            $this->product_grid($this->dataArray, $this->cell, $this->template);

            // �������� � ���������� ������� � ��������
            return $this->compile();
        }
    }

    /**
     * ����� ����� � ��������
     * @return string
     */
    function setCell($d1, $d2 = null, $d3 = null, $d4 = null, $d5 = null, $d6 = null, $d7 = null) {

        // �������� ������, ��������� � ������ ������� ������ ��� �����������
        if ($this->memory_get(__CLASS__ . '.' . __FUNCTION__, true)) {
            $Arg = func_get_args();
            $hook = $this->setHook(__CLASS__, __FUNCTION__, $Arg);
            if ($hook) {
                return $hook;
            } else
                $this->memory_set(__CLASS__ . '.' . __FUNCTION__, 0);
        }

        return parent::setCell($d1, $d2, $d3, $d4, $d5, $d6, $d7);
    }

    /**
     * ���� ������ �� ������� � �������
     * @return string
     */
    function compile() {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__);
        if ($hook) {
            return $hook;
        }

        return parent::compile();
    }

}

/**
 * ������� ���������� ������ ��������� �������
 * @author PHPShop Software
 * @version 1.5
 * @package PHPShopElements
 */
class PHPShopShopCatalogElement extends PHPShopProductElements {

    /**
     * �������
     * @var bool
     */
    var $debug = false;

    /**
     * ������ ����� ��� ������� � ���� ��� ����������� ����. �������� �������� ��������.
     * @var array
     */
    var $cache_format = array('content');
    var $memory = true;

    /**
     * ���������� �������� ��������� [num|name]
     * @var string 
     */
    var $root_order = 'num, name';
    var $grid = true;

    /**
     * ����������� ���������� ������ ���������
     * @var bool 
     */
    var $multimenu = false;

    /**
     * �����������
     */
    function __construct() {

        parent::__construct();
        $this->objBase = $GLOBALS['SysValue']['base']['categories'];

        // HTML ����� �������
        $this->setHtmlOption(__CLASS__);
    }

    function __call($name, $arguments) {
        if ($name == __CLASS__) {
            self::__construct();
        }
    }

    /**
     * @deprecated ������������ ������ � ��������, class PHPShopNtCatalogElement.
     * ������ ������ ������������ � ��������
     * @param array $val ������ ������
     * @return string
     */
    function template_cat_table($val) {

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $val);
        if ($hook)
            return $hook;

        if ($val['tile'] == 1) {

            $this->set('catalogId', $val['id']);
            $this->set('catalogTitle', $val['name']);
            $this->set('catalogName', $val['name']);
            $this->set('catalogIcon', $val['icon']);
            $this->set('catalogContent', null);

            return ParseTemplateReturn("catalog/catalog_table_forma.tpl");
        }
    }

    /**
     * ������� ��������� � ��������
     * @return string
     */
    function leftCatalTable() {

        // ���������� ������ � Index
        if ($this->PHPShopNav->index()) {

            // �������� ������
            $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
            if ($hook)
                return $hook;

            $dis = null;

            // �� �������� ������� ��������
            $where['skin_enabled'] = "!='1' and tile='1'";

            // ����������
            if (defined("HostID"))
                $where['servers'] = " REGEXP 'i" . HostID . "i'";
            elseif (defined("HostMain"))
                $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

            $PHPShopCategoryArray = new PHPShopCategoryArray($where);
            $PHPShopCategoryArray->order = array('order' => $this->root_order);
            $categories = $PHPShopCategoryArray->getArray();

            if (is_array($categories))
                foreach ($categories as $category) {

                    $this->set('catalogId', $category['id']);
                    $this->set('catalogTitle', $category['name']);
                    $this->set('catalogName', $category['name']);
                    $this->set('catalogIcon', $this->setImage($category['icon']));
                    $this->set('catalogColor', (int) $category['color']);
                    $this->set('catalogContent', null);

                    $dis .= ParseTemplateReturn("catalog/catalog_table_forma.tpl");

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $category, 'END');
                }

            return $dis;
        }
    }

    // ���������� ������������ ������ ���������
    function treegenerator($array) {
        $tree_select = $check = false;

        if (is_array($array) and is_array($array['sub'])) {
            foreach ($array['sub'] as $k => $v) {

                if ($this->multimenu and $this->tree_array[$k]['vid'] != 1)
                    $check = $this->treegenerator($this->tree_array[$k]);
                else
                    $check = false;

                $this->set('catalogName', $v);
                $this->set('catalogUid', $k);
                $this->set('catalogId', $k);

                // ������
                if (empty($this->CategoryArray[$k]['icon']))
                    $this->CategoryArray[$k]['icon'] = $this->no_photo;
                $this->set('catalogIcon', $this->CategoryArray[$k]['icon']);

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $this->CategoryArray[$k]);

                if (empty($check)) {
                    $tree_select .= $this->parseTemplate($this->getValue('templates.podcatalog_forma'));
                } else {
                    $this->set('catalogPodcatalog', $check);

                    $tree_select .= $this->parseTemplate($this->getValue('templates.catalog_forma'));
                }
            }
        }
        return $tree_select;
    }

    /**
     * ����� ��������� ���������
     * @param array $replace ������ ������ ������
     * @param array $where ������ ���������� �������, ������������ ��� ������ ������������� ��������
     * PHPShopShopCatalogElement::leftCatal(false,$where['id']=1);
     * @return string
     */
    function leftCatal($replace = null, $where = null) {

        $this->set('thisCat', $this->PHPShopNav->getId());


        // ����� ������������ ������
        if ($this->getValue('sys.multimenu') == 'true')
            $this->multimenu = true;
        else
            $this->multimenu = false;

        $tree_select = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $where, 'START');
        if ($hook)
            return $hook;

        // �� �������� ������� ��������
        $where['skin_enabled'] = "!='1' and (vid !='1' or parent_to =0)";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';


        $PHPShopCategoryArray = new PHPShopCategoryArray($where);
        $PHPShopCategoryArray->order = array('order' => $this->root_order);

        $this->CategoryArray = $PHPShopCategoryArray->getArray();
        $CategoryArrayKey = $PHPShopCategoryArray->getKey('parent_to.id', true);

        if (is_array($CategoryArrayKey))
            foreach ($CategoryArrayKey as $k => $v) {
                foreach ($v as $cat) {
                    $this->tree_array[$k]['sub'][$cat] = $this->CategoryArray[$cat]['name'];

                    // ��� ��������
                    if (strstr($this->CategoryArray[$cat]['dop_cat'], "#")) {

                        $dop_cat_array = explode("#", $this->CategoryArray[$cat]['dop_cat']);

                        if (is_array($dop_cat_array)) {
                            foreach ($dop_cat_array as $vc) {
                                $this->tree_array[$vc]['sub'][$cat] = $this->CategoryArray[$cat]['name'];
                            }
                        }
                    }

                    // �������� ������
                    $this->setHook(__CLASS__, __FUNCTION__, $this->CategoryArray[$cat], 'MIDDLE');
                }

                if (!empty($this->CategoryArray[$k]['name']))
                    $this->tree_array[$k]['name'] = $this->CategoryArray[$k]['name'];

                $this->tree_array[$k]['id'] = $k;

                if (!empty($this->CategoryArray[$k]['icon']))
                    $this->tree_array[$k]['icon'] = $this->CategoryArray[$k]['icon'];

                $this->tree_array[$k]['vid'] = $this->CategoryArray[$k]['vid'];

                if (!empty($this->CategoryArray[$k]['tile']))
                    $this->tree_array[$k]['tile'] = $this->CategoryArray[$k]['tile'];
            }


        if (is_array($this->tree_array[0]['sub'])) {

            // ������� ������������ � ������������ ��� ������ ���� ���� ��������
            if (defined("HostID") and count($this->tree_array[0]['sub']) == 1) {
                $parent = array_keys($this->tree_array[0]['sub']);
                if (is_array($this->tree_array[$parent[0]]['sub'])) {
                    foreach ($this->tree_array[$parent[0]]['sub'] as $k => $v) {
                        $this->tree_array_host[0]['sub'][$k] = $this->CategoryArray[$k]['name'];
                    }

                    $this->tree_array[0] = $this->tree_array_host[0];
                }
            }


            foreach ($this->tree_array[0]['sub'] as $k => $v) {

                if (is_array($this->tree_array) and $this->tree_array[$k]['vid'] != 1)
                    $check = $this->treegenerator($this->tree_array[$k]);

                $this->set('catalogName', $v);
                $this->set('catalogUid', $k);
                $this->set('catalogId', $k);

                // ������
                if (empty($this->CategoryArray[$k]['icon']))
                    $this->CategoryArray[$k]['icon'] = $this->no_photo;
                $this->set('catalogIcon', $this->CategoryArray[$k]['icon']);

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $this->CategoryArray[$k], 'END');

                if (empty($check) or $this->tree_array[$k]['vid'] == 1)
                    $tree_select .= $this->parseTemplate($this->getValue('templates.catalog_forma_3'));
                else {
                    $this->set('catalogPodcatalog', $check);
                    $tree_select .= $this->parseTemplate($this->getValue('templates.catalog_forma'));
                }
            }
        }

        // ������ ������
        if (is_array($replace)) {
            foreach ($replace as $key => $val)
                $tree_select = str_replace($key, $val, $tree_select);
        }

        return $tree_select;
    }

    /**
     * �������� ������������
     * @param Int $id �� ��������
     * @return bool
     */
    function chek($n) {
        if (!is_array($this->tree_array[$n]['sub']))
            return true;
    }

    /**
     * ����� ��������� � �������� �������������� ����
     * @return string
     */
    function topcatMenu() {
        $dis = null;

        // �������� ������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, null, 'START');
        if ($hook)
            return $hook;

        $where['skin_enabled'] = "!='1'";
        $where['menu'] = "='1'";

        // ����������
        if (defined("HostID"))
            $where['servers'] = " REGEXP 'i" . HostID . "i'";
        elseif (defined("HostMain"))
            $where['skin_enabled'] .= ' and (servers ="" or servers REGEXP "i1000i")';

        $PHPShopOrm = new PHPShopOrm($this->objBase);
        $PHPShopOrm->debug = false;
        $data = $PHPShopOrm->select(array('id', 'name'), $where, array('order' => 'num,name'), array("limit" => 20));
        if (is_array($data))
            foreach ($data as $row) {

                // �������� ������
                $this->setHook(__CLASS__, __FUNCTION__, $row, 'MIDDLE');

                // ��������� ������
                if (PHPShopParser::checkFile($this->getValue('templates.catalog_top_menu'))) {

                    $this->set('catalogName', $row['name']);
                    $this->set('catalogUid', $row['id']);
                    $this->set('catalogIcon', $row['icon']);

                    $dis .= $this->parseTemplate($this->getValue('templates.catalog_top_menu'));
                }
                // ���������� ������ ����
                else {

                    // ���������� ����������
                    $this->set('topMenuName', $row['name']);
                    $this->set('topMenuLink', $row['id']);

                    $dis .= str_replace('page/', 'shop/CID_', $this->parseTemplate($this->getValue('templates.top_menu')));
                }
            }

        return $dis;
    }

}

/**
 * ������� ������� �������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopElements
 */
class PHPShopCartElement extends PHPShopElements {

    /**
     * �����������
     * @param bool $order ����� ������� � ������
     */
    function __construct($order = false) {

        PHPShopObj::loadClass('cart');
        $this->PHPShopCart = new PHPShopCart();
        $this->order = $order;

        parent::__construct();
    }

    /**
     *  ���� �������
     */
    function miniCart() {

        if (!empty($_SESSION['compare']))
            $compare = $_SESSION['compare'];
        else
            $compare = array();
        $numcompare = 0;

        // ���� ���� ���������
        if (count($compare) > 0) {
            if (is_array($compare)) {
                $numcompare = count($compare);
            }
            $this->set('compareEnabled', 'block');
        } else {
            $numcompare = "0";
            $this->set('compareEnabled', 'none');
        }

        // ���������
        $this->set('numcompare', $numcompare);

        // ���� ����� �� � �������� ��������� ������
        if ($this->PHPShopNav->notPath(array('order', 'done')) or ! empty($this->order)) {

            // ���� ���� ������ � �������
            if ($this->PHPShopCart->getNum() > 0) {
                $this->set('orderEnabled', 'block');

                // ���������� ������ ���� ��������� ��� �������� ������� ��� ������ �� ����
                $this->setValue("cache.last_modified", false);
            } else
                $this->set('orderEnabled', 'none');

            // �����������
            $this->set('tovarNow', $this->getValue('lang.cart_tovar_now'));
            $this->set('summaNow', $this->getValue('cart_summa_now'));
            $this->set('orderNow', $this->getValue('cart_order_now'));

            // �������
            $this->set('num', $this->PHPShopCart->getNum());

            // �����
            $this->set('sum', $this->PHPShopCart->getSum(true, ' '));
        } else {
            $this->set('productValutaName', $this->PHPShopSystem->getDefaultValutaCode(false));
            // �������
            $this->set('num', 0);
            // �����
            $this->set('sum', 0);
        }

        // �������� ������
        $this->setHook(__CLASS__, __FUNCTION__);
    }

}

/**
 * ������� ����� ������
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopElements
 */
class PHPShopCurrencyElement extends PHPShopElements {

    /**
     * �����������
     */
    function __construct() {
        global $PHPShopValutaArray;
        parent::__construct();
        $this->PHPShopValuta = $PHPShopValutaArray->getArray();
        $this->setAction(array('post' => 'valuta'));
    }

    /**
     * ��������������� ����� ����� ������
     */
    function valuta() {
        $currency = intval($_POST['valuta']);
        if (!empty($this->PHPShopValuta[$currency])) {
            $_SESSION['valuta'] = $currency;
            header("Location: " . $_SERVER['REQUEST_URI']);
        }
    }

    /**
     * ����� ������ ������
     * @return string
     */
    function valutaDisp() {

        if ($this->PHPShopNav->notPath('order')) {

            if (isset($_SESSION['valuta']))
                $valuta = $_SESSION['valuta'];
            else
                $valuta = $this->PHPShopSystem->getParam('dengi');

            if (is_array($this->PHPShopValuta))
                foreach ($this->PHPShopValuta as $v) {
                    if ($valuta == $v['id'])
                        $sel = "selected";
                    else
                        $sel = false;
                    $value[] = array($v['name'], $v['id'], $sel);
                }

            // ���������� ����������
            $this->set('leftMenuName', '������');
            $select = PHPShopText::select('valuta', $value, 100, "none", false, "ChangeValuta()");
            $this->set('leftMenuContent', PHPShopText::form($select, 'ValutaForm'));

            // �������� ������
            $this->setHook(__CLASS__, __FUNCTION__, $this->PHPShopValuta);

            // ���������� ������
            $dis = $this->parseTemplate($this->getValue('templates.valuta_forma'));
            return $dis;
        }
    }

}

/**
 * ������� ������ �����
 * @author PHPShop Software
 * @version 1.3
 * @package PHPShopElements
 */
class PHPShopCloudElement extends PHPShopElements {

    var $debug = false;

    /**
     * ����� ������� ��� �������
     * @var int
     */
    var $page_limit = 100;

    /**
     * ����� ���� ��� ������
     * @var int
     */
    var $word_limit = 20;

    /**
     * ���� ������ ������ �����
     * @var string
     */
    var $color = "0x518EAD";

    /**
     * �����������
     */
    function __construct() {
        $this->objBase = $GLOBALS['SysValue']['base']['products'];
        parent::__construct();
    }

    /**
     * ������ �����
     * @param array $row ������ ������
     * @return string
     */
    function index($row = null) {
        $disp = $dis = $CloudCount = $ArrayWords = $CloudCountLimit = null;
        $ArrayLinks = array();

        // �������� ������ � ������ �������
        $hook = $this->setHook(__CLASS__, __FUNCTION__, $row, 'START');
        if ($hook)
            return $hook;

        if ($this->PHPShopSystem->ifSerilizeParam('admoption.cloud_enabled')) {
            switch ($GLOBALS['SysValue']['nav']['nav']) {

                case(""):
                    $tip = "search";
                    $str = array('enabled' => "='1'", 'keywords' => " !=''");
                    break;

                case("CID"):
                    $tip = "words";
                    if (empty($row))
                        return false;
                    else
                        $data = $row;
                    break;

                case("UID"):
                    $tip = "words";
                    if (empty($row))
                        return false;
                    else
                        $data[] = $row;
                    break;

                default:
                    $tip = "search";
                    $str = array('enabled' => "='1'", 'keywords' => " !=''");
                    break;
            }

            if (empty($row))
                $data = $this->PHPShopOrm->select(array('keywords', 'id'), $str, false, array("limit" => $this->page_limit), __CLASS__, __FUNCTION__);

            if (is_array($data))
                foreach ($data as $row) {
                    $explode = explode(", ", $row['keywords']);
                    foreach ($explode as $ev)
                        if (!empty($ev)) {
                            $ArrayWords[] = $ev;
                            $ArrayLinks[$ev] = $row['id'];
                        }
                }
            if (is_array($ArrayWords))
                foreach ($ArrayWords as $k => $v) {
                    $count = array_keys($ArrayWords, $v);
                    $CloudCount[$v]['size'] = count($count);
                }

            // ������� ������ ��������
            $i = 0;
            if (is_array($CloudCount))
                foreach ($CloudCount as $k => $v) {
                    if ($i < $this->word_limit)
                        $CloudCountLimit[$k] = $v;
                    $i++;
                }


            //!!!!!! ������ ������, ���� ����� ����� � ���� �����, ��� ���� � ���������� �������!!!!
            $tip = "words";

            if (is_array($CloudCountLimit))
                foreach ($CloudCountLimit as $key => $val) {

                    // ������ ����
                    $key = str_replace('"', '', $key);
                    $key = str_replace("'", '', $key);
                    if ($tip == "words")
                        $disp .= '<div><a href="/search/?words=' . urlencode($key) . '">' . $key . '</a></div>';
                    else
                        $disp .= "<a href='/search/?words=" . urlencode($key) . "' style='font-size:12pt;'>$key</a>";
                }

            // ������ ����
            $disp = str_replace('\n', '', $disp);

            if ($tip == "search" and ! empty($disp))
                $disp = '
<div id="wpcumuluscontent">�������� ����...</div><script type="text/javascript">
var dd=new Date();
var spath = "' . $this->get('dir.dir') . 'phpshop/lib/templates";
var so = new SWFObject(spath+"/tagcloud/tagcloud.swf?rnd="+dd.getTime(), "tagcloudflash", "180", "180", "9", "' . $this->color . '");
so.addParam("wmode", "transparent");
so.addParam("allowScriptAccess", "always");
so.addVariable("tcolor", "' . $this->color . '");
so.addVariable("tspeed", "150");
so.addVariable("distr", "true");
so.addVariable("mode", "tags");
so.addVariable("tagcloud", "<tags>' . $disp . '</tags>");
so.write("wpcumuluscontent");</script>
';

            // ������ ����������
            $disp = str_replace('\n', '', $disp);
            $disp = str_replace(chr(13), '', $disp);
            $disp = str_replace(chr(10), '', $disp);

            // ���������� ����������
            if (!empty($disp)) {
                $this->set('leftMenuName', __("������ �����"));
                $this->set('leftMenuContent', '<div class="product-tags">' . $disp . '</div>');

                // �������� ������ � ����� �������
                $this->setHook(__CLASS__, __FUNCTION__, $disp, 'END');

                // ���������� ������
                $dis .= $this->parseTemplate($this->getValue('templates.left_menu'));
            }
            return $dis;
        }
    }

}

?>