(
	function( $ ) {
		'use strict';

		var $window           = $( window ),
		    $body             = $( 'body' ),
		    $pageTopBar       = $( '#page-top-bar' ),
		    $pageHeader       = $( '#page-header' ),
		    $headerInner      = $pageHeader.find( '#page-header-inner' ),
		    queueResetDelay,
		    animateQueueDelay = 200,
		    wWidth            = window.innerWidth,
		    wHeight           = window.innerHeight,
		    Helpers           = window.minimog.Helpers;

		window.minimog.LightGallery = {
			selector: '.zoom',
			mode: 'lg-fade',
			thumbnail: $minimog.light_gallery_thumbnail === '1',
			download: $minimog.light_gallery_download === '1',
			autoplay: $minimog.light_gallery_auto_play === '1',
			zoom: $minimog.light_gallery_zoom === '1',
			share: $minimog.light_gallery_share === '1',
			fullScreen: $minimog.light_gallery_full_screen === '1',
			hash: false,
			animateThumb: false,
			showThumbByDefault: false,
			getCaptionFromTitleOrAlt: false
		};

		// Call this asap for better rendering.
		calMobileMenuBreakpoint();

		$window.on( 'resize', function() {
			if ( wWidth !== window.innerWidth ) {
				$window.trigger( 'hresize' );
			}

			if ( wHeight !== window.innerHeight ) {
				$window.trigger( 'vresize' );
			}

			wWidth = window.innerWidth;
			wHeight = window.innerHeight;
		} );

		$window.on( 'hresize', function() {
			calMobileMenuBreakpoint();
		} );

		$window.on( 'load', function() {
			initPreLoader();
			initStickyHeader();
			handlerEntranceAnimation();
			handlerEntranceQueueAnimation();
			handlerLanguageSwitcherAlignment();
			handlerTopbarSubMenuAlignment();
			handlerHeaderCurrencySwitcherAlignment();
		} );

		$( document ).ready( function() {
			initSliders();

			initGridMainQuery();
			initSearchPopup();
			initTopBarCollapsible();
			initSmartmenu();
			initSplitNavHeader();
			initTopBarCountdown();
			initMobileMenu();
			initCookieNotice();
			initLightGalleryPopups();
			initVideoPopups();
			handlerPageNotFound();

			initGridWidget();
			initAccordion();
			initNiceSelect();
			initSmoothScrollLinks();
			initModal();
			scrollToTop();
			handlerVerticalCategoryMenu();
			/**
			 * We need call init on window load to avoid plugin working wrongly
			 */
			initLazyLoaderImages();
		} );

		function handlerEntranceAnimation() {
			var items = $( '.modern-grid' ).children( '.grid-item' );

			items.elementorWaypoint( function() {
				// Fix for different ver of waypoints plugin.
				var _self = this.element ? this.element : this,
				    $self = $( _self );
				$self.addClass( 'animate' );
				this.destroy(); // trigger once.
			}, {
				offset: '100%'
			} );
		}

		function handlerEntranceQueueAnimation() {
			$( '.minimog-entrance-animation-queue' ).each( function() {
				var itemQueue  = [],
				    queueTimer,
				    queueDelay = $( this ).data( 'animation-delay' ) ? $( this ).data( 'animation-delay' ) : animateQueueDelay;

				$( this ).children( '.item' ).elementorWaypoint( function() {
					// Fix for different ver of waypoints plugin.
					var _self = this.element ? this.element : $( this );

					queueResetDelay = setTimeout( function() {
						queueDelay = animateQueueDelay;
					}, animateQueueDelay );

					itemQueue.push( _self );
					processItemQueue( itemQueue, queueDelay, queueTimer );
					queueDelay += animateQueueDelay;

					this.destroy(); // trigger once.
				}, {
					offset: '100%'
				} );
			} );
		}

		function processItemQueue( itemQueue, queueDelay, queueTimer, queueResetDelay ) {
			clearTimeout( queueResetDelay );
			queueTimer = window.setInterval( function() {
				if ( itemQueue !== undefined && itemQueue.length ) {
					$( itemQueue.shift() ).addClass( 'animate' );
					processItemQueue();
				} else {
					window.clearInterval( queueTimer );
				}
			}, queueDelay );
		}

		function initPreLoader() {
			$body.addClass( 'loaded' );

			setTimeout( function() {
				var $loader = $( '#page-preloader' );

				if ( $loader.length > 0 ) {
					$loader.remove();
				}
			}, 2000 );
		}

		function initSliders() {
			$( '.tm-slider' ).each( function() {
				if ( $( this ).hasClass( 'minimog-swiper-linked-yes' ) ) {
					var mainSlider = $( this ).children( '.minimog-main-swiper' ).MinimogSwiper();
					var thumbsSlider = $( this ).children( '.minimog-thumbs-swiper' ).MinimogSwiper();

					mainSlider.controller.control = thumbsSlider;
					thumbsSlider.controller.control = mainSlider;
				} else {
					$( this ).MinimogSwiper();
				}
			} );
		}

		function initLightGalleryPopups() {
			if ( $.fn.lightGallery ) {
				$( '.minimog-light-gallery' ).each( function() {
					$( this ).lightGallery( window.minimog.LightGallery );
				} );
			}
		}

		function initVideoPopups() {
			if ( $.fn.lightGallery ) {
				var options = {
					selector: 'a',
					fullScreen: false,
					zoom: false,
					getCaptionFromTitleOrAlt: false,
					counter: false
				};

				$( '.tm-popup-video' ).each( function() {
					$( this ).lightGallery( options );
				} );
			}
		}

		function initGridMainQuery() {
			if ( $.fn.MinimogGridLayout ) {
				$( '.minimog-main-post' ).MinimogGridLayout();
			}
		}

		function initGridWidget() {
			if ( $.fn.MinimogGridLayout ) {
				$( '.minimog-instagram-widget' ).MinimogGridLayout();
			}
		}

		function initNiceSelect() {
			if ( $.fn.MinimogNiceSelect ) {
				$( '.minimog-nice-select' ).MinimogNiceSelect();
			}
		}

		function initAccordion() {
			if ( $.fn.MinimogAccordion ) {
				$( '.minimog-accordion' ).MinimogAccordion();
			}
		}

		function initModal() {
			if ( $.fn.MinimogModal ) {
				$body.on( 'click', '[data-minimog-toggle="modal"]', function( evt ) {
					evt.preventDefault();
					var $target = $( $( this ).data( 'minimog-target' ) );

					if ( $target.length > 0 ) {
						if ( $( this ).attr( 'data-minimog-dismiss' ) === '1' ) {
							$target.MinimogModal( 'close' );
						} else {
							$target.MinimogModal( 'open' );
						}
					}
				} );
			}
		}

		function initSmoothScrollLinks() {
			if ( ! $.fn.smoothScroll ) {
				return;
			}

			// Allows for easy implementation of smooth scrolling for buttons.
			$( '.smooth-scroll-link' ).on( 'click', function( evt ) {
				var target = $( this ).attr( 'href' );

				if ( Helpers.isValidSelector( target ) ) {
					evt.preventDefault();
					evt.stopPropagation();

					handlerSmoothScroll( target );
				}
			} );
		}

		function handlerSmoothScroll( target ) {
			$.smoothScroll( {
				offset: - 30,
				scrollTarget: $( target ),
				speed: 600,
				easing: 'linear'
			} );
		}

		function initSmartmenu() {
			var $primaryMenu = $pageHeader.find( '.sm.sm-simple' );

			$primaryMenu.smartmenus( {
				showTimeout: 0,
				hideTimeout: 150,
			} );

			// Add animation for sub menu.
			$primaryMenu.on( {
				'show.smapi': function( e, menu ) {
					var $thisMenu = $( menu );
					$thisMenu.removeClass( 'hide-animation' ).addClass( 'show-animation' );

					if ( ! $thisMenu.hasClass( 'menu-loaded' ) ) {
						if ( $.fn.laziestloader ) {
							var $images = $thisMenu.find( '.ll-image' );
							handleLazyImages( $images );

							var $backgroundImages = $thisMenu.find( '.ll-background' );
							handleLazyBackgrounds( $backgroundImages );
						}

						// Update Swiper Size.
						$thisMenu.find( '.tm-swiper' ).each( function() {
							var swiper = $( this ).children( '.swiper-inner' ).children( '.swiper-container' )[ 0 ].swiper;
							swiper.update();
						} );

						// Update Grid Layout.
						if ( $.fn.MinimogGridLayout ) {
							$thisMenu.find( '.minimog-grid-wrapper' ).MinimogGridLayout( 'updateLayout' );
						}

						$thisMenu.addClass( 'menu-loaded' );
					}
				},
				'hide.smapi': function( e, menu ) {
					$( menu ).removeClass( 'show-animation' ).addClass( 'hide-animation' );
				}
			} ).on( 'animationend webkitAnimationEnd oanimationend MSAnimationEnd', 'ul', function( e ) {
				$( this ).removeClass( 'show-animation hide-animation' );
				e.stopPropagation();
			} );
		}

		function scrollToTop() {
			if ( $minimog.scroll_top_enable != 1 ) {
				return;
			}
			var $scrollUp = $( '#page-scroll-up' );
			var lastScrollTop = 0;

			$window.on( 'scroll', function() {
				var st = $( this ).scrollTop();
				if ( st > lastScrollTop ) {
					$scrollUp.removeClass( 'show' );
				} else {
					if ( $window.scrollTop() > 200 ) {
						$scrollUp.addClass( 'show' );
					} else {
						$scrollUp.removeClass( 'show' );
					}
				}
				lastScrollTop = st;
			} );

			$scrollUp.on( 'click', function( evt ) {
				$( 'html, body' ).animate( { scrollTop: 0 }, 600 );
				evt.preventDefault();
			} );
		}

		function openMobileMenu() {
			$body.addClass( 'page-mobile-menu-opened' );

			Helpers.setBodyOverflow();

			$( document ).trigger( 'mobileMenuOpen' );
		}

		function closeMobileMenu() {
			$body.removeClass( 'page-mobile-menu-opened' );

			Helpers.unsetBodyOverflow();

			$( document ).trigger( 'mobileMenuClose' );
		}

		function calMobileMenuBreakpoint() {
			var menuBreakpoint = parseInt( $minimog.mobile_menu_breakpoint );

			if ( wWidth <= menuBreakpoint ) {
				$body.removeClass( 'primary-nav-rendering' ).removeClass( 'desktop-menu' ).addClass( 'mobile-menu' );
			} else {
				$body.removeClass( 'primary-nav-rendering' ).addClass( 'desktop-menu' ).removeClass( 'mobile-menu' );
			}
		}

		function initMobileMenu() {
			var $btnOpenMobileMenu = $( '#page-open-mobile-menu' );
			var duration = 300;

			if ( $btnOpenMobileMenu.length > 0 ) {
				var settings = $btnOpenMobileMenu.data( 'menu-settings' );

				var animation = settings.animation ? settings.animation : 'slide';
				var direction = settings.direction ? settings.direction : 'left';

				$body.addClass( 'mobile-menu-' + animation + '-to-' + direction );
			}

			$btnOpenMobileMenu.on( 'click', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				openMobileMenu();
			} );

			var $mobileMenu = $( '#page-mobile-main-menu' );
			var $menu = $( '#mobile-menu-primary' );

			if ( $.fn.perfectScrollbar && ! Helpers.isHandheld() ) {
				$mobileMenu.find( '.page-mobile-menu-content' ).perfectScrollbar();
			}

			$mobileMenu.on( 'click', '.tm-button', function() {
				closeMobileMenu();
			} );

			$mobileMenu.on( 'click', '#page-close-mobile-menu', function( e ) {
				e.preventDefault();
				e.stopPropagation();

				closeMobileMenu();
			} );

			$mobileMenu.on( 'click', function( e ) {
				if ( e.target !== this ) {
					return;
				}

				closeMobileMenu();
			} );

			$menu.on( 'click', '.toggle-sub-menu', function( e ) {
				var _li = $( this ).parents( 'li' ).first();

				e.preventDefault();
				e.stopPropagation();

				var _friends = _li.siblings( '.opened' );
				_friends.removeClass( 'opened' );
				_friends.find( '.opened' ).removeClass( 'opened' );
				_friends.find( '.sub-menu' ).stop().slideUp( duration );

				if ( _li.hasClass( 'opened' ) ) {
					_li.removeClass( 'opened' );
					_li.find( '.opened' ).removeClass( 'opened' );
					_li.find( '.sub-menu' ).stop().slideUp( duration );
				} else {
					_li.addClass( 'opened' );
					var $thisMenu = _li.children( '.sub-menu' ).stop().slideDown( duration, function() {
						// Need wait for animation end to make ll image working properly.
						if ( ! $thisMenu.hasClass( 'menu-loaded' ) ) {
							if ( $.fn.laziestloader ) {
								var $images = $thisMenu.find( '.ll-image' );
								handleLazyImages( $images, true );

								var $backgroundImages = $thisMenu.find( '.ll-background' );
								handleLazyBackgrounds( $backgroundImages, true );
							}

							// Update Swiper Size.
							$thisMenu.find( '.tm-swiper' ).each( function() {
								var swiper = $( this ).children( '.swiper-inner' ).children( '.swiper-container' )[ 0 ].swiper;
								swiper.update();
							} );

							// Update Grid Layout.
							if ( $.fn.MinimogGridLayout ) {
								$thisMenu.find( '.minimog-grid-wrapper' ).MinimogGridLayout( 'updateLayout' );
							}

							$thisMenu.addClass( 'menu-loaded' );
						}
					} );
				}
			} );
		}

		function initSplitNavHeader() {
			if ( 0 >= $headerInner.length || 1 !== $headerInner.data( 'centered-logo' ) ) {
				return;
			}

			var $navigation = $headerInner.find( '#page-navigation' ),
			    $navItems   = $navigation.find( '#menu-primary > li' ),
			    $logo       = $headerInner.find( '.branding__logo img.logo' ),
			    itemsNumber = $navItems.length,
			    isRTL       = $body.hasClass( 'rtl' ),
			    midIndex    = parseInt( itemsNumber / 2 + .5 * isRTL - .5 ),
			    $midItem    = $navItems.eq( midIndex ),
			    rule        = isRTL ? 'marginLeft' : 'marginRight';

			var recalc = function() {
				var logoWidth  = $logo.outerWidth(),
				    logoHeight = $logo.closest( '.branding__logo' ).height(),
				    leftWidth  = 0,
				    rightWidth = 0;

				$logo.closest( '.header-content-inner' ).css( 'min-height', logoHeight + 'px' );

				for ( var i = itemsNumber - 1; i >= 0; i -- ) {
					var itemWidth = $navItems.eq( i ).outerWidth();

					if ( i > midIndex ) {
						rightWidth += itemWidth;
					} else {
						leftWidth += itemWidth;
					}
				}

				var diff = leftWidth - rightWidth;

				if ( isRTL ) {
					if ( leftWidth > rightWidth ) {
						$navigation.find( '#menu-primary > li:first-child' ).css( 'marginRight', - diff );
					} else {
						$navigation.find( '#menu-primary > li:last-child' ).css( 'marginLeft', diff );
					}
				} else {
					if ( leftWidth > rightWidth ) {
						$navigation.find( '#menu-primary > li:last-child' ).css( 'marginRight', diff );
					} else {
						$navigation.find( '#menu-primary > li:first-child' ).css( 'marginLeft', - diff );
					}
				}

				$midItem.css( rule, logoWidth + 66 );
			};

			recalc();

			$logo.on( 'loaded', function() {
				setTimeout( function() {
					recalc();
					$navigation.addClass( 'menu-calculated' );
				}, 100 ); // Delay 100 wait for logo rendered.
			} );

			$window.on( 'hresize', recalc );
		}

		function initStickyHeader() {
			if ( $minimog.header_sticky_enable != 1 || 0 >= $pageHeader.length || $headerInner.data( 'sticky' ) != '1' ) {
				return;
			}

			var $headerHolder = $pageHeader.children( '.page-header-place-holder' ),
			    offset        = $headerInner.offset().top,
			    _hHeight      = $headerInner.outerHeight(),
			    ACTIVE_CLASS  = 'header-pinned',
			    lastST        = 0,
			    isPinned      = false,
			    stickyTimer   = null;

			// Fix offset top return negative value on some devices.
			if ( offset < 0 ) {
				offset = 0;
			}

			offset += _hHeight;

			if ( ! $pageHeader.hasClass( 'header-layout-fixed' ) ) {
				$headerHolder.height( _hHeight );
				$headerInner.addClass( 'held' );

				$window.on( 'hresize', function() {
					var _hHeight = $headerInner.outerHeight();

					$headerHolder.height( _hHeight );
				} );
			}

			$window.on( 'scroll', function() {
				var currentST = $( this ).scrollTop();

				clearTimeout( stickyTimer );

				if ( currentST < lastST ) { // Scroll up.
					if ( currentST > offset ) {
						if ( ! isPinned ) {
							toggleSticky();
						}
					} else {
						if ( isPinned ) {
							toggleSticky();
						}
					}
				} else {  // Scroll down.
					if ( isPinned ) {
						toggleSticky();
					}
				}

				lastST = currentST;
			} );

			function toggleSticky() {
				stickyTimer = setTimeout( function() {
					if ( ! isPinned ) {
						isPinned = true;
						$pageHeader.addClass( ACTIVE_CLASS );
						$pageHeader.css( '--logo-sticky-height', $pageHeader.find( '.branding__logo' ).height() + 'px' );
					} else {
						isPinned = false;
						$pageHeader.removeClass( ACTIVE_CLASS );
					}
				}, 200 );
			}
		}

		function initTopBarCountdown() {
			if ( ! $.fn.countdown ) {
				return;
			}

			if ( 0 >= $pageTopBar.length ) {
				return;
			}

			var $countdownWrap = $pageTopBar.find( '.top-bar-countdown-timer' );

			if ( 0 >= $countdownWrap.length ) {
				return;
			}

			var $countdown = $countdownWrap.find( '.countdown-timer' );
			var settings = $countdownWrap.data( 'countdown' );
			var daysText = settings.labels.days;
			var hoursText = settings.labels.hours;
			var minutesText = settings.labels.minutes;
			var secondsText = settings.labels.seconds;

			$countdown.countdown( settings.datetime, function( event ) {
				var templateStr = '<div class="countdown-clock">'
				                  + '<div class="clock-item days"><span class="number">%D</span><span class="text">' + daysText + '</span></div>'
				                  + '<span class="clock-divider days"></span>'
				                  + '<div class="clock-item hours"><span class="number">%H</span><span class="text">' + hoursText + '</span></div>'
				                  + '<span class="clock-divider hours"></span>'
				                  + '<div class="clock-item minutes"><span class="number">%M</span><span class="text">' + minutesText + '</span></div>'
				                  + '<span class="clock-divider minutes"></span>'
				                  + '<div class="clock-item seconds"><span class="number">%S</span><span class="text">' + secondsText + '</span></div>'
				                  + '</div>';
				$( this ).html( event.strftime( templateStr ) );
			} );
		}

		function initSearchPopup() {
			var settings = $minimog.search,
			    delay    = parseInt( settings.delay );

			delay = ! isNaN( delay ) ? delay : 1000;

			var $popupSearch     = $( '#popup-search' ),
			    $popupSearchForm = $popupSearch.find( '.search-form' );

			$( document ).on( 'click', '.page-open-popup-search', function( evt ) {
				evt.preventDefault();

				openSearch();
			} );

			$pageHeader.on( 'focus', '.search-field', function( evt ) {
				evt.preventDefault();

				openSearch();
			} );
			$pageHeader.on( 'change', '.search-select', function( evt ) {
				evt.preventDefault();

				$popupSearchForm.find( '.search-select' ).val( $( this ).val() );

				openSearch();
			} );
			$pageHeader.on( 'click', '.search-submit', function( evt ) {
				evt.preventDefault();

				openSearch();
			} );

			$( '#search-popup-close' ).on( 'click', function( evt ) {
				evt.preventDefault();

				closeSearch();
			} );

			$popupSearch.on( 'click', function( evt ) {
				if ( evt.target !== this ) {
					return;
				}

				closeSearch();
			} );

			var $gridWrapper = $popupSearch.find( '.minimog-grid-wrapper' );
			$gridWrapper.MinimogGridLayout();

			var searching = null;

			$popupSearchForm.on( 'focus', '.search-field', function() {
				$popupSearchForm.addClass( 'search-field-focused' );
			} );

			$popupSearchForm.on( 'blur', '.search-field', function() {
				$popupSearchForm.removeClass( 'search-field-focused' );
			} );

			$popupSearchForm.on( 'keyup', '.search-field', function() {
				var $field = $( this );

				clearTimeout( searching );
				searching = setTimeout( function() {
					if ( $field.val().length > 2 ) {
						doSearch()
					} else {
						clearSearch();
					}
				}, delay );
			} );

			function doSearch() {
				var formData   = $popupSearchForm.serializeArray(),
				    searchTerm = '';

				formData.forEach( function( item, index, array ) {
					if ( 's' === item.name ) {
						searchTerm = item.value;

						return false;
					}
				} );

				if ( '' === searchTerm ) {
					$popupSearch.find( '.popup-search-results' ).hide();
					$popupSearch.find( '.row-popular-search-keywords' ).show();
					return;
				}

				formData.push( {
					name: 'action',
					value: 'minimog_search_products'
				} );

				$.ajax( {
					url: $minimog.ajaxurl,
					type: 'GET',
					data: $.param( formData ),
					dataType: 'json',
					cache: true,
					success: function( response ) {
						$popupSearch.find( '.row-popular-search-keywords' ).hide();
						$popupSearch.find( '.popup-search-results' ).show();
						$popupSearch.find( '.popup-search-current' ).text( searchTerm );

						var $gridWrapper = $popupSearch.find( '.minimog-grid-wrapper' ),
						    $grid        = $gridWrapper.find( '.minimog-grid' );
						$grid.children( '.grid-item' ).remove();
						$gridWrapper.MinimogGridLayout( 'update', $( response.data.template ) );
					},
					beforeSend: function() {
						$popupSearch.removeClass( 'loaded' ).addClass( 'loading' );
					},
					complete: function() {
						$popupSearch.removeClass( 'loading' ).addClass( 'loaded' );
					},
				} );
			}

			function clearSearch() {
				$popupSearch.find( '.popup-search-results' ).hide();
				$popupSearch.find( '.row-popular-search-keywords' ).show();
			}

			function openSearch() {
				Helpers.setBodyOverflow();
				$popupSearch.addClass( 'open' );

				setTimeout( function() {
					$popupSearch.find( '.search-field' ).trigger( 'focus' );
				}, 300 );

				$popupSearch.find( '.ll-image.ll-notloaded' ).trigger( 'laziestloader' );

				if ( $.fn.perfectScrollbar && ! Helpers.isHandheld() ) {
					$popupSearch.children( '.inner' ).perfectScrollbar();
				}
			}

			function closeSearch() {
				Helpers.unsetBodyOverflow();
				$popupSearch.removeClass( 'open' );
			}
		}

		function initTopBarCollapsible() {
			if ( 0 >= $pageTopBar.length ) {
				return;
			}

			var ACTIVE_CLASS = 'expanded';
			var $topbarContent = $pageTopBar.find( '.top-bar-section' );

			$pageTopBar.on( 'click', '#top-bar-collapsible-toggle', function() {
				if ( $pageTopBar.hasClass( ACTIVE_CLASS ) ) {
					$pageTopBar.removeClass( ACTIVE_CLASS );
					$pageTopBar.find( '.top-bar-wrap' ).css( { height: '26px' } );
				} else {
					$pageTopBar.addClass( ACTIVE_CLASS );
					$pageTopBar.find( '.top-bar-wrap' ).css( { height: $topbarContent.outerHeight() + 'px' } );
				}
			} );

			$window.on( 'hresize', function() {
				if ( $pageTopBar.hasClass( ACTIVE_CLASS ) ) {
					$pageTopBar.find( '.top-bar-wrap' ).css( { height: $topbarContent.outerHeight() + 'px' } );
				}
			} );
		}

		function initCookieNotice() {
			// Fix Nginx Redis Cache skip cookie.
			if ( typeof Storage !== 'undefined' && localStorage.getItem( 'minimog_cookie_accepted' ) === 'yes' ) {
				return;
			}

			var $cookiePopup = $( '#cookie-notice-popup' );

			if ( 0 >= $cookiePopup.length ) {
				return;
			}

			$cookiePopup.removeClass( 'close' ).addClass( 'show' );

			$cookiePopup.on( 'click', '#btn-accept-cookie', function() {
				$cookiePopup.removeClass( 'show' ).addClass( 'close' );

				var data = $.param( {
					action: 'minimog_cookie_accepted'
				} );

				$.ajax( {
					url: $minimog.ajaxurl,
					type: 'POST',
					data: data,
					dataType: 'json',
					success: function( response ) {
						if ( typeof Storage !== 'undefined' ) {
							localStorage.setItem( 'minimog_cookie_accepted', 'yes' );
						}
					}
				} );
			} );
		}

		function handlerPageNotFound() {
			if ( ! $body.hasClass( 'error404' ) ) {
				return;
			}

			$( '#btn-go-back' ).on( 'click', function( e ) {
				e.preventDefault();

				window.history.back();
			} );
		}

		function handlerLanguageSwitcherAlignment() {
			var $languageSwitcher = $( '#switcher-language-wrapper' );

			if ( 0 >= $languageSwitcher.length ) {
				return;
			}

			// WPML.
			$languageSwitcher.on( 'mouseenter', '.wpml-ls-current-language', function() {
				handlerSubMenuAlignment( $( this ) );
			} );

			// Polylang.
			$languageSwitcher.on( 'mouseenter', '.current-lang', function() {
				handlerSubMenuAlignment( $( this ) );
			} );
		}

		function handlerTopbarSubMenuAlignment() {
			var $topBarMenus = $pageTopBar.find( '.menu' );

			if ( 0 >= $topBarMenus.length ) {
				return;
			}

			$topBarMenus.on( 'mouseenter', '.menu-item-has-children', function() {
				handlerSubMenuAlignment( $( this ) );
			} );
		}

		function handlerHeaderCurrencySwitcherAlignment() {
			var $currencySwitcher = $pageHeader.find( '.currency-switcher-menu' );

			if ( 0 >= $currencySwitcher.length ) {
				return;
			}

			$currencySwitcher.on( 'mouseenter', '.menu-item-has-children', function() {
				handlerSubMenuAlignment( $( this ) );
			} );
		}

		function handlerSubMenuAlignment( $listItem, args = {} ) {
			let docWidth = $( document ).width(),
			    options  = $.extend( true, {}, {
				    children: 'ul',
				    offset: 200,
				    appendClass: 'hover-back'
			    }, args );

			$listItem.children( options.children ).each( function() {
				var $subMenu = $( this );
				$subMenu.removeClass( options.appendClass );
				if ( $subMenu.offset().left + options.offset >= docWidth ) {
					$subMenu.addClass( options.appendClass );
				}
			} );
		}

		function handlerVerticalCategoryMenu() {
			var $wrapper = $( '#header-categories-nav' );

			if ( 0 >= $wrapper.length ) {
				return;
			}

			var FIXED_NAV_CLASS = 'categories-nav-fixed';

			$wrapper.on( 'mouseenter', function() {
				$wrapper.removeClass( 'hide-animation' ).addClass( 'show-animation' );
			} ).on( 'mouseleave', function() {
				if ( $wrapper.hasClass( FIXED_NAV_CLASS ) && ! $pageHeader.hasClass( 'header-pinned' ) ) {
					$wrapper.removeClass( 'show-animation hide-animation' );
				} else {
					$wrapper.removeClass( 'show-animation' ).addClass( 'hide-animation' );
				}
			} ).on( 'animationend webkitAnimationEnd oanimationend MSAnimationEnd', 'nav', function( evt ) {
				evt.stopPropagation();
				$wrapper.removeClass( 'hide-animation' );
			} );
		}

		function initLazyLoaderImages() {
			if ( $.fn.laziestloader ) {
				var $images = $( '.ll-image' );
				handleLazyImages( $images );

				var $backgroundImages = $( '.ll-background' );
				handleLazyBackgrounds( $backgroundImages );
			}
		}

		function handleLazyImages( $images, force = false ) {
			$images.laziestloader( {}, function() {
				$( this ).unwrap( '.minimog-lazy-image' );
			} );

			if ( force ) {
				$images.trigger( 'laziestloader' );
			}
		}

		function handleLazyBackgrounds( $backgroundImages, force = false ) {
			$backgroundImages.laziestloader( {
				setSourceMode: true
			}, function() {
				var $lazyItem = $( this );
				var src = $lazyItem.data( 'src' );

				$( '<img/>' ).attr( 'src', src ).on( 'load', function() {
					$lazyItem.css( 'background-image', 'url( ' + src + ' )' ).removeClass( 'll-background-unload' );

					$( this ).remove(); // Prevent memory leaks.
				} );
			} );

			if ( force ) {
				$backgroundImages.trigger( 'laziestloader' );
			}
		}
	}( jQuery )
);
