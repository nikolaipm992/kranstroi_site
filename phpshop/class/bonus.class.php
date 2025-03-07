<?php

/**
 * ���������� �������
 * @author PHPShop Software
 * @version 1.1
 * @package PHPShopClass
 */
class PHPShopBonus {

    /**
     * �����������
     * @param int ID ������������
     */
    function __construct($user_id) {
        global $PHPShopSystem;

        $this->user_id = $user_id;
        $this->bonus_plus = intval($PHPShopSystem->getSerilizeParam('admoption.bonus'));
        $this->max_order_bonus = intval($PHPShopSystem->getSerilizeParam('admoption.order_bonus'));
        $this->PHPShopUser = new PHPShopUser($user_id);
        $this->bonus = $this->PHPShopUser->getBonus();
    }

    /**
     * ����� ������� ������������
     * @param float $sum ����� ������
     * @return int
     */
    function getUserBonus($sum) {

        $sum_bonus = $sum * $this->max_order_bonus / 100;
        if ($sum_bonus < $this->bonus)
            $this->bonus_minus = $sum_bonus;
        else
            $this->bonus_minus = $this->bonus;

        return intval($this->bonus_minus);
    }

    /**
     * ������ ������������ ������� �� �����
     * @param float $sum ����� ������
     * @return int 
     */
    function setUserBonus($sum) {
        return intval($sum * $this->bonus_plus / 100);
    }

    /**
     * ���������� ������� ������������
     * @param int $bonus_minus ������ ��� ����������
     * @param int $bonus_plus ������ ��� ����������
     */
    function updateUserBonus($bonus_minus, $bonus_plus) {

        $bonus = $this->bonus - $bonus_minus + $bonus_plus;

        if ($bonus < 0)
            $bonus = 0;

        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['shopusers']);
        $PHPShopOrm->update(array('bonus_new' => $bonus), array('id' => '=' . (int) $this->user_id));
    }

    /**
     * ���������� ���� �������
     * @param string $order_id ID ������
     * @param string $order_uid ����� ������
     * @param int $bonus_minus ������ ��� ����������
     * @param int $bonus_plus ������ ��� ����������
     */
    function updateBonusLog($order_id,$order_uid, $bonus_minus, $bonus_plus) {
        $PHPShopOrm = new PHPShopOrm($GLOBALS['SysValue']['base']['bonus']);

        if (!empty($bonus_plus))
            $PHPShopOrm->insert(array('date_new' => time(), 'comment_new' => __('���������� ������� �� �����') . ' &#8470;' . $order_uid, 'user_id_new' => $this->user_id, 'bonus_operation_new' => $bonus_plus,'order_id_new'=>$order_id));

        if (!empty($bonus_minus))
            $PHPShopOrm->insert(array('date_new' => time(), 'comment_new' => __('�������� ������� �� �����') . ' &#8470;' . $order_uid, 'user_id_new' => $this->user_id, 'bonus_operation_new' => "-$bonus_minus",'order_id_new'=>$order_id));
    }

}

?>