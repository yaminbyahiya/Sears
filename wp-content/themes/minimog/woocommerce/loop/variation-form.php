<?php
/**
 * Swatches selection
 */

defined( 'ABSPATH' ) || exit;

/**
 * @var \WC_Product_Variable $product
 */
global $product;

if ( 'variable' !== $product->get_type() ) {
	return;
}

if ( ! class_exists( 'Insight_Swatches' ) ) {
	return;
}

$selected_attribute = get_post_meta( $product->get_id(), 'variation_attributes_show_on_loop', true );
if ( empty( $selected_attribute ) ) {
	return;
}

$taxonomy_id   = wc_attribute_taxonomy_id_by_name( $selected_attribute );
$taxonomy_info = wc_get_attribute( $taxonomy_id );

if ( is_wp_error( $taxonomy_info ) || empty( $taxonomy_info ) ) {
	return;
}

$available_variations = $product->get_available_variations();

if ( empty( $available_variations ) ) {
	return;
}

$terms = wp_get_post_terms( $product->get_id(), $taxonomy_info->slug, array( 'hide_empty' => false ) );

if ( is_wp_error( $terms ) || empty( $terms ) ) {
	return;
}

/**
 * @since 1.2.1
 * Test up if terms used as variation
 * Skip render terms without used.
 */
foreach ( $available_variations as $variation ) {
	$attributes = $variation['attributes'];

	foreach ( $attributes as $attribute_name => $attribute_value ) {
		if ( strpos( $attribute_name, $taxonomy_info->slug ) !== false ) {
			foreach ( $terms as $term ) {
				if ( $term->slug === $attribute_value ) {
					$term->hasVariation = true;
				}
			}
		}
	}
}

$variations_images = [];

foreach ( $available_variations as $variation ) {
	$variation_image_src = Minimog_Image::get_attachment_url_by_id( [
		'id'   => $variation['image_id'],
		'size' => $args['thumbnail_size'],
	] );

	$variations_images[ $variation['variation_id'] ] = $variation_image_src;
}

$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

$term_link_classes = 'js-product-variation-term term-link hint--top';
?>
<div class="loop-product-variation-selector variation-selector-type-<?php echo esc_attr( $taxonomy_info->type ); ?>"
     data-product_attribute="attribute_<?php echo esc_attr( $taxonomy_info->slug ); ?>"
     data-product_variations="<?php echo '' . $variations_attr; // WPCS: XSS ok. ?>"
     data-product_variations_images="<?php echo esc_attr( wp_json_encode( $variations_images ) ); ?>"
>
	<div class="inner">
		<?php
		switch ( $taxonomy_info->type ) :
			case 'color':
				foreach ( $terms as $term ) :
					if ( empty( $term->hasVariation ) ) {
						continue;
					}

					$val     = get_term_meta( $term->term_id, 'sw_color', true ) ? : '#fff';
					$tooltip = get_term_meta( $term->term_id, 'sw_tooltip', true ) ? : $term->name;
					?>
					<a href="#" aria-label="<?php echo esc_attr( $tooltip ); ?>"
					   class="<?php echo esc_attr( $term_link_classes ); ?>"
					   data-term="<?php echo esc_attr( $term->slug ); ?>">
						<div class="term-shape">
							<span style="background: <?php echo esc_attr( $val ); ?>" class="term-shape-bg"></span>
							<span class="term-shape-border"></span>
						</div>
						<div class="term-name"><?php echo esc_html( $term->name ); ?></div>
					</a>
				<?php
				endforeach;
				break;
			case 'image':
				foreach ( $terms as $term ) :
					if ( empty( $term->hasVariation ) ) {
						continue;
					}
					$val     = get_term_meta( $term->term_id, 'sw_image', true );
					$tooltip = get_term_meta( $term->term_id, 'sw_tooltip', true ) ? : $term->name;

					if ( ! empty( $val ) ) {
						$image_url = wp_get_attachment_thumb_url( $val );
					} else {
						$image_url = wc_placeholder_img_src();
					}
					?>
					<a href="#" aria-label="<?php echo esc_attr( $tooltip ); ?>"
					   class="<?php echo esc_attr( $term_link_classes ); ?>"
					   data-term="<?php echo esc_attr( $term->slug ); ?>">
						<div class="term-shape">
						<span style="background-image: url(<?php echo esc_attr( $image_url ); ?>);"
						      class="term-shape-bg"></span>
							<span class="term-shape-border"></span>
						</div>
						<div class="term-name"><?php echo esc_html( $term->name ); ?></div>
					</a>
				<?php
				endforeach;
				break;
			default:
				foreach ( $terms as $term ) :
					if ( empty( $term->hasVariation ) ) {
						continue;
					}
					$tooltip = get_term_meta( $term->term_id, 'sw_tooltip', true ) ? : $term->name;

					?>
					<a href="#" aria-label="<?php echo esc_attr( $tooltip ); ?>"
					   class="<?php echo esc_attr( $term_link_classes ); ?>"
					   data-term="<?php echo esc_attr( $term->slug ); ?>">
						<div class="term-name"><?php echo esc_html( $term->name ); ?></div>
					</a>
				<?php
				endforeach;
				break;
		endswitch;
		?>
	</div>
</div>
