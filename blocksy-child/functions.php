<?php
if (! defined('ABSPATH')) {
	die( 'Direct access forbidden.' );
}
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
});

/**
 * Remove product data tabs
 */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] );
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

add_action('init', function() {
    $query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    if (strpos($query_string, 'filter_') === 0) {
        status_header(403);
        wp_die('Access Denied', 'Forbidden', ['response' => 403]);
    }
});

/**
 * Custom Compare Popup Integration
 */

// 1. Unhook the default Blocksy Compare Bar to avoid duplicates
add_filter('blocksy:footer:offcanvas-drawer', 'custom_remove_default_compare_bar', 20, 2);
function custom_remove_default_compare_bar($els, $payload) {
	foreach ($els as $key => $el) {
		if (isset($el['attr']) && isset($el['attr']['data-compare-bar'])) {
			unset($els[$key]);
		}
	}
	return $els;
}

// 2. Inject custom compare popup into wp_footer
add_action('wp_footer', 'custom_inject_compare_popup_footer', 99);
function custom_inject_compare_popup_footer() {
	if (
		!function_exists('blocksy_companion_theme_functions')
		||
		blocksy_companion_theme_functions()->blocksy_get_theme_mod('product_compare_bar', 'no') === 'no'
		||
		(defined('REST_REQUEST') && REST_REQUEST)
	) {
		return;
	}

	$initial_conditions = [['type' => 'include', 'rule' => 'everywhere']];
	$conditions = blocksy_companion_theme_functions()->blocksy_get_theme_mod('compare_bar_conditions', $initial_conditions);

	if (class_exists('\Blocksy\ConditionsManager')) {
		$conditions_manager = new \Blocksy\ConditionsManager();
		if (! $conditions_manager->condition_matches($conditions)) {
			return;
		}
	}

	include get_stylesheet_directory() . '/custom-compare-popup.php';
}

/**
 * Handle AJAX for loading custom Compare Popup UI (Overrides native ajax)
 */
add_action('wp_ajax_blocksy_get_woo_compare_bar', 'custom_blocksy_get_woo_compare_popup_override', 1);
add_action('wp_ajax_nopriv_blocksy_get_woo_compare_bar', 'custom_blocksy_get_woo_compare_popup_override', 1);

function custom_blocksy_get_woo_compare_popup_override() {
	ob_start();
	include get_stylesheet_directory() . '/custom-compare-popup.php';
	$content = ob_get_clean();

	$ext = function_exists('blocksy_companion_get_ext') ? blocksy_companion_get_ext('woocommerce-extra') : null;
	$items = [];
	if ($ext && method_exists($ext, 'get_compare')) {
		$items = $ext->get_compare()->get_current_compare_list();
	}

	wp_send_json_success([
		'content' => $content,
		'items_to_compare' => $items,
	]);
	wp_die();
}

/**
 * AJAX Endpoint for Product Search in Compare
 */
add_action('wp_ajax_custom_compare_search', 'custom_compare_search_handler');
add_action('wp_ajax_nopriv_custom_compare_search', 'custom_compare_search_handler');

function custom_compare_search_handler() {
	if (!isset($_POST['keyword'])) {
		wp_send_json_error('No keyword');
	}

	$keyword = sanitize_text_field($_POST['keyword']);

	$args = [
		'post_type' => 'product',
		'post_status' => 'publish',
		's' => $keyword,
		'posts_per_page' => 10,
	];

	$query = new WP_Query($args);
	$results = [];

	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$product = wc_get_product(get_the_ID());
			if ($product) {
				$results[] = [
					'id' => $product->get_id(),
					'title' => get_the_title(),
				];
			}
		}
		wp_reset_postdata();
	}

	wp_send_json_success($results);
}

/**
 * Enqueue Custom JavaScript and CSS
 */
add_action('wp_enqueue_scripts', 'custom_compare_enqueue_scripts', 99);
function custom_compare_enqueue_scripts() {
	wp_enqueue_script(
		'custom-compare-js',
		get_stylesheet_directory_uri() . '/custom-compare.js',
		['jquery'],
		time(),
		true
	);

	wp_localize_script('custom-compare-js', 'custom_compare_vars', [
		'ajax_url' => admin_url('admin-ajax.php')
	]);
}
