<?php
Redux::set_section( Minimog_Redux::OPTION_NAME, array(
	'title'      => esc_html__( 'Category Page', 'minimog' ),
	'id'         => 'shop_category',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'          => 'shop_sub_categories_position',
			'type'        => 'select',
			'title'       => esc_html__( 'Product Categories Position', 'minimog' ),
			'description' => esc_html__( 'Select position of product top categories display on the main shop page.', 'minimog' ),
			'options'     => [
				'above_sidebar'  => esc_html__( 'Above Sidebar', 'minimog' ),
				'beside_sidebar' => esc_html__( 'Beside Sidebar', 'minimog' ),
			],
			'default'     => Minimog_Redux::get_default_setting( 'shop_sub_categories_position' ),
		),
		array(
			'id'          => 'shop_sub_categories_carousel_style',
			'type'        => 'select',
			'title'       => esc_html__( 'Product Categories Style', 'minimog' ),
			'description' => esc_html__( 'Select style of product top categories display on the main shop page.', 'minimog' ),
			'options'     => [
				'01' => '01',
				'02' => '02',
				'03' => '03',
				'04' => '04',
				'05' => '05',
				'06' => '06',
			],
			'default'     => '01',
		),

		array(
			'id'          => 'product_category_sub_categories_position',
			'type'        => 'select',
			'title'       => esc_html__( 'Product Categories Position', 'minimog' ),
			'description' => esc_html__( 'Select position of product categories display on product category pages.', 'minimog' ),
			'options'     => [
				'above_sidebar'  => esc_html__( 'Above Sidebar', 'minimog' ),
				'beside_sidebar' => esc_html__( 'Beside Sidebar', 'minimog' ),
			],
			'default'     => Minimog_Redux::get_default_setting( 'product_category_sub_categories_position' ),
		),
		array(
			'id'          => 'product_category_sub_categories_carousel_style',
			'type'        => 'select',
			'title'       => esc_html__( 'Product Sub Categories Style', 'minimog' ),
			'description' => esc_html__( 'Select style of product categories display on product category pages.', 'minimog' ),
			'options'     => [
				'01' => '01',
				'02' => '02',
				'03' => '03',
				'04' => '04',
				'05' => '05',
				'06' => '06',
			],
			'default'     => '02',
		),
	),
) );
