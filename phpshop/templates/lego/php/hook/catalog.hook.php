<?php
function leftCatalTable_hook($obj,$data,$rout) {
 if ($rout == 'START') {

       $obj->cell=3;
    }
}
function template_subcatalog_hook($obj,$data){

        if($data['i'] > 7){
            $obj->set('catalogClass','hide');
            $obj->set('catalogMoreClass','show');
        }
        else {
            $obj->set('catalogClass','');
            $obj->set('catalogMoreClass','hide');
        }
    
}

function leftCatalIcon($obj, $data, $route)
{
    if($route === 'END') {
        $PHPShopCategory = new PHPShopCategory($data['id']);
        $obj->set('customCatalogIcon', $PHPShopCategory->getParam("option6"));
    }
}

$addHandler = array
    (
    'subcatalog' => 'template_subcatalog_hook',
    'leftCatalTable'=>'leftCatalTable_hook',
    '#leftCatal' => 'leftCatalIcon'
);
?>
