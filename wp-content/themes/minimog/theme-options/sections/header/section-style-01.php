<?php
Redux::set_section( Minimog_Redux::OPTION_NAME, array(
	'title'      => 'Header Style 01',
	'id'         => 'header_style_01',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'      => 'header_style_01_content_width',
			'type'    => 'select',
			'title'   => esc_html__( 'Content Width', 'minimog' ),
			'default' => Minimog_Redux::get_default_setting( 'header_style_01_content_width' ),
			'options' => Minimog_Site_Layout::instance()->get_container_wide_list(),
		),
		array(
			'id'      => 'header_style_01_header_above_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Header Above', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'header_style_01_info_list_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Info List', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'header_style_01_info_list_secondary_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Info List (Secondary)', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'header_style_01_search_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Search', 'minimog' ),
			'options' => array(
				'0'      => esc_html__( 'Hide', 'minimog' ),
				'inline' => esc_html__( 'Inline Form', 'minimog' ),
				'popup'  => esc_html__( 'Popup Search', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'header_style_01_search_enable' ),
		),
		array(
			'id'      => 'header_style_01_login_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Login', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'header_style_01_login_enable' ),
		),
		array(
			'id'      => 'header_style_01_wishlist_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Wishlist', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'header_style_01_wishlist_enable' ),
		),
		array(
			'id'      => 'header_style_01_cart_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Cart', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'header_style_01_cart_enable' ),
		),
		array(
			'id'      => 'header_style_01_currency_switcher_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Currency Switcher', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'header_style_01_language_switcher_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Language Switcher', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'header_style_01_social_networks_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Social Networks', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'header_style_01_text_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Text', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'header_style_01_button_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Button', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
	),
) );
