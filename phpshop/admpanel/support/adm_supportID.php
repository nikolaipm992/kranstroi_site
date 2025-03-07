<?php

$TitlePage = __('����� � ���������') . ' �' . $_GET['id'];

$licFile = PHPShopFile::searchFile('../../license/', 'getLicense', true);
@$License = parse_ini_file_true("../../license/" . $licFile, 1);

function actionStart() {
    global $PHPShopGUI, $License;

    $PHPShopGUI->addCSSFiles('./css/support.css');
    $PHPShopGUI->addJSFiles('./js/jquery.waypoints.min.js', './support/gui/support.gui.js');
    PHPShopObj::loadClass('xml');

    // ����� ���������
    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'] . '&id=' . intval($_GET['id']) . '&do=track';
    if(function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArrayTrack = readDatabase($data, 'row', false);
    } else {
        $dataArrayTrack = readDatabase($path, "row");
    }

    // ������
    $status = $dataArrayTrack[0]['status'];
    $user = $dataArrayTrack[0]['name'];

    if ($status == 3)
        $status_off = 'hide';
    else
        $status_off = null;

    $PHPShopGUI->action_button['���������'] = array(
        'name' => __('������ ���������'),
        'class' => 'btn btn-default btn-sm navbar-btn support-close ' . $status_off,
        'type' => 'button',
        'icon' => 'glyphicon glyphicon-ok',
        'tooltip' => 'data-toggle="tooltip" data-placement="bottom" title="' . __('��� ������ �����') . '"'
    );

    $PHPShopGUI->action_button['PUSH'] = array(
        'name' => __('����������� �� �����������'),
        'class' => 'btn btn-default btn-sm navbar-btn btn-action-panel-blank '. $status_off,
        'type' => 'button',
        'action'=>'https://help.phpshop.ru/ticket/?track='.$dataArrayTrack[0]['trackid'],
        'icon' => 'glyphicon glyphicon-bell',
        'tooltip' => 'data-toggle="tooltip" data-placement="left" title="' . __('��������� �� ������ � ��������� �������� �����������') . '"'
    );

    // ��� ������
    if (!is_array($dataArrayTrack)) {
        header('Location: ?path=' . $_GET['path']);
        exit();
    }
    
    // �����
    if (!empty($dataArrayTrack[0]['attachments'])) {

        $attachments = explode(",", $dataArrayTrack[0]['attachments']);

        foreach ($attachments as $f) {

            $files = explode("#", $f);
            if (!empty($f))
                if (in_array(PHPShopSecurity::getExt($files[1]), array('gif', 'png', 'jpg', 'jpeg'))) {
                    $flist .= '<div class="col-xs-6 col-md-6">
                             <a href="https://help.phpshop.ru/download_attachment.php?att_id=' . $files[0] . '&track=' . $dataArrayTrack[0]['trackid'] . '" class="thumbnail" target="_blank" title="' . $files[1] . '"><img src="https://help.phpshop.ru/download_attachment.php?att_id=' . $files[0] . '&track=' . $dataArrayTrack[0]['trackid'] . '" alt="' . $files[1] . '" ></a></div>';
                } else {
                    $flist .= '<div class="col-xs-6 col-md-6"><a title="' . $files[1] . '" target="_blank" href="https://help.phpshop.ru/download_attachment.php?att_id=' . $files[0] . '&track=' . $dataArrayTrack[0]['trackid'] . '"><span class="glyphicon glyphicon-paperclip"></span> ' . $files[1] . '</a></div>';
                }
        }
    } else
        $flist = null;

    $message = ' 
          <div class="incoming_msg">
              <div class="received_msg">
                <div class="received_withd_msg">
                   <span class="time_date">' . $dataArrayTrack[0]['dt'] . '</span>
                  <p>' . preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'set_kbd', __($dataArrayTrack[0]['message'])) . '</p>
                    <span class="time_date">' . $flist . '</span>
                 </div>
              </div>
            </div>';

    // ���������
    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'] . '&id=' . intval($_GET['id']) . '&do=id';
    if(function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        $dataArray = readDatabase($data, 'row', false);
    } else {
        $dataArray = readDatabase($path, "row");
    }

    $PHPShopGUI->setActionPanel(__("�����") . " &#8470; " . $_GET['id'] . ' / ' . __($dataArrayTrack[0]['subject']), false, array('PUSH','���������'));

    if (is_array($dataArray))
        foreach ($dataArray as $row) {

            if (empty($row['message']))
                continue;
            else
                $row['message'] = preg_replace_callback("/@([a-zA-Z0-9_]+)@/", 'set_kbd', $row['message']);

            // �����
            if (!empty($row['attachments'])) {

                $attachments = explode(",", $row['attachments']);

                foreach ($attachments as $k => $f) {

                    $files = explode("#", $f);
                    if (!empty($f)) {

                        if (in_array(PHPShopSecurity::getExt($files[1]), array('gif', 'png', 'jpg', 'jpeg'))) {
                            $flist .= '<div class="col-xs-6 col-md-6">
                             <a href="https://help.phpshop.ru/download_attachment.php?att_id=' . $files[0] . '&track=' . $dataArrayTrack[0]['trackid'] . '" class="thumbnail" target="_blank" title="' . $files[1] . '"><img src="https://help.phpshop.ru/download_attachment.php?att_id=' . $files[0] . '&track=' . $dataArrayTrack[0]['trackid'] . '" alt="' . $files[1] . '" ></a></div>';
                        } else {
                            $flist .= '<div class="col-xs-6 col-md-6"><a title="' . $files[1] . '" target="_blank" href="https://help.phpshop.ru/download_attachment.php?att_id=' . $files[0] . '&track=' . $dataArrayTrack[0]['trackid'] . '"><span class="glyphicon glyphicon-paperclip"></span> ' . $files[1] . '</a></div>';
                        }
                    }
                }
            } else
                $flist = null;


            if (empty($row['staffid'])) {
                $message .= '
             <div class="incoming_msg">
              <div class="received_msg">
                <div class="received_withd_msg">
                   <span class="time_date">' . $row['dt'] . '</span>
                    <p>' . __($row['message']) . '</p>
                    <span class="time_date"><div class="row">' . $flist . '</div></span>
                 </div>
              </div>
            </div>';
            } else {
                $message .= '
            <div class="outgoing_msg">
              <div class="sent_msg">
                <span class="time_date text-right">' . __($row['name']) . ': ' . $row['dt'] . '</span>
                <p>' . __($row['message']) . '</p>
                <span class="time_date"><div class="row">' . $flist . '</div></span>
               </div>
            </div>';
            }
        }

    $PHPShopGUI->_CODE = '

          ' . $message . '
          <form method="post" enctype="multipart/form-data" name="product_edit" id="product_edit" class="form-horizontal" role="form" data-toggle="validator">
          <a id="m"></a>
          <div class="' . $status_off . '">
          <p>
          <textarea class="form-control" name="message" id="message" placeholder="' . __('������� �����') . '"></textarea>
          </p>
          <p>
             <button class="btn btn-default pull-right send-message disabled" type="submit"><span class="glyphicon glyphicon-send"></span> ' . __('��������� ���������') . '</button>
             <a id="attachment-disp" class="text-muted" href="#f"><span class="glyphicon glyphicon-paperclip"></span> ' . __('���������� ����') . '</a>
             <a id="f"></a>
             <div id="attachment" class="hide" style="max-width:70%">
             ' . $PHPShopGUI->setIcon(null, "attachment", false, array('load' => true, 'server' => true, 'url' => false, 'multi' => false, 'view' => false)) . '
             </div>
          </p>
          </div>
          <input type="hidden" name="selectID" value="true">
          <input type="hidden" name="actionList[selectID]" value="actionReplies">
          <input type="hidden" name="name" value="' . $user . '">
         </form>
      ';


    // ����� ������ ��������� � ����� � �����
    $ContentFooter = $PHPShopGUI->setInput("hidden", "rowID", $_GET['id']);

    // �����
    $PHPShopGUI->setFooter($ContentFooter);
    return true;
}

// ������� �������� ������
function actionClose() {
    global $License;

    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'] . '&id=' . intval($_GET['id']) . '&do=close';

    if(function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($ch);
        curl_close($ch);
    } else {
        readDatabase($path, "row");
    }

    return array("success" => true);
}

// ������� ������ �� �����
function actionReplies() {
    global $License;

    $path = 'https://help.phpshop.ru/base-xml-manager/search/xml.php?s=' . $License['License']['Serial'] . '&u=' . $License['License']['DomenLocked'] . '&id=' . intval($_GET['id']) . '&do=insert&code='.$GLOBALS['PHPShopBase']->codBase;
    $ch = curl_init();

    if (!empty($_POST['attachment'])) {
        
        $fileAdd=fileAdd();
        if(!empty($fileAdd))
            $_POST['attachment'] = $fileAdd;
        
        $pathinfo = pathinfo($_POST['attachment']);
        $_POST['message'] .= '

<a href="http://' . $_SERVER['SERVER_NAME'] . $_POST['attachment'] . '" target="_blank"><span class="glyphicon glyphicon-paperclip"></span> ' . $pathinfo['basename'] . '</a>';
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    curl_setopt($ch, CURLOPT_TIMEOUT, 600);
    curl_setopt($ch, CURLOPT_URL, $path);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    $res = curl_exec($ch);
    curl_close($ch);
    //return array("success" => true);
    header('Location: ?path=support&id='.$_GET['id'].'#f');
}

function set_kbd($var) {
    return '<kbd>' . $var[0] . '</kbd>';
}

// ���������� �����
function fileAdd() {
    global $PHPShopSystem;

    // ����� ����������
    $path = $GLOBALS['SysValue']['dir']['dir'] . '/UserFiles/Image/' . $PHPShopSystem->getSerilizeParam('admoption.image_result_path');

    // �������� �� ������������
    if (!empty($_FILES['file']['name'])) {
        $_FILES['file']['ext'] = PHPShopSecurity::getExt($_FILES['file']['name']);
        $_FILES['file']['name'] = PHPShopString::toLatin(str_replace('.' . $_FILES['file']['ext'], '', PHPShopString::utf8_win1251($_FILES['file']['name']))) . '.' . $_FILES['file']['ext'];
       if (!empty($_FILES['file']['ext'])) {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'])) {
                $file = $GLOBALS['dir']['dir'] . $path . $_FILES['file']['name'];
            }
        }
        else $file='Error_PHP_ext';
    }

    if (empty($file))
        $file = '';

    return $file;
}

// ��������� �������
$PHPShopGUI->getAction();

// ����� ����� ��� ������
$PHPShopGUI->setAction($_GET['id'], 'actionStart', 'none');
?>