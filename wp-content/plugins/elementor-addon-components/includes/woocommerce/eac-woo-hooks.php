<?php

/*===============================================================================================================
* Class: Eac_Woo_Filters
*
* Description: Intercepte les filtres WooCommerce pour modifier différents contenus ou redirections
*
* @since 1.9.8
* @since 1.9.9	Ajout d'une notice pour le mode catalogue
*===============================================================================================================*/

namespace EACCustomWidgets\Includes\Woocommerce;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

// WooCommerce n'est pas installé, on sort
//if(!class_exists('WooCommerce')) { return; }

//require_once(trailingslashit(ABSPATH) . 'wp-load.php');

class Eac_Woo_Filters {
    
	public static $instance = null;
	
	/**
	 * $wc_options_shop_name
	 *
	 * Le libellé de l'option des hooks Woocommerce
	 */
	private $wc_options_shop_name = 'eac_options_woo_hooks';
	
	/** Constructeur */
	public function __construct() {
		// La dernière des trois actions d'initialisation de WooCommerce
		// 'woocommerce_after_register_post_type' will work better and load all aspects of the product
		// https://github.com/woocommerce/woocommerce/issues/24954
		add_action('woocommerce_after_register_post_type', array($this, 'add_woo_filters'), 9999);
	}
	
	/**
	 * add_woo_filters.
	 *
	 * Ajout des filtres WooCommerce de redirection des URLs et de la transformation de la boutique en catalogue
	 */
	public function add_woo_filters() {
		/** Redirection de l'URL des pages boutique, panier et commande vers la page Product Grid */
		//add_action('template_redirect', array($this, 'wc_url_redirect_to_product_grid'));
		
		/** Change le libellé du bouton 'Add to cart' de la page produit */
		//add_filter('woocommerce_product_single_add_to_cart_text', array($this, 'change_single_cart_text'), 10, 2);
		
		/** Après avoir clicker sur le bouton 'continue shopping' de la page panier 'cart' */
		add_filter('woocommerce_continue_shopping_redirect', array($this, 'shop_redirect_url'), 9999, 1);
		
		/** Après avoir clicker sur le bouton 'return to shop' de la page panier 'cart' */
		add_filter('woocommerce_return_to_shop_redirect', array($this, 'shop_redirect_url'), 9999, 1);
		
		/** Après avoir ajouté un item au panier */
		//add_filter('woocommerce_add_to_cart_redirect', array($this, 'shop_redirect_url'));
		
		/** Supprime les zéros à la fin des prix */
		add_filter('woocommerce_price_trim_zeros', '__return_true');
		
		/** Change l'url des catégories du breadcrumb de la page produit */
		add_filter('woocommerce_get_breadcrumb', array($this, 'change_terms_breadcrumb_url'), 9999, 2);
		
		/** Supprime le breadcrumb de la page produit */
		//add_action('woocommerce_before_main_content', array($this, 'remove_product_breadcrumb'));
		
		/** Supprime le SKU, les catégories et les tags de la page produit */
		add_action('woocommerce_single_product_summary', array($this, 'remove_product_meta_tags'));
		//add_filter('wc_product_sku_enabled', array($this, 'remove_product_meta_sku'));
		
		/** Ajoute le SKU, les catégories et les tags de la page produit */
		//add_action('woocommerce_single_product_summary', array($this, 'add_product_meta_sku'), 40);
		//add_action('woocommerce_single_product_summary', array($this, 'add_product_meta_cats'), 41);
		//add_action('woocommerce_single_product_summary', array($this, 'add_product_meta_tags'), 42);
		
		/** Transforme le site en catalogue de produits */
		add_action('woocommerce_single_product_summary', array($this, 'turn_catalog_mode_request_quote'), 19);
		add_filter('woocommerce_get_price_html', array($this, 'turn_catalog_mode_on_for_product'), 9999);
		add_filter('woocommerce_sale_flash', array($this, 'turn_catalog_mode_on_for_sale'), 9999, 3);
		add_filter('woocommerce_get_stock_html', array($this, 'turn_catalog_mode_on_for_stock'), 9999, 2);
	}
	
	/**
	 * wc_url_redirect_to_product_grid
	 *
	 * Redirige l'URL de la boutique, du panier et de la page de commande vers la page 'product grid'
	 */
	function wc_url_redirect_to_product_grid() {
		if(is_shop() || is_cart() || is_checkout()) {
			$options = get_option($this->wc_options_shop_name, false);
			$url = $options && isset($options['product-page']['redirect']) && $options['product-page']['redirect'] && !empty($options['product-page']['shop']['url']) ? esc_url($options['product-page']['shop']['url']) : '';
			
			if(!empty($url)) {
				wp_safe_redirect($url);
				exit();
			}
		}
	}
	
	/**
	 * turn_catalog_mode_on_for_stock
	 *
	 * Transforme le site woocommerce en catalogue, cache le stock
	 */
	function turn_catalog_mode_on_for_stock($html, $product) {
		$options = get_option($this->wc_options_shop_name, false);
		
		if((is_front_page()||is_shop()||is_product()||is_product_category()||is_product_tag()) && $options && isset($options['catalog']) && $options['catalog']) {
			return '';
		} else {
			return $html;
		}
	}
	
	/**
	 * turn_catalog_mode_on_for_sale
	 *
	 * Transforme le site woocommerce en catalogue, cache 'on sale' badge
	 */
	function turn_catalog_mode_on_for_sale($html, $text, $product) {
		$options = get_option($this->wc_options_shop_name, false);
		
		// Vitrine woocommerce, page produit, page des catégories ou étiquettes de produit
		if((is_front_page()||is_shop()||is_product()||is_product_category()||is_product_tag()) && $options && isset($options['catalog']) && $options['catalog']) {
			return '';
		} else {
			//$html = '<span class="onsale">ON OFFER</span>';
			return $html;
		}
	}
	
	/**
	 * turn_catalog_mode_request_quote
	 *
	 * Affiche une notice dans la page de détail d'un produit
	 * 
	 *	add_filter('eac_woo_catalog_product_request_a_quote', 'request_a_quote', 10, 2);
	 *	function request_a_quote($notice, $id) {
	 *		$ids = array('7243', '3735', '3180', '2421');
	 * 		if(in_array($id, $ids)) {
	 *			$notice = 'Contact us to request a quote';
	 *		}
	 *		return $notice;
	 *	}
	 *	
	 *	add_filter('eac_woo_catalog_product_request_a_quote', 'request_a_quote');
	 *	function request_a_quote() {
	 *		return 'Contact us to request a quote';
	 *	}
	 *
	 * @since 1.9.9	Ajout d'un filtre pour afficher une notice dans la page produit
	 */
	function turn_catalog_mode_request_quote() {
		$options = get_option($this->wc_options_shop_name, false);
		
		if($options && isset($options['catalog']) && $options['catalog']) {
			global $product;
			$product_id = absint($product->get_id());
			$notice = '';
			
			/**
			 * Affiche une notice dans la page détail du produit
			 *
			 * @since 1.9.9
			 *
			 * @param String $notice La notice à afficher dans la page produit sous le titre/avis
			 * @param Int $product_id L'ID du produit courant pour filtrer/cibler des produits spécifiques
			 */
			$notice = apply_filters('eac_woo_catalog_product_request_a_quote', $notice, $product_id);
			
			if(!empty($notice)) {
				wc_print_notice($notice, 'success');
			}
		}
	}
	
	/**
	 * turn_catalog_mode_on_for_product
	 *
	 * Transforme le site woocommerce en catalogue, cache les boutons 'add to cart' et cache le prix
	 * @since 1.9.9 L'administrateur ou un manager woocommerce peuvent voir le prix
	 */
	function turn_catalog_mode_on_for_product($html_price) {
		$options = get_option($this->wc_options_shop_name, false);
		
		// Vitrine woocommerce, page produit, page des catégories de produit
		if((is_front_page()||is_shop()||is_product()||is_product_category()||is_product_tag()) && $options && isset($options['catalog']) && $options['catalog']) {
			// Désactive le bouton 'Add to cart'
			add_filter('woocommerce_is_purchasable', '__return_false');
			
			// Cache le prix
			if(! current_user_can('manage_options') && ! current_user_can('manage_woocommerce')) {
				return '';
			}
		}
		return $html_price;
	}
	
	/**
	 * remove_product_breadcrumb
	 *
	 * Supprime le fil d'ariane de la page produit
	 */
	function remove_product_breadcrumb() {
		$options = get_option($this->wc_options_shop_name, false);
		
		// Page produit
		if(is_product() && $options && isset($options['product-page']['breadcrumb']) && $options['product-page']['breadcrumb']) {
			remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
		}
	}
	
	/**
	 * remove_product_meta_tags
	 *
	 * Supprime le meta block de la page produit
	 */
	function remove_product_meta_tags() {
		$options = get_option($this->wc_options_shop_name, false);
		
		// Page produit
		if(is_product() && $options && isset($options['product-page']['metas']) && $options['product-page']['metas']) {
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
		}
	}
	
	/**
	 * remove_product_meta_sku
	 *
	 * Supprime le meta SKU de la page produit
	 */
	function remove_product_meta_sku($sku) {
		$options = get_option($this->wc_options_shop_name, false);
		
		// Page produit
		if(is_product() && $options && isset($options['product-page']['metas']) && $options['product-page']['metas']) {
			return false;
		}
		return $sku;
	}
	
	/**
	 * add_product_meta_sku
	 *
	 * Ajoute le code SKU de la page produit dans le meta block
	 */
	function add_product_meta_sku() {
		global $product;
		$options = get_option($this->wc_options_shop_name, false);
		if($options && isset($options['product-page']['breadcrumb']) && $options['product-page']['breadcrumb']) {
		?>
			<div class="product_meta">
				<?php if(wc_product_sku_enabled() && $product->get_sku()) : ?>
					<span class="sku_wrapper"><?php esc_html_e('UGS:', 'eac-components'); ?> <span class="sku"><?php echo ($sku = $product->get_sku()) ? $sku : esc_html__('N/A', 'eac-components'); ?></span></span>
				<?php endif; ?>
			</div>
		<?php
		}
	}
	
	/**
	 * add_product_meta_cats
	 *
	 * Ajoute les metas categories de la page produit dans le meta block
	 */
	function add_product_meta_cats() {
		global $product;
		$links = array();
		$url = '';
		$options = get_option($this->wc_options_shop_name, false);
		if($options && isset($options['product-page']['breadcrumb']) && $options['product-page']['breadcrumb']) {
			$url = $options && !empty($options['product-page']['shop']['url']) ? esc_url($options['product-page']['shop']['url']) : '';
		}
		
		if(!empty($url)) {
			$cat_ids = $product->get_category_ids();
			
			foreach($cat_ids as $cat_id) {
				$term = get_term($cat_id, 'product_cat');
				if(!is_wp_error($term) && !empty($term)) {
					$links[] = '<a href="' . $url . '?filter=' . $term->slug . '" rel="tag">' . esc_attr(ucfirst($term->name)) . '</a>';
				}
			}
			$cats = count($links) > 1 ? esc_html__('Catégories: ', 'eac-components') : esc_html__('Catégorie: ', 'eac-components');
			?>
			<div class="product_meta">
				<?php
					echo '<span class="posted_in">' . $cats . implode(' | ', $links) . '</span>';
				?>
			</div>
			<?php
		}
	}
	
	/**
	 * add_product_meta_cats
	 *
	 * Ajoute les metas categories de la page produit dans le meta block
	 */
	function add_product_meta_cats__() {
		global $product;
		?>
		<div class="product_meta">
			<?php echo wc_get_product_category_list($product->get_id(), ' | ', '<span class="posted_in">' . _n('Catégorie: ', 'Catégories: ', count($product->get_category_ids()), 'eac-components') . ' ', '</span>'); ?> 
		</div>
		<?php
	}
	
	/**
	 * add_product_meta_tags
	 *
	 * Ajoute les metas categories de la page produit dans le meta block
	 */
	function add_product_meta_tags() {
		global $product;
		$links = array();
		$url = '';
		$options = get_option($this->wc_options_shop_name, false);
		if($options && isset($options['product-page']['breadcrumb']) && $options['product-page']['breadcrumb']) {
			$url = !empty($options['product-page']['shop']['url']) ? esc_url($options['product-page']['shop']['url']) : '';
		}
		
		if(!empty($url)) {
			$tag_ids = $product->get_tag_ids();
			
			foreach($tag_ids as $tag_id) {
				$term = get_term($tag_id, 'product_tag');
				if(!is_wp_error($term) && !empty($term)) {
					$links[] = '<a href="' . $url . '?filter=' . $term->slug . '" rel="tag">' . esc_attr(ucfirst($term->name)) . '</a>';
				}
			}
			$tags = count($links) > 1 ? esc_html__('Étiquettes: ', 'eac-components') : esc_html__('Étiquette: ', 'eac-components');
			?>
			<div class="product_meta">
				<?php
					echo '<span class="tagged_as">' . $tags . implode(' | ', $links) . '</span>';
				?>
			</div>
			<?php
		}
	}
	
	/**
	 * add_product_meta_tags
	 *
	 * Ajoute les metas tags de la page produit dans le meta block
	 */
	function add_product_meta_tags__() {
		global $product;
		?>
		<div class="product_meta">
			<?php echo wc_get_product_tag_list($product->get_id(), ' | ', '<span class="tagged_as">' . _n('Étiquette: ', 'Étiquettes: ', count($product->get_tag_ids()), 'eac-components') . ' ', '</span>'); ?> 
		</div>
		<?php
	}
	
	/**
	 * change_terms_breadcrumb_url
	 *
	 * @return le breadcrumb avec les nouvelles URL sur les catégories
	 $ Ajoute un paramètre dans l'URL pour activer le filtre dans la page de la grille des produits
	 */
	function change_terms_breadcrumb_url($crumbs, $object_class) {
		$options = get_option($this->wc_options_shop_name, false);
		$url = '';
		if($options && isset($options['product-page']['breadcrumb']) && $options['product-page']['breadcrumb']) {
			$url = !empty($options['product-page']['shop']['url']) ? esc_url($options['product-page']['shop']['url']) : '';
		}
		
		if(!empty($url)) {
			foreach($crumbs as $key => $crumb) {
				$taxonomy = 'product_cat'; // The product category taxonomy
				//error_log($key."::".json_encode($crumb)."::".json_encode($crumb[1]));
				
				// Check if it is a product category term
				$term_array = term_exists($crumb[0], $taxonomy);
				
				// if it is a product category term
				if($term_array !== 0 && $term_array !== null) {
					
					// Get the WP_Term instance object
					$term = get_term($term_array['term_id'], $taxonomy);
					
					// Ajoute le slug de la catégorie au paramètre 'filter' de l'URL
					$crumbs[$key][1] = $url . '?filter=' . $term->slug;
					//$crumbs[$key][1] = $url;
				}
			}
		}
		return $crumbs;
	}
	
	/**
	 * change_single_cart_text
	 *
	 * @return le label du bouton 'Ajouter au panier' de la page 'Produit'
	 */
	public function change_single_cart_text($button_text, $product) {
		if(! is_a($product, 'WC_Product')) { return $button_text; }
		$product_type = $product->get_type();
		
		switch($product_type) {
			case 'simple':
				return esc_html__('Ajouter au panier!!', 'eac-components');
				break;
			case 'variable':
				return esc_html__('Select the variations, yo!', 'eac-components');
				break;
			default:
				return $button_text;
		}
	}
	
	/**
	 * shop_redirect_url
	 *
	 * Redirige les boutons 'continue shopping' et 'return to shop' vers la page 'product grid'
	 *
	 * @return L'url du bouton
	 */
	public function shop_redirect_url($shop_url) {
		$options = get_option($this->wc_options_shop_name, false);
		$url = $options && isset($options['product-page']['redirect']) && $options['product-page']['redirect'] && !empty($options['product-page']['shop']['url']) ? esc_url($options['product-page']['shop']['url']) : esc_url($shop_url);
		return $url;
	}
	
    /**
	 * instance
	 *
	 * Une seule instance de la class
	 *
	 * @return Eac_Woo_Filters une instance de la class
	 */
	public static function instance() {
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
	
} Eac_Woo_Filters::instance();