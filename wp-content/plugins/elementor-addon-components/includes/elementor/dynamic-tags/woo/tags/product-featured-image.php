<?php

/*===============================================================================
* Class: Eac_Product_Image
*
*
* @return affiche l'image du produit
* @since 1.9.8
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags;

use EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags\Traits\Eac_Product_Dynamic_Woo;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Product_Image extends Data_Tag {
	use Eac_Product_Dynamic_Woo;
	
	public function get_name() {
		return 'eac-addon-woo-image';
	}

	public function get_title() {
		return esc_html__('Image du produit', 'eac-components');
	}

	public function get_group() {
		return 'eac-woo-groupe';
	}

	public function get_categories() {
		return [TagsModule::IMAGE_CATEGORY];
	}
	
	protected function register_controls() {
		$this->register_product_id_control();
	}
	
	public function get_value(array $options = []) {
		$product_id = $this->get_settings('product_id');
		
		if(empty($product_id)) return [];
		
		$product = wc_get_product($product_id);
		if(! $product) { return [];	}
		
		$image_id  = $product->get_image_id();
		if(! $image_id) return [];
		
		$image_url = wp_get_attachment_image_url($image_id, 'full');
		
		return ['id' => $image_id, 'url' => $image_url];
	}
}