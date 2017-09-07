<?php
/*------------------------------------------------------------------------
 # SM Super Categories - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
 
$helper = Mage::helper('supercategories/data');
if ($this->_isAjax()) {
	$catid = $this->getRequest()->getPost('categoryid');
	$start = (int)$this->getRequest()->getPost('ajax_reslisting_start');
	$list = $this->getListCriterionFilter($catid);
	$child_items = $list[$catid]->child;
}

$small_image_config = array(
	'type' => $this->_getConfig('imgcfg_type'),
	'width' => $this->_getConfig('imgcfg_width'),
	'height' => $this->_getConfig('imgcfg_height'),
	'quality' => 90,
	'function' => ($this->_getConfig('imgcfg_function') == 'none') ? null : 'resize',
	'function_mode' => ($this->_getConfig('imgcfg_function') == 'none') ? null : substr($this->_getConfig('imgcfg_function'), 7),
	'transparency' => $this->_getConfig('imgcfg_transparency', 1) ? true : false,
	'background' => $this->_getConfig('imgcfg_background'));

if (!empty($child_items)) {
	$k = $this->getRequest()->getPost('ajax_reslisting_start', 0);
	foreach ($child_items as $item) {
		$k++; ?>
		<div class="spc-item new-spc-item">
			<div class="item-inner">
				<?php
				if ( $this->_getConfig('product_image_display', 1) == 1 ) {
					?>
					<div class="item-image">
						<a class="rspl-image">
							<img title="<?php echo $item->title; ?>"
							     alt="<?php echo $item->title; ?>"
							     src="<?php echo $item->_image; ?>"/>
						</a>
					</div>
				<?php
				}?>
				<?php if ($this->_getConfig('product_title_display', 1) == 1) {
					?>
					<div class="item-title ">
						<a href="<?php echo $item->link ?>" <?php echo $helper::parseTarget($this->_getConfig('product_links_target', '_self')) ?>
						   title="<?php echo $item->title ?>">
							<?php echo $helper->truncate($item->title, $this->_getConfig('product_title_maxlength', 25)); ?>
						</a>
					</div>
				<?php } ?>
				<?php if ((int)$this->_getConfig('product_price_display', 1)) {
					?>
					<div class="item-price">
						<div class="sale-price">
							<?php echo $this->getPriceHtml($item, true); ?>
						</div>
					</div>
				<?php }?>
				<?php if ((int)$this->_getConfig('product_reviews_count', 1)) {
					?>
					<div class="item-review">
						<?php echo $this->getReviewsSummaryHtml($item, true, true);?>
					</div>
				<?php }?>

				<?php if ($this->_getConfig('product_description_display', 1) == 1 && $helper::_trimEncode($item->description) != '') { ?>
					<div class="item-desc">
						<?php echo $helper->truncate($item->description, $this->_getConfig('product_description_maxlength', 200)); ?>
					</div>
				<?php } ?>

				<?php if ((int)$this->_getConfig('product_addcart_display', 1)) { ?>
					<?php if($item->isSaleable()){ ?>
						<div class="item-addtocart"><button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="button btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($item) ?>')"><span><span><?php echo $this->__('Add to Cart') ?></span></span></button></div>
					<?php }else { ?>
						<p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
					<?php }}?>
				<?php if ((int)$this->_getConfig('product_addwishlist_display', 1) || (int)$this->_getConfig('product_addcompare_display', 1)) :?>
				<ul class="add-to-links">
					<?php if ($this->helper('wishlist')->isAllow() && (int)$this->_getConfig('product_addwishlist_display', 1)) : ?>
						<li><a href="<?php echo $this->helper('wishlist')->getAddUrl($item) ?>" class="link-wishlist"><?php echo $this->__('Add to Wishlist') ?></a></li>
					<?php endif; ?>
					<?php
					if( (int)$this->_getConfig('product_addcompare_display', 1) ):
					if( $_compareUrl = $this->getAddToCompareUrl($item) ): ?>
						<li><a href="<?php echo $_compareUrl ?>" class="link-compare"><?php echo $this->__('Add to Compare') ?></a></li>
					<?php endif;
					endif;
					?>
				</ul>
				<?php endif;?>

				<?php if ((int)$this->_getConfig('product_readmore_display', 1)) { ?>
					<div class="item-readmore">
						<a href="<?php echo $item->link; ?>"
						   title="<?php echo $item->title ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')); ?> >
							<?php echo $this->_getConfig('product_readmore_text', 'Detail'); ?>
						</a>
					</div>
				<?php } ?>
			</div>
		</div>

		<?php /*$clear = 'clr1';
		if ($k % 2 == 0) $clear .= ' clr2';
		if ($k % 3 == 0) $clear .= ' clr3';
		if ($k % 4 == 0) $clear .= ' clr4';
		if ($k % 5 == 0) $clear .= ' clr5';
		if ($k % 6 == 0) $clear .= ' clr6';
		*/
		?>
		<!-- <div class="<?php echo $clear; ?>"></div> -->
	<?php
	}
}?>

