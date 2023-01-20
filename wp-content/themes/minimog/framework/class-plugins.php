<?php
defined( 'ABSPATH' ) || exit;

/**
 * Plugin installation and activation for WordPress themes
 */
if ( ! class_exists( 'Minimog_Register_Plugins' ) ) {
	class Minimog_Register_Plugins {

		protected static $instance = null;

		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function initialize() {
			add_filter( 'insight_core_tgm_plugins', [ $this, 'register_required_plugins' ] );

			add_filter( 'insight_core_compatible_plugins', [ $this, 'register_compatible_plugins' ] );

			add_filter( 'tgmpa_table_data_items', [ $this, 'tgmpa_table_remove_data_items' ] );
		}

		public function register_required_plugins( $plugins ) {
			/*
			 * Array of plugin arrays. Required keys are name and slug.
			 * If the source is NOT from the .org repo, then source is also required.
			 */
			$new_plugins = array(
				array(
					'name'        => 'Insight Core',
					'description' => 'Core functions for WordPress theme',
					'slug'        => 'insight-core',
					'logo'        => 'insight',
					'source'      => 'https://www.dropbox.com/s/fos2dabmc7x0gk7/insight-core-2.4.11.zip?dl=1',
					'version'     => '2.4.11',
					'required'    => true,
				),
				array(
					'name'        => 'Redux Framework',
					'description' => 'Build better sites in WordPress fast',
					'slug'        => 'redux-framework',
					'logo'        => 'redux-framework',
					'required'    => true,
				),
				array(
					'name'        => 'Elementor',
					'description' => 'The Elementor Website Builder has it all: drag and drop page builder, pixel perfect design, mobile responsive editing, and more.',
					'slug'        => 'elementor',
					'logo'        => 'elementor',
					'required'    => true,
				),
				array(
					'name'        => 'Thememove Addons For Elementor',
					'description' => 'Additional functions for Elementor',
					'slug'        => 'tm-addons-for-elementor',
					'logo'        => 'insight',
					'source'      => Minimog_Google_Manager::get_google_driver_url( '1QxYx83fGLfJK_YWiKGOKSGn2Q1pxvIqg' ),
					'version'     => '1.2.0',
					'required'    => true,
				),
				array(
					'name'        => 'WPForms',
					'description' => 'Beginner friendly WordPress contact form plugin. Use our Drag & Drop form builder to create your WordPress forms',
					'slug'        => 'wpforms-lite',
					'logo'        => 'wpforms-lite',
				),
				array(
					'name'        => 'WooCommerce',
					'description' => 'An eCommerce toolkit that helps you sell anything. Beautifully.',
					'slug'        => 'woocommerce',
					'logo'        => 'woocommerce',
				),
				array(
					'name'        => 'Insight Swatches',
					'description' => 'Allows you set a style for each attribute variation as color, image, or label on product page.',
					'slug'        => 'insight-swatches',
					'logo'        => 'insight',
					'source'      => Minimog_Google_Manager::get_google_driver_url( '1IvIQkzvSX9-yG8F92kymG6jjRujkAH4t' ),
					'version'     => '1.3.1',
				),
				array(
					'name'        => 'Insight Product Brands',
					'description' => 'Add brands for products',
					'slug'        => 'insight-product-brands',
					'logo'        => 'insight',
					'source'      => 'https://www.dropbox.com/s/i693kiu0gg21wb5/insight-product-brands-1.1.0.zip?dl=1',
					'version'     => '1.1.0',
				),
				array(
					'name'        => 'Conditional Discounts for WooCommerce',
					'description' => 'This plugin is a simple yet advanced WooCommerce dynamic discount plugin ideal for all types of deals.',
					'slug'        => 'woo-advanced-discounts',
					'logo'        => 'woo-advanced-discounts',
				),
				array(
					'name'        => 'Sales Countdown Timer (Premium)',
					'description' => 'Create a sense of urgency with a countdown to the beginning or end of sales, store launch or other events for higher conversions.',
					'slug'        => 'sctv-sales-countdown-timer',
					'logo'        => 'sctv-sales-countdown-timer',
					'source'      => Minimog_Google_Manager::get_google_driver_url( '15F_wCLj56-LOngvOgD-yjLRY0JfS-yjy' ),
					'version'     => '1.0.6.1',
				),
				array(
					'name'        => 'WPC Smart Compare for WooCommerce (Premium)',
					'description' => 'Allows your visitors to compare some products of your shop.',
					'slug'        => 'woo-smart-compare-premium',
					'logo'        => 'woo-smart-compare',
					'source'      => 'https://www.dropbox.com/s/kmtvir2i89ixf1m/woo-smart-compare-premium-5.3.0.zip?dl=1',
					'version'     => '5.3.0',
				),
				array(
					'name'        => 'WPC Smart Wishlist for WooCommerce (Premium)',
					'description' => 'Allows your visitors save products for buy later.',
					'slug'        => 'woo-smart-wishlist-premium',
					'logo'        => 'woo-smart-wishlist',
					'source'      => 'https://www.dropbox.com/s/orxivyksr9xrlrg/woo-smart-wishlist-premium-4.4.2.zip?dl=1',
					'version'     => '4.4.2',
				),
				array(
					'name'        => 'WPC Frequently Bought Together for WooCommerce (Premium)',
					'description' => 'Increase your sales with personalized product recommendations',
					'slug'        => 'woo-bought-together-premium',
					'logo'        => 'woo-bought-together-premium',
					'source'      => 'https://www.dropbox.com/s/qfgp0rgusoibjk5/woo-bought-together-premium-4.4.6.zip?dl=1',
					'version'     => '4.4.6',
				),
				array(
					'name'        => 'WPC Product Bundles for WooCommerce (Premium)',
					'description' => 'This plugin helps you bundle a few products, offer them at a discount and watch the sales go up.',
					'slug'        => 'woo-product-bundle-premium',
					'logo'        => 'woo-product-bundle-premium',
					'source'      => 'https://www.dropbox.com/s/ky39htf0tylf8we/woo-product-bundle-premium-6.5.1.zip?dl=1',
					'version'     => '6.5.1',
				),
				array(
					'name'        => 'WPC Product Tabs for WooCommerce (Premium)',
					'description' => 'Allows adding custom tabs to your products and provide your buyers with extra details for boosting customers’ confidence in the items.',
					'slug'        => 'wpc-product-tabs-premium',
					'logo'        => 'wpc-product-tabs-premium',
					'source'      => 'https://www.dropbox.com/s/bc9mpr7gmpgvh6t/wpc-product-tabs-premium-2.0.3.zip?dl=1',
					'version'     => '2.0.3',
				),
				array(
					'name'        => 'Shoppable Images',
					'description' => 'Easily add \'shoppable images\' (images with hotspots) to your website or store',
					'slug'        => 'mabel-shoppable-images-lite',
					'logo'        => 'mabel-shoppable-images-lite',
					'source'      => Minimog_Google_Manager::get_google_driver_url( '1kYgyy0zZ-Q4Dn8PLHbrfXlIC86i0VRpD' ),
					'version'     => '1.1.8',
				),
			);

			$plugins = array_merge( $plugins, $new_plugins );

			return $plugins;
		}

		protected function get_compatible_plugins() {
			/**
			 * Each Item should have 'compatible'
			 * 'compatible': set be "true" to work correctly
			 */
			$plugins = array(
				array(
					'name'        => 'Multi Currency for WooCommerce (Premium)',
					'description' => 'Allows to display prices and accepts payments in multiple currencies.',
					'slug'        => 'woocommerce-multi-currency',
					'logo'        => 'woocommerce-multi-currency',
					'source'      => Minimog_Google_Manager::get_google_driver_url( '1K716P3MDmgjV2HX4QPZME6wjgSglT8qH' ),
					'version'     => '2.1.36',
					'compatible'  => true,
				),
				array(
					'name'        => 'WPC Smart Notification for WooCommerce (Premium)',
					'description' => 'Increase trust, credibility, and sales with smart notifications.',
					'slug'        => 'wpc-smart-notification-premium',
					'logo'        => 'wpc-smart-notification',
					'source'      => 'https://www.dropbox.com/s/oasf0i8h5lcjogo/wpc-smart-notification-premium-2.1.6.zip?dl=1',
					'version'     => '2.1.6',
					'compatible'  => true,
				),
				array(
					'name'        => 'Revolution Slider',
					'description' => 'This plugin helps beginner-and mid-level designers WOW their clients with pro-level visuals. You’ll be able to create anything you can imagine, not just amazing, responsive sliders.',
					'slug'        => 'revslider',
					'logo'        => 'revslider',
					'source'      => 'https://www.dropbox.com/s/ubqdd8a2oyn71g7/revslider-6.6.5.zip?dl=1',
					'version'     => '6.6.5',
					'compatible'  => true,
				),
				array(
					'name'        => 'WordPress Social Login',
					'description' => 'Allows your visitors to login, comment and share with Facebook, Google, Apple, Twitter, LinkedIn etc using customizable buttons.',
					'slug'        => 'miniorange-login-openid',
					'logo'        => 'miniorange-login-openid',
					'compatible'  => true,
				),
				array(
					'name'        => 'User Profile Picture',
					'description' => 'Allows your visitors upload their avatar with the native WP uploader.',
					'slug'        => 'metronet-profile-picture',
					'logo'        => 'metronet-profile-picture',
					'compatible'  => true,
				),
				array(
					'name'        => 'DCO Comment Attachment',
					'description' => 'Allows your visitors to attach files with their comments.',
					'slug'        => 'dco-comment-attachment',
					'logo'        => 'dco-comment-attachment',
					'compatible'  => true,
				),
				array(
					'name'        => 'hCaptcha for WordPress',
					'description' => 'Add captcha to protects user privacy, rewards websites, and helps companies get their data labeled. Help build a better web.',
					'slug'        => 'hcaptcha-for-forms-and-more',
					'logo'        => 'hcaptcha-for-forms-and-more',
					'compatible'  => true,
				),
			);

			return $plugins;
		}

		public function register_compatible_plugins( $plugins ) {
			$new_plugins = $this->get_compatible_plugins();

			$plugins = array_merge( $plugins, $new_plugins );

			return $plugins;
		}

		/**
		 * Remove Compatible Plugins From Table
		 */
		public function tgmpa_table_remove_data_items( $items ) {
			$plugins = $this->get_compatible_plugins();

			foreach ( $items as $index => $item ) {
				foreach ( $plugins as $plugin ) {
					if ( $plugin['slug'] == $item['slug'] ) {
						unset( $items[ $index ] );
					}
				}
			}

			return $items;
		}
	}

	Minimog_Register_Plugins::instance()->initialize();
}
