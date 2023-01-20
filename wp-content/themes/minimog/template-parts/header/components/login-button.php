<?php
/**
 * Login button on header
 *
 * @package Minimog
 * @since   1.0.0
 * @version 1.9.1
 */

defined( 'ABSPATH' ) || exit;

$link_classes = 'header-icon header-login-link hint--bounce hint--bottom';
$link_classes .= ' style-' . $args['style'];

if ( is_user_logged_in() ) {
	$button_text = apply_filters( 'minimog/user_profile/text', __( 'Log out', 'minimog' ) );
	$button_url  = minimog_get_user_profile_url();
} else {
	$button_text = apply_filters( 'minimog/user_login/text', __( 'Log in', 'minimog' ) );
	$button_url  = minimog_get_login_url();

	/**
	 * Force remove link.
	 */
	if ( Minimog::setting( 'login_popup_enable' ) ) {
		$button_url   = '#';
		$link_classes .= ' open-modal-login';
	}
}

if ( empty( $button_text ) || empty( $button_url ) ) {
	return;
}
?>

<a href="<?php echo esc_url( $button_url ); ?>" class="<?php echo esc_attr( $link_classes ); ?>"
   aria-label="<?php echo esc_attr( $button_text ); ?>">
	<?php if ( in_array( $args['display'], [ 'icon', 'icon-text' ], true ) ): ?>
		<?php
		$icon_style = Minimog::setting( 'header_icons_style' );

		switch ( $icon_style ) {
			case 'icon-set-02':
				$icon_key = 'user-light';
				break;
			case 'icon-set-03':
				$icon_key = 'phr-user';
				break;
			case 'icon-set-04':
				$icon_key = 'user-solid';
				break;
			default :
				$icon_key = 'user';
				break;
		}
		?>
		<span class="icon">
			<?php echo Minimog_SVG_Manager::instance()->get( $icon_key ) ?>
		</span>
	<?php endif; ?>

	<?php if ( in_array( $args['display'], [ 'text', 'icon-text' ], true ) ): ?>
		<span class="button-text"><?php echo esc_html( $button_text ); ?></span>
	<?php endif; ?>
</a>
