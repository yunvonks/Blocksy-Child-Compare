<?php

if (! defined('ABSPATH')) {
    return;
}

// Ensure Blocksy is active and the extension function exists
if (! function_exists('blocksy_companion_get_ext')) {
    return;
}

$ext = blocksy_companion_get_ext('woocommerce-extra');
if (!$ext || !method_exists($ext, 'get_compare')) {
    return;
}

$compare_list = $ext->get_compare()->get_current_compare_list();

// Generate the Compare Popup HTML.
$max_slots = 2;
$slots_html = '';

$compare_items = array_values($compare_list);
$items_count = count($compare_items);

// Floating Button
$button_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>';
echo '<a href="#" class="custom-floating-compare-btn" style="' . ($items_count == 0 ? 'display:none;' : '') . '">';
echo $button_icon;
echo '<span class="compare-count">' . $items_count . '</span>';
echo '</a>';

for ($i = 0; $i < $max_slots; $i++) {
    $product = isset($compare_items[$i]) ? wc_get_product($compare_items[$i]['id']) : null;

    if ($product) {
        // Slot filled with product
        $thumbnail = $product->get_image('thumbnail');
        if (function_exists('blocksy_media') && get_post_thumbnail_id($product->get_id())) {
            $maybe_thumbnail = blocksy_media([
                'attachment_id' => get_post_thumbnail_id($product->get_id()),
                'post_id' => $product->get_id(),
                'ratio' => '1',
                'size' => 'thumbnail',
                'tag_name' => 'figure',
            ]);
            if ($maybe_thumbnail) {
                $thumbnail = $maybe_thumbnail;
            }
        }

        $title = $ext->utils->get_formatted_title($product->get_id());

        $remove_icon = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';

        $remove_btn = '<a href="#" class="ct-compare-remove custom-compare-remove-btn" data-product_id="' . esc_attr($product->get_id()) . '" title="' . esc_attr__('Remove Product', 'blocksy-companion') . '">' . $remove_icon . '</a>';

        $slot_content = '<div class="compare-slot-item filled-slot">';
        $slot_content .= '<div class="slot-thumbnail">' . $thumbnail . '</div>';
        $slot_content .= '<div class="slot-info">';
        $slot_content .= '<span class="slot-title">' . $title . '</span>';
        $slot_content .= '</div>';
        $slot_content .= $remove_btn;
        $slot_content .= '</div>';
    } else {
        // Empty slot
        $slot_content = '<div class="compare-slot-item empty-slot">';
        $slot_content .= '<div class="custom-compare-search-wrapper">';
        $slot_content .= '<input type="text" class="custom-compare-search-input" placeholder="Search product..." autocomplete="off">';
        $slot_content .= '<div class="custom-compare-search-results"></div>';
        $slot_content .= '</div>';
        $slot_content .= '</div>';
    }

    $slots_html .= $slot_content;
}

// Compare Link
$url = '#ct-compare-modal';
if (function_exists('blocksy_companion_theme_functions')) {
    $maybe_page_id = blocksy_companion_theme_functions()->blocksy_get_theme_mod('woocommerce_compare_page');
    if (!empty($maybe_page_id)) {
        $maybe_permalink = get_permalink($maybe_page_id);
        if ($maybe_permalink) {
            $url = $maybe_permalink;
        }
    }
}

$compare_label = function_exists('blocksy_companion_theme_functions') ? blocksy_companion_theme_functions()->blocksy_get_theme_mod(
    'product_compare_bar_button_label',
    __('Compare Products', 'blocksy-companion')
) : __('Compare Products', 'blocksy-companion');

$compare_link = '<a href="' . esc_url($url) . '" class="custom-compare-main-btn ct-compare-button" data-behaviour="modal"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 8px;"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5"/></svg>' . esc_html($compare_label) . '</a>';

$header = '<div class="custom-compare-popup-header">';
$header .= '<h3 class="custom-compare-popup-title">Compare <span>(' . $items_count . ') items</span></h3>';
$header .= '<a href="#" class="custom-compare-popup-close"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></a>';
$header .= '</div>';

$content = '<div class="custom-compare-popup" data-compare-bar data-custom-compare-override="true">';
$content .= $header;
$content .= '<div class="custom-compare-popup-body">' . $slots_html . '</div>';
$content .= '<div class="custom-compare-popup-footer">' . $compare_link . '</div>';
$content .= '</div>';

echo $content;
