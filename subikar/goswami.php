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

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		$query = 'SELECT * FROM  `oc_product` AS p LEFT JOIN  `oc_product_description` AS pd ON  `p`.`product_id` =  `pd`.`product_id` WHERE p.done=0 LIMIT 0,50 ';
		$Products = $readConnection->fetchAll($query);	
		foreach($Products as $key=>$Product)
			 {

				$query = 'SELECT pa.text, ad.name FROM  `oc_product_attribute` AS pa LEFT JOIN  `oc_attribute_description` AS ad ON  `pa`.`attribute_id` =  `ad`.`attribute_id` WHERE pa.product_id='.$Product['product_id'];
				$AttruteData = $readConnection->fetchAll($query);	
				$Products[$key]['attribute'] = $AttruteData;


				$query = 'SELECT cd.name FROM  `oc_product_to_category` AS p2c LEFT JOIN  `oc_category_description` AS cd ON  `p2c`.`category_id` =  `cd`.`category_id` WHERE p2c.product_id='.$Product['product_id'];
				$CatagoryData = $readConnection->fetchAll($query);	
				$Products[$key]['catagory'] = $CatagoryData;

			 }
       // print_r($Products); exit;
        foreach($Products as $product)  
          {
          //	print_r($product); exit;
            
          	$mproduct = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($product['model']));
          //	print_r($mproduct); exit;
          	$simpleProduct = Mage::getModel('catalog/product');
          	if(!$mproduct)
          	  {
          	  	 $categoryId = array();
                 foreach($product['catagory'] as $Cat)
                   {
                   	 $category = Mage::getResourceModel('catalog/category_collection')
					->addFieldToFilter('name', $Cat['name'])
					->getFirstItem(); // The parent category
					
					$categoryId[] = $category->getId();
                   }
                // print_r($categoryId); exit;  
                 //print_r($product['attribute']);exit;  
                 $AttributeData = array();   
                 foreach($product['attribute'] as $attribute)
                   {
                   	  $text = $attribute['text'];
                      if($attribute['name'] != 'Model No')
                      {

							$attributeCode = trim($attribute['name']);
							$attributeAdminValue = trim($text);

							//$sattribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, $attributeCode);
							$Query = "select attribute_id from zimw_eav_attribute where frontend_label = '".$attributeCode."'";
							$Aid = $readConnection->fetchCol($Query);
							//if($attributeCode == 'Collection')
							  // { print($text); exit; }                             

							$collection = Mage::getResourceModel('eav/entity_attribute_option_collection')
							    ->setAttributeFilter($Aid)
							    ->setStoreFilter(Mage_Core_Model_App::ADMIN_STORE_ID)
							    ->addFieldToFilter('tdv.value', $attributeAdminValue);
                           // $text = $Aid;   
							if ($collection->getSize() > 0) {
							    $text = $collection->getFirstItem()->getId();
							}


					  }   	
                      $AttributeData[str_replace(" ",'_',strtolower($attribute['name']))] = $text;

                   }                    
                //print_r($AttributeData); 
                //exit; 
               // print($product['image']); exit;
                if($product['image'] != '')
                  {
		                $imageurl = "http://www.zimsonwatches.com/image/".$product['image'];	
						$image_type = substr(strrchr($imageurl,"."),1);
						//find the image extension 
						$filename   = time().$product['model'].'.'.$image_type;
						//give a new name, you can modify as per your requirement 
						$filepath   = Mage::getBaseDir('media') . DS . 'import'. DS . $filename;
						//print($filepath); exit;
						//path for temp storagefolder: ./media/import/ 
						$curl_handle=curl_init();
						curl_setopt($curl_handle, CURLOPT_URL,$imageurl);
						curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Cirkel');
						$query = curl_exec($curl_handle);
						curl_close($curl_handle);
						//Mage::Log('68. File Path ' . $filepath . ', image url ' . $image_url);
						file_put_contents($filepath, $query);
						//store the image from external url to the temp storage folder 
						file_put_contents($filepath, file_get_contents(trim($imageurl)));                     	
                  }  
                $fileSize = filesize($filepath);
                if(file_exists($filepath) && $fileSize > 0)
                {
                              
                $description = ($product['description'] == '')?$product['meta_description']:strip_tags($product['description']);

                 $simpleProduct
                ->setWebsiteIds(array('1')) //website ID the product is assigned to, as an array
				->setAttributeSetId(12) //ID of a attribute set named 'default'
				->setAttributeSetName('Watches') //ID of a attribute set named 'default'
				->setTypeId('simple') //product type
				->setCreatedAt(strtotime('now')) //product creation time
				->setSku($product['model']) //SKU
				->setName($product['name']) //product name
				->setWeight($product['weight'])
				->setStatus(1) //product status (1 - enabled, 2 - disabled)
				->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
				->setNewsFromDate('') //product set as new from
				->setNewsToDate('') //product set as new to
				->setUrlKey(str_replace(" ",'-',strtolower($mainProduct['name'])))
				->setCountryOfManufacture('') //country of manufacture (2-letter country code)
				->setCaseMaterial($AttributeData['case_material'])
				->setCaseSize($AttributeData['case_size'])
				->setWatchCollection($AttributeData['watch_collection'])
				->setDialColor($AttributeData['dial_color'])
				 ->setGender($AttributeData['watch_gender'])
				 ->setGlassMaterial($AttributeData['glass_material'])
                 ->setMovement($AttributeData['movement'])
                 ->setShape($AttributeData['shape'])
                 ->setStrapType($AttributeData['strap_type'])
                 ->setWarranty($AttributeData['warranty']) 
                 ->setWaterResistance($AttributeData['water_resistance'])
                 ->setModelNo($AttributeData['model_no'])
                 ->setFeatures($AttributeData['features'])
                 ->setBrand($AttributeData['brand'])  
                 ->setBatteryLife($AttributeData['battery_life'])   
                 ->setCompatibility($AttributeData['compatibility'])               
				->setPrice($product['price']) //price in form 11.22
				->setCost('') //price in form 11.22
				->setSpecialPrice('') //special price in form 11.22
				->setSpecialFromDate('') //special price from (MM-DD-YYYY)
				->setSpecialToDate('') //special price to (MM-DD-YYYY)
				->setMsrpEnabled(1) //enable MAP
				->setMsrpDisplayActualPriceType(4) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
				->setMsrp('') //Manufacturer's Suggested Retail Price
				->setMetaTitle($product['meta_title'])
				->setMetaKeyword($product['meta_keyword'])
				->setMetaDescription($product['meta_description'])
				->setStoreId(0)
				->setDescription($description )
				->setShortDescription($product['meta_description'])
				->setData($imagestack)				
				
				->setCategoryIds($categoryId) //assign product to categories
				->setCanSaveConfigurableAttributes(true)
				->setCanSaveCustomOptions(true) 
				->addImageToMediaGallery($filepath, array('image','thumbnail','small_image'), false, false); 

			
				}
				else{
				 $description = ($product['description'] == '')?$product['meta_description']:strip_tags($product['description']);	
                 $simpleProduct
                ->setWebsiteIds(array('1')) //website ID the product is assigned to, as an array
				->setAttributeSetId(12) //ID of a attribute set named 'default'
				->setAttributeSetName('Watches') //ID of a attribute set named 'default'
				->setTypeId('simple') //product type
				->setCreatedAt(strtotime('now')) //product creation time
				->setSku($product['model']) //SKU
				->setName($product['name']) //product name
				->setWeight($product['weight'])
				->setStatus(2) //product status (1 - enabled, 2 - disabled)
				->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
				->setNewsFromDate('') //product set as new from
				->setNewsToDate('') //product set as new to
				->setUrlKey(str_replace(" ",'-',strtolower($mainProduct['name'])))
				->setCountryOfManufacture('') //country of manufacture (2-letter country code)
				->setCaseMaterial($AttributeData['case_material'])
				->setCaseSize($AttributeData['case_size'])
				->setWatchCollection($AttributeData['watch_collection'])
				->setDialColor($AttributeData['dial_color'])
				 ->setGender($AttributeData['watch_gender'])
				 ->setGlassMaterial($AttributeData['glass_material'])
                 ->setMovement($AttributeData['movement'])
                 ->setShape($AttributeData['shape'])
                 ->setStrapType($AttributeData['strap_type'])
                 ->setWarranty($AttributeData['warranty']) 
                 ->setWaterResistance($AttributeData['water_resistance'])
                 ->setModelNo($AttributeData['model_no'])
                 ->setFeatures($AttributeData['features'])
                 ->setBrand($AttributeData['brand'])  
                 ->setBatteryLife($AttributeData['battery_life'])   
                 ->setCompatibility($AttributeData['compatibility'])               
				->setPrice($product['price']) //price in form 11.22
				->setCost('') //price in form 11.22
				->setSpecialPrice('') //special price in form 11.22
				->setSpecialFromDate('') //special price from (MM-DD-YYYY)
				->setSpecialToDate('') //special price to (MM-DD-YYYY)
				->setMsrpEnabled(1) //enable MAP
				->setMsrpDisplayActualPriceType(4) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
				->setMsrp('') //Manufacturer's Suggested Retail Price
				->setMetaTitle($product['meta_title'])
				->setMetaKeyword($product['meta_keyword'])
				->setMetaDescription($product['meta_description'])
				->setStoreId(0)
				->setDescription($description)
				->setShortDescription($product['meta_description'])
				->setData($imagestack)				

				
				->setCategoryIds($categoryId) //assign product to categories
				->setCanSaveConfigurableAttributes(true)
				->setCanSaveCustomOptions(true); 


		            
				} 
				//print_r($simpleProduct); exit;               
                  //print_r($categoryId); exit; 
				//$simpleProduct->save();
				$quantity = ($product['quantity'] > 0)?$product['quantity']:1;
				$simpleProduct->getResource()->save($simpleProduct);
				$stockItem = Mage::getModel('cataloginventory/stock_item');
				$stockItem->assignProduct($simpleProduct);
				$stockItem->setData('is_in_stock', 1);
				$stockItem->setData('stock_id', 1);
				$stockItem->setData('store_id', 2);
				$stockItem->setData('manage_stock', 1);
				$stockItem->setData('use_config_manage_stock', 0);
				$stockItem->setData('min_sale_qty', 1);
				$stockItem->setData('use_config_min_sale_qty', 0);
				$stockItem->setData('max_sale_qty', 1000);
				$stockItem->setData('use_config_max_sale_qty', 0);
				$stockItem->setData('qty', $quantity);
				$stockItem->save();					

				//store the image from external url to the temp storage folder
				//echo "<br>file Path+++"; print_r(count($filepath)); 
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$data = array("done" => "1"); 
			$where = "model = '".$product['model']."'"; 
			$write->update("oc_product", $data, $where); 						

			}


          	  	//print("Product not exists");
          	  
          }   		

           /* $indexCollection = Mage::getModel('index/process')->getCollection();
			foreach ($indexCollection as $index) {
 			  $index->reindexAll();}	
			  Mage::app()->getCacheInstance()->flush();       
			}  
		  */	
?>

