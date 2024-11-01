<?php
/*
* Plugin Name: SWE Product 360o View
* Plugin Url:http://sanjaywebexpert.com
* Description: This is 360 Degree Woocomerce Product View Plugin Developed by Sanjay Sharma
* Version: 1.0
* Author Name: Sanjay Sharma
* Author Url: http://sanjaywebexpert.com
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Define Plugin Path and Directory Path */
define("SWE_PV_PLUGIN_DIR_PATH",plugin_dir_path(__FILE__));
 define("SWE_PV_PLUGIN_URL",plugin_dir_url(__FILE__));

/*
* Enqueue Scripts
*
*/
function load_admin_style_em360css(){
		wp_enqueue_style( 'swe360_gallery_admin', SWE_PV_PLUGIN_URL.'assets/css/swe360_gallery.css',__FILE__);
		wp_enqueue_script( 'swe360_gallery_admin', SWE_PV_PLUGIN_URL.'assets/js/swe360_gallery.js',__FILE__);
}
add_action( 'admin_enqueue_scripts', 'load_admin_style_em360css');
/*  Registered All the Scripts and Styles */
function swe360view_slider_script(){
	wp_enqueue_style("swe_rotate_slider_style",SWE_PV_PLUGIN_URL.'assets/css/swe.rotate.css',__FILE__);
	wp_enqueue_style("swe_ninja_slider_style",SWE_PV_PLUGIN_URL.'assets/css/swe.ninja-slider.css',__FILE__);
	wp_enqueue_style("swe_thumbnail_slider_style",SWE_PV_PLUGIN_URL.'assets/css/swe.thumbnail-slider.css',__FILE__);
	wp_enqueue_script('jquery');
	wp_enqueue_script('swe_rotate_slider_js', SWE_PV_PLUGIN_URL.'assets/js/swe.rotate.js',array(), '1.0.0', false);
	wp_enqueue_script('swe_ninja_slider_js', SWE_PV_PLUGIN_URL.'assets/js/swe.ninja-slider.js',array(), '1.0.0', true);
	wp_enqueue_script('swe_thumbnail_slider_js', SWE_PV_PLUGIN_URL.'assets/js/swe.thumbnail-slider.js',array(), '1.0.0', true);
}
add_action('wp_enqueue_scripts','swe360view_slider_script');


/*
* Meta Box For Upload Product Gallery Images
*
*/
add_action( 'add_meta_boxes', 'swe360_WooCommerce_add_swe360_metabox' );
function swe360_WooCommerce_add_swe360_metabox(){
    if (get_post_type() === 'product'){
        wp_enqueue_script( 'swe360-gallery', plugins_url('/assets/js/swe360_gallery.js', __FILE__), array('jquery'), '1.0.0');
        wp_enqueue_style('swe360-gallery', plugins_url('/assets/css/swe360_gallery.css', __FILE__));

        add_meta_box(
            'woocommerce-swe360-gallery',
            __( 'SWE 360&#176; Product View Gallery' ),
            'swe_WooCommerce_SWE360_swe360_meta_output',
            'product',
            'side',
            'low'
        );
    }
}
function swe_WooCommerce_SWE360_swe360_meta_output($post){
    ?>
    <div id="swe360_images_container">
        <div class="swe360-options">

            <p class="form-field" style="display: block;">
                <input type="checkbox" value="0" name="swe360[multi_rows]" id="swe360_multi_rows">
                <label class="description">Check To Show 360 View</label>
            </p>

            <p class="form-field">
                <label for="_sku">Number of images</label>
                <input type="text" class="short" value="0" name="swe360[columns]" id="swe360_columns">
            </p>

        </div>
        <ul class="swe360_images">
                <?php
                    if ( metadata_exists( 'post', $post->ID, '_swe360_data' ) ) {
                        $data = json_decode((get_post_meta( $post->ID, '_swe360_data', true )), true);
                        if(!empty($data) && !empty($data['images_ids'])){
                            $images_ids = $data['images_ids'];//explode(',', $data['images_ids'][0]);

                            $count = count($images_ids);
                            $update_meta = false;

                            foreach ( $images_ids as $attachment_id ) {
                                $attachment = wp_get_attachment_image( $attachment_id, 'thumbnail' );

                                // if attachment is empty skip
                                if ( empty( $attachment ) ) {
                                    $update_meta = true;

                                    continue;
                                }

                                echo '<li class="swe360-image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                                    ' . $attachment . '
                                    <ul class="actions">
                                        <li><a href="#" class="delete tips" data-tip="' . esc_attr__( 'Delete image', 'woocommerce' ) . '">' . __( 'Delete', 'woocommerce' ) . '</a></li>
                                    </ul>
                                </li>';

                                // rebuild ids to be saved
                                $updated_gallery_ids[] = $attachment_id;
                            }

                            if ( $update_meta ) {
                                $data['images_ids'] = $updated_gallery_ids;

                                update_post_meta( $post->ID, '_swe360_data', json_encode($data) );
                            }
                        }else{
                            $data = array( "images_ids" => array(), "options" => array( "checked" => false, "columns" => 0, 'set_columns' => false ) ) ;
                        }
                    }else{
                            $data = array( "images_ids" => array(), "options" => array( "checked" => false, "columns" => 0, 'set_columns' => false ) ) ;
                        }
    ?>
        </ul>
            <?php
            if(isset($count) && $count >= 5){
                echo '<a class="button button-primary button-large swe360-delete-all">Delete all images</a>';
            }
            ?>
    </div>
    <input type="hidden" id="swe360_data" name="swe360_data" value="<?php echo esc_attr( json_encode($data) ); ?>" />
    <p class="add_swe360_images hide-if-no-js">
	<?php  if( $count > 0){ ?>
        <a href="#"  data-choose="<?php esc_attr_e( 'Add Images to Product Gallery', 'woocommerce' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'woocommerce' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'woocommerce' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'woocommerce' ); ?>"><?php _e( 'Add More Product Gallery images', 'woocommerce' ); ?></a>
	<?php } else{ ?>
		<a href="#" id="create_product_direct"  data-choose="<?php esc_attr_e( 'Add Images to Product Gallery', 'woocommerce' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'woocommerce' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'woocommerce' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'woocommerce' ); ?>"><?php _e( 'Add 360 Product Gallery images', 'woocommerce' ); ?></a>
	<?php } ?>
	</p>
    <?php

}

/*
* Save Meta Box
*/

add_action( 'save_post', 'swe_WooCommerce_swe360_save_swe360_meta' );

function swe_WooCommerce_swe360_save_swe360_meta( $post_id ) {
        if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'editpost') {
            $attachment_ids = isset( $_POST['swe360_data'] ) ? json_decode(stripcslashes( $_POST['swe360_data'] ), true )  : array();
            update_post_meta( $post_id, '_swe360_data', json_encode($attachment_ids) );
        }
}


function sweplugin_plugin_path() {
  // gets the absolute path to this plugin directory
  return untrailingslashit( plugin_dir_path( __FILE__ ) );
}

add_filter( 'woocommerce_locate_template', 'swe360_woocommerce_locate_template', 10, 3 );

function swe360_woocommerce_locate_template( $template, $template_name, $template_path ) {
  global $woocommerce;

  $_template = $template;

  if ( ! $template_path ) $template_path = $woocommerce->template_url;

  $plugin_path  = sweplugin_plugin_path() . '/templates/';
  $template = locate_template(

    array(
      $template_path . $template_name,
      $template_name
    )
  );
  if ( ! $template && file_exists( $plugin_path . $template_name ) )
    $template = $plugin_path . $template_name;

  if ( ! $template )
    $template = $_template;
  return $template;
}