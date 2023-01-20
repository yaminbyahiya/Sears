<?php
/**
 * Mobile menu
 *
 * @package Minimog
 * @since   1.0.0
 * @version 2.0.0
 */
defined( 'ABSPATH' ) || exit;
?>
<div id="page-mobile-main-menu" class="page-mobile-main-menu">
	<div class="inner">
		<div id="page-close-mobile-menu" class="page-close-mobile-menu">
			<span class="fal fa-times"></span>
		</div>
		<div class="page-mobile-menu-content scroll-y">
			<?php Minimog::menu_mobile_primary(); ?>

			<div class="mobile-menu-components">
				<?php do_action( 'minimog/mobile_menu/components/before' ); ?>

				<?php
				if ( '1' === Minimog::setting( 'mobile_menu_login_enable' ) ) {
					minimog_load_template( 'mobile-menu/components/user-buttons' );
				}
				?>

				<?php
				if ( '1' === Minimog::setting( 'mobile_menu_wishlist_enable' ) ) {
					minimog_load_template( 'mobile-menu/components/wishlist-button' );
				}
				?>

				<?php
				if ( '1' === Minimog::setting( 'mobile_menu_language_switcher_enable' ) ) {
					minimog_load_template( 'mobile-menu/components/language-switcher' );
				}
				?>

				<?php
				$info_list_enable = Minimog::setting( 'mobile_menu_info_list_enable' );
				$info_list        = Minimog_Helper::parse_redux_repeater_field_values( Minimog::setting( 'info_list' ) );

				if ( '1' === $info_list_enable && ! empty( $info_list ) ) {
					minimog_load_template( 'mobile-menu/components/info-list', null, $args = [ 'info_list' => $info_list ] );
				}
				?>

				<?php
				if ( '1' === Minimog::setting( 'mobile_menu_social_networks_enable' ) ) {
					minimog_load_template( 'mobile-menu/components/social-network' );
				}
				?>

				<?php do_action( 'minimog/mobile_menu/components/after' ); ?>
			</div>
		</div>
	</div>
</div>
