<?php

/**
 * ������ ���������� �������� ������
 * @param array $row ������ ������
 * @return string 
 */
function tab_cart($data, $option = false) {
    global $PHPShopInterface;


    $PHPShopInterface->action_title['cart-value-edit'] = '�������������';
    $PHPShopInterface->action_title['cart-value-remove'] = '�������';


    // ���������� ������
    $PHPShopOrder = new PHPShopOrderFunction($data['id']);

    $order = unserialize($data['orders']);
    $status = unserialize($data['status']);
    $CART = $order['Cart'];
    $PERSON = $order['Person'];
    $cart = $CART['cart'];

    $cartForSession = [];
    if (is_array($cart))
        foreach ($cart as $k => $item) {
            unset($item['name']);
            $cartForSession[$k] = $item;
        }

    $_SESSION['selectCart'] = $cartForSession;
    $num = $data_id = $sum = null;
    $n = $promo = 1;


    // ���� �����
    if ($PHPShopOrder->default_valuta_iso == 'RUB')
        $currency = ' <span class="rubznak">p</span>';
    else
        $currency = $PHPShopOrder->default_valuta_iso;

    $PHPShopInterface->checkbox_action = false;
    $PHPShopInterface->dropdown_action_form = false;
    $PHPShopInterface->setCaption(array("������������", "50%"), array("����", "15%"), array('<span class="hidden-xs">���-��</span><span class="visible-xs">���.</span>', "10%", array('align' => 'center')), array(null, "10%"), array('�����', '15%', array('align' => 'right')));

    if (sizeof($cart) != 0)
        if (is_array($cart))
            foreach ($cart as $key => $val) {

                if (!empty($val['id'])) {

                    // �������� ������� ������
                    if (!empty($val['parent']))
                        $val['id'] = $val['parent'];
                    if (!empty($val['parent_uid']))
                        $val['uid'] = $val['parent_uid'];

                    // �������
                    if (!empty($val['uid']))
                        $code = __('�������') . ': ' . $val['uid'];
                    else
                        $code = __('���') . ': ' . $val['id'];

                    // ��������
                    if (!empty($val['promo_code']))
                        $code .= '<br><span class="text-info">'.__('�����') . ': ' . $val['promo_code'] . '</span>';

                    // ������ �� �����������
                    if (!empty($val['promotion_discount']))
                        $code .= '<br><span class="text-success">' . __('��������� ������ ����������') . '</span>';

                    if (!empty($val['order_discount_disabled']))
                        $code .= '<br><span class="text-success">' . __('������ �� ����� ������ �� ���������') . '</span>';

                    if (!empty($val['pic_small']))
                        $icon = '<img src="' . $val['pic_small'] . '" onerror="this.onerror = null;this.src = \'./images/no_photo.gif\'" class="media-object">';
                    else
                        $icon = '<img class="media-object" src="./images/no_photo.gif">';

                    $name = '
<div class="media">
  <div class="media-left">
    <a href="?path=product&id=' . $val['id'] . '" >
      ' . $icon . '
    </a>
  </div>
   <div class="media-body">
    <div class="media-heading"><a href="?path=product&id=' . $val['id'] . '&return=order.' . $data['id'] . '" >' . $val['name'] . '</a></div>
    ' . $code . '
  </div>
</div>';
                    // ����
                    $price = $PHPShopOrder->ReturnSumma($val['price']).$currency;
                    if (!empty((int)$val['price_n'])){
                        $price .= '<br><s class="text-muted">' . $PHPShopOrder->ReturnSumma($val['price_n']) . '</s>'.$currency;
                        $promo++;
                    }

                    $PHPShopInterface->setRow(array('name' => $name, 'align' => 'left'), $price, array('name' => $val['num'], 'align' => 'center'), array('action' => array('cart-value-edit', '|', 'cart-value-remove', 'id' => $key), 'align' => 'center'), array('name' => $PHPShopOrder->ReturnSumma($val['price'] * $val['num']) . $currency, 'align' => 'right'));

                    $n++;
                    $num += $val['num'];
                    $sum += $val['price'] * $val['num'];
                }
            }
            
    $total = '<table class="pull-right totals">
      <tbody>
      <tr>
      <td>&nbsp;</td>
      <td class="text-right"><h4>' . __('�����') . '</h4></td>
      </tr>
      <tr>
      <td width="130">' . __('�����') . ':</td>
      <td class="text-right">
      ' . ($PHPShopOrder->returnSumma($sum) ) . $currency . '
      </td>
      </tr>
      <tr>
      <td>' . __('��������') . ':</td>
      <td class="text-right">
      ' . number_format($PHPShopOrder->getDeliverySumma(), $PHPShopOrder->format, '.', ' ') . $currency . '
      </td>
      </tr>';

    if (!empty($CART['weight']))
        $total .= '
      <tr>
      <td>' . __('���') . ':</td>
      <td class="text-right">
      ' . $CART['weight'] . ' ' . __('��.') . '
      </td>
      </tr>';
    
    // ������� ������ ���� ��� ������ �����
    if($n == $promo)
        $PERSON['discount'] = 0;

    $total .= '<tr>
      <td>' . __('������') . ':</td>
      <td class="text-right">
      ' . (int) $PERSON['discount'] . ' %
      </td>
      </tr>';
    
    if(!empty($data['bonus_minus']))
        $total .= '<tr>
      <td>' . __('������� �������') . ':</td>
      <td class="text-right">
      ' . (int) $data['bonus_minus']. $currency . '
      </td>
      </tr>';
    
    $total .= '<tr>
      <td><h5>' . __('�����') . ':</h5></td>
      <td class="text-right">
      <h5 class="text-success">' . ($PHPShopOrder->getTotal(false, ' ')) . $currency . '</h5>
      </td>
      </tr>
      </tbody>
      </table>';

    // ������
    if (!empty($PERSON['discount']))
        $discount = $PERSON['discount'];
    else
        $discount = null;

    $disp = '<table class="table table-hover cart-list">' . $PHPShopInterface->getContent() . '</table>
<div class="row">
  <div class="col-lg-9 col-md-8 col-xs-6">
   <div class="input-group-btn">
   <button  class="btn btn-default btn-sm cart-add"><span class="glyphicon glyphicon-plus"></span> ' . __('�������� ������') . '</button>
   <div class="btn btn-default btn-sm btn-file"><span class="glyphicon glyphicon-save"></span><input type="file" id="uploadimage" name="file"></div>
   </div>
  </div>
  <div class="col-lg-3 col-md-4 col-xs-6">
    <div class="input-group">
      <span class="input-group-addon input-sm">%</span>
      <input type="text" class="form-control input-sm discount-value" placeholder="' . __('������') . '" value="' . $discount . '"> 
      <span class="input-group-btn">
        <button class="btn btn-default btn-sm discount" type="button">' . __('���������') . '</button>
     </span>
    </div>
  </div>
</div>
<p class="clearfix"> </p>
' . $total . '
<p class="clearfix"> </p>
<div class="row">
  <div class="col-md-6">
  <label for="dop_info">' . __('���������� ����������') . '</label>
  <textarea class="form-control" id="dop_info" name="dop_info_new">' . $data['dop_info'] . '</textarea>
  </div>
  <div class="col-md-6">
    <label for="status_maneger">' . __('���������� ��������������') . '</label>
    <textarea class="form-control" id="status_maneger" name="status[maneger]">' . $status['maneger'] . '</textarea>
  </div>
</div>
';
    return $disp;
}

?>