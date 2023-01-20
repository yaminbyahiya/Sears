<?php
defined( 'ABSPATH' ) || exit;

$total_frames         = absint( get_post_meta( $attachment_id, 'minimog_360_total_frames', true ) );
$total_frames_per_row = absint( get_post_meta( $attachment_id, 'minimog_360_total_frames_per_row', true ) );

$product_360_settings = [
	'source'  => $sprite_image_url,
	'frames'  => $total_frames,
	'framesX' => $total_frames_per_row,
	'width'   => 540,
	'height'  => Minimog_Woo::instance()->get_product_image_height_by_width( 540 ),
];
?>
<div class="minimog-modal modal-product-360" id="<?php echo 'modal-product-360-' . $attachment_id; ?>">
	<div class="modal-overlay"></div>
	<div class="modal-content">
		<div class="button-close-modal"></div>
		<div class="modal-content-wrap">
			<div class="modal-content-inner">
				<div class="product-spritespin"
				     data-spritespin-settings="<?php echo esc_attr( wp_json_encode( $product_360_settings ) ); ?>"></div>
			</div>
		</div>
	</div>
</div>
