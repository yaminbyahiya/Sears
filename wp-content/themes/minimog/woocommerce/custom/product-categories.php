<?php
defined( 'ABSPATH' ) || exit;

$shop_page_display = Minimog_Woo::instance()->get_shop_display();
$category_style    = Minimog_Woo::instance()->get_shop_categories_carousel_style();

$classes = 'tm-swiper tm-slider minimog-product-categories minimog-animation-zoom-in';
$classes .= ' style-' . $category_style;

// Default settings.
$carousel_defaults = [
	'data-nav' => '1',
	//'data-loop' => '1',
];

$carousel_args = [];

switch ( $category_style ) {
	case '02' :
		$carousel_args = [
			'data-items-desktop'      => '4',
			'data-items-tablet-extra' => '3',
			'data-items-mobile-extra' => 'auto',
			'data-gutter-desktop'     => '30',
			'data-gutter-mobile'      => '0', // Use content margin.
		];
		$classes       .= ' nav-style-01';
		break;
	case '03' :
	case '04' :
		$carousel_args = [
			'data-items-desktop'       => '5',
			'data-items-tablet-extra'  => '4',
			'data-items-tablet'        => '3',
			'data-items-mobile-extra'  => '2',
			'data-gutter-desktop'      => '30',
			'data-gutter-tablet-extra' => '20',
			'data-gutter-mobile-extra' => '16',
		];
		$classes       .= ' nav-style-02';
		break;
	case '05' :
		$carousel_args = [
			'data-items-desktop'       => '5',
			'data-items-tablet-extra'  => '4',
			'data-items-tablet'        => '3',
			'data-items-mobile-extra'  => '2',
			'data-gutter-desktop'      => '50',
			'data-gutter-laptop'       => '30',
			'data-gutter-tablet-extra' => '20',
			'data-gutter-mobile-extra' => '16',
		];
		$classes       .= ' nav-style-02 v-middle h-center';
		break;
	case '06' :
		$carousel_args = [
			'data-items-desktop'       => '4',
			'data-items-tablet'        => '3',
			'data-items-mobile-extra'  => '2',
			'data-gutter-desktop'      => '30',
			'data-gutter-tablet-extra' => '20',
			'data-gutter-mobile-extra' => '10',
		];
		$classes       .= ' nav-style-02 v-middle h-center';
		break;
	default :
		$carousel_args = [
			'data-items-desktop'       => '3',
			'data-items-tablet-extra'  => '2',
			'data-items-mobile-extra'  => 'auto',
			'data-gutter-desktop'      => '30',
			'data-gutter-mobile-extra' => '0', // Use content margin.
		];
		$classes       .= ' nav-style-01';
		break;
}

$carousel_args = wp_parse_args( $carousel_args, $carousel_defaults );

$carousel_attributes_string = Minimog_Helper::slider_args_to_html_attr( $carousel_args );

if ( in_array( $shop_page_display, [ 'subcategories', 'both' ] ) ) {
	woocommerce_output_product_categories( [
		'before'    => '<div class="' . $classes . '" ' . $carousel_attributes_string . '><div class="swiper-inner"><div class="swiper-container"><div class="swiper-wrapper">',
		'after'     => '</div></div></div></div>',
		'parent_id' => is_product_category() ? get_queried_object_id() : 0,
	] );
}
