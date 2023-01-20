<?php
Redux::set_section( Minimog_Redux::OPTION_NAME, array(
	'title'      => esc_html__( 'Search Popup', 'minimog' ),
	'id'         => 'search_popup',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'       => 'section_popup_search_form',
			'type'     => 'tm_heading',
			'title'    => 'Search Form',
			'indent'   => true,
			'collapse' => 'show',
		),
		array(
			'id'       => 'popup_search_categories_enable',
			'type'     => 'switch',
			'title'    => 'Categories dropdown',
			'subtitle' => 'Display categories dropdown to narrow search results.',
			'default'  => false,
			'on'       => esc_html__( 'Show', 'minimog' ),
			'off'      => esc_html__( 'Hide', 'minimog' ),
		),
		array(
			'type'        => 'text',
			'id'          => 'popup_search_ajax_auto_delay',
			'title'       => 'Search delay',
			'subtitle'    => 'Control delay time before auto searching. Leave blank to use default.',
			'description' => 'Within (millisecond). Default 1000 ms',
			'attributes'  => [
				'type' => 'number',
				'step' => 50,
				'min'  => 0,
			],
		),
		array(
			'id'       => 'section_popup_search_scope',
			'type'     => 'tm_heading',
			'title'    => 'Search Scope',
			'indent'   => true,
			'collapse' => 'show',
		),
		array(
			'id'    => 'popup_search_in_content',
			'type'  => 'switch',
			'title' => 'Search in description',
			'on'    => esc_html__( 'Yes', 'minimog' ),
			'off'   => esc_html__( 'No', 'minimog' ),
		),
		array(
			'id'    => 'popup_search_in_excerpt',
			'type'  => 'switch',
			'title' => 'Search in short description',
			'on'    => esc_html__( 'Yes', 'minimog' ),
			'off'   => esc_html__( 'No', 'minimog' ),
		),
		array(
			'id'    => 'popup_search_in_sku',
			'type'  => 'switch',
			'title' => 'Search in SKU',
			'on'    => esc_html__( 'Yes', 'minimog' ),
			'off'   => esc_html__( 'No', 'minimog' ),
		),
		array(
			'id'       => 'section_popup_search_extra',
			'type'     => 'tm_heading',
			'title'    => 'Extra Options',
			'indent'   => true,
			'collapse' => 'show',
		),
		array(
			'id'           => 'popular_search_keywords',
			'type'         => 'repeater',
			'title'        => esc_html__( 'Popular search keywords', 'minimog' ),
			'item_name'    => esc_html__( 'Keyword', 'minimog' ),
			'bind_title'   => 'text',
			'group_values' => true,
			'fields'       => array(
				array(
					'id'    => 'text',
					'title' => esc_html__( 'Keyword', 'minimog' ),
					'type'  => 'text',
				),
			),
			'default'      => [
				'Redux_repeater_data' => [
					[ 'title' => '' ],
					[ 'title' => '' ],
					[ 'title' => '' ],
				],
				'text'                => [
					'T-Shirt',
					'Blue',
					'Jacket',
				],
			],
		),
	),
) );
