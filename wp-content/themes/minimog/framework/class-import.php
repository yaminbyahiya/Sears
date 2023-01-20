<?php
defined( 'ABSPATH' ) || exit;

/**
 * Initial OneClick import for this theme
 */
if ( ! class_exists( 'Minimog_Import' ) ) {
	class Minimog_Import {

		protected static $instance = null;

		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		public function initialize() {
			add_filter( 'insight_core_import_demos', [ $this, 'import_demos' ] );
			add_filter( 'insight_core_import_generate_thumb', '__return_false' );
			add_filter( 'insight_core_import_delete_exist_posts', '__return_true' );

			add_action( 'insight_core_importer_dispatch_after', [ $this, 'delete_attachment_cropped_info' ] );
			add_action( 'insight_core_importer_dispatch_after', [ $this, 'update_links' ] );
			add_action( 'insight_core_importer_dispatch_after', [ $this, 'update_theme_options' ] );
		}

		public function import_demos() {
			$import_img_url = MINIMOG_THEME_URI . '/assets/import';

			return [
				'main'      => [
					'screenshot'  => $import_img_url . '/main/preview.jpg',
					'name'        => 'Main Store',
					'description' => 'Include 56 homepages and all inner pages.',
					'preview_url' => 'https://minimog.thememove.com',
					'url'         => Minimog_Google_Manager::get_google_driver_url( '1RO1dtGLGgPtYewsJlJjMWOqlTjmK_lRw' ),
				],
				'supergear' => [
					'screenshot'  => $import_img_url . '/supergear/preview.jpg',
					'name'        => 'Supergear Store',
					'description' => 'Include 1 homepage and all inner pages.',
					'preview_url' => 'https://minimog.thememove.com/supergear',
					'url'         => Minimog_Google_Manager::get_google_driver_url( '1Y583LWp2tmLBo3YnncX-v0H3GxJN21qx' ),
				],
				'megamog'   => [
					'screenshot'  => $import_img_url . '/megamog/preview.jpg',
					'name'        => 'Megamog Store',
					'description' => 'Include 1 homepage and all inner pages.',
					'preview_url' => 'https://minimog.thememove.com/megamog',
					'url'         => Minimog_Google_Manager::get_google_driver_url( '1sVfoKVEY9ILGO84Ons0QaRt33bsbcU2J' ),
				],
				'megastore' => [
					'screenshot'  => $import_img_url . '/megastore/preview.jpg',
					'name'        => 'Mega Store',
					'description' => 'Include 1 homepage and all inner pages.',
					'preview_url' => 'https://minimog.thememove.com/megastore',
					'url'         => Minimog_Google_Manager::get_google_driver_url( '1wB5Ho4PzKN__RkNA2h2TYTCGN4DQGviE' ),
				],
				'rtl'       => [
					'screenshot'  => $import_img_url . '/rtl/preview.jpg',
					'name'        => 'RTL Demo',
					'description' => 'Include 1 RTL homepage and all inner pages.',
					'preview_url' => 'https://minimog.thememove.com/rtl',
					'url'         => Minimog_Google_Manager::get_google_driver_url( '1RxkdAkImAiZFpfU6jQAheFLmL3BxD04W' ),
				],
				'next'      => [
					'screenshot'  => $import_img_url . '/next/preview.jpg',
					'name'        => 'Next Store',
					'description' => 'Include 14 homepages and all inner pages.',
					'preview_url' => 'https://minimog.thememove.com/next',
					'url'         => 'https://www.dropbox.com/s/tivvoos80l36whl/minimog-insightcore-next.zip?dl=1',
				],
				'robust'    => [
					'screenshot'  => $import_img_url . '/robust/preview.jpg',
					'name'        => 'Robust Store',
					'description' => 'Include 1 homepage. (Some homepages is coming soon)',
					'preview_url' => 'https://minimog.thememove.com/robust',
					'url'         => 'https://www.dropbox.com/s/9rnxj3r0ri4wdcr/minimog-insightcore-robust-1.10.0.zip?dl=1',
				],
			];
		}

		/**
		 * Images package has no cropped images then
		 * need delete cropped data to crop attachment again.
		 */
		public function delete_attachment_cropped_info() {
			Minimog_Attachment::instance()->delete_all_cropped_info();
		}

		/**
		 * Fix links in Elementor after import
		 *
		 * @param $importer
		 */
		public function update_links( $importer ) {
			if ( ! isset( $importer->demo ) ) {
				return;
			}

			$demo_info = $this->get_demo_imported_url( $importer->demo );

			if ( empty( $demo_info ) ) {
				return;
			}

			// First replace WP upload dir.
			$old_upload_dir = $demo_info['upload_dir'];
			$wp_upload_dir  = wp_upload_dir();
			$new_upload_dir = $wp_upload_dir['baseurl'];

			$result = $this->replace_url( $old_upload_dir, $new_upload_dir );

			// Finally replace all other links.
			$from = $demo_info['site_url'];
			$to   = home_url();

			$result = $this->replace_url( $from, $to );
		}

		public function update_theme_options( $importer ) {
			$json_file   = MINIMOG_THEME_DIR . '/assets/import/' . $importer->demo . '/redux_options.json';
			$option_name = class_exists( 'Minimog_Redux' ) ? Minimog_Redux::OPTION_NAME : '';

			if ( ! empty( $json_file ) && file_exists( $json_file ) && ! empty( $option_name ) ) {
				global $wp_filesystem;

				minimog_require_file_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();

				$file_content = $wp_filesystem->get_contents( $json_file );
				$options      = json_decode( $file_content, true );

				if ( is_array( $options ) && ! empty( $options ) ) {
					// Change url from placeholder to current site.
					$home_url = home_url();
					foreach ( $options as $key => $option ) {
						if ( ! empty( $option['url'] ) && is_string( $option['url'] ) ) {
							$value = $option['url'];

							$option['url'] = str_replace( '%SITE_URL%', $home_url, $value );

							$options[ $key ] = $option;
						}
					}

					update_option( $option_name, $options );
				}
			}
		}

		public function get_demo_imported_url( $imported_demo ) {
			$demos = [
				'main'      => [
					'site_id' => 1,
				],
				'supergear' => [
					'site_id' => 2,
				],
				'megamog'   => [
					'site_id' => 3,
				],
				'megastore' => [
					'site_id' => 4,
				],
				'rtl'       => [
					'site_id' => 5,
				],
				'next'      => [
					'site_id' => 6,
				],
				'robust'    => [
					'site_id' => 7,
				],
			];

			foreach ( $demos as $demo_name => $demo_info ) {
				if ( $imported_demo === $demo_name ) {
					if ( 1 === $demo_info['site_id'] ) {
						return [
							'site_url'   => "https://minimog.thememove.com",
							'upload_dir' => "https://minimog.thememove.com/wp-content/uploads",
						];
					} else {
						return [
							'site_url'   => "https://minimog.thememove.com/{$demo_name}",
							'upload_dir' => "https://minimog.thememove.com/{$demo_name}/wp-content/uploads/sites/{$demo_info['site_id']}",
						];
					}
				}
			}

			return false;
		}

		public function replace_url( $from, $to ) {
			$is_valid_urls = ( filter_var( $from, FILTER_VALIDATE_URL ) && filter_var( $to, FILTER_VALIDATE_URL ) );
			if ( ! $is_valid_urls ) {
				return false;
			}
			global $wpdb;

			// @codingStandardsIgnoreStart cannot use `$wpdb->prepare` because it remove's the backslashes
			$rows_affected = $wpdb->query(
				"UPDATE {$wpdb->postmeta} " .
				"SET `meta_value` = REPLACE(`meta_value`, '" . str_replace( '/', '\\\/', $from ) . "', '" . str_replace( '/', '\\\/', $to ) . "') " .
				"WHERE `meta_key` = '_elementor_data' AND `meta_value` LIKE '[%' ;" ); // meta_value LIKE '[%' are json formatted
			// @codingStandardsIgnoreEnd

			return $rows_affected;
		}
	}

	Minimog_Import::instance()->initialize();
}
