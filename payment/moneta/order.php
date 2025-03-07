<?php
/**
 * ���������� ������ ������ ����� PayAnyWay
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopPayment
 */


if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));


// ��������������� ����������
$payment_url = $SysValue['payanyway']['PAYMENT_URL'];
$mnt_id = $SysValue['payanyway']['MNT_ID'];
$mnt_dataintegrity_code = $SysValue['payanyway']['MNT_DATAINTEGRITY_CODE'];
$mnt_test_mode = $SysValue['payanyway']['MNT_TEST_MODE'];


// ��������� ��������
$mrh_ouid = explode("-", $_POST['ouid']);
$inv_id = $mrh_ouid[0]."".$mrh_ouid[1];     //����� �����

// �������� �������
$inv_desc  = "PHPShopPaymentService";

// ����� �������
$out_summ  = number_format($GLOBALS['SysValue']['other']['total'], 2, '.', '');

// ��� ������ � ������
$mnt_currency = $GLOBALS['PHPShopSystem']->getDefaultValutaIso();

// ���������� �������
$PHPShopCart = new PHPShopCart();

/**
 * ������ ������ ������� �������
 */
function cartpaymentdetails($val) {
     $dis=$val['uid']."  ".$val['name']." (".$val['num']." ��. * ".$val['price'].") -- ".$val['total']."
";

    return $dis;
}



// ����������� ���
$mnt_signature = md5($mnt_id . $inv_id . $out_summ . $mnt_currency . $mnt_test_mode . $mnt_dataintegrity_code);

// ����� HTML �������� � ������� ��� ������
$disp= '
<div align="center">
<p>
������� ����� ������ <b>PayAnyWay</b> � ��� ������� � ���������� ������ ������ ��������� ������� � �����.
</p>
 <p><br></p>
 
<form method="POST" name="pay" id="pay" action="https://'.$payment_url.'/assistant.htm?">
<input type="hidden" name="MNT_ID" value="'.$mnt_id.'">
<input type="hidden" name="MNT_TRANSACTION_ID" value="'.$inv_id.'">
<input type="hidden" name="MNT_AMOUNT" value="'.$out_summ.'">
<input type="hidden" name="MNT_CURRENCY_CODE" value="'.$mnt_currency.'">
<input type="hidden" name="MNT_TEST_MODE" value="'.$mnt_test_mode.'">
<input type="hidden" name="MNT_SIGNATURE" value="'.$mnt_signature.'">

<input type=hidden name="OrderDetails" value="'.$PHPShopCart->display('cartpaymentdetails').'">
	  <table>
<tr>
	<td><img src="images/shop/icon-client-new.gif"  width="16" height="16" border="0" align="left">
	<a href="javascript:pay.submit();">�������� ����� ��������� �������</a></td>
</tr>
</table>
      </form>
</div>';

?>