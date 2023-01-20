<?php
/**
 * Product quantity select
 *
 * @package Minimog\WooCommerce\Templates
 * @since   1.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty"
		       name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>"/>
	</div>
	<?php
} else {
	global $product;
	$ranges  = explode( "\n", str_replace( "\r", "", $values ) );
	$options = [];

	if ( empty( $values ) ) {
		$options[] = 1;
	} else {
		foreach ( $ranges as $value ) {
			if ( is_numeric( $value ) ) {
				$options[] = intval( $value );
			} elseif ( strpos( $value, '-' ) !== false ) {
				$range = explode( '-', $value );

				if ( count( $range ) === 2 ) {
					$min = intval( $range[0] );
					$max = intval( $range[1] );

					$options = array_merge( $options, range( $min, $max ) );
				}
			}
		}

		$options = array_unique( $options );
		foreach ( $options as $key => $number ) {
			if ( $min_value > $number || ( '' !== $max_value && $max_value < $number ) ) {
				unset( $options[ $key ] );
			}
		}
	}

	/* translators: %s: Quantity. */
	$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'minimog' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'minimog' );
	?>
	<div class="quantity quantity-select">
		<label class="screen-reader-text"
		       for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_attr( $label ); ?></label>
		<select name="<?php echo esc_attr( $input_name ); ?>" id="<?php echo esc_attr( $input_id ); ?>" class="qty woosb-qty">
			<?php foreach ( $options as $option ): ?>
				<option
					value="<?php echo esc_attr( $option ) ?>" <?php selected( $input_value, $option ); ?>><?php echo esc_html( $option ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php
}
