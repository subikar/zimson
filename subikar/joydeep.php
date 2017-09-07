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


// Joydeep coding start here 
        //$discontinue= array('Case Material','case Size','Collection','Dial Color','Model No');
        $resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');

		$Query = "SELECT  pa.attribute_id, ad.name FROM  `oc_product_attribute` as pa LEFT JOIN `oc_attribute_description` as ad on pa.attribute_id = ad.attribute_id  WHERE ad.done =0 GROUP BY  `attribute_id` LIMIT 0, 1 ";
        $Attributes = $readConnection->fetchAll($Query);  
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
        foreach($Attributes as $Attribute)
        {

           // if(in_array($Attribute['name'], $discontinue))
            // continue;  
			$arg_attribute = $Attribute['name'];
            echo $arg_attribute; 
			//print($arg_attribute); exit;
			//$key_data = array('red','black','orange');
			//$attr_model = Mage::getModel('catalog/resource_eav_attribute');
			//$attr = $attr_model->loadByCode('catalog_product', $arg_attribute);
            $Query = "SELECT  `attribute_id` FROM  `zimw_eav_attribute` WHERE frontend_label LIKE '".$arg_attribute."%'";
	        $attr_id = $readConnection->fetchAll($Query); 			
	       // echo '<h2>'.$Attribute['name']."(".$attr_id[0]['attribute_id'].")</h2>";
	        //echo '<hr />';
			$Query = "SELECT  `text` FROM  `oc_product_attribute` WHERE attribute_id=".$Attribute['attribute_id']." GROUP BY  `text`";
	        $AttributeValue = $readConnection->fetchAll($Query);  
	        //print_r($AttributeValue); exit;
	        $key_data = array();
	        foreach($AttributeValue as $Value)
	        {
	        	//echo $Value['text'];
	        	//echo "<br />";
			    $option = array();
			    $arg_value = trim($Value['text']);
			   // $attr_id = $attr_id; //$attr->getAttributeId();
			    if($Value['text'] != '')
			      {	
			    $option['attribute_id'] = $attr_id[0]['attribute_id'];
			    $option['value']['any_option_name'][0] = $arg_value;
			    $option['value']['any_option_name'][1] = $arg_value;
			    $option['value']['any_option_name'][2] = $arg_value;
                //print_r($option); exit;  
			    $setup->addAttributeOption($option);
			   }


	        }
           
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$data = array("done" => "1"); 
			$where = "attribute_id = '".$Attribute['attribute_id']."'"; 
			$write->update("oc_attribute_description", $data, $where); 	
	        //echo '<hr />';
	        //exit;
            
        }

		


        
?>

