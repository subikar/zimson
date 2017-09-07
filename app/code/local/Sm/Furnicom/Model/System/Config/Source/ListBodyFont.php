<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Furnicom_Model_System_Config_Source_ListBodyFont
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'', 'label'=>Mage::helper('furnicom')->__('')),
			array('value'=>'Arial', 'label'=>Mage::helper('furnicom')->__('Arial')),
			array('value'=>'Arial Black', 'label'=>Mage::helper('furnicom')->__('Arial-black')),
			array('value'=>'Courier New', 'label'=>Mage::helper('furnicom')->__('Courier New')),
			array('value'=>'Georgia', 'label'=>Mage::helper('furnicom')->__('Georgia')),
			array('value'=>'Tahoma', 'label'=>Mage::helper('furnicom')->__('Tahoma')),
			array('value'=>'Times New Roman', 'label'=>Mage::helper('furnicom')->__('Times New Roman')),
			array('value'=>'Trebuchet', 'label'=>Mage::helper('furnicom')->__('Trebuchet')),
			array('value'=>'Verdana', 'label'=>Mage::helper('furnicom')->__('Verdana')),
			array('value'=>'maingooglefont', 'label'=>Mage::helper('furnicom')->__('Google Font'))
		);
	}
}
