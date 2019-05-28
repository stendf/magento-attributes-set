<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

chdir('../');

require_once 'app/Mage.php';
umask(0);

Mage::init();


$errors = [];
$params = Mage::app()->getRequest()->getParams();

if (!isset($params['setid']) || !isset($params['groupid'])) {
    $err[] = 'no params';
    return;
} else if (trim($params['setid'])=='' || trim($params['groupid'])=='') {
    $err[] = 'enpty params';
    return;
}

$setId   = $params['setid'];
$groupid = $params['groupid'];


$_g = Mage::getModel('eav/entity_attribute_group')->load($groupid);

$attributes = Mage::getResourceModel('catalog/product_attribute_collection')
        ->setAttributeGroupFilter($_g->getId())
        ->addVisibleFilter()
        ->checkConfigurableProducts()
        ->load();

$out = "<ul id='attrs-data'>";
$attributeCodes = array();

if ($attributes->getSize() > 0) {
    $i=0;
    foreach ($attributes->getItems() as $attribute) {
        $data = $attribute->getData();
        $attributeCodes[] = [
            'code'                  => $data['attribute_code'],
            'attribute_id'          => $data['attribute_id'],
            'frontend_label'        => $data['frontend_label'],
            'entity_attribute_id'   => $data['entity_attribute_id'],
            'attribute_set_id'      => $data['attribute_set_id'],
            'attribute_group_id'    => $data['attribute_group_id'],
            'sort_order'            => $data['sort_order']
        ];
        $link = Mage::helper('adminhtml')->getUrl('wbsadmin/bravoindex/index');
        if ($i%2==0) {
            $out .= "<li class='even'>";
        } else {
            $out .= "<li>";
        }
        $out .= "<div class='data-col icon'><img src='/skin/adminhtml/default/default/bravo/drag.png' width='16px' /></div>";
        $out .= "<div class='data-col'>{$data['attribute_code']} - <small>{$data['frontend_label']}</small></div>";
        //$out .= "<div class='data-col'><input class='input-text' type='text' name='sort[{$data['entity_attribute_id']}][value]' value='{$data['sort_order']}' /></div>";
        $out .= "<div class='data-col icon'><a data-attentid='{$data['entity_attribute_id']}' class='remove-attr' href='javascript:void(0)'><img class='trash-img' src='/skin/adminhtml/default/default/bravo/trash.png' width='16px' /></a></div>";
        $out .= "<input type='hidden' name='sort[{$data['entity_attribute_id']}][attribute_id]' value='{$data['attribute_id']}' />";
        $out .= "<input class='posholder' type='hidden' name='sort[{$data['entity_attribute_id']}][position]' value='' />";
        $out .= "</li>";
        
        $i++;
    }
}
$out .= "</ul>";


echo json_encode(['html'=>$out]);
//echo json_encode($attributeCodes);