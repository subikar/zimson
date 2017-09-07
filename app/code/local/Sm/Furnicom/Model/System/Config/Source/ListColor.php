<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Furnicom_Model_System_Config_Source_ListColor
{
	public function toOptionArray()
	{	
		return array(
		array('value'=>'brown', 'label'=>Mage::helper('furnicom')->__('Brown')),
		array('value'=>'blue', 'label'=>Mage::helper('furnicom')->__('Blue')),
		array('value'=>'red', 'label'=>Mage::helper('furnicom')->__('Red')),
		array('value'=>'orange', 'label'=>Mage::helper('furnicom')->__('Orange')),
		array('value'=>'green', 'label'=>Mage::helper('furnicom')->__('Green'))
		);
	}
}
