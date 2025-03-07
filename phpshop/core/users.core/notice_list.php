<?php

/**
 * ����� ������ ����������� ������������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopCoreFunction
 * @param obj $obj ������ ������
 */
function notice_list($obj) {
    $tr = $table = $table_archive = null;
    $PHPShopOrm = new PHPShopOrm($obj->getValue('base.notice'));

    // ������� �����������
    $PHPShopOrm->debug = $obj->debug;
    $data = $PHPShopOrm->select(array('*'), array('user_id' => '=' . $obj->UsersId, 'enabled' => "='0'"), array('order' => 'datas desc'), array('limit' => 100));

    if (is_array($data)) {
        foreach ($data as $row) {

            if (PHPShopSecurity::true_num($row['product_id'])) {
                $link = '/shop/UID_' . $row['product_id'] . '.html';
                $PHPShopProduct = new PHPShopProduct($row['product_id']);
                $td1 = PHPShopText::a($link, $PHPShopProduct->getName(), $PHPShopProduct->getName(), false, false, false, 'b');
            }

            $td2 = PHPShopDate::dataV($row['datas_start']) . ' - ' . PHPShopDate::dataV($row['datas']);
            $td3 = PHPShopText::a('./notice.html?noticeId=' . $row['id'], __('�������'));
            $tr.=$obj->tr($td1, $td2, $td3);
        }

        $title = PHPShopText::h4(__('������� ������'));
        $caption = $obj->caption(__('������������'), __('������'), __('������'));

        $table = PHPShopText::table($caption . $tr, 3, 1, 'center', '100%', false, 0, 'allspecwhite', 'list table table-striped table-bordered table-hover');
    }

    // ����� �����������
    $PHPShopOrm->clean();
    $data = $PHPShopOrm->select(array('*'), array('user_id' => '=' . $obj->UsersId, 'enabled' => "='1'"), array('order' => 'datas desc'), array('limit' => 100));
    $tr = null;
    if (is_array($data)) {
        foreach ($data as $row) {

            if (PHPShopSecurity::true_num($row['product_id'])) {
                $link = '/shop/UID_' . $row['product_id'] . '.html';
                $PHPShopProduct = new PHPShopProduct($row['product_id']);
                $td1 = PHPShopText::a($link, $PHPShopProduct->getName(), $PHPShopProduct->getName(), false, false, false, 'b');
            }

            $td2 = PHPShopDate::dataV($row['datas_start']) . ' - ' . PHPShopDate::dataV($row['datas']);
            $td3 = __('���������');
            $tr.=$obj->tr($td1, $td2, $td3);
        }

        $title = PHPShopText::h4(__('�����'));
        $caption = $obj->caption(__('������������'), __('������'), __('������'));

        $table_archive = $title . PHPShopText::table($caption . $tr, 3, 1, 'center', '100%', false, 0, 'allspecwhite', 'list table table-striped table-bordered table-hover');
    }

    if ($table . $table_archive)
        $disp = $table . $table_archive;
    else
        $disp = __("� ��� ��� �����������.");

    $obj->set('formaTitle', __('�����������'));
    $obj->set('formaContent', $disp);
    $obj->ParseTemplate($obj->getValue('templates.users_page_list'));
}

?>