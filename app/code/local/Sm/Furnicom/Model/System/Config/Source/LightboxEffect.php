<?php
/*------------------------------------------------------------------------
 # SM Zen - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Furnicom_Model_System_Config_Source_LightboxEffect
{
	public function toOptionArray()
	{	
		return array(
			array('value'=>'none', 'label'=>Mage::helper('furnicom')->__('None')),
			array('value'=>'fade', 'label'=>Mage::helper('furnicom')->__('Fade')),
			array('value'=>'elastic', 'label'=>Mage::helper('furnicom')->__('Elastic'))
		);
	}
}
