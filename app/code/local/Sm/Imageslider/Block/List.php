<?php

/*------------------------------------------------------------------------
 # SM Image Slider - Version 1.1.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
class Sm_Imageslider_Block_List extends Mage_Catalog_Block_Product_Abstract
{
	protected $_config = null;
	protected $_storeId = null;
	protected $hash = null;

	public function __construct($attr)
	{
		parent::__construct($attr);
		$this->_config = $this->_getCfg($attr);
		if (!$this->_getConfig('active', 1)) return;
		$this->_storeId = Mage::app()->getStore()->getId();
	}

	public function _getCfg($attr = null)
	{
		// get default config.xml
		$defaults = array();
		$def_cfgs = Mage::getConfig()->loadModulesConfiguration('config.xml')->getNode('default/imageslider_cfg')->asArray();
		if (empty($def_cfgs)) return;
		$groups = array();
		foreach ($def_cfgs as $def_key => $def_cfg) {
			$groups[] = $def_key;
			foreach ($def_cfg as $_def_key => $cfg) {
				$defaults[$_def_key] = $cfg;
			}
		}

		// get configs after change
		$_configs = (array)Mage::getStoreConfig("imageslider_cfg");
		if (empty($_configs)) return;
		$cfgs = array();

		foreach ($groups as $group) {
			$_cfgs = Mage::getStoreConfig('imageslider_cfg/' . $group . '');
			foreach ($_cfgs as $_key => $_cfg) {
				$cfgs[$_key] = $_cfg;
			}
		}

		// get output config
		$configs = array();
		foreach ($defaults as $key => $def) {
			if (isset($defaults[$key])) {
				$configs[$key] = $cfgs[$key];
			} else {
				unset($cfgs[$key]);
			}
		}
		$this->_config = ($attr != null) ? array_merge($configs, $attr) : $configs;
		return $this->_config;
	}

	public function _getConfig($name = null, $value_def = null)
	{
		if (is_null($this->_config)) $this->_getCfg();
		if (!is_null($name)) {
			$value_def = isset($this->_config[$name]) ? $this->_config[$name] : $value_def;
			return $value_def;
		}
		return $this->_config;
	}


	public function _setConfig($name, $value = null)
	{
		if (is_null($this->_config)) $this->_getCfg();
		if (is_array($name)) {
			$this->_config = array_merge($this->_config, $name);
			return;
		}
		if (!empty($name) && isset($this->_config[$name])) {
			$this->_config[$name] = $value;
		}
		return true;
	}


	protected function _toHtml()
	{
		if (!$this->_getConfig('active', 1)) return;
		$use_cache = (int)$this->_getConfig('use_cache');
		$cache_time = (int)$this->_getConfig('cache_time');
		$folder_cache = Mage::getBaseDir('cache');
		$folder_cache = $folder_cache.'/Sm/Imageslider/';
		if(!file_exists($folder_cache))
			mkdir ($folder_cache, 0777, true);

		if (!class_exists('Cache_Lite'))
			require_once($this->getBaseDir() .  'lib' . DS .  'Sm' .DS . 'Imageslider' .DS . 'Cache_Lite' . DS . 'Lite.php');

		$options = array(
			'cacheDir' => $folder_cache,
			'lifeTime' => $cache_time
		);
		$Cache_Lite = new Cache_Lite($options);
		if ($use_cache){
			$this->hash = md5( serialize($this->_getConfig()) );
			if ($data = $Cache_Lite->get($this->hash)) {
				return  $data;
			} else {
				$template_file = $this->getTemplate();
				$template_file = (!empty($template_file)) ? $template_file : "sm/imageslider/default.phtml";
				$this->setTemplate($template_file);
				$data = parent::_toHtml();
				$Cache_Lite->save($data);
			}
		}
		else{
			if(file_exists($folder_cache))
				$Cache_Lite->_cleanDir($folder_cache);
			$template_file = $this->getTemplate();
			$template_file = (!empty($template_file)) ? $template_file : "sm/imageslider/default.phtml";
			$this->setTemplate($template_file);
		}
//		$template_file = "sm/imageslider/default.phtml";
//		$this->setTemplate($template_file);
		//Zend_Debug::dump(get_class($this), 'get_class');die;
		return parent::_toHtml();
	}

	public function _getProductMedia()
	{
		$items = $this->_getConfig('product_additem');
		//print_r($items); exit;
		$items = unserialize($items);
		if (empty($items)) return;
		return $items;
	}

	public function _getProducts()
	{
		$helper = Mage::helper('imageslider/data');
		$items = $this->_getProductMedia();

		$image_config = array(
			'width' => (int)$this->_getConfig('imgcfg_width', 200),
			'height' => $this->_getConfig('imgcfg_height', null),
			'constrainOnly' => (bool)$this->_getConfig('imgcfg_constrainOnly'),
			'keepAspectRatio' => (bool)$this->_getConfig('imgcfg_keepAspectRatio'),
			'keepFrame' => (bool)$this->_getConfig('imgcfg_keepFrame'),
			'keepTransparency' => (bool)$this->_getConfig('imgcfg_keepTransparency'),
			'background' => (string)$this->_getConfig('imgcfg_background'),
			'function' => (int)$this->_getConfig('imgcfg_function')
		);

		$list = array();
		$i = 0;
		if (!empty($items)) {
			foreach ($items as $item) {
				$i++;
				$item['id'] = $i;
				if ($item['image'] != '' && $item['title'] != '') {
					$item['image'] = (strpos($item['image'], 'http') !== false) ? $item['image'] : Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $item['image'];
					if ($this->_getConfig('imgcfg_function') == 1) {
						$item['_image'] = $helper->_resizeImage($item['image'], $image_config);
					} else {
						$item['_image'] = $item['image'];
					}

					if (@getimagesize($item['_image']) == false) {
						if ($this->_getConfig('imgcfg_function') == 1) {
							$img_placeholder = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/' . $this->_getConfig('imgcfg_placeholder');
							$item['_image'] = $helper->_resizeImage($img_placeholder, $image_config);
						} else {
							$item['_image'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) . 'frontend/base/default/' . $this->_getConfig('imgcfg_placeholder');
						}
					}

					$_title = $helper->_cleanText($item['title']);
					$item['title'] = $helper->truncate($_title, $this->_getConfig('product_title_maxlength'));

					$description = $helper->_cleanText($item['content']);
					$description = $helper->truncate($description, $this->_getConfig('product_description_maxlength'));
					$item['_description'] = $description;
					$list[] = (object)$item;
				}
			}
		}
		return $list;
	}

	/*
	 * Check Categories is Active ?
	 */
	private function _getCatActive($catids = null, $orderby = true)
	{
		if (is_null($catids)) {
			$catids = $this->_getConfig('product_category');
		}
		!is_array($catids) && $catids = preg_split('/[\s|,|;]/', $catids, -1, PREG_SPLIT_NO_EMPTY);
		if (empty($catids)) return;
		$catidsall = array();
		$categoryIds = array('in' => $catids);
		$categories = Mage::getModel('catalog/category')
			->getCollection()
			->addAttributeToSelect('*')
			->setStoreId($this->_storeId)
			->addAttributeToFilter('entity_id', $categoryIds)
			->addIsActiveFilter();

		if ($orderby) {
			$attribute = 'random'; // name | position | entry_id | random
			$dir = 'ASC';
			switch ($attribute) {
				case 'name':
				case 'position':
				case 'entry_id':
					$categories->addAttributeToSort($attribute, $dir);
					break;
				case 'random':
					$categories->getSelect()->order(new Zend_Db_Expr('RAND()'));
					break;
				default:
			}
		}

		$_catids = array();
		if (empty($categories)) return;
		foreach ($categories as $category) {
			$_catids[] = $category->getId();
		}

		return $_catids;
	}

	/*
	 * array $catids
	 * bool $allcat = true return with parentid else return only childId
	 * int $limitCat = 0 return unlimit else return limit
	 * int $levels =  1 
	 * return $catids
	 */

	private function _childCategory($catids, $allcat = true, $limitCat = 0, $levels = 0)
	{
		!is_array($catids) && settype($catids, 'array');
		$additional_catids = array();
		if (!empty($catids)) {

			foreach ($catids as $catid) {
				$category_model = Mage::getModel('catalog/category');
				$_category = $category_model->load($catid);
				$levelCat = $_category->getLevel();
				$subcategories = $category_model->getCollection()
					->addAttributeToSelect('*')
					->addFieldToFilter('parent_id', $catid)
					->addIsActiveFilter()
					->addAttributeToSort('position', 'ASC')
					->setPageSize($limitCat)->load();
				foreach ($subcategories as $each_subcat) {
					$condition = ($each_subcat->getLevel() - $levelCat <= $levels);
					if ($condition) {
						$additional_catids[] = $each_subcat->getId();
					}
				}
			}

			$catids = $allcat ? array_unique(array_merge($catids, $additional_catids)) : array_unique($additional_catids);
		}

		return $catids;
	}

	/*
	 * return countProduct;
	 */

	protected function _countProducts($catids)
	{
		!is_array($catids) && settype($catids, 'array');
		$countProduct = $this->_getProductsBasic($catids, true);
		return $countProduct;
	}

	public function _getProductCatalog()
	{
		$catids = $this->_getConfig('product_category');
		$inlucde = (int)$this->_getConfig('child_category_products', 1);
		$level = (int)$this->_getConfig('max_depth', 1);
		if ($catids == null) return;
		$_catids = $this->_getCatActive($catids);
		$_catids = ($inlucde && $level > 0) ? $this->_childCategory($_catids, true, 0, $level) : $_catids;

		if (empty($_catids)) return;
		$products = $this->_getProductsBasic($_catids);
		return $products;

	}

	public function _getProductsIDs()
	{
		$catids = $this->_getConfig('product_ids');
		!is_array($catids) && $catids = preg_split('/[\s|,|;]/', $catids, -1, PREG_SPLIT_NO_EMPTY);
		if (empty($catids)) return;
		$products = array();
		$products = Mage::getModel('catalog/product')
			->getCollection()
			->addAttributeToSelect('*');
		$products->addIdFilter($catids);
		$products->getSelect()->order(new Zend_Db_Expr('FIELD(e.entity_id, ' . implode(',', $catids) . ')'));
		return $products;
	}

	public function _getProductsBasic($catids, $countProduct = false)
	{
		$collection = array();
		$productIds = array();
		!is_array($catids) && settype($catids, 'array');
		if (!empty($catids)) {
			$attributes = Mage::getSingleton('catalog/config')->getProductAttributes();
			$categoryIds = array('in' => $catids);
			$collection = Mage::getModel('catalog/product')
				->getCollection()
				->addAttributeToSelect($attributes)
				->addAttributeToSelect('featured')
				->addAttributeToSelect('*')
				->addMinimalPrice()
				->addFinalPrice()
				->addTaxPercents()
				->addTierPriceData()
				->addUrlRewrite()
				->setStoreId($this->_storeId)
				->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
				->addAttributeToFilter('category_id', $categoryIds);
			if ($this->_getFeaturedProduct($collection) == false) return null;
			$this->_getFeaturedProduct($collection);
			Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
			Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
			$this->_addViewsCount($collection);
			$this->_addOrderedCount($collection);
			$this->_addReviewsCount($collection);
			$collection->getSelect()->group('entity_id')->distinct(true);
			$this->_getOrder($collection);
			$collection->clear();
			if ($countProduct) return $collection->count();
			$_start = 0;
			$_limit = (int)$this->_getConfig('product_limitation', 5);
			$_limit = $_limit <= 0 ? 0 : $_limit;
			if ($_limit >= 0) {
				$collection->getSelect()->limit($_limit, $_start);
			}
		}
		return $collection;
	}

	/*
	 *	Get Featured Product
	 */
	private function _getFeaturedProduct(& $collection)
	{
		$filter = (int)$this->_getConfig('product_featured', 0);
		$attributeModel = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'featured');
		switch ($filter) {
			// Show All
			case '0':
				break;
			// None Featured	
			case '1':
				if ($attributeModel->usesSource()) {
					$collection->addAttributeToFilter('featured', array('null' => 1), 'left');
				}
				break;
			// Only Featured	
			case '2':
				if ($attributeModel->usesSource()) {
					$collection->addAttributeToFilter('featured', array('eq' => 1));
				} else {
					return false;
				}
				break;
		}
		return $collection;
	}

	/*
	 *	Get Lastest Product
	 */
	private function _getLastestProduct($collection)
	{
		$todayStartOfDayDate = Mage::app()->getLocale()->date()
			->setTime('00:00:00')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

		$todayEndOfDayDate = Mage::app()->getLocale()->date()
			->setTime('23:59:59')
			->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

		$collection = $this->_addProductAttributesAndPrices($collection)
			->addStoreFilter()
			->addAttributeToFilter('news_from_date',
				array('or' => array(
					0 => array('date' => true, 'to' => $todayEndOfDayDate),
					1 => array('is' => new Zend_Db_Expr('null'))
				)), 'left')
			->addAttributeToFilter('news_to_date',
				array('or' => array(
					0 => array('date' => true, 'from' => $todayStartOfDayDate),
					1 => array('is' => new Zend_Db_Expr('null'))
				)), 'left')
			->addAttributeToSort('news_from_date', 'DESC');
		return $collection;
	}

	/*
	 *	Get Order
	 */
	private function _getOrder($collection)
	{
		$attribute = (string)$this->_getConfig('product_order_by', 'name');
		$dir = (string)$this->_getConfig('product_order_dir', 'ASC');
		switch ($attribute) {
			case 'position':
			case 'entity_id':
			case 'name':
			case 'created_at':
				$collection->setOrder($attribute, $dir);
				break;
			case 'price':
				$collection->getSelect()->order('final_price ' . $dir . '');
				break;
			case 'random':
				$collection->getSelect()->order(new Zend_Db_Expr('RAND()'));
				break;
			case 'lastest_product':
				$this->_getLastestProduct($collection);
				break;
			case 'top_rating':
				$collection->getSelect()->order('num_rating_summary DESC');
				break;
			case 'most_reviewed':
				$collection->getSelect()->order('num_reviews_count DESC');
				break;
			case 'most_viewed':
				$collection->getSelect()->order('num_view_counts DESC');
				break;
			case 'best_sellers':
				$collection->getSelect()->order('ordered_qty DESC ');
				break;
			default:
		}
		return $collection;
	}


	private function _addOrderedCount(& $collection)
	{
		$order_item_table = Mage::getSingleton('core/resource')->getTableName('sales/order_item');
		$select = Mage::getSingleton('core/resource')->getConnection('core_read')
			->select()
			->from($order_item_table, array('product_id', 'ordered_qty' => 'SUM(`qty_ordered`)'))
			->group('product_id');

		$collection->getSelect()
			->joinLeft(array('bs' => $select),
				'bs.product_id = e.entity_id');
	}

	// add views_count
	private function _addViewsCount(& $collection)
	{
		$reports_event_table = Mage::getSingleton('core/resource')->getTableName('reports/event');
		$select = Mage::getSingleton('core/resource')->getConnection('core_read')
			->select()
			->from($reports_event_table, array('*', 'num_view_counts' => 'COUNT(`event_id`)'))
			->where('event_type_id = 1')
			->group('object_id');
		$collection->getSelect()
			->joinLeft(array('mv' => $select),
				'mv.object_id = e.entity_id');

	}

	// add reviews_count and rating_summary
	private function _addReviewsCount(& $collection)
	{
		$review_summary_table = Mage::getSingleton('core/resource')->getTableName('review/review_aggregate');
		$collection->getSelect()
			->joinLeft(
				array("ra" => $review_summary_table),
				"e.entity_id = ra.entity_pk_value AND ra.store_id=" . $this->_storeId,
				array(
					'num_reviews_count' => "ra.reviews_count",
					'num_rating_summary' => "ra.rating_summary"
				)
			);
	}
}