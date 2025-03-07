<?php
/**
 * ���������� ������ ������ ����� Platron
 * @author PHPShop Software
 * @version 1.0
 * @package PHPShopPayment
 */

if(empty($GLOBALS['SysValue'])) exit(header("Location: /"));

$mrh_ouid = explode("-", $_POST['ouid']);
$inv_id = $mrh_ouid[0]."".$mrh_ouid[1];     //����� �����

$OrderId=$inv_id;
$Amount=$GLOBALS['SysValue']['other']['total'];
$Currency="RUB";

// ��������������� ����������
$PrivateSecurityKey=$SysValue['platron']['secret_key'];
$MerchantId=$SysValue['platron']['merchant_id'];


$arrReq = array();

/* ������������ ��������� */
$arrReq['pg_merchant_id'] = $MerchantId;// ������������� ��������
$arrReq['pg_order_id']    = $inv_id;		// ������������� ������ � ������� ��������
$arrReq['pg_amount']      = $Amount;		// ����� ������
$arrReq['pg_currency']    = 'RUR';
$arrReq['pg_description'] = $SysValue['platron']['description']; // �������� ������ (������������ � �������� �������)
$arrReq['pg_salt'] = rand(21,43433);

$arrReq['pg_sig'] = md5('payment.php;'.$arrReq['pg_amount'].';'.$arrReq['pg_amount'].';'.$arrReq['pg_currency'].';'.$arrReq['pg_description'].';'.$arrReq['pg_merchant_id'].';'.$arrReq['pg_order_id'].';'.$arrReq['pg_salt'].';'.$PrivateSecurityKey);

// ����� HTML �������� � ������� ��� ������
$disp= "
<div align=\"center\">

 <p><br></p>
 
 <img src=\"phpshop/lib/templates/icon/bank/visa.gif\" border=\"0\" hspace=5>
  <img src=\"phpshop/lib/templates/icon/bank/mastercard.gif\" border=\"0\" hspace=5>
  <p><br></p>

<p>�� ������ �������� ���� ������ � ������ ��-���� ��������� ������� (VISA, MasterCard, DCL, JCB, AmEx). ��������� �������� �������������� �������������� ������� <b>Platron</b>. </p>

<form name=\"PaymentForm\" action=\"https://www.platron.ru/payment.php\" method=\"get\" target=\"_top\" >
<input type=\"hidden\" name=\"pg_merchant_id\" value=\"$arrReq[pg_merchant_id]\">
<input type=\"hidden\" name=\"pg_order_id\" value=\"$arrReq[pg_order_id]\">
<input type=\"hidden\" name=\"pg_amount\" value=\"$arrReq[pg_amount]\">
<input type=\"hidden\" name=\"pg_currency\" value=\"$arrReq[pg_currency]\">
<input type=\"hidden\" name=\"pg_description\" value=\"$arrReq[pg_description]\">
<input type=\"hidden\" name=\"amount\" value=\"$arrReq[pg_amount]\">
<input type=\"hidden\" name=\"pg_salt\" value=\"$arrReq[pg_salt]\">
<input type=\"hidden\" name=\"pg_sig\" value=\"$arrReq[pg_sig]\">
<input type=\"hidden\" name=\"partner\" value=\"phpshop\">
	<table>
<tr>
	<td><img src=\"images/shop/icon-client-new.gif\" alt=\"\" width=\"16\" height=\"16\" border=\"0\" align=\"left\">
	<a href=\"javascript:PaymentForm.submit();\">�������� ����� ��������� �������</a></td>
</tr>
</table>
</form>

</div>";

?>