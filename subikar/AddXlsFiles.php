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
	
		 
class Pwsmage_HkcsvImporter_Model_Job{
  //  const ADMIN_CONFIG_PATH = 'hkcsvimporter/general/';
  
function get_xlsfields(){
	
	 echo "Here in xls fields"; 
	
	$mageColName='';
	$_SESSION['configProduct']='';
	$_SESSION['imageurl'] ='';
	$x=2;
	$Val='';
	$cnt=1;

	   /**
     * Retrieve the read connection
     */
		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		
		echo $query = 'SELECT * FROM fengde_products WHERE con_simpel = "configureerbaar product" and insert_flag=0 LIMIT 0,1 ';
		$results = $readConnection->fetchAll($query);
	//	print_r($results); exit;
    /*Print out the results*/
		if(!empty($results)){
			
		if($cnt==1)	{
		$this->insertProducts($results);
		}
		$cnt++;
		echo "<br><br>Start again.....";
		}
	
  	}


function insertProducts($mainproductData){
	
	echo "<br><br>In the insert Products <br>";  
	$simpleProducts =array();
	$totalMainProducts =	count($mainproductData);
	
		$resource = Mage::getSingleton('core/resource');
		foreach($mainproductData as $mdata){
		//
			$readConnection2 = $resource->getConnection('core_read');
			
			echo "<br><br>".$proQuery = 'SELECT * FROM fengde_products WHERE Artikelnr_ LIKE "'.$mdata['Artikelnr_'].'%" AND con_simpel = "simpel" ';
			$itmQueryresults = $readConnection2->fetchAll($proQuery);
			//print_r($proQueryresults); exit;
			//$mainProductId=$mdata['Product_Id'];
		
			
			
			$mageProductID =$this->creteProducts($mdata,$itmQueryresults);	
			/////Update Sheet1
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
			$data = array("insert_flag" => "1"); 
			$where = "Artikelnr_ = ".$mdata['Artikelnr_']." AND con_simpel = 'configureerbaar product' "; 
			$write->update("fengde_products", $data, $where); 
			///DB connection Closing
			$readConnection2->closeConnection();
			$readConnection3->closeConnection();
		}
	} ///function insertProducts end
	

	
function creteProducts($mainProduct,$itemProduct){
$product='';
$cat_ary=array();
      
	  $product = Mage::getModel('catalog/product')->loadByAttribute('sku',trim($mainProduct['Artikelnr_']));
	
	//  print_r($mainProduct); exit;
	  if(!$product){
		         $simpleProduct1 = Mage::getModel('catalog/product');
				$category = Mage::getResourceModel('catalog/category_collection')
				->addFieldToFilter('name', $mainProduct['Hoofdcategorie'])
				->getFirstItem(); // The parent category
					
					$categoryId = $category->getId();
						
/*					$Subcat = Mage::getResourceModel('catalog/category_collection')
					->addFieldToFilter('name','Basis Vormen')
					->getFirstItem(); // The category
*/				//}

				$SubcatID = 12;
				$cat_ary = array($categoryId,$SubcatID);
				
               // $url = $mainProduct['Titel_EN']  
					 
				$simpleProduct1
				//    ->setStoreId(1) //you can set data in store scope
				->setWebsiteIds(array('1')) //website ID the product is assigned to, as an array
				->setAttributeSetId(15) //ID of a attribute set named 'default'
				->setAttributeSetName('formaat') //ID of a attribute set named 'default'
				->setTypeId('configurable') //product type
				->setCreatedAt(strtotime('now')) //product creation time
				->setSku($mainProduct['Artikelnr_']) //SKU
				->setName($mainProduct['Titel_EN']) //product name
				->setWeight('0.00')
				->setStatus(1) //product status (1 - enabled, 2 - disabled)
				->setTaxClassId(2) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH) //catalog and search visibility
				->setNewsFromDate('') //product set as new from
				->setNewsToDate('') //product set as new to
				->setUrlKey($mainProduct['Artikelnr_'])
				->setCountryOfManufacture('') //country of manufacture (2-letter country code)
				->setMaterial($mainProduct['Materiaal'])
				->setTechnique($mainProduct['Techniek'])
				->setTotalsize($mainProduct['Formaat'])
				->setColor($mainProduct['Kleur'])
				->setMoq($mainProduct['Min_afname_variant'])
				->setPrice(0) //price in form 11.22
				->setCost('') //price in form 11.22
				->setSpecialPrice('') //special price in form 11.22
				->setSpecialFromDate('') //special price from (MM-DD-YYYY)
				->setSpecialToDate('') //special price to (MM-DD-YYYY)
				->setMsrpEnabled(1) //enable MAP
				->setMsrpDisplayActualPriceType(4) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
				->setMsrp('') //Manufacturer's Suggested Retail Price
				->setMetaTitle($mainProduct['Titel_EN'])
				->setMetaKeyword($mainProduct['Beschrijving_Kort_EN'])
				->setMetaDescription($mainProduct['Meta_Titel_EN'])
				->setDescription($mainProduct['Beschrijving_Kort_EN'])
				->setShortDescription($mainProduct['Beschrijving_Kort_EN'])
				->setData($imagestack)				
				->setStockData(array(
					'use_config_manage_stock' => 0, //'Use config settings' checkbox
					'manage_stock' =>999999, //manage stock
					'min_sale_qty' => 0, //Minimum Qty Allowed in Shopping Cart
					'max_sale_qty' =>0, //Maximum Qty Allowed in Shopping Cart
					'is_in_stock' => 1, //Stock Availability
					'qty' => 0 //qty
					))
				
				->setCategoryIds($cat_ary) //assign product to categories
				->setCanSaveConfigurableAttributes(true)
				->setCanSaveCustomOptions(true);
				
				$simpleProduct1->setCanSaveConfigurableAttributes(true);
				$simpleProduct1->setCanSaveCustomOptions(true);
				
				//empty $cat_ary
				$cat_ary=array();
				$imgname =explode('|',$mainProduct['ImageURL']);
				
		////////
		$cProductTypeInstance = $simpleProduct1->getTypeInstance();
		$colorAttributeId = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', 'material');
		$sizeAttributeId = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', 'color');
		$bandsAttributeId = Mage::getModel('eav/entity_attribute')->getIdByCode('catalog_product', 'technique');
		$attribute_ids = array($colorAttributeId,$sizeAttributeId,$bandsAttributeId);
		$cProductTypeInstance->setUsedProductAttributeIds($attribute_ids);
		$attributes_array = $cProductTypeInstance->getConfigurableAttributesAsArray($attribute_ids);
		
		foreach($attributes_array as $key => $attribute_array) {
		
		$attributes_array[$key]['use_default'] = 1;
		$attributes_array[$key]['position'] = 0;
		if (isset($attribute_array['frontend_label'])){
		$attributes_array[$key]['label'] = $attribute_array['frontend_label'];
		}else {
		$attributes_array[$key]['label'] = $attribute_array['attribute_code'];
		}
		}
		 //Add it back to the configurable product..
		$simpleProduct1->setConfigurableAttributesData($attributes_array);
		$simpleProduct1->save();
		
		$stockStatus1 = Mage::getModel('cataloginventory/stock_status');
		$stockStatus1->assignProduct($simpleProduct1);
		$stockStatus1->saveProductStatus($simpleProduct1->getId(), 1);
		
		$storeid=1;
		$array_of_simple_product_ids =array(); ///empty array
		$newProduct = Mage::getModel('catalog/product')->load($simpleProduct1->getId());
		//if($_SESSION['imageurl']) $imgname =$_SESSION['imageurl'];
		$imgname =$imgname;
		//echo "<br>Total No.of images-----";
		for($cnt=0; $cnt<=count($imgname); $cnt++ ){
		$image_url =$imgname[$cnt];
		$image_url  =str_replace("https://", "http://", $image_url);
		// replace https tp http 
		$image_type = substr(strrchr($image_url,"."),1);
		//find the image extension 
		$filename   = $mainProduct['parent sku_'.$cnt].'.'.$image_type;
		//give a new name, you can modify as per your requirement 
		$filepath   = Mage::getBaseDir('media') . DS . 'import'. DS . $filename;
		//path for temp storagefolder: ./media/import/ 
		$curl_handle=curl_init();
		curl_setopt($curl_handle, CURLOPT_URL,$image_url);
		curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Cirkel');
		$query = curl_exec($curl_handle);
		curl_close($curl_handle);
		//Mage::Log('68. File Path ' . $filepath . ', image url ' . $image_url);
		file_put_contents($filepath, $query);
		//store the image from external url to the temp storage folder 
		file_put_contents($filepath, file_get_contents(trim($image_url))); //store the image from external url to the temp storage folder
		//echo "<br>file Path+++"; print_r(count($filepath)); 
		if (file_exists($filepath) && $filepath!='') { 
		try{
		$newProduct->addImageToMediaGallery($filepath, array('image', 'small_image', 'thumbnail'), false, false);
	
		
		} catch (Exception $e) {
			echo $e->getMessage();
			}
			} else {
			 echo "<br>Product does not have an image or the path is incorrect. Path was: {$filePath}<br/>";
			}
		}
			$newProduct->save();
			exit;
		echo "<br> Main product added".$simpleproductID=$newProduct->getId();
				$this->addChildProduct($mainProductId,$simpleproductID,$itemProduct,$newProduct->getName());
		return $simpleproductID;
	}
		echo "<br>Main already product exist";print_r($product->getId());
		$this->addChildProduct($mainProductId,$product->getId(),$itemProduct,$product->getName());
		
		
		
	
		$indexCollection = Mage::getModel('index/process')->getCollection();
		foreach ($indexCollection as $index) {
		/* @var $index Mage_Index_Model_Process */
		$index->reindexAll();
		}
	return $simpleproductID;
}

function addChildProduct($mainProductID,$mageProductID,$childproductData,$mainProductName){
	
	foreach($childproductData as $proRow) {
		  echo"<br>Child Products=".$proRow['Id']."=>>"; 
		  //$itemProduct
		  if($proRow['Product_Id']==$mainProductID){
			  
			  if($proRow['SKU'] != ''){
				$smpProduct = Mage::getModel('catalog/product')->loadByAttribute('sku',$proRow['SKU']);
				if(!$smpProduct){
					
			$simpleProduct = Mage::getModel('catalog/product');
			$pro_attibutes=explode(',',$proRow['Item_Name']); ///0-size ,1 - color, 2 - sku.
				if(count($pro_attibutes)==4){
					$optionId = $this->addNewAttributeOption('color',trim($pro_attibutes[1]));
					$optionSizeId = $this->addNewAttributeOption('size',trim($pro_attibutes[0]));
					$optionBandId = $this->addNewAttributeOption('bands',trim($pro_attibutes[2]));
					$optionMpnId = $this->addNewAttributeOption('mpn',trim($pro_attibutes[3])); 
					}
				if(count($pro_attibutes) ==3){
					$optionId = $this->addNewAttributeOption('color',trim($pro_attibutes[1]));
					$optionSizeId = $this->addNewAttributeOption('size',trim($pro_attibutes[0]));
					$optionMpnId = $this->addNewAttributeOption('mpn',trim($pro_attibutes[2]));
					$pro_attibutes[3]='';
					$optionBandId=$this->addNewAttributeOption('bands','No Band');
					
					}	
			if($proRow['Disable Inventory Control']=='false') $stock_manage='1'; else $stock_manage='0';
			if($proRow['Backorder_able']=='true') $backOrder='1'; else $backOrder='0';
			$simpleProduct
			->setWebsiteIds(array('1')) //website ID the product is assigned to, as an array
			->setAttributeSetId(4) //ID of a attribute set named 'default'
			->setTypeId('simple') //product type
			->setCreatedAt(strtotime('now')) //product creation time
			->setParentproductid($proRow['Product_Id']) ///////Specification 
			->setColor($optionId)
			->setSize($optionSizeId)
			->setBands($optionBandId)
			->setMpn($optionMpnId)
			->setSku($proRow['SKU']) //SKU
			->setName($mainProductName.'-'.$pro_attibutes[0].'-'.$pro_attibutes[1].'-'.$pro_attibutes[2].'-'.$pro_attibutes[3]) //product name
			->setWeight($proRow['Add_to_Base_Weight'])
			->setStatus(1) //product status (1 - enabled, 2 - disabled)
			->setTaxClassId(0) //tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
			->setVisibility('1') //do no visible individully visibility
			->setManufacturer('') //manufacturer id
			->setManufacturerPn($proRow['Attribute_Specifications_ManufacturerPN'])
			->setNewsFromDate('') //product set as new from
			->setNewsToDate('') //product set as new to
			->setMsrpEnabled(1) //enable MAP
			->setMsrpDisplayActualPriceType(4) //display actual price (1 - on gesture, 2 - in cart, 3 - before order confirmation, 4 - use config)
			->setMsrp('') //Manufacturer's Suggested Retail Price
			->setCountryOfManufacture('') //country of manufacture (2-letter country code)
			->setPrice($proRow['Add_to_Base_Price']) //price in form 11.22
			->setCost($proRow['Add_to_Base_Price']) //price in form 11.22
			->setSpecialPrice('') //special price in form 11.22
			->setMetaTitle($proRow['Item Name'])
			->setMetaKeyword($proRow['Item Name'])
			->setMetaDescription('-')
			->setDescription('-')
			->setShortDescription('-')
				
			->setStockData(array(
			'use_config_manage_stock' => 0, //'Use config settings' checkbox
			'manage_stock' => $stock_manage, //manage stock
			'min_sale_qty' => $proRow['Min_Purchase_Amount'], //Minimum Qty Allowed in Shopping Cart
			'max_sale_qty' =>$proRow['Max_Purchase_Amount'], //Maximum Qty Allowed in Shopping Cart
			'is_in_stock' => 1, //Stock Availability
			'qty' => $proRow['Current_Inventory'] //qty
			)
			)
			->setData('stock_id', 1)
			->setData('qty', $proRow['Current_Inventory'])
			->setData('use_config_min_qty', 1)
			->setData('use_config_backorders', $backOrder)
			->setData('min_sale_qty', 1)
			->setData('use_config_min_sale_qty', 1)
			->setData('use_config_max_sale_qty', 1)
			->setData('is_in_stock', 1)
			->setData('use_config_notify_stock_qty', 1)
			->setData('manage_stock', $stock_manage)
			//->setData('size',$optionSizeId)
			->setcategoriIds($proRow['Item_Category_Mapping']) //categori_ids
			//->setCategoryIds(array(2,3)) //assign product to categories
			->setChilditemid($proRow['Id'])
			->setColorSpec($proRow['Attribute_Specifications_Color'])
			->setSizeSpec($proRow['Attribute_Specifications_Size'])
			->setSizeWeightSpec($proRow['Attribute_Specifications_SizeWeight'])
			->setSpecCompression($proRow['Attribute_Specifications_Compression'])
			->setSpecGender($proRow['Attribute_Specifications_Gender'])
			->setSpecLength($proRow['Attribute_Specifications_Length'])
			->setManufacturerStyleDescription($proRow['Attribute_Specifications_Manufacturer_Style_Description'])
			->setSpecSize($proRow['Attribute_Specifications_Size'])
			->setInventoryProductType($proRow['Attribute_Inventory_ProductType'])
			->setInventoryHcpcs($proRow['Attribute_Inventory_HCPCS'])
			->setLengthOptionCrotchFly($proRow['Attribute_Specifications_Length_Option_Crotch_Fly'])
			->setExtraWidth($proRow['Attribute_Specifications_Length_Option_Extra_Width'])
			->setFootToeFingers($proRow['Attribute_Specifications_Length_Option_Foot_Toe_Fingers'])
			->setLeftRight($proRow['Attribute_Specifications_Length_Option_Left_Right'])
			->setOptionOther($proRow['Attribute_Specifications_Length_Option_Other'])
			->setSizeScale($proRow['Attribute_Specifications_Length_Option_Size_Scale'])
			->setCustomField($proRow['Custom_Field_2'])
			->setLinkTitleTag($proRow['Link Title Tag'])
			->setManufacturerBackEndName($proRow['Manufacturer_Back_End_Name'])
			->setUpc($proRow['UPC']);
			
			$simpleProduct->save();
			
	
			//This section is what was required.
			$stockStatus = Mage::getModel('cataloginventory/stock_status');
			$stockStatus->assignProduct($simpleProduct);
			$stockStatus->saveProductStatus($simpleProduct->getId(), 1);
			
			array_push($array_of_simple_product_ids,$simpleProduct->getId());
			
			$_product = Mage::getModel('catalog/product')->load($mageProductID);
			$ids = $_product->getTypeInstance()->getUsedProductIds();
						if($ids !=''){
							$newids = array();
							foreach ( $ids as $id ) {
								$newids[$id] = 1;
							}
						}
				$newids[$simpleProduct->getId()] = 1;
				Mage::getResourceModel('catalog/product_type_configurable')->saveProducts($_product, array_keys($newids));
				echo"Product Added".$simpleProduct->getId();	
				//echo"<br>Product already exists".$smpProduct->getId();	
				}
			
			} ///////  
			
		  }
	}
				$indexCollection = Mage::getModel('index/process')->getCollection();
					foreach ($indexCollection as $index) {
					/* @var $index Mage_Index_Model_Process */
					$index->reindexAll();
					 return ;
					}
	
			
	}

/**  returns the option id for any attribute code by passing the label                           
 $attribute_code e.g. 'size','color','article'  
$label e.g. 'M','Red','art_21312'   
     
function getoptionsId($attribute_code,$label) {        

  	 $attribute_model = Mage::getModel('eav/entity_attribute'); 
  	 $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table') ;                 
  	 $attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_code);
	 $attribute = $attribute_model->load($attribute_code);
	 $attribute_table = $attribute_options_model->setAttribute($attribute);
	 $options = $attribute_options_model->getAllOptions(false);
 
         foreach($options as $option) {
                if ($option['label'] == $label){
                    $optionId=$option['value'];
                    break;
                }
         }
		 return $optionId;
    }*/ 
	
function addNewAttributeOption($arg_attribute,$arg_value){
//$arg_value = 'Black';
//$arg_attribute = 'color'
$attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $arg_attribute);
        $flag=0;

    foreach ( $attribute->getSource()->getAllOptions(true, true) as $option )
    {

        if($arg_value == $option['label'])
        {

            unset($attribute);
            $flag=1;
            return $option['value'] ; 
        }
    }
    if($flag==0){
        $attribute_model        = Mage::getModel('eav/entity_attribute');
        $attribute_options_model= Mage::getModel('eav/entity_attribute_source_table') ;
        $attribute_code         = $attribute_model->getIdByCode('catalog_product', $arg_attribute);
        $attribute              = $attribute_model->load($attribute_code);
        $attribute_table        = $attribute_options_model->setAttribute($attribute);
        $options                = $attribute_options_model->getAllOptions(false);
        $value['option'] 		= array($arg_value,$arg_value);
        $result 				= array('value' => $value);
		
        $attribute->setData('option',$result);
        $attribute->save();

        $attribute = Mage::getModel('eav/config')->getAttribute('catalog_product', $arg_attribute);
        foreach ( $attribute->getSource()->getAllOptions(true, true) as $option ){
            if($arg_value == $option['label']){
                unset($attribute);
                return $option['value'] ; 
            }
        }

    }
}
  
}////Class closing 
/************************/// calling class from here  ***************************************************************/
$my_controller = new Pwsmage_HkcsvImporter_Model_Job;
$my_controller->get_xlsfields();

/***************************************************************************************/
