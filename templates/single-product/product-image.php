<?php
defined( 'ABSPATH' ) || exit;
global $post, $woocommerce, $product, $main_id;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters( 'woocommerce_single_product_image_gallery_classes', array(
	'woocommerce-product-gallery',
	'woocommerce-product-gallery--' . ( has_post_thumbnail() ? 'with-images' : 'without-images' ),
	'woocommerce-product-gallery--columns-' . absint( $columns ),
	'images',
) );

?>
<div class="qw <?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">

		<?php 
		$pid =  get_the_ID();
		$swe360_data = json_decode((get_post_meta( $pid, '_swe360_data', true )), true);
		$checked_360view =  $swe360_data['options']['checked'];
		$swe360_image_gallery = $swe360_data['images_ids'];
		if(!empty($swe360_image_gallery)){
			$image_src = wp_get_attachment_image_src($swe360_image_gallery[0], 'original');
			$image_src = preg_replace('/.*(\/wp-content.*)/','$1', $image_src[0]);	
		}
		if($checked_360view!="" && $checked_360view > 0){
			?>
		<div id="popup_360view" style="display:none;">
			<div class="popup_360vie_wrapper">
				<button id="close_popupview"><i class="fa fa-close"></i></button>
				<div id="swe_360product_wrap"></div>
				<div id="swe_360product_room" style="display:none;">
					<?php 
							if(!empty($swe360_image_gallery)){
								$il=1;
									foreach($swe360_image_gallery as $i => $image_id) {
										$image_src = wp_get_attachment_image_src($image_id, 'original');
										$image_src = preg_replace('/.*(\/wp-content.*)/','$1', $image_src[0]);
									    echo '<img src="'.site_url().$image_src.'" id="img_'.$il.'">';
										$il++;
									}
							}
					?>
				</div>
				<?php echo '<div class="blkicon_360_logo"><img src="'.SWE_PV_PLUGIN_URL.'/assets/img/360imageblk.png" class="blkicon_360"></div>'; ?>
			</div>			
		</div>
	<?php } ?>
		<div class="woocommerce-product-gallery__wrapper custom_slider_byem">
			<?php 
				if ( has_post_thumbnail() ) {
				
					if ( $woocommerce->version >= '3.0' ){
						$attachment_ids = $product->get_gallery_image_ids();
					}else{
						$attachment_ids = $product->get_gallery_attachment_ids();
					}

					 ?>
					 <div id="thumbnail-slider" style="float:left;">
						<div class="inner">
							<ul>
							<?php 
								$full_size_image2  =  wp_get_attachment_image_src( $post_thumbnail_id, 'full' );
								$full_size_image2 = preg_replace('/.*(\/wp-content.*)/','$1', $full_size_image2[0]); 
								echo '<li><a class="thumb" href="'.site_url().$full_size_image2.'"></a></li>';
								foreach ( $attachment_ids as $i => $attachimage_id ) {
									$full_size_image  =  wp_get_attachment_image_src( $attachimage_id, 'full' );
									$shop_single_img       = wp_get_attachment_image_src( $attachimage_id, 'shop_single' );
										$full_size_image = preg_replace('/.*(\/wp-content.*)/','$1', $full_size_image[0]); 
								echo '<li><a class="thumb" href="'.site_url().$full_size_image.'"></a><div class="overlay"></div></li>';
								} 
							?>
							</ul>
						</div>
						</div>
						<?php if($checked_360view!="" && $checked_360view > 0){ ?>
							
						<div id="thumb_360view">
							<div class="thumb_360view_inner">
							<?php       
								if(!empty($swe360_image_gallery)){
									$image_src = wp_get_attachment_image_src($swe360_image_gallery[0], 'original');
									$image_src = preg_replace('/.*(\/wp-content.*)/','$1', $image_src[0]);
										echo '<div class="icon_360_main"><img src="'.SWE_PV_PLUGIN_URL.'/assets/img/360image.png" class="icon_360"></div>';
										echo '<div class="click_360thumnail_main"><img src="'.site_url().$image_src.'" class="click_360thumnail"></div>';
								}
							?>
							</diV>
						</div>
						
						<?php } ?>
					
					
					<div id="ninja-slider" style="float:left;">
						<div class="slider-inner">
							<ul>
								<?php 
								echo '<li><a class="ns-img" href="'.site_url().$full_size_image2.'"></a></li>';	
								foreach ( $attachment_ids as $i => $attachimage_id ) {
									$full_size_image  =  wp_get_attachment_image_src( $attachimage_id, 'full' );
									$shop_single_img       = wp_get_attachment_image_src( $attachimage_id, 'shop_single' );
											$full_size_image = preg_replace('/.*(\/wp-content.*)/','$1', $full_size_image[0]); 
									echo '<li><a class="ns-img" href="'.site_url().$full_size_image.'"></a></li>';
									} ?>
							</ul>
							<div class="fs-icon" title="Expand/Close"></div>
						</div>
					</div>
						 <div style="clear:both;"></div>
						<?php
				
				}
			?>
		</div>


	
	
</div>
<script type="application/javascript">
jQuery(document).ready(function(){
			jQuery('#thumb_360view').click(function(){
				jQuery('#popup_360view').fadeToggle("slow");
			})
			jQuery('#close_popupview').click(function(){
				jQuery('#popup_360view').fadeOut("slow");
			})
			jQuery("#swe_360product_wrap").vc3dEye({
				imagePath:"images/",
				totalImages:<?php if(!empty($swe360_image_gallery)){ echo count($swe360_image_gallery); }else{ echo "10"; } ?>,
				imageExtension:"png"
			});			
});
</script>