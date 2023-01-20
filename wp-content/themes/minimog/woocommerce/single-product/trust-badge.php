<?php
/**
 * Trust badge
 */

defined( 'ABSPATH' ) || exit;

global $product;

$trust_badge_image = Minimog_Helper::get_redux_image_url( 'single_product_trust_badge_image' );

if ( empty( $trust_badge_image ) ) {
	return;
}

$text = \Minimog::setting( 'single_product_trust_badge_text' );

if ( empty( $text ) ) {
	$text = __( 'Guaranteed safe & secure checkout', 'minimog' );
}
?>
<div class="product-trust-badge">
	<div class="trust-badge-image">
		<img src="<?php echo esc_url( $trust_badge_image ); ?>"
		     alt="<?php esc_attr_e( 'Trust Badge', 'minimog' ); ?>">
	</div>
	<div class="trust-badge-text"><?php echo esc_html( $text ); ?></div>
</div>
