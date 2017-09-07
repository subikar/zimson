<?php

class Sm_Furnicom_Helper_Config extends Mage_Core_Helper_Abstract
{
	
	public function getConfig($name = null)
	{
		if (!$name) return null;

		return Mage::getStoreConfig($name, Mage::app()->getStore()->getId());
	}

	public function getGeneral($name)
	{
		return $this->getConfig('furnicom_cfg/general/' . $name);
	}
	
	public function getThemeLayout($name)
	{
		return $this->getConfig('furnicom_cfg/theme_layout/' . $name);
	}
	
	public function getDetailFurnicom($name)
	{
		return $this->getConfig('furnicom_cfg/detail_furnicom/' . $name);
	}
	
	public function getProductSetting($name)
	{
		return $this->getConfig('furnicom_cfg/product_setting/' . $name);
	}
	
	public function getAdvanced($name)
	{
		return $this->getConfig('furnicom_cfg/advanced/' . $name);
	}
	
	public function getSocialSetting($name)
	{
		return $this->getConfig('furnicom_cfg/social_setting/' . $name);
	}
	
	public function getRichSnippetsSetting($name)
	{
		return $this->getConfig('furnicom_cfg/rich_snippets_setting/' . $name);
	}
	
	public function getProductListing($name)
	{
		return $this->getConfig('furnicom_cfg/product_listing/' . $name);
	}
	
}