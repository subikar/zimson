<?php
/*------------------------------------------------------------------------
 # SM Super Categories - Version 1.0.0
 # Copyright (c) 2014 YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/
 
$helper = Mage::helper('supercategories/data');
$config = Mage::helper('furnicom/config');
$effect_style = $config->getProductListing('effect_style');
$display_nav = $config->getProductListing('show_nav');
$display_dot = $config->getProductListing('show_dot');

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

$img_product_w = $this->_getConfig('product_image_width');
$img_product_h = $this->_getConfig('product_image_height');
$limitation = $this->_getConfig('product_limitation');
//$row_items = $this->_getConfig('row_items');
$row_items = 2;

if (!empty($child_items)) {
	$k = $this->getRequest()->getPost('ajax_reslisting_start', 0);
?>

<?php
	$j = 0;
	$i = 0;
	foreach ($child_items as $item) {
		$k++; 

		if($j<=$limitation) {
	        if($j % $row_items == 0 ) {
	            echo '<div class="col-item">';
	        }
	    }

        ?>
			<div class="item no-padding <?php echo $j;?>">
				<div class="item-inner">
					<!-- BOX IMAGE -->
					<div class="box-image">
						<?php if($effect_style == 'default'){?>
							<div class="effect-default">
								<a href="<?php echo $item->link ?>" class="product-image">
									<img src="<?php echo $this->helper('catalog/image')->init($item, 'small_image')->resize($img_product_w, $img_product_h); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($item, 'small_image'), null, true) ?>" />
								</a>
							</div>
							<?php } else if($effect_style == 'thumbs'){?>
							<div class="effect-thumbs">
								<a href="<?php echo $item->link ?>" class="product-image">
									<img src="<?php echo $this->helper('catalog/image')->init($item, 'small_image')->resize($img_product_w, $img_product_h); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($item, 'small_image'), null, true) ?>" />
									<?php if($item->getThumbnail() != $item->getSmallImage()) { ?> 
										<img class="second-image" src="<?php echo $this->helper('catalog/image')->init($item, 'thumbnail')->resize($img_product_w, $img_product_h); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($item, 'small_image'), null, true) ?>" />
									<?php } ?>
								</a>
							</div>
							<?php } else if($effect_style == 'slider'){?>
							<div class="effect-slider">
								<div class="image-slider-product">
									<div class="super-img-slider">
										<?php $_media = Mage::getModel('catalog/product')->load($item->getId())->getMediaGalleryImages() ?>
										<?php foreach($_media as $_img): ?>
											<div class="item_super">
												<a href="<?php echo $item->link ?>" title="<?php echo $item->title;?>">
													<img  src="<?php echo $this->helper('catalog/image')->init($item, 'image', $_img->getFile())->resize(470,608); ?>" alt="<?php echo $this->stripTags($this->getImageLabel($item, 'small_image'), null, true); ?>" />
												</a>
											</div>
										<?php endforeach; ?>
									</div>
								</div>
								
							</div>
							<?php } ?>	

						<!--LABEL PRODUCT-->
						<div class="box-label">
							<?php
	                        $id_product = Mage::getModel('catalog/product')->load($item->getId());
	                        $specialprice = $id_product->getSpecialPrice();
	                        $specialPriceFromDate = $id_product->getSpecialFromDate();
	                        $specialPriceToDate = $id_product->getSpecialToDate();
	                        $today = time();

	                        if ($specialprice) {
	                            if ($today >= strtotime($specialPriceFromDate) && $today <= strtotime($specialPriceToDate) || $today >= strtotime($specialPriceFromDate) && is_null($specialPriceToDate)) { ?>
	                                <div class="label-product label-sale">
										<span class="sale-product-icon">
											<?php
												$saleof = abs(($specialprice / ($item->getPrice())) * 100 - 100);
												print_r('-' . floor($saleof) . '%');
											?>
										</span>
	                                </div>
	                            <?php }
	                        }?>


	                        <?php
	                        $now = date("Y-m-d");
	                        $newsFrom = substr($item->getData('news_from_date'), 0, 10);
	                        $newsTo = substr($item->getData('news_to_date'), 0, 10);
	                        if ($newsTo != '' || $newsFrom != '') {
	                            if (($newsTo != '' && $newsFrom != '' && $now >= $newsFrom && $now <= $newsTo) || ($newsTo == '' && $now >= $newsFrom) || ($newsFrom == '' && $now <= $newsTo)) {?>
	                                    <div class="label-product label-new">
											<span class="new-product-icon"><?php echo $this->__('New'); ?></span>
										</div>
	                            <?php }
	                        } ?>
						</div>
						<!--END LABEL PRODUCT-->
					</div>
					<!-- BOX INFO -->
					<div class="box-info">
						
						<!-- TITLE -->
						<?php if ($this->_getConfig('product_title_display', 1) == 1) {
								?>
							<div class="product-name">
								<a href="<?php echo $item->link ?>" <?php echo $helper::parseTarget($this->_getConfig('product_links_target', '_self')) ?>
								   title="<?php echo $item->title ?>">
									<?php echo $helper->truncate($item->title, $this->_getConfig('product_title_maxlength', 25)); ?>
								</a>
							</div>
						<?php } ?>
						<!-- PRICE -->
						<?php if ((int)$this->_getConfig('product_price_display', 1)) {
							?>
							<div class="item-price">
								<div class="sale-price">
									<?php echo $this->getPriceHtml($item, true); ?>
								</div>
							</div>
						<?php }?>
						<!-- REVIEW -->
						<?php if ((int)$this->_getConfig('product_reviews_count', 1)) {
							?>
							<div class="item-review">
								<?php echo $this->getReviewsSummaryHtml($item, true, true);?>
							</div>
						<?php }?>
						
						<!-- DESC -->
						<?php if ($this->_getConfig('product_description_display', 1) == 1 && $helper::_trimEncode($item->description) != '') { ?>
							<div class="item-desc">
								<?php echo $helper->truncate($item->description, $this->_getConfig('product_description_maxlength', 200)); ?>
							</div>
						<?php } ?>
						<!-- REAMORE -->
						<?php if ((int)$this->_getConfig('product_readmore_display', 1)) { ?>
							<div class="item-readmore">
								<a href="<?php echo $item->link; ?>"
								   title="<?php echo $item->title ?>" <?php echo $helper->parseTarget($this->_getConfig('product_links_target', '_self')); ?> >
									<?php echo $this->_getConfig('product_readmore_text', 'Detail'); ?>
								</a>
							</div>
						<?php } ?>
						
					</div>
					
					<!-- BOTOM ACTION -->
					<div class="bottom-action">
						<!-- BUTTON COMPARE -->
						<?php 
						if( (int)$this->_getConfig('product_addcompare_display', 1) ):
						if( $_compareUrl = $this->getAddToCompareUrl($item) ): ?>
							<a data-toggle="tooltip" data-placement="top" class="btn-action link-compare" title="<?php echo $this->__('Compare') ?>" href="<?php echo $_compareUrl ?>">
								<span><?php echo $this->__('Add to Compare') ?></span>
							</a>
						<?php endif; 
						endif;
						?>
						<a style="display:none;" class="rspl-image" href="<?php echo $item->link ?>"> </a>
						<!-- BUTTON CART -->
						<?php if ((int)$this->_getConfig('product_addcart_display', 1)) { ?>
							<?php if($item->isSaleable()){ ?>
								<button type="button" title="<?php echo $this->__('Add to Cart') ?>" class="btn-action btn-cart" onclick="setLocation('<?php echo $this->getAddToCartUrl($item) ?>')">
									<span>
										<span><?php echo $this->__('Add to Cart') ?></span>
									</span>
								</button>
							<?php }else { ?>
								<p class="availability out-of-stock"><span><?php echo $this->__('Out of stock') ?></span></p>
							<?php }}?>
						<?php if ((int)$this->_getConfig('product_addwishlist_display', 1) || (int)$this->_getConfig('product_addcompare_display', 1)) :?>	
						<!-- BUTTON WISLIST -->
						<?php if ($this->helper('wishlist')->isAllow() && (int)$this->_getConfig('product_addwishlist_display', 1)) : ?>
						<a data-toggle="tooltip" data-placement="top" class="btn-action link-wishlist" title="<?php echo $this->__('Wishlist') ?>" href="<?php echo $this->helper('wishlist')->getAddUrl($item) ?>">
							<span><?php echo $this->__('Add to Wishlist') ?></span>
						</a>
						<?php endif; ?>
						
					</div>

					<?php endif;?>				
				</div>
			</div>
		
		<?php 
        	$j++;
            if($j% $row_items == 0 || $j== count($child_items)) {
                    echo '</div>'; // endrow
            }
        ?> 

	<?php
	} ?>
	

<?php }?>

