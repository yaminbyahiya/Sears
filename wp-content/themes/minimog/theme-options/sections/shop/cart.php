<?php
Redux::set_section( Minimog_Redux::OPTION_NAME, array(
	'title'      => esc_html__( 'Cart', 'minimog' ),
	'id'         => 'cart_page',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'section_shopping_cart_features',
			'type'     => 'tm_heading',
			'collapse' => 'show',
			'title'    => esc_html__( 'Cart Countdown', 'minimog' ),
			'subtitle' => esc_html__( 'Show countdown timer as soon as any product has been added to the cart. This can help your store make those product sales quicker.', 'minimog' ),
			'indent'   => true,
		),
		array(
			'id'      => 'shopping_cart_countdown_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Visibility', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'shopping_cart_countdown_enable' ),
			'class'   => 'redux-row-field-parent redux-row-field-first-parent',
		),
		array(
			'id'       => 'shopping_cart_countdown_loop_enable',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Enable Loop', 'minimog' ),
			'options'  => array(
				'0' => esc_html__( 'No', 'minimog' ),
				'1' => esc_html__( 'Yes', 'minimog' ),
			),
			'default'  => Minimog_Redux::get_default_setting( 'shopping_cart_countdown_loop_enable' ),
			'required' => array(
				[ 'shopping_cart_countdown_enable', '=', '1' ],
			),
			'class'    => 'redux-row-field-child',
		),
		array(
			'id'            => 'shopping_cart_countdown_length',
			'title'         => esc_html__( 'Length', 'minimog' ),
			'type'          => 'slider',
			'default'       => Minimog_Redux::get_default_setting( 'shopping_cart_countdown_length' ),
			'description'   => esc_html__( 'Countdown length in minute(s)', 'minimog' ),
			'min'           => 0,
			'max'           => 400,
			'step'          => 1,
			'display_value' => 'text',
			'required'      => array(
				[ 'shopping_cart_countdown_enable', '=', '1' ],
			),
			'class'         => 'redux-row-field-child',
		),
		array(
			'id'          => 'shopping_cart_countdown_message',
			'type'        => 'textarea',
			'title'       => esc_html__( 'Text', 'minimog' ),
			'default'     => esc_html__( '{fire} These products are limited, checkout within {timer}', 'minimog' ),
			'description' => '{timer} will be replace with countdown timer.<br/> {fire} will be replace with icon 🔥',
			'required'    => array(
				[ 'shopping_cart_countdown_enable', '=', '1' ],
			),
			'class'       => 'redux-row-field-child',
		),
		array(
			'id'       => 'shopping_cart_countdown_expired_message',
			'type'     => 'textarea',
			'title'    => esc_html__( 'Expired Message', 'minimog' ),
			'default'  => esc_html__( 'You\'re out of time! Checkout now to avoid losing your order!', 'minimog' ),
			'required' => array(
				[ 'shopping_cart_countdown_enable', '=', '1' ],
			),
			'class'    => 'redux-row-field-child',
		),
		array(
			'id'       => 'section_shopping_cart_drawer',
			'type'     => 'tm_heading',
			'collapse' => 'show',
			'title'    => esc_html__( 'Cart Drawer', 'minimog' ),
			'indent'   => true,
		),
		array(
			'id'      => 'shopping_cart_drawer_modal_customer_notes_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Customer Notes Modal', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'shopping_cart_drawer_modal_customer_notes_enable' ),
		),
		array(
			'id'      => 'shopping_cart_drawer_modal_shipping_calculator_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Shipping Calculator Modal', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'shopping_cart_drawer_modal_shipping_calculator_enable' ),
		),
		array(
			'id'      => 'shopping_cart_drawer_modal_coupon_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Coupon Modal', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'shopping_cart_drawer_modal_coupon_enable' ),
		),
		array(
			'id'      => 'shopping_cart_drawer_view_cart_button_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'View Cart Button', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'shopping_cart_drawer_view_cart_button_enable' ),
		),
		array(
			'id'       => 'section_shopping_cart_page',
			'type'     => 'tm_heading',
			'collapse' => 'show',
			'title'    => esc_html__( 'Cart Page', 'minimog' ),
			'indent'   => true,
		),
		array(
			'id'      => 'shopping_cart_modal_customer_notes_enable',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Customer Notes Modal', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => Minimog_Redux::get_default_setting( 'shopping_cart_modal_customer_notes_enable' ),
		),
		array(
			'id'          => 'shopping_cart_cross_sells_enable',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Cross-sells products', 'minimog' ),
			'description' => esc_html__( 'Turn on to display the cross-sells products section. This is helpful if you have dozens of products with cross-sells and you don\'t want to go and edit each single page.', 'minimog' ),
			'options'     => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default'     => '1',
		),
		array(
			'id'       => 'section_shopping_cart_empty',
			'type'     => 'tm_heading',
			'collapse' => 'show',
			'title'    => esc_html__( 'Cart Empty', 'minimog' ),
			'indent'   => true,
		),
		array(
			'id'    => 'shopping_cart_empty_image',
			'type'  => 'media',
			'title' => esc_html__( 'Image', 'minimog' ),
		),
	),
) );
