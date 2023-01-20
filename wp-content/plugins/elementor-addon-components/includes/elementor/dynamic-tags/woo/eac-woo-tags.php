<?php

/*=====================================================================================================================
* Class: Eac_Woo_Tags
*
* Description: Module de base qui enregistre les objets des balises dynamiques WooCommerce
*
* @since 1.9.8
=====================================================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

// WooCommerce n'est pas installé, on sort
if(!class_exists('WooCommerce')) { return; }

// Version PRO Elementor, on sort
if(defined('ELEMENTOR_PRO_VERSION')) { return; }

class Eac_Woo_Tags {
	
	const TAG_DIR = __DIR__ . '/tags/';
	const TAG_DIR_TRAITS =  __DIR__ . '/tags/traits/';
    const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';
	
	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'product-add-to-cart' => 'Eac_Product_Add_To_Cart',
		'product-excerpt' => 'Eac_Product_Excerpt',
		'product-featured-image' => 'Eac_Product_Image',
		'product-onsale' => 'Eac_Product_Sale',
		'product-prices' => 'Eac_Product_Prices',
		'product-rating' => 'Eac_Product_Rating',
		'product-sku' => 'Eac_Product_Sku',
		'product-stock' => 'Eac_Product_Stock',
		'product-terms' => 'Eac_Product_Terms',
		'product-title' => 'Eac_Product_Title',
		'product-url' => 'Eac_Products_Url',
		'product-field-keys' => 'Eac_Product_Field_Keys',
		'product-field-values' => 'Eac_Product_Field_Values',
		'product-gallery-images' => 'Eac_Product_Gallery_images',
		'product-sale' => 'Eac_Product_Sale_Total',
		'product-category-image' => 'Eac_Product_Category_image',
	);
	
	/**
	 * Constructeur de la class
	 *
	 * @access public
	 */
	public function __construct() {
		// Charge le trait 'product id'
		include_once(self::TAG_DIR_TRAITS . 'product-trait.php');
		
		if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
			add_action('elementor/dynamic_tags/register_tags', array($this, 'register_tags'));
		} else {
			add_action('elementor/dynamic_tags/register', array($this, 'register_tags'));
		}
		
		/** Supprime les zéros à la fin des prix */
		add_filter('woocommerce_price_trim_zeros', '__return_true');
	}
	
	/**
	 * Enregistre le groupe et les balises dynamiques WooCommerce
	 */
	public function register_tags($dynamic_tags) {
		// Enregistre le nouveau groupe avant d'enregistrer les Tags
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-woo-groupe', ['title' => esc_html__('WooCommerce', 'eac-components')]);
		
        foreach($this->tags_list as $file => $className) {
			$fullClassName = self::TAG_NAMESPACE . $className;
			$fullFile = self::TAG_DIR . $file . '.php';
			
			if(!file_exists($fullFile)) {
				continue;
			}
			
			include_once($fullFile);
			
			if(class_exists($fullClassName)) {
				if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
					$dynamic_tags->register_tag(new $fullClassName());
				} else {
					$dynamic_tags->register(new $fullClassName());
				}
			}
        }
	}
} new Eac_Woo_Tags();