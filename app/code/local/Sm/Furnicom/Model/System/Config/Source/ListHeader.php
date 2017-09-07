<?php
/*------------------------------------------------------------------------
 # SM Furnicom - Version 1.1
 # Copyright (c) 2013 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Furnicom_Model_System_Config_Source_ListHeader
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'1', 'label'=>Mage::helper('furnicom')->__('Header 1')),
			array('value'=>'2', 'label'=>Mage::helper('furnicom')->__('Header 2')),
			array('value'=>'3', 'label'=>Mage::helper('furnicom')->__('Header 3')),
			array('value'=>'4', 'label'=>Mage::helper('furnicom')->__('Header 4')),
			array('value'=>'5', 'label'=>Mage::helper('furnicom')->__('Header 5'))
		);
	}
}
