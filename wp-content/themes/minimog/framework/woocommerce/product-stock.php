<?php

namespace Minimog\Woo;

defined( 'ABSPATH' ) || exit;

class Product_Stock {
	protected static $instance = null;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function initialize() {
		add_filter( 'woocommerce_get_availability_text', [ $this, 'change_availability_text' ], 20, 2 );
		add_filter( 'woocommerce_format_stock_quantity', [ $this, 'change_quantity_format' ], 20, 2 );
	}

	/**
	 * @param string      $availability
	 * @param \WC_Product $product
	 *
	 * @return string
	 */
	public function change_availability_text( $availability, $product ) {
		if ( ! $product->is_in_stock() ) {
			$availability = __( 'Out of stock', 'minimog' );
		} elseif ( $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
			$availability = $product->backorders_require_notification() ? __( 'Available on backorder', 'minimog' ) : '';
		} elseif ( ! $product->managing_stock() && $product->is_on_backorder( 1 ) ) {
			$availability = __( 'Available on backorder', 'minimog' );
		} elseif ( $product->managing_stock() ) {
			$availability     = __( 'In stock', 'minimog' );
			$stock_amount     = $product->get_stock_quantity();
			$low_stock_amount = wc_get_low_stock_amount( $product );

			switch ( get_option( 'woocommerce_stock_format' ) ) {
				case 'low_amount':
					if ( $stock_amount <= $low_stock_amount ) {
						/* translators: %s: stock amount */
						$availability = sprintf(
							_n( 'Only %s item left in stock!', 'Only %s items left in stock!', $stock_amount, 'minimog' ),
							wc_format_stock_quantity_for_display( $stock_amount, $product )
						);
					}
					break;
				case '':
					if ( $stock_amount <= $low_stock_amount ) {
						/* translators: %s: stock amount */
						$availability = sprintf(
							_n( 'Only %s item left in stock!', 'Only %s items left in stock!', $stock_amount, 'minimog' ),
							wc_format_stock_quantity_for_display( $stock_amount, $product )
						);
					} else {
						/* translators: %s: stock amount */
						$availability = sprintf( __( '%s in stock', 'woocommerce' ), wc_format_stock_quantity_for_display( $stock_amount, $product ) );
					}
					break;
			}

			if ( $product->backorders_allowed() && $product->backorders_require_notification() ) {
				$availability .= ' ' . __( '(can be backordered)', 'minimog' );
			}
		} else {
			$availability = '';
		}

		return $availability;
	}

	/**
	 * @param $stock_quantity
	 * @param $product
	 *
	 * @return string
	 */
	public function change_quantity_format( $stock_quantity, $product ) {
		return '<span class="value">' . $stock_quantity . '</span>';
	}
}

Product_Stock::instance()->initialize();
