<?php

namespace Minimog\Woo;

defined( 'ABSPATH' ) || exit;

class Product_Bought_Together {
	protected static $localization = array();
	protected static $types        = array(
		'simple',
		'variable',
		'variation',
		'woosb',
		'bundle',
		'subscription',
	);

	protected static $instance = null;

	const MINIMUM_PLUGIN_VERSION   = '4.3.0';
	const RECOMMEND_PLUGIN_VERSION = '4.4.6';

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function initialize() {
		if ( ! $this->is_activate() ) {
			return;
		}

		if ( defined( 'WOOBT_VERSION' ) ) {
			if ( version_compare( WOOBT_VERSION, self::MINIMUM_PLUGIN_VERSION, '<' ) ) {
				return;
			}

			if ( version_compare( WOOBT_VERSION, self::RECOMMEND_PLUGIN_VERSION, '<' ) ) {
				add_action( 'admin_notices', [ $this, 'admin_notice_recommend_plugin_version' ] );
			}
		}

		add_action( 'init', [ $this, 'wp_init' ] );

		/**
		 * Remove all hooks then re-add
		 *
		 * @see \WPCleverWoobt::__construct()
		 */
		minimog_remove_filters_for_anonymous_class( 'woocommerce_before_add_to_cart_form', 'WPCleverWoobt', 'add_to_cart_form' );
		minimog_remove_filters_for_anonymous_class( 'woocommerce_after_add_to_cart_form', 'WPCleverWoobt', 'add_to_cart_form' );
		minimog_remove_filters_for_anonymous_class( 'woocommerce_single_product_summary', 'WPCleverWoobt', 'add_to_cart_form', 6 );
		minimog_remove_filters_for_anonymous_class( 'woocommerce_single_product_summary', 'WPCleverWoobt', 'add_to_cart_form', 11 );
		minimog_remove_filters_for_anonymous_class( 'woocommerce_single_product_summary', 'WPCleverWoobt', 'add_to_cart_form', 21 );
		minimog_remove_filters_for_anonymous_class( 'woocommerce_single_product_summary', 'WPCleverWoobt', 'add_to_cart_form', 41 );
		minimog_remove_filters_for_anonymous_class( 'woocommerce_after_single_product_summary', 'WPCleverWoobt', 'add_to_cart_form', 9 );
	}

	public function is_activate() {
		return class_exists( 'WPCleverWoobt' );
	}

	public function admin_notice_recommend_plugin_version() {
		minimog_notice_required_plugin_version( 'Frequently Bought Together for WooCommerce', self::RECOMMEND_PLUGIN_VERSION );
	}

	public function wp_init() {
		self::$localization = (array) get_option( 'woobt_localization' );
		self::$types        = apply_filters( 'woobt_product_types', self::$types );

		// Add to cart button & form.
		$woobt_position = apply_filters( 'woobt_position', get_option( '_woobt_position', apply_filters( 'woobt_default_position', 'before' ) ) );

		switch ( $woobt_position ) {
			case 'before':
				add_action( 'woocommerce_before_add_to_cart_form', [ $this, 'add_to_cart_form' ] );
				break;
			case 'after':
				add_action( 'woocommerce_after_add_to_cart_form', [ $this, 'add_to_cart_form' ] );
				break;
			case 'below_title':
				add_action( 'woocommerce_single_product_summary', [ $this, 'add_to_cart_form' ], 6 );
				break;
			case 'below_price':
				add_action( 'woocommerce_single_product_summary', [ $this, 'add_to_cart_form' ], 11 );
				break;
			case 'below_excerpt':
				add_action( 'woocommerce_single_product_summary', [ $this, 'add_to_cart_form' ], 21 );
				break;
			case 'below_meta':
				add_action( 'woocommerce_single_product_summary', [ $this, 'add_to_cart_form' ], 46 );
				break;
			case 'below_summary':
				add_action( 'woocommerce_after_single_product_summary', [ $this, 'add_to_cart_form' ], 9 );
				break;
		}
	}

	public function add_to_cart_form() {
		global $product;

		if ( ! $product || $product->is_type( 'grouped' ) ) {
			return;
		}

		$this->show_items();
	}

	/**
	 * Override html
	 *
	 * @see \WPCleverWoobt::show_items()
	 *
	 * @param null $product_id
	 * @param bool $is_custom_position
	 */
	public function show_items( $product_id = null, $is_custom_position = false ) {
		if ( ! $product_id ) {
			global $product;

			if ( $product ) {
				$product_id = $product->get_id();
			}
		} else {
			$product = wc_get_product( $product_id );
		}

		if ( ! $product_id || ! $product ) {
			return;
		}

		wp_enqueue_script( 'wc-add-to-cart-variation' );

		$items       = array();
		$pricing     = get_option( '_woobt_pricing', 'sale_price' );
		$custom_qty  = apply_filters( 'woobt_custom_qty', get_post_meta( $product_id, 'woobt_custom_qty', true ) === 'on', $product_id );
		$sync_qty    = apply_filters( 'woobt_sync_qty', get_post_meta( $product_id, 'woobt_sync_qty', true ) === 'on', $product_id );
		$checked_all = apply_filters( 'woobt_checked_all', get_post_meta( $product_id, 'woobt_checked_all', true ) === 'on', $product_id );
		$separately  = apply_filters( 'woobt_separately', get_post_meta( $product_id, 'woobt_separately', true ) === 'on', $product_id );
		$selection   = apply_filters( 'woobt_selection', get_post_meta( $product_id, 'woobt_selection', true ) ? : 'multiple', $product_id );
		$order       = 1;

		if ( $ids = $this->get_ids( $product_id ) ) {
			$items = $this->get_items( $ids, $product_id );
		}

		$default_products = get_option( '_woobt_default', 'none' );

		if ( ! $items ) {
			switch ( $default_products ) {
				case 'upsells':
					$items = $product->get_upsell_ids();
					break;
				case 'related':
					$items = wc_get_related_products( $product_id );
					break;
				case 'related_upsells':
					$items_upsells = $product->get_upsell_ids();
					$items_related = wc_get_related_products( $product_id );
					$items         = array_merge( $items_upsells, $items_related );
					break;
			}
		}

		// filter items before showing
		$items = apply_filters( 'woobt_show_items', $items, $product_id );

		if ( empty( $items ) ) {
			return;
		}

		$layout             = get_option( '_woobt_layout', 'default' );
		$is_separate_layout = $layout === 'separate';
		$is_separate_atc    = get_option( '_woobt_atc_button', 'main' ) === 'separate';

		$wrap_class = 'woobt-wrap woobt-layout-' . esc_attr( $layout ) . ' woobt-wrap-' . esc_attr( $product_id ) . ' ' . ( get_option( '_woobt_responsive', 'yes' ) === 'yes' ? 'woobt-wrap-responsive' : '' );

		if ( $is_custom_position ) {
			$wrap_class .= ' woobt-wrap-shortcode woobt-wrap-custom-position';
		}

		if ( $is_separate_atc ) {
			$wrap_class .= ' woobt-wrap-separate-atc';
		}

		$woobt_position    = apply_filters( 'woobt_position', get_option( '_woobt_position', apply_filters( 'woobt_default_position', 'before' ) ) );
		$is_outside_layout = 'below_summary' === $woobt_position && $is_separate_layout;

		$wrap_class .= ' woobt-position-' . $woobt_position;

		foreach ( $items as $key => $item ) {
			if ( is_array( $item ) ) {
				$_item['id']      = $item['id'];
				$_item['price']   = $item['price'];
				$_item['qty']     = $item['qty'];
				$_item['product'] = wc_get_product( $_item['id'] );
			} else {
				// make it works with upsells & related
				$_item['id']      = absint( $item );
				$_item['price']   = '100%';
				$_item['qty']     = 1;
				$_item['product'] = wc_get_product( $_item['id'] );
			}

			if ( ! $_item['product'] || ! in_array( $_item['product']->get_type(), self::$types, true ) || ( ( get_option( '_woobt_exclude_unpurchasable', 'no' ) === 'yes' ) && ( ! $_item['product']->is_purchasable() || ! $_item['product']->is_in_stock() ) ) ) {
				unset( $items[ $key ] );
				continue;
			}

			$items[ $key ] = $_item;
		}

		$thumbnail_width = 60;

		if ( $is_separate_layout ) {
			$thumbnail_width = 'below_summary' === $woobt_position ? 235 : 120;
		}

		$thumbnail_size = \Minimog_Woo::instance()->get_loop_product_image_size( $thumbnail_width );

		echo '<div class="' . esc_attr( $wrap_class ) . '" data-id="' . esc_attr( $product_id ) . '" data-selection="' . esc_attr( $selection ) . '">';

		echo '<h6 class="woobt-block-heading">' . esc_html__( 'Frequently Bought Together', 'minimog' ) . '</h6>';

		if ( $before_text = apply_filters( 'woobt_before_text', get_post_meta( $product_id, 'woobt_before_text', true ) ? : self::localization( 'above_text' ), $product_id ) ) {
			echo '<div class="woobt_before_text woobt-before-text woobt-text">' . do_shortcode( stripslashes( $before_text ) ) . '</div>';
		}

		do_action( 'woobt_wrap_before', $product );

		echo '<div class="woobt-block-content">';

		echo '<div class="woobt-body">';
		?>

		<?php if ( $is_separate_layout && 'below_summary' !== $woobt_position ) : ?>
			<div class="woobt_images woobt-images">
				<?php
				echo '<div class="woobt_image woobt-image-this woobt-image woobt-image-' . esc_attr( $product_id ) . '">' . \Minimog_Woo::instance()->get_product_image( $product, $thumbnail_size ) . '</div>';
				echo '<div class="woobt-image-plus">' . \Minimog_SVG_Manager::instance()->get( 'fal-plus' ) . '</div>';

				foreach ( $items as $item ) {
					$item_product = $item['product'];

					echo '<div class="woobt_image woobt-image woobt-image-' . esc_attr( $item['id'] ) . '">' . \Minimog_Woo::instance()->get_product_image( $item_product, $thumbnail_size ) . '</div>';
				}
				?>
			</div>
		<?php endif; ?>
		<div class="woobt-products-wrap">
			<div class="woobt_products woobt-products"
			     data-show-price="<?php echo esc_attr( get_option( '_woobt_show_price', 'yes' ) ); ?>"
			     data-optional="<?php echo esc_attr( $custom_qty ? 'on' : 'off' ); ?>"
			     data-sync-qty="<?php echo esc_attr( $sync_qty ? 'on' : 'off' ); ?>"
			     data-variables="<?php echo esc_attr( $this->has_variables( $items ) ? 'yes' : 'no' ); ?>"
			     data-product-id="<?php echo esc_attr( $product->is_type( 'variable' ) ? '0' : $product_id ); ?>"
			     data-product-type="<?php echo esc_attr( $product->get_type() ); ?>"
			     data-product-price-suffix="<?php echo esc_attr( htmlentities( $product->get_price_suffix() ) ); ?>"
			     data-product-sku="<?php echo esc_attr( $product->get_sku() ); ?>"
			     data-product-o-sku="<?php echo esc_attr( $product->get_sku() ); ?>"
			     data-product-price-html="<?php echo esc_attr( htmlentities( $product->get_price_html() ) ); ?>"
			     data-pricing="<?php echo esc_attr( $pricing ); ?>"
			     data-discount="<?php echo esc_attr( ! $separately && get_post_meta( $product_id, 'woobt_discount', true ) ? get_post_meta( $product_id, 'woobt_discount', true ) : '0' ); ?>"
			>
				<?php
				// this item
				if ( $is_custom_position || $is_separate_atc || $is_separate_layout || get_option( '_woobt_show_this_item', 'yes' ) !== 'no' ) {
					?>
					<div class="woobt-product woobt-product-this"
					     data-id="<?php echo esc_attr( $product->is_type( 'variable' ) || ! $product->is_in_stock() ? 0 : $product_id ); ?>"
					     data-pid="<?php echo esc_attr( $product_id ); ?>"
					     data-name="<?php echo esc_attr( $product->get_name() ); ?>"
					     data-new-price="<?php echo esc_attr( ! $separately && ( $discount = get_post_meta( $product_id, 'woobt_discount', true ) ) ? ( 100 - (float) $discount ) . '%' : '100%' ); ?>"
					     data-price-suffix="<?php echo esc_attr( htmlentities( $product->get_price_suffix() ) ); ?>"
					     data-price="<?php echo esc_attr( wc_get_price_to_display( $product ) ); ?>"
					     data-regular-price="<?php echo esc_attr( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) ); ?>"
					     data-qty="1"
					     data-qty-ori="1">

						<?php do_action( 'woobt_product_before', $product ); ?>

						<div class="woobt-thumb-wrap">
							<?php if ( ! $is_outside_layout ) : ?>
								<div class="woobt-choose">
									<input class="woobt-checkbox woobt-checkbox-this" type="checkbox" checked disabled/>
								</div>
							<?php endif; ?>

							<?php if ( get_option( '_woobt_show_thumb', 'yes' ) !== 'no' || ( 'below_summary' === $woobt_position && $is_separate_layout ) ) { ?>
								<div class="woobt-thumb">
									<?php echo \Minimog_Woo::instance()->get_product_image( $product, $thumbnail_size ); ?>

									<?php echo '<div class="woobt-image-plus">' . \Minimog_SVG_Manager::instance()->get( 'fal-plus' ) . '</div>'; ?>
								</div>
							<?php } ?>
						</div>

						<div class="woobt-product-info">
							<h3 class="woobt-title">
								<?php echo '<span>' . esc_html__( 'This item:', 'minimog' ) . '</span> ' . $product->get_name(); ?>
							</h3>

							<div class="woobt-product-cart">
								<?php if ( $is_outside_layout ) : ?>
									<div class="woobt-choose">
										<input class="woobt-checkbox woobt-checkbox-this" type="checkbox" checked
										       disabled/>
									</div>
								<?php endif; ?>

								<?php if ( ( ! $is_separate_layout && get_option( '_woobt_show_price', 'yes' ) !== 'no' ) ) : ?>
									<div class="woobt-price">
										<div class="woobt-price-new">
											<?php
											if ( ! $separately && ( $discount = get_post_meta( $product_id, 'woobt_discount', true ) ) ) {
												$sale_price = $product->get_price() * ( 100 - (float) $discount ) / 100;
												echo wc_format_sale_price( $product->get_price(), $sale_price ) . $product->get_price_suffix( $sale_price );
											} else {
												echo '' . $product->get_price_html();
											}
											?>
										</div>
										<div class="woobt-price-ori">
											<?php echo '' . $product->get_price_html(); ?>
										</div>
									</div>
								<?php endif; ?>

								<?php if ( $product->is_type( 'variable' ) ) : ?>
									<div class="minimog-variation-select-wrap">
										<?php
										if ( ( get_option( '_woobt_variations_selector', 'default' ) === 'wpc_radio' ) && class_exists( 'WPClever_Woovr' ) ) {
											echo '<div class="wpc_variations_form">';
											// use class name wpc_variations_form to prevent found_variation in woovr
											\WPClever_Woovr::woovr_variations_form( $product );
											echo '</div>';
										} else {
											if ( $is_separate_atc ) {
												\Minimog_Woo::instance()->get_product_variation_dropdown_html( $product, [
													'show_label' => false,
													'show_price' => true,
												] );
											}

											$attributes           = $product->get_variation_attributes();
											$available_variations = $product->get_available_variations();

											if ( is_array( $attributes ) && ( count( $attributes ) > 0 ) ) {
												echo '<div class="variations_form" data-product_id="' . absint( $product_id ) . '" data-product_variations="' . htmlspecialchars( wp_json_encode( $available_variations ) ) . '">';
												echo '<div class="variations">';

												foreach ( $attributes as $attribute_name => $options ) { ?>
													<div class="variation">
														<div class="label">
															<?php echo wc_attribute_label( $attribute_name ); ?>
														</div>
														<div class="select">
															<?php
															$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $product->get_variation_default_attribute( $attribute_name );
															wc_dropdown_variation_attribute_options( array(
																'options'          => $options,
																'attribute'        => $attribute_name,
																'product'          => $product,
																'selected'         => $selected,
																'show_option_none' => esc_html__( 'Choose', 'minimog' ) . ' ' . wc_attribute_label( $attribute_name ),
															) );
															?>
														</div>
													</div><!-- /variation -->
												<?php }

												echo '<div class="reset">' . apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'minimog' ) . '</a>' ) . '</div>';
												echo '</div><!-- /variations -->';
												echo '</div><!-- /variations_form -->';

												if ( get_option( '_woobt_show_description', 'no' ) === 'yes' ) {
													echo '<div class="woobt-variation-description"></div>';
												}
											}
										} ?>
									</div>
								<?php endif; ?>

								<?php if ( $custom_qty ) : ?>
									<div class="woobt-quantity">
										<?php
										if ( get_option( '_woobt_plus_minus', 'no' ) === 'yes' ) {
											echo '<div class="woobt-quantity-input">';
											echo '<div class="woobt-quantity-input-minus">-</div>';
										}

										woocommerce_quantity_input( array(
											'input_name' => 'woobt_qty_0',
											'classes'    => array(
												'input-text',
												'woobt-qty',
												'woobt-this-qty',
												'qty',
												'text',
											),
										), $product );

										if ( get_option( '_woobt_plus_minus', 'no' ) === 'yes' ) {
											echo '<div class="woobt-quantity-input-plus">+</div>';
											echo '</div><!-- /woobt-quantity-input -->';
										}
										?>
									</div>
								<?php endif; ?>

								<?php echo '<div class="woobt-availability">' . wc_get_stock_html( $product ) . '</div>'; ?>
							</div>

							<?php do_action( 'woobt_product_after', $product ); ?>
						</div>
					</div>
					<?php
				} else {
					?>
					<div class="woobt-product woobt-product-this woobt-hide-this"
					     data-id="<?php echo esc_attr( $product->is_type( 'variable' ) || ! $product->is_in_stock() ? 0 : $product_id ); ?>"
					     data-pid="<?php echo esc_attr( $product_id ); ?>"
					     data-name="<?php echo esc_attr( $product->get_name() ); ?>"
					     data-price="<?php echo esc_attr( wc_get_price_to_display( $product ) ); ?>"
					     data-regular-price="<?php echo esc_attr( wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ) ); ?>"
					     data-qty="1" style="display:none;">
						<div class="woobt-choose">
							<input class="woobt-checkbox woobt-checkbox-this" type="checkbox" checked disabled/>
						</div>
					</div>
					<?php
				}

				// other items
				foreach ( $items as $item ) {
					$item_id    = $item['id'];
					$item_price = $item['price'];
					$item_qty   = $item['qty'];
					/**
					 * @var \WC_Product_Variable $item_product
					 */
					$item_product = $item['product'];
					$item_qty_min = 1;
					$item_qty_max = 1000;

					if ( $custom_qty ) {
						if ( get_post_meta( $product_id, 'woobt_limit_each_min_default', true ) === 'on' ) {
							$item_qty_min = $item_qty;
						} else {
							$item_qty_min = absint( get_post_meta( $product_id, 'woobt_limit_each_min', true ) ? : 0 );
						}

						$item_qty_max = absint( get_post_meta( $product_id, 'woobt_limit_each_max', true ) ? : 1000 );

						if ( $item_qty < $item_qty_min ) {
							$item_qty = $item_qty_min;
						}

						if ( $item_qty > $item_qty_max ) {
							$item_qty = $item_qty_max;
						}
					}

					$checked_individual = apply_filters( 'woobt_checked_individual', false, $item_id, $product_id );
					$item_price         = apply_filters( 'woobt_item_price', ! $separately ? $item_price : '100%', $item_id, $product_id );
					?>
					<div class="woobt-product woobt-product-together"
					     data-id="<?php echo esc_attr( $item_product->is_type( 'variable' ) || ! $item_product->is_in_stock() ? 0 : $item_id ); ?>"
					     data-pid="<?php echo esc_attr( $item_id ); ?>"
					     data-name="<?php echo esc_attr( $item_product->get_name() ); ?>"
					     data-new-price="<?php echo esc_attr( $item_price ); ?>"
					     data-price-suffix="<?php echo esc_attr( htmlentities( $item_product->get_price_suffix() ) ); ?>"
					     data-price="<?php echo esc_attr( ( $pricing === 'sale_price' ) ? wc_get_price_to_display( $item_product ) : wc_get_price_to_display( $item_product, array( 'price' => $item_product->get_regular_price() ) ) ); ?>"
					     data-regular-price="<?php echo esc_attr( wc_get_price_to_display( $item_product, array( 'price' => $item_product->get_regular_price() ) ) ); ?>"
					     data-qty="<?php echo esc_attr( $item_qty ); ?>"
					     data-qty-ori="<?php echo esc_attr( $item_qty ); ?>">

						<?php do_action( 'woobt_product_before', $item_product, $order ); ?>

						<?php ob_start(); ?>
						<div class="woobt-choose">
							<input class="woobt-checkbox" type="checkbox"
							       value="<?php echo esc_attr( $item_id ); ?>"
								<?php if ( ! $item_product->is_in_stock() ) : ?>
									disabled="disabled"
								<?php endif; ?>
								<?php if ( $item_product->is_in_stock() && ( $checked_all || $checked_individual ) ): ?>
									checked="checked"
								<?php endif; ?>
							/>
						</div>
						<?php $choose_html = ob_get_clean(); ?>

						<div class="woobt-thumb-wrap">
							<?php if ( ! $is_outside_layout ) : ?>
								<?php echo '' . $choose_html; ?>
							<?php endif; ?>

							<?php if ( get_option( '_woobt_show_thumb', 'yes' ) !== 'no' || ( 'below_summary' === $woobt_position && $is_separate_layout ) ) { ?>
								<div class="woobt-thumb">
									<?php
									/**
									 * Disabled variation image changed.
									 * Because it not support properly image size.
									 * Move img out of div to make js disabled.
									 */
									?>
									<!--<div class="woobt-thumb-ori"></div>
									<div class="woobt-thumb-new"></div>-->
									<?php echo \Minimog_Woo::instance()->get_product_image( $item_product, $thumbnail_size ); ?>
								</div>
							<?php } ?>
						</div>

						<div class="woobt-product-info">
							<h3 class="woobt-title">
								<?php
								/*if ( ! $custom_qty ) {
									$item_product_qty = '<span class="woobt-qty-num"><span class="woobt-qty">' . $item_qty . '</span> × </span>';
								} else {
									$item_product_qty = '';
								}

								echo apply_filters( 'woobt_product_qty', $item_product_qty, $item_qty, $item_product );*/

								if ( $item_product->is_in_stock() ) {
									$item_product_name = $item_product->get_name();
								} else {
									$item_product_name = '<s>' . $item_product->get_name() . '</s>';
								}

								if ( get_option( '_woobt_link', 'yes' ) !== 'no' ) {
									$item_product_name = '<a ' . ( get_option( '_woobt_link', 'yes' ) === 'yes_popup' ? 'class="woosq-btn" data-id="' . $item_id . '"' : '' ) . ' href="' . $item_product->get_permalink() . '" ' . ( get_option( '_woobt_link', 'yes' ) === 'yes_blank' ? 'target="_blank"' : '' ) . '>' . $item_product_name . '</a>';
								} else {
									$item_product_name = '<span>' . $item_product_name . '</span>';
								}

								echo apply_filters( 'woobt_product_name', $item_product_name, $item_product );
								?>
							</h3><!-- /woobt-title -->

							<?php if ( get_option( '_woobt_show_description', 'no' ) === 'yes' && ! $is_separate_layout ) : ?>
								<?php echo '<div class="woobt-description">' . $item_product->get_short_description() . '</div>'; ?>
							<?php endif; ?>
							<div class="woobt-product-cart">
								<?php if ( $is_outside_layout ) : ?>
									<?php echo '' . $choose_html; ?>
								<?php endif; ?>

								<?php if ( 'variable' !== $item_product->get_type() || ! $is_separate_layout ) : ?>
									<div class="woobt-price">
										<div class="woobt-price-new"></div>
										<div class="woobt-price-ori">
											<?php
											if ( ! $separately && ( $item_price !== '100%' ) ) {
												if ( $item_product->is_type( 'variable' ) ) {
													$item_ori_price_min = ( $pricing === 'sale_price' ) ? $item_product->get_variation_price( 'min', true ) : $item_product->get_variation_regular_price( 'min', true );
													$item_ori_price_max = ( $pricing === 'sale_price' ) ? $item_product->get_variation_price( 'max', true ) : $item_product->get_variation_regular_price( 'max', true );
													$item_new_price_min = self::new_price( $item_ori_price_min, $item_price );
													$item_new_price_max = self::new_price( $item_ori_price_max, $item_price );

													if ( $item_new_price_min < $item_new_price_max ) {
														$item_product_price = wc_format_price_range( $item_new_price_min, $item_new_price_max );
													} else {
														$item_product_price = wc_format_sale_price( $item_ori_price_min, $item_new_price_min );
													}
												} else {
													$item_ori_price = ( $pricing === 'sale_price' ) ? wc_get_price_to_display( $item_product, array( 'price' => $item_product->get_price() ) ) : wc_get_price_to_display( $item_product, array( 'price' => $item_product->get_regular_price() ) );
													$item_new_price = self::new_price( $item_ori_price, $item_price );

													if ( $item_new_price < $item_ori_price ) {
														$item_product_price = wc_format_sale_price( $item_ori_price, $item_new_price );
													} else {
														$item_product_price = wc_price( $item_new_price );
													}
												}

												$item_product_price .= $item_product->get_price_suffix();
											} else {
												$item_product_price = $item_product->get_price_html();
											}

											echo apply_filters( 'woobt_product_price', $item_product_price, $item_product, $item );
											?>
										</div>
									</div><!-- /woobt-price -->
								<?php endif; ?>

								<?php if ( $item_product->is_type( 'variable' ) ) : ?>
									<div class="minimog-variation-select-wrap">
										<?php
										if ( ( get_option( '_woobt_variations_selector', 'default' ) === 'wpc_radio' ) && class_exists( 'WPClever_Woovr' ) ) {
											echo '<div class="wpc_variations_form">';
											// use class name wpc_variations_form to prevent found_variation in woovr
											\WPClever_Woovr::woovr_variations_form( $item_product );
											echo '</div>';
										} else {
											\Minimog_Woo::instance()->get_product_variation_dropdown_html( $item_product, [
												'show_label' => false,
												'show_price' => true,
											] );

											$attributes           = $item_product->get_variation_attributes();
											$available_variations = $item_product->get_available_variations();

											if ( is_array( $attributes ) && ( count( $attributes ) > 0 ) ) {
												echo '<div class="variations_form" data-product_id="' . absint( $item_product->get_id() ) . '" data-product_variations="' . htmlspecialchars( wp_json_encode( $available_variations ) ) . '">';
												echo '<div class="variations">';

												foreach ( $attributes as $attribute_name => $options ) { ?>
													<div class="variation">
														<div class="label">
															<?php echo wc_attribute_label( $attribute_name ); ?>
														</div>
														<div class="select">
															<?php
															$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( stripslashes( urldecode( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ) ) : $item_product->get_variation_default_attribute( $attribute_name );
															wc_dropdown_variation_attribute_options( array(
																'options'          => $options,
																'attribute'        => $attribute_name,
																'product'          => $item_product,
																'selected'         => $selected,
																'show_option_none' => esc_html__( 'Choose', 'minimog' ) . ' ' . wc_attribute_label( $attribute_name ),
															) );
															?>
														</div>
													</div><!-- /variation -->
												<?php }

												echo '<div class="reset">' . apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'minimog' ) . '</a>' ) . '</div>';
												echo '</div><!-- /variations -->';
												echo '</div><!-- /variations_form -->';

												if ( get_option( '_woobt_show_description', 'no' ) === 'yes' ) {
													echo '<div class="woobt-variation-description"></div>';
												}
											}
										}
										?>
									</div>
								<?php endif; ?>

								<?php if ( $custom_qty ) : ?>
									<?php
									echo '<div class="woobt-quantity">';

									if ( get_option( '_woobt_plus_minus', 'no' ) === 'yes' ) {
										echo '<div class="woobt-quantity-input">';
										echo '<div class="woobt-quantity-input-minus">-</div>';
									}

									woocommerce_quantity_input( array(
										'classes'     => array( 'input-text', 'woobt-qty', 'qty', 'text' ),
										'input_value' => $item_qty,
										'min_value'   => $item_qty_min,
										'max_value'   => $item_qty_max,
										'input_name'  => 'woobt_qty_' . $order,
										'woobt_qty'   => array(
											'input_value' => $item_qty,
											'min_value'   => $item_qty_min,
											'max_value'   => $item_qty_max,
										)
										// compatible with WPC Product Quantity
									), $item_product );

									if ( get_option( '_woobt_plus_minus', 'no' ) === 'yes' ) {
										echo '<div class="woobt-quantity-input-plus">+</div>';
										echo '</div><!-- /woobt-quantity-input -->';
									}

									echo '</div><!-- /woobt-quantity -->';
									?>
								<?php endif; ?>

								<?php echo '<div class="woobt-availability">' . wc_get_stock_html( $item_product ) . '</div>'; ?>
							</div>

							<?php do_action( 'woobt_product_after', $item_product, $order ); ?>
						</div>
					</div>
					<?php
					$order++;
				} ?>
			</div>
		</div>
		<?php
		echo '</div><!-- /woobt-body -->';

		echo '<div class="woobt-footer"><div class="woobt-footer-inner">';

		echo '<div class="woobt_total woobt-total"></div>';

		if ( $is_custom_position || $is_separate_atc ) {
			echo '<div class="woobt-actions">';
			echo '<div class="woobt-form">';
			echo '<input type="hidden" name="woobt_ids" class="woobt-ids woobt-ids-' . esc_attr( $product->get_id() ) . '" data-id="' . esc_attr( $product->get_id() ) . '"/>';
			echo '<input type="hidden" name="quantity" value="1"/>';
			echo '<input type="hidden" name="product_id" value="' . esc_attr( $product_id ) . '">';
			echo '<input type="hidden" name="variation_id" class="variation_id" value="0">';
			echo '<button type="submit" class="single_add_to_cart_button button alt"><span>' . self::localization( 'add_all_to_cart', esc_html__( 'Add selected items', 'minimog' ) ) . '</span></button>';
			echo '</div>';
			echo '</div>';
		}

		echo '<div class="woobt_alert woobt-alert" style="display: none"></div>';

		echo '</div></div><!-- /woobt-footer -->';

		echo '</div><!-- /woobt-block-content -->';

		if ( $after_text = apply_filters( 'woobt_after_text', get_post_meta( $product_id, 'woobt_after_text', true ) ? : self::localization( 'under_text' ), $product_id ) ) {
			echo '<div class="woobt_after_text woobt-after-text woobt-text">' . do_shortcode( stripslashes( $after_text ) ) . '</div>';
		}

		do_action( 'woobt_wrap_after', $product );

		echo '</div><!-- /woobt-wrap -->';
	}

	public function get_ids( $product_id, $context = 'display' ) {
		$ids = get_post_meta( $product_id, 'woobt_ids', true );

		return apply_filters( 'woobt_get_ids', $ids, $product_id, $context );
	}

	public function get_items( $ids, $product_id = 0, $context = 'view' ) {
		$items = array();
		$ids   = self::clean_ids( $ids );

		if ( ! empty( $ids ) ) {
			$_items = explode( ',', $ids );

			if ( is_array( $_items ) && count( $_items ) > 0 ) {
				foreach ( $_items as $_item ) {
					$_item_data    = explode( '/', $_item );
					$_item_id      = apply_filters( 'woobt_item_id', absint( $_item_data[0] ? : 0 ) );
					$_item_product = wc_get_product( $_item_id );

					if ( ! $_item_product ) {
						continue;
					}

					if ( ( $context === 'view' ) && ( ( get_option( '_woobt_exclude_unpurchasable', 'no' ) === 'yes' ) && ( ! $_item_product->is_purchasable() || ! $_item_product->is_in_stock() ) ) ) {
						continue;
					}

					$items[] = array(
						'id'    => $_item_id,
						'price' => isset( $_item_data[1] ) ? self::format_price( $_item_data[1] ) : '100%',
						'qty'   => (float) ( isset( $_item_data[2] ) ? $_item_data[2] : 1 ),
						'attrs' => isset( $_item_data[3] ) ? (array) json_decode( rawurldecode( $_item_data[3] ) ) : array(),
					);
				}
			}
		}

		$items = apply_filters( 'woobt_get_items', $items, $ids, $product_id, $context );

		if ( $items && is_array( $items ) && count( $items ) > 0 ) {
			return $items;
		}

		return false;
	}

	public static function clean_ids( $ids ) {
		//$ids = preg_replace( '/[^.%,\/0-9]/', '', $ids );

		return apply_filters( 'woobt_clean_ids', $ids );
	}

	public static function format_price( $price ) {
		// format price to percent or number
		$price = preg_replace( '/[^.%0-9]/', '', $price );

		return apply_filters( 'woobt_format_price', $price );
	}

	public static function new_price( $old_price, $new_price ) {
		if ( strpos( $new_price, '%' ) !== false ) {
			$calc_price = ( (float) $new_price * $old_price ) / 100;
		} else {
			$calc_price = $new_price;
		}

		return apply_filters( 'woobt_new_price', $calc_price, $old_price );
	}

	public function localization( $key = '', $default = '' ) {
		$str = '';

		if ( ! empty( $key ) && ! empty( self::$localization[ $key ] ) ) {
			$str = self::$localization[ $key ];
		} elseif ( ! empty( $default ) ) {
			$str = $default;
		}

		return apply_filters( 'woobt_localization_' . $key, $str );
	}

	public function has_variables( $items ) {
		foreach ( $items as $item ) {
			if ( is_array( $item ) && isset( $item['id'] ) ) {
				$item_id = $item['id'];
			} else {
				$item_id = absint( $item );
			}

			$item_product = wc_get_product( $item_id );

			if ( ! $item_product ) {
				continue;
			}

			if ( $item_product->is_type( 'variable' ) ) {
				return true;
			}
		}

		return false;
	}
}

Product_Bought_Together::instance()->initialize();
