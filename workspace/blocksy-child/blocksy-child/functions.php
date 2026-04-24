<?php
if (! defined('WP_DEBUG')) {
	die( 'Direct access forbidden.' );
}
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
});

/**

/**
 * Remove product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {


    unset( $tabs['additional_information'] );  	// Remove the additional information tab

    return $tabs;
}



add_filter( 'woocommerce_structured_data_product', 'structured_data_product_nulled', 10, 2 );
function structured_data_product_nulled( $markup, $product ){
    if( is_product() ) {
        $markup = '';
    }
    return $markup;
}

/**
 * Function to disable WooCommerce breadcrumbs
 */
function remove_wc_breadcrumbs() {
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
}
add_action( 'init', 'remove_wc_breadcrumbs' );

add_filter( 'rank_math/sitemap/enable_caching', '__return_false');

add_filter( 'rank_math/snippet/breadcrumb', "__return_false");

add_filter( 'woocommerce_product_description_heading', '__return_null' );

add_filter( 'woocommerce_loop_add_to_cart_link', 'replacing_add_to_cart_button', 10, 2 );
function replacing_add_to_cart_button( $button, $product  ) {
    $button_text = __("View full specs", "woocommerce");
    $button = '<a class="button" href="' . $product->get_permalink() . '">' . $button_text . '</a>';

    return $button;
}

add_filter( 'litespeed_const_DONOTCACHEPAGE', '__return_false' );

function cc_mime_types($mimes) {
    $mimes['json'] = 'application/json';
    $mimes['svg'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');


add_action('admin_head', function() {
      echo '<style>#trp-link-id { display: none; }</style>';
  });




// Tambahkan di functions.php atau plugin kustom
add_action('init', function() {
    $query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    if (strpos($query_string, 'filter_') === 0) {
        status_header(403);
        wp_die('Access Denied', 'Forbidden', ['response' => 403]);
    }
});

/**
 * Initialize custom Compare feature
 */
require_once get_stylesheet_directory() . '/inc/compare/feature.php';
require_once get_stylesheet_directory() . '/inc/compare/views/table.php';

add_action('after_setup_theme', function() {
    new \BlocksyChild\Compare\CompareView();
});
