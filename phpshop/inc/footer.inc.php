<?php

/*
 * ������
 */

// �������� ������
if ($PHPShopNav->notPath('print')) {
    $PHPShopModules->setHookHandler('footer', 'footer');
}

// PopUp
$PHPShopBannerElement->getPopup();

// ���������
$PHPShopAnalitica->counter();

echo '
    </div>
  </body>
</html>';
?>