<?php
require_once('app/Mage.php'); 
umask(0);
Mage::app('default');
Mage::app ()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
$categoryId = $_GET['cat_id']; // a category id that you can get from admin
if($categoryId > 0)
  {
$category = Mage::getModel('catalog/category')->load($categoryId);

$productCollection = Mage::getModel('catalog/product')
    ->getCollection()
    ->addCategoryFilter($category);

$array_product = $productCollection->getAllIds();    

Mage::getSingleton('catalog/product_action')->updateAttributes($array_product, array('special_price_zimson' => 11560), Mage_Core_Model_App::ADMIN_STORE_ID);    
  } 
?>

