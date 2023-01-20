<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Minimog_WP_Widget_Product_Brand_Nav' ) ) {
	class Minimog_WP_Widget_Product_Brand_Nav extends Minimog_WC_Widget_Base {

		const TAXONOMY_NAME = 'product_brand';

		public function __construct() {
			$this->widget_id          = 'minimog-wp-widget-product-brand-nav';
			$this->widget_cssclass    = 'minimog-wp-widget-product-brand-nav minimog-wp-widget-filter';
			$this->widget_name        = sprintf( '%1$s %2$s', '[Minimog]', esc_html__( 'Product Brand Layered Nav', 'minimog' ) );
			$this->widget_description = esc_html__( 'Shows brands in a widget which lets you narrow down the list of products when viewing products.', 'minimog' );
			$this->settings           = array(
				'title'             => array(
					'type'  => 'text',
					'std'   => esc_html__( 'Brands', 'minimog' ),
					'label' => esc_html__( 'Title', 'minimog' ),
				),
				'display_type'      => array(
					'type'    => 'select',
					'std'     => 'list',
					'label'   => esc_html__( 'Display type', 'minimog' ),
					'options' => array(
						'list'     => esc_html__( 'List', 'minimog' ),
						'dropdown' => esc_html__( 'Dropdown', 'minimog' ),
					),
				),
				'list_style'        => array(
					'type'    => 'select',
					'std'     => 'normal',
					'label'   => esc_html__( 'List Style', 'minimog' ),
					'options' => array(
						'normal'   => esc_html__( 'Normal List', 'minimog' ),
						'checkbox' => esc_html__( 'Checkbox List', 'minimog' ),
					),
				),
				'items_count'       => array(
					'type'    => 'select',
					'std'     => 'on',
					'label'   => esc_html__( 'Show items count', 'minimog' ),
					'options' => array(
						'on'  => esc_html__( 'ON', 'minimog' ),
						'off' => esc_html__( 'OFF', 'minimog' ),
					),
				),
				'show_hierarchy'    => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => esc_html__( 'Show hierarchy', 'minimog' ),
				),
				'enable_scrollable' => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => esc_html__( 'Enable scrollable', 'minimog' ),
				),
				'enable_collapsed'  => array(
					'type'  => 'checkbox',
					'std'   => 0,
					'label' => esc_html__( 'Collapsed ?', 'minimog' ),
				),
			);

			parent::__construct();
		}

		public function widget( $args, $instance ) {
			if ( ! is_shop() && ! is_product_taxonomy() ) {
				return;
			}

			if ( ! taxonomy_exists( self::TAXONOMY_NAME ) ) {
				return;
			}

			// Get only parent terms. Methods will recursively retrieve children.
			$terms = get_terms( self::TAXONOMY_NAME, array(
				'hide_empty' => '1',
				'parent'     => 0,
			) );

			if ( empty( $terms ) ) {
				return;
			}

			ob_start();

			$this->widget_start( $args, $instance );

			$display_type = isset( $instance['display_type'] ) ? $instance['display_type'] : 'list';
			if ( 'dropdown' === $display_type ) {
				$found = $this->layered_nav_dropdown( $terms, $instance );
			} else {
				$found = $this->layered_nav_list( $terms, $instance );
			}

			$this->widget_end( $args, $instance );

			// Force found when option is selected - do not force found on taxonomy attributes.
			$_chosen_attributes = \Minimog\Woo\Product_Query::get_layered_nav_chosen_attributes();
			if ( ! is_tax() && is_array( $_chosen_attributes ) && array_key_exists( self::TAXONOMY_NAME, $_chosen_attributes ) ) {
				$found = true;
			}

			if ( ! $found ) {
				ob_end_clean();
			} else {
				echo ob_get_clean();
			}
		}

		public function get_chosen_attributes() {
			$terms = [];

			if ( ! empty( $_GET['filter_product_brand'] ) ) {
				$terms = array_map( 'intval', explode( ',', $_GET['filter_product_brand'] ) );
			}

			/**
			 * Send from Ajax request.
			 */
			if ( isset( $_GET['is_current_term_id'] ) ) {
				$terms[] = $_GET['is_current_term_id'];
			} elseif ( Minimog_Woo::instance()->is_product_taxonomy() ) {
				$terms[] = get_queried_object()->term_id;
			}

			return $terms;
		}

		protected function layered_nav_dropdown( $terms, $instance, $depth = 0 ) {
			$found = false;

			if ( self::TAXONOMY_NAME !== $this->get_current_taxonomy() ) {
				$term_counts        = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), self::TAXONOMY_NAME, 'or' );
				$_chosen_attributes = $this->get_chosen_attributes();

				if ( 0 == $depth ) {
					echo '<select class="wc-brand-dropdown-layered-nav-' . esc_attr( self::TAXONOMY_NAME ) . '">';
					echo '<option value="">' . __( 'Any Brand', 'minimog' ) . '</option>';
				}

				foreach ( $terms as $term ) {
					// If on a term page, skip that term in widget list
					if ( $term->term_id === $this->get_current_term_id() ) {
						continue;
					}

					// Get count based on current view
					$current_values = ! empty( $_chosen_attributes ) ? $_chosen_attributes : array();
					$option_is_set  = in_array( $term->term_id, $current_values );
					$count          = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

					// Only show options with count > 0
					if ( 0 < $count ) {
						$found = true;
					} elseif ( 0 === $count && ! $option_is_set ) {
						continue;
					}

					echo '<option value="' . esc_attr( $term->term_id ) . '" ' . selected( $option_is_set, true, false ) . '>' . str_repeat( '&nbsp;', 2 * $depth ) . esc_html( $term->name ) . '</option>';

					$child_terms = get_terms( self::TAXONOMY_NAME, array(
						'hide_empty' => 1,
						'parent'     => $term->term_id,
					) );

					if ( ! empty( $child_terms ) ) {
						$found |= $this->layered_nav_dropdown( $child_terms, $instance, $depth + 1 );
					}
				}

				if ( 0 == $depth ) {
					echo '</select>';

					wc_enqueue_js( "
					jQuery( '.wc-brand-dropdown-layered-nav-" . esc_js( self::TAXONOMY_NAME ) . "' ).change( function() {
						var slug = jQuery( this ).val();
						location.href = '" . preg_replace( '%\/page\/[0-9]+%', '', str_replace( array(
							'&amp;',
							'%2C',
						), array( '&', ',' ), esc_js( add_query_arg( 'filtering', '1', remove_query_arg( array(
							'page',
							'filter_' . self::TAXONOMY_NAME,
						) ) ) ) ) ) . "&filter_" . esc_js( self::TAXONOMY_NAME ) . "=' + jQuery( '.wc-brand-dropdown-layered-nav-" . esc_js( self::TAXONOMY_NAME ) . "' ).val();
					});
				" );
				}
			}

			return $found;
		}

		protected function layered_nav_list( $terms, $instance, $depth = 0 ) {
			$found = false;

			$items_count    = $this->get_value( $instance, 'items_count' );
			$display_type   = $this->get_value( $instance, 'display_type' );
			$list_style     = $this->get_value( $instance, 'list_style' );
			$show_hierarchy = $this->get_value( $instance, 'show_hierarchy' );

			if ( self::TAXONOMY_NAME !== $this->get_current_taxonomy() ) {
				$class = [
					'show-display-' . $display_type,
					'show-items-count-' . $items_count,
					'list-style-' . $list_style,
				];

				if ( $depth > 0 ) {
					$class[] = 'children';
				}

				echo '<ul class="' . esc_attr( implode( ' ', $class ) ) . '">';

				$term_counts        = $this->get_filtered_term_product_counts( wp_list_pluck( $terms, 'term_id' ), self::TAXONOMY_NAME, 'or' );
				$_chosen_attributes = $this->get_chosen_attributes();
				$current_values     = ! empty( $_chosen_attributes ) ? $_chosen_attributes : array();
				$found              = false;

				$filter_name = 'filter_' . self::TAXONOMY_NAME;
				$base_link   = $this->get_current_page_url();

				foreach ( $terms as $term ) {
					$option_is_set = in_array( $term->term_id, $current_values );
					$count         = isset( $term_counts[ $term->term_id ] ) ? $term_counts[ $term->term_id ] : 0;

					// Only show options with count > 0.
					if ( $count > 0 ) {
						$found = true;
					} else {
						continue;
					}

					$current_filter = isset( $_GET[ $filter_name ] ) ? explode( ',', wc_clean( $_GET[ $filter_name ] ) ) : array();
					$current_filter = array_map( 'intval', $current_filter );

					if ( ! in_array( $term->term_id, $current_filter ) ) {
						$current_filter[] = $term->term_id;
					}

					$link = $base_link;

					// Add current filters to URL.
					foreach ( $current_filter as $key => $value ) {
						// Exclude query arg for current term archive term
						if ( $value === $this->get_current_term_id() ) {
							unset( $current_filter[ $key ] );
						}

						// Exclude self so filter can be unset on click.
						if ( $option_is_set && $value === $term->term_id ) {
							unset( $current_filter[ $key ] );
						}
					}

					if ( ! empty( $current_filter ) ) {
						$link = add_query_arg( array(
							'filtering'  => '1',
							$filter_name => implode( ',', $current_filter ),
						), $link );
					}

					$link = apply_filters( 'woocommerce_layered_nav_link', $link, $term, self::TAXONOMY_NAME );

					$item_class = 'wc-layered-nav-term';
					$link_class = 'filter-link';

					if ( $option_is_set ) {
						$item_class .= ' chosen';
					}

					$count_html = '';

					if ( $items_count ) {
						$count_html = '<span class="count">(' . $count . ')</span>';
					}

					echo '<li class="' . esc_attr( $item_class ) . '">';

					printf(
						'<a href="%1$s" class="%2$s">%3$s %4$s</a>',
						esc_url( $link ),
						esc_attr( $link_class ),
						esc_html( $term->name ),
						$count_html
					);

					if ( $show_hierarchy ) {
						$child_terms = get_terms( self::TAXONOMY_NAME, array(
							'hide_empty' => 1,
							'parent'     => $term->term_id,
						) );

						if ( ! empty( $child_terms ) ) {
							$found |= $this->layered_nav_list( $child_terms, $instance, $depth + 1 );
						}
					}

					echo '</li>';
				}

				echo '</ul>';
			} else {
				$class = [
					'show-display-' . $display_type,
					'show-items-count-' . $items_count,
					'list-style-' . $list_style,
				];

				if ( $depth > 0 ) {
					$class[] = 'children';
				}

				echo '<ul class="' . esc_attr( implode( ' ', $class ) ) . '">';

				$_chosen_attributes = $this->get_chosen_attributes();
				$found              = true;

				foreach ( $terms as $term ) {
					$option_is_set = in_array( $term->term_id, $_chosen_attributes );
					$term_link     = get_term_link( $term );
					$link          = $term_link;

					$item_class = [ 'wc-layered-nav-term' ];
					$link_class = 'item-link';

					if ( $option_is_set ) {
						$item_class[] = 'chosen';
					}

					echo '<li class="' . esc_attr( implode( ' ', $item_class ) ) . '">';

					printf(
						'<a href="%1$s" class="%2$s">%3$s</a>',
						esc_url( $link ),
						esc_attr( $link_class ),
						esc_html( $term->name )
					);

					if ( $show_hierarchy ) {
						$child_terms = get_terms( [
							'taxonomy'   => self::TAXONOMY_NAME,
							'hide_empty' => 1,
							'parent'     => $term->term_id,
						] );

						if ( ! empty( $child_terms ) ) {
							$found |= $this->layered_nav_list( $child_terms, $instance, $depth + 1 );
						}
					}

					echo '</li>';
				}

				echo '</ul>';
			}

			return $found;
		}
	}
}
