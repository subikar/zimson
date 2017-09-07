<?php
//require_once '../app/Mage.php';

/**
 * Error reporting
 */

	error_reporting(E_ALL | E_STRICT);
	ini_set('memory_limit','512M');
	ini_set('display_errors', 1);
	ini_set('max_execution_time', 600); //300 seconds = 5 minutes
	session_start();

	require_once '../app/Mage.php';
	Mage::app();


	$resource = Mage::getSingleton('core/resource');
	$readConnection = $resource->getConnection('core_read');

	$Query = "SELECT  product_id FROM  `oc_product_description` as pd where `pd`.`description` = ''";
	$des = $readConnection->fetchAll($Query);
	print_r($des);
    //foreach($des as $key => $val)
    //{
          
    //}