<?php

if (! defined('ABSPATH')) {
	die('direct access forbidden');
}

$compare_list = (new \BlocksyChild\Compare\CompareView())
	->get_current_compare_list();

$content = '';

$visiblity_class = blocksy_visibility_classes(
	get_theme_mod(
		'product_compare_bar_visibility',
		[
			'desktop' => true,
			'tablet' => true,
			'mobile' => true,
		]
	)
);

$items_count = count($compare_list);

$products = [];
$class = 'qcfw-m-side-pop-button';

if (!function_exists('wc_get_endpoint_url')) {
	return;
}

$url = '#ct-compare-modal';

$maybe_page_id = get_theme_mod('woocommerce_compare_page');

if (!empty($maybe_page_id)) {
	$maybe_permalink = get_permalink($maybe_page_id);

	if ($maybe_permalink) {
		$url = $maybe_permalink;
	}
}

$compare_label = get_theme_mod(
	'product_compare_bar_button_label',
	__('Compare products', 'blocksy-companion')
);

$opener_html = '<a class="qcfw-m-side-pop-opener ct-compare-bar-opener" aria-label="Compare" rel="noopener noreferrer">
    <div class="qcfw-compare-counter-label">
        <svg class="qcfw-svg--compare" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="15px" height="14px" viewBox="0 0 15 14" fill="currentColor"><g><path d="M12.4,3.8L11.4,5c-0.3,0.3-0.3,0.7,0,1c0.2,0.3,0.6,0.3,0.9,0c0,0,0,0,0,0l2.2-2.4 c0.3-0.3,0.3-0.7,0-1l-2.2-2.4c-0.2-0.3-0.6-0.3-0.9,0c0,0,0,0,0,0c-0.3,0.3-0.3,0.7,0,1l1.1,1.2h-1.1c-1.3,0-3,1.8-5,3.8 C4.7,8,2.7,10.1,1.6,10.1h-1c-0.4,0-0.7,0.4-0.7,0.8c0,0.4,0.3,0.6,0.7,0.7h1c1.6,0,3.5-2.1,5.6-4.2c1.5-1.5,3.3-3.4,4.1-3.4 L12.4,3.8L12.4,3.8z"></path><path d="M12.2,8c-0.3-0.3-0.7-0.3-1,0c-0.3,0.3-0.3,0.7,0,1l1.1,1.1h-1.1c-0.7,0-1.7-1-2.7-1.8 C8.3,8,7.9,8.1,7.6,8.4C7.4,8.6,7.4,9,7.6,9.3c1.5,1.4,2.6,2.2,3.6,2.2h1.1l-1.1,1.1c-0.3,0.3-0.2,0.7,0,1c0.3,0.3,0.7,0.3,0.9,0 l2.3-2.3c0.3-0.3,0.3-0.7,0-1L12.2,8z"></path><path d="M0.7,3.8h1c0.8,0,2,1,2.9,1.8c0.3,0.2,0.7,0.2,1-0.1c0.2-0.3,0.2-0.6,0-0.9 C4.1,3.3,2.9,2.4,1.7,2.4h-1C0.3,2.4,0,2.7,0,3.1C0,3.5,0.3,3.8,0.7,3.8L0.7,3.8z"></path></g></svg>
        <span class="qcfw-compare-counter-label-count">' . $items_count . '</span>
    </div>
</a>';

foreach ($compare_list as $single_product) {
	$product = wc_get_product($single_product['id']);

	if (! $product) {
		continue;
	}

	$thumbnail = $product->get_image('thumbnail', ['class' => 'attachment-thumbnail size-thumbnail']);
	$product_url = $product->get_permalink();

	$remove_btn = '<a href="#" class="qcfw-m-remove-button ct-compare-remove" aria-label="Remove this item" data-product_id="' . $product->get_id() . '" rel="noopener noreferrer">
        <span class="qcfw-m-remove-button-icon"><svg class="qcfw-svg--close" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 18.1213 18.1213" stroke-miterlimit="10" stroke-width="2"><line x1="1.0607" y1="1.0607" x2="17.0607" y2="17.0607"></line><line x1="17.0607" y1="1.0607" x2="1.0607" y2="17.0607"></line></svg></span>
    </a>';

	$products[] = '
    <div class="qcfw-m-side-pop-item" data-item-id="' . $product->get_id() . '">
        <div class="qcfw-m-side-pop-item-image-wrapper">
            <a class="qcfw-m-side-top-item-image-link" href="' . $product_url . '">' . $thumbnail . '</a>
        </div>
        <div class="qcfw-m-side-pop-item-info-wrapper">
            <a class="qcfw--product-name" href="' . $product_url . '">' . $product->get_title() . '</a>
        </div>
        ' . $remove_btn . '
    </div>';
}

$items_html = implode('', $products);

$content = $opener_html . '
<div class="qcfw-m-side-pop-content">
    <div class="qcfw-m-side-pop-content-heading">
        <span class="qcfw-m-side-pop-content-heading--label">' . __('Compare', 'blocksy-companion') . '</span>
        <span class="qcfw-m-side-pop-content-heading--counter">(' . $items_count . ') items</span>
    </div>
    <div class="qcfw-m-side-pop-items">
        ' . $items_html . '
    </div>
    <div class="qcfw-m-side-pop-bottom">
        <a class="qcfw-m-side-pop-button ct-button" href="' . $url . '" data-behaviour="' . get_theme_mod('compare_table_placement', 'modal') . '">
            <svg class="qcfw-svg--compare" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" width="15px" height="14px" viewBox="0 0 15 14" fill="currentColor"><g><path d="M12.4,3.8L11.4,5c-0.3,0.3-0.3,0.7,0,1c0.2,0.3,0.6,0.3,0.9,0c0,0,0,0,0,0l2.2-2.4 c0.3-0.3,0.3-0.7,0-1l-2.2-2.4c-0.2-0.3-0.6-0.3-0.9,0c0,0,0,0,0,0c-0.3,0.3-0.3,0.7,0,1l1.1,1.2h-1.1c-1.3,0-3,1.8-5,3.8 C4.7,8,2.7,10.1,1.6,10.1h-1c-0.4,0-0.7,0.4-0.7,0.8c0,0.4,0.3,0.6,0.7,0.7h1c1.6,0,3.5-2.1,5.6-4.2c1.5-1.5,3.3-3.4,4.1-3.4 L12.4,3.8L12.4,3.8z"></path><path d="M12.2,8c-0.3-0.3-0.7-0.3-1,0c-0.3,0.3-0.3,0.7,0,1l1.1,1.1h-1.1c-0.7,0-1.7-1-2.7-1.8 C8.3,8,7.9,8.1,7.6,8.4C7.4,8.6,7.4,9,7.6,9.3c1.5,1.4,2.6,2.2,3.6,2.2h1.1l-1.1,1.1c-0.3,0.3-0.2,0.7,0,1c0.3,0.3,0.7,0.3,0.9,0 l2.3-2.3c0.3-0.3,0.3-0.7,0-1L12.2,8z"></path><path d="M0.7,3.8h1c0.8,0,2,1,2.9,1.8c0.3,0.2,0.7,0.2,1-0.1c0.2-0.3,0.2-0.6,0-0.9 C4.1,3.3,2.9,2.4,1.7,2.4h-1C0.3,2.4,0,2.7,0,3.1C0,3.5,0.3,3.8,0.7,3.8L0.7,3.8z"></path></g></svg>
            <span class="qcfw-m-side-pop-button--text">' . $compare_label . '</span>
        </a>
    </div>
</div>';

$attr = [
	'class' => trim('ct-compare-bar ct-compare-bar-side qcfw-side-pop-wrapper ' . $visiblity_class),
	'data-items' => $items_count
];

if (is_customize_preview()) {
	$attr['data-shortcut'] = 'border:outside';
	$attr['data-shortcut-location'] = 'woocommerce_general:has_compare_panel';
}

blocksy_html_tag_e('div', $attr, $content);
