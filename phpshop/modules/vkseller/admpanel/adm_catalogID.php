<?php

function addVksellerTab($data) {
    global $PHPShopGUI;

    // �������� �� ������� �������
    if (isset($data['skin_enabled'])) {

        // �������� �� �����������
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['categories']);
        $data_categories = $PHPShopOrm->getOne(['id'], ['parent_to' => '=' . (int) $data['id']]);
        if (is_array($data_categories))
            return false;

        $PHPShopGUI->addJSFiles('../modules/vkseller/admpanel/gui/vkseller.gui.js');
        include_once dirname(__FILE__) . '/../class/VkSeller.php';
        $VkSeller = new VkSeller();

        if ($VkSeller->model == 'API') {

            $data_vk = $VkSeller->getTree(PHPShopString::win_utf8($_POST['words']))['response']['items'];

            $tree_value[] = array(__('������ �� �������'), 0, $data['category_vkseller']);
            if (is_array($data_vk)) {
                foreach ($data_vk as $row) {
                    $tree_value[] = [PHPShopString::utf8_win1251($row['section']['name']) . ' &rarr; ' . PHPShopString::utf8_win1251($row['name']), $row['id'], $data['category_vkseller']];
                }
            }

            // ����������
            $Tab1 = $PHPShopGUI->setCollapse('���������� � ���������', $PHPShopGUI->setSelect('category_vkseller_new', $tree_value, '100%', false, false, true));

            $PHPShopGUI->addTabSeparate(array("���������", $Tab1, true));
        }
    }
}

$addHandler = array(
    'actionStart' => 'addVksellerTab',
    'actionDelete' => false,
    'actionUpdate' => false
);
?>