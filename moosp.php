<?php
/*
Plugin Name: Move Out of Stock Products
Plugin URI: https://github.com/cassio707/moosp
Description: Move out-of-stock products to the end of the list in WooCommerce and affect product display
Version: 1.5
Author: Sajad
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Move out-of-stock products to the end of the product listings
add_action('woocommerce_product_query', 'move_out_of_stock_products_to_end');
function move_out_of_stock_products_to_end($q) {
    if (!is_admin() && $q->is_main_query()) {
        // Filter products with either 'instock' or 'outofstock' status
        $meta_query = $q->get('meta_query');

        $meta_query[] = array(
            'relation' => 'OR',
            array(
                'key'     => '_stock_status',
                'value'   => 'instock',
                'compare' => '='
            ),
            array(
                'key'     => '_stock_status',
                'value'   => 'outofstock',
                'compare' => '='
            )
        );

        $q->set('meta_query', $meta_query);

        // Sort products: in-stock first, then out-of-stock, and newest first
        $q->set('meta_key', '_stock_status');
        $q->set('orderby', array(
            'meta_value' => 'ASC', // instock < outofstock
            'date'       => 'DESC'
        ));
    }
}

// Add custom CSS for out-of-stock product images
add_action('wp_head', 'add_custom_css_for_out_of_stock');
function add_custom_css_for_out_of_stock() {
    echo '<style>
        .products .product.outofstock img,
        .woodmart-carousel .product.outofstock img,
        .woodmart-products-holder .product.outofstock img {
            filter: grayscale(100%) !important;
        }
    </style>';
}
