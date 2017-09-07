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

    $discontinue= array('Case Material','case Size','Collection','Dial Color','Model No');
    $resource = Mage::getSingleton('core/resource');
    $readConnection = $resource->getConnection('core_read');

    $Query = "SELECT  pa.attribute_id, ad.name FROM  `oc_product_attribute` as pa LEFT JOIN `oc_attribute_description` as ad on pa.attribute_id = ad.attribute_id GROUP BY  `attribute_id`";
        $Attributes = $readConnection->fetchAll($Query);
        print "<pre>";
        print_r($Attributes);
        //$c=0;

    foreach($Attributes as $key => $Attribute)
    {
    if(in_array($Attribute['name'], $discontinue))
        continue;  
	$arg_attribute[$key] = $Attribute['name'];
    //$c++;
    }
    print "<br><br>";
    print "<pre>";
    print_r($arg_attribute);

	?>