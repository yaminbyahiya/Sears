<?php
$sidebar_positions   = Minimog_Helper::get_list_sidebar_positions();
$registered_sidebars = Minimog_Redux::instance()->get_registered_widgets_options();

Redux::set_section( Minimog_Redux::OPTION_NAME, array(
	'title'      => esc_html__( 'Archive Product', 'minimog' ),
	'id'         => 'archive_product',
	'subsection' => true,
	'fields'     => array(
		array(
			'id'     => 'section_start_product_archive_header',
			'type'   => 'tm_heading',
			'title'  => esc_html__( 'Header Settings', 'minimog' ),
			'indent' => true,
		),
		array(
			'id'          => 'product_archive_header_type',
			'type'        => 'select',
			'title'       => esc_html__( 'Header Style', 'minimog' ),
			'description' => esc_html__( 'Select default header style that displays on archive product page.', 'minimog' ),
			'placeholder' => esc_html__( 'Use Global Setting', 'minimog' ),
			'options'     => Minimog_Header::instance()->get_list( true ),
		),
		array(
			'id'          => 'product_archive_header_overlay',
			'type'        => 'select',
			'title'       => esc_html__( 'Header Overlay', 'minimog' ),
			'placeholder' => esc_html__( 'Use Global Setting', 'minimog' ),
			'options'     => Minimog_Header::instance()->get_overlay_list(),
		),
		array(
			'id'          => 'product_archive_header_skin',
			'type'        => 'select',
			'title'       => esc_html__( 'Header Skin', 'minimog' ),
			'placeholder' => esc_html__( 'Use Global Setting', 'minimog' ),
			'options'     => Minimog_Header::instance()->get_skin_list(),
		),
		array(
			'id'     => 'section_start_product_archive_title_bar',
			'type'   => 'tm_heading',
			'title'  => esc_html__( 'Title Bar Settings', 'minimog' ),
			'indent' => true,
		),
		array(
			'id'          => 'product_archive_title_bar_layout',
			'type'        => 'select',
			'title'       => esc_html__( 'Title Bar Style', 'minimog' ),
			'description' => esc_html__( 'Select default Title Bar that displays on all archive product (included cart, checkout, my-account...) pages.', 'minimog' ),
			'placeholder' => esc_html__( 'Use Global Setting', 'minimog' ),
			'options'     => Minimog_Title_Bar::instance()->get_list( true ),
		),
		array(
			'type'    => 'text',
			'id'      => 'product_archive_title_bar_title',
			'title'   => esc_html__( 'Heading Text', 'minimog' ),
			'default' => esc_html__( 'Shop', 'minimog' ),
		),
		array(
			'id'     => 'section_start_product_archive_sidebar',
			'type'   => 'tm_heading',
			'title'  => esc_html__( 'Sidebar Settings', 'minimog' ),
			'indent' => true,
		),
		array(
			'id'      => 'product_archive_page_sidebar_1',
			'type'    => 'select',
			'title'   => esc_html__( 'Sidebar 1', 'minimog' ),
			'options' => $registered_sidebars,
			'default' => 'shop_sidebar',
		),
		array(
			'id'       => 'product_archive_off_sidebar',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Sidebar 1 Off-Canvas', 'minimog' ),
			'options'  => array(
				'0'      => esc_html__( 'No', 'minimog' ),
				'1'      => esc_html__( 'Always', 'minimog' ),
				'mobile' => esc_html__( 'Only Mobile', 'minimog' ),
			),
			'default'  => 'mobile',
			'required' => array(
				[ 'product_archive_page_sidebar_1', '!=', 'none' ],
			),
		),
		array(
			'id'       => 'product_archive_page_sidebar_1_off_canvas_toggle_text',
			'type'     => 'text',
			'title'    => esc_html__( 'Sidebar 1 Toggle Text', 'minimog' ),
			'required' => array(
				[ 'product_archive_page_sidebar_1', '!=', 'none' ],
				[ 'product_archive_off_sidebar', '!=', '0' ],
			),
		),
		array(
			'id'      => 'product_archive_page_sidebar_2',
			'type'    => 'select',
			'title'   => esc_html__( 'Sidebar 2', 'minimog' ),
			'options' => $registered_sidebars,
			'default' => 'none',
		),
		array(
			'id'       => 'product_archive_page_sidebar_2_off_canvas_enable',
			'type'     => 'button_set',
			'title'    => esc_html__( 'Sidebar 2 Off-Canvas', 'minimog' ),
			'options'  => array(
				'0'      => esc_html__( 'No', 'minimog' ),
				'1'      => esc_html__( 'Always', 'minimog' ),
				'mobile' => esc_html__( 'Only Mobile', 'minimog' ),
			),
			'default'  => 'mobile',
			'required' => array(
				[ 'product_archive_page_sidebar_2', '!=', 'none' ],
			),
		),
		array(
			'id'       => 'product_archive_page_sidebar_2_off_canvas_toggle_text',
			'type'     => 'text',
			'title'    => esc_html__( 'Sidebar 2 Toggle Text', 'minimog' ),
			'required' => array(
				[ 'product_archive_page_sidebar_2', '!=', 'none' ],
				[ 'product_archive_page_sidebar_2_off_canvas_enable', '!=', '0' ],
			),
		),
		array(
			'id'      => 'product_archive_page_sidebar_position',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Sidebar Position', 'minimog' ),
			'options' => $sidebar_positions,
			'default' => 'left',
		),
		array(
			'id'             => 'product_archive_single_sidebar_width',
			'type'           => 'dimensions',
			'units'          => array( '%' ),
			'units_extended' => 'false',
			'title'          => esc_html__( 'Single Sidebar Width', 'minimog' ),
			'description'    => esc_html__( 'Controls the width of the sidebar when only one sidebar is present. Leave blank to use global setting.', 'minimog' ),
			'height'         => false,
			'default'        => array(
				'width' => 25,
			),
		),
		array(
			'id'             => 'product_archive_single_sidebar_offset',
			'type'           => 'dimensions',
			'units'          => array( 'px' ),
			'units_extended' => 'false',
			'title'          => esc_html__( 'Single Sidebar Offset', 'minimog' ),
			'description'    => esc_html__( 'Controls the offset of the sidebar when only one sidebar is present. Leave blank to use global setting.', 'minimog' ),
			'height'         => false,
			'default'        => array(
				'width' => 30,
			),
		),
		array(
			'id'      => 'product_archive_sidebar_style',
			'type'    => 'select',
			'title'   => esc_html__( 'Sidebar Style', 'minimog' ),
			'options' => Minimog_Sidebar::instance()->get_supported_style_options(),
			'default' => Minimog_Redux::get_default_setting( 'product_archive_sidebar_style' ),
		),
		array(
			'id'          => 'shop_archive_filtering',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Filters Button', 'minimog' ),
			'description' => esc_html__( 'Show filters button that displays above products list. This button toggle sidebar 1.', 'minimog' ),
			'options'     => array(
				'0'             => esc_html__( 'Hide', 'minimog' ),
				'toolbar_left'  => esc_html__( 'Toolbar left', 'minimog' ),
				'toolbar_right' => esc_html__( 'Toolbar right', 'minimog' ),
			),
			'default'     => 'toolbar_right',
			'required'    => array(
				[ 'product_archive_off_sidebar', '=', '1' ],
			),
		),
		array(
			'id'     => 'section_start_product_archive_layout',
			'type'   => 'tm_heading',
			'title'  => esc_html__( 'Layout Settings', 'minimog' ),
			'indent' => true,
		),
		array(
			'id'      => 'shop_archive_site_layout',
			'type'    => 'select',
			'title'   => esc_html__( 'Site Layout', 'minimog' ),
			'options' => Minimog_Site_Layout::instance()->get_container_wide_list(),
			'default' => Minimog_Site_Layout::CONTAINER_WIDE,
		),
		array(
			'id'      => 'shop_archive_page_title',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Page Title?', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'shop_archive_grid_style',
			'type'    => 'select',
			'title'   => esc_html__( 'Grid Style', 'minimog' ),
			'options' => Minimog_Woo::instance()->get_shop_loop_style_options(),
			'default' => 'grid-01',
		),
		array(
			'id'      => 'shop_archive_grid_caption_style',
			'type'    => 'select',
			'title'   => esc_html__( 'Caption Style', 'minimog' ),
			'options' => Minimog_Woo::instance()->get_shop_loop_caption_style_options(),
			'default' => Minimog_Redux::get_default_setting( 'shop_archive_grid_caption_style' ),
		),
		array(
			'id'          => 'shop_archive_grid_alternating',
			'type'        => 'select',
			'title'       => esc_html__( 'Grid Alternating?', 'minimog' ),
			'description' => esc_html__( 'Even rows has more or less than odd rows 1 column', 'minimog' ),
			'options'     => [
				'0'  => esc_attr__( 'Disabled', 'minimog' ),
				'1'  => esc_attr__( 'Normal Alternating', 'minimog' ),
				'-1' => esc_attr__( 'Reverse Alternating', 'minimog' ),
			],
			'default'     => '0',
		),
		array(
			'id'            => 'shop_archive_number_item',
			'title'         => esc_html__( 'Number items', 'minimog' ),
			'description'   => esc_html__( 'Controls the number of products display on shop archive page', 'minimog' ),
			'type'          => 'slider',
			'default'       => 12,
			'min'           => 1,
			'max'           => 100,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'            => 'shop_archive_lg_columns',
			'title'         => esc_html__( 'Grid Columns', 'minimog' ),
			'type'          => 'slider',
			'default'       => 4,
			'min'           => 1,
			'max'           => 6,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'            => 'shop_archive_lg_gutter',
			'title'         => esc_html__( 'Grid Gutter', 'minimog' ),
			'type'          => 'slider',
			'default'       => 30,
			'min'           => 0,
			'max'           => 100,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'            => 'shop_archive_md_columns',
			'title'         => esc_html__( 'Grid Columns (Tablet)', 'minimog' ),
			'type'          => 'slider',
			'default'       => 3,
			'min'           => 1,
			'max'           => 6,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'            => 'shop_archive_md_gutter',
			'title'         => esc_html__( 'Grid Gutter (Tablet)', 'minimog' ),
			'type'          => 'slider',
			'default'       => 20,
			'min'           => 0,
			'max'           => 100,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'            => 'shop_archive_sm_columns',
			'title'         => esc_html__( 'Grid Columns (Mobile)', 'minimog' ),
			'type'          => 'slider',
			'default'       => 2,
			'min'           => 1,
			'max'           => 6,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'            => 'shop_archive_sm_gutter',
			'title'         => esc_html__( 'Grid Gutter (Mobile)', 'minimog' ),
			'type'          => 'slider',
			'default'       => 16,
			'min'           => 0,
			'max'           => 100,
			'step'          => 1,
			'display_value' => 'text',
		),
		array(
			'id'     => 'section_start_product_archive_other_settings',
			'type'   => 'tm_heading',
			'title'  => esc_html__( 'Other Settings', 'minimog' ),
			'indent' => true,
		),
		array(
			'id'      => 'shop_archive_toolbar_position',
			'type'    => 'select',
			'title'   => esc_html__( 'Toolbar Position', 'minimog' ),
			'options' => [
				'above-content'         => esc_attr__( 'Above Content', 'minimog' ),
				'above-content-sidebar' => esc_attr__( 'Above Content & Sidebar', 'minimog' ),
			],
			'default' => 'above-content',
		),
		array(
			'id'      => 'shop_archive_result_count',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Result Count', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'          => 'shop_archive_sorting',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Sorting', 'minimog' ),
			'description' => esc_html__( 'Turn on to show sorting select options that displays above products list.', 'minimog' ),
			'options'     => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default'     => '1',
		),
		array(
			'id'      => 'shop_archive_pagination_type',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Pagination Type', 'minimog' ),
			'options' => array(
				''          => esc_html__( 'Numbered list', 'minimog' ),
				'load-more' => esc_html__( 'Load more button', 'minimog' ),
				'infinite'  => esc_html__( 'Infinite scrolling', 'minimog' ),
			),
			'default' => 'load-more',
		),
		array(
			'id'          => 'shop_archive_layout_switcher',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Layout Switcher', 'minimog' ),
			'description' => esc_html__( 'Display layout switcher buttons that show above products list.', 'minimog' ),
			'options'     => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default'     => '1',
		),
		array(
			'id'          => 'shop_archive_hover_image',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Hover Image', 'minimog' ),
			'description' => esc_html__( 'Turn on to show the first gallery image when hover', 'minimog' ),
			'options'     => array(
				'0' => esc_html__( 'None', 'minimog' ),
				'1' => esc_html__( 'Yes', 'minimog' ),
			),
			'default'     => '1',
		),
		array(
			'id'          => 'shop_archive_compare',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Compare', 'minimog' ),
			'description' => esc_html__( 'Turn on to display compare button', 'minimog' ),
			'options'     => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default'     => '1',
		),
		array(
			'id'          => 'shop_archive_wishlist',
			'type'        => 'button_set',
			'title'       => esc_html__( 'Wishlist', 'minimog' ),
			'description' => esc_html__( 'Turn on to display love button', 'minimog' ),
			'options'     => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default'     => '1',
		),
		array(
			'id'      => 'shop_archive_show_price',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Price', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'shop_archive_show_variation',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Variation', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '1',
		),
		array(
			'id'      => 'shop_archive_show_category',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Category', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'shop_archive_show_brand',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Brand', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'shop_archive_show_rating',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Rating', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'shop_archive_show_availability',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Availability', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
		array(
			'id'      => 'shop_archive_show_stock_bar',
			'type'    => 'button_set',
			'title'   => esc_html__( 'Product Stock Bar', 'minimog' ),
			'options' => array(
				'0' => esc_html__( 'Hide', 'minimog' ),
				'1' => esc_html__( 'Show', 'minimog' ),
			),
			'default' => '0',
		),
	),
) );
