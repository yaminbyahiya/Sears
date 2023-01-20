(
	function( $ ) {
		'use strict';

		var MinimogProductTabs = function( $scope, $ ) {
			var $tabPanels = $scope.find( '.minimog-tabs' );

			$tabPanels.each( function() {
				var $tabs = $( this );
				var options = {};

				var $components = $tabs.find( '.tm-tab-product-element' );
				$components.each( function() {
					var $component = $( this );

					if ( $component.hasClass( 'minimog-grid-wrapper' ) ) {
						$component.MinimogGridLayout();

						$component.on( 'MinimogGridLayoutResized', function() {
							$tabs.MinimogTabPanel( 'updateLayout' );
						} );
					} else if ( $component.hasClass( 'tm-swiper' ) ) {
						$component.MinimogSwiper();
					}
				} );

				if ( $tabs.hasClass( 'minimog-tabs--nav-type-dropdown' ) ) {
					options.navType = 'dropdown';
				}

				$tabs.MinimogTabPanel( options );
			} );

			$( document.body ).on( 'MinimogTabChange', function( e, tabPanel, $newTabContent ) {
				if ( ! $newTabContent.hasClass( 'ajax-loaded' ) ) {
					loadProductData( $newTabContent );

					$newTabContent.addClass( 'ajax-loaded' );
				}
			} );

			function loadProductData( currentTab ) {
				var $component = currentTab.find( '.tm-tab-product-element' );
				var layout = currentTab.data( 'layout' );

				var query = currentTab.data( 'query' );
				query.action = 'get_product_tabs';

				$.ajax( {
					url: $minimog.ajaxurl,
					type: 'GET',
					data: query,
					dataType: 'json',
					cache: true,
					success: function( response ) {
						var result = response.data;

						if ( ! result.found ) {
							$component.remove();
							currentTab.find( '.minimog-grid-response-messages' ).html( result.template );
						} else {
							if ( 'grid' === layout ) {
								var $grid = $component.children( '.minimog-grid' );
								$grid.children().not( '.grid-sizer' ).remove();
								$component.MinimogGridLayout( 'update', $( result.template ) );
							} else {
								var swiper = $component.children( '.swiper-inner' ).children( '.swiper-container' )[ 0 ].swiper;
								swiper.removeAllSlides();
								swiper.appendSlide( result.template );
								swiper.update();

								var llImages = $component.find( '.ll-image' );

								if ( llImages.length > 0 ) {
									llImages.laziestloader( {}, function() {
										$( this ).unwrap( '.minimog-lazy-image' );
									} ).trigger( 'laziestloader' );
								}

								var autoplay = currentTab.attr( 'data-slider-autoplay' );

								if ( autoplay && autoplay !== '' ) {
									swiper.params.autoplay.enabled = true;
									swiper.params.autoplay.delay = parseInt( autoplay );
									swiper.params.autoplay.disableOnInteraction = false;
									swiper.autoplay.start();
								}
							}

							currentTab.find( '.loop-product-variation-selector' ).each( function() {
								$( this ).find( '.term-link' ).first().trigger( 'click' );
							} );
						}
					}
				} );
			}
		};

		$( window ).on( 'elementor/frontend/init', function() {
			elementorFrontend.hooks.addAction( 'frontend/element_ready/tm-product-tabs.default', MinimogProductTabs );
			elementorFrontend.hooks.addAction( 'frontend/element_ready/tm-carousel-product-tabs.default', MinimogProductTabs );
		} );
	}
)( jQuery );
