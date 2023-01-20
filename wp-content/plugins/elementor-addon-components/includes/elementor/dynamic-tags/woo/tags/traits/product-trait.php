<?php

/** @since 1.9.8 Création du trait pour les balises dynamiques Woocommerce */
namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Woo\Tags\Traits;

trait Eac_Product_Dynamic_Woo {
	public function register_product_id_control() {
		$this->add_control('product_id',
			[
				'label' => esc_html__("Sélectionner un titre", 'eac-components'),
				'type' => 'eac-select2',
				'object_type' => 'product',
				'default' => false,
			]
		);
	}
	
	/** @since 1.9.9 'query_type' de 'term' à 'taxonomy' */
	public function register_product_taxonomy_control() {
		$this->add_control('product_taxo',
			[
				'label' => esc_html__("Sélectionner la taxonomie", 'eac-components'),
				'type' => 'eac-select2',
				'object_type' => 'product',
				'query_type' => 'taxonomy',
				'default' => false,
			]
		);
	}
	
	/** @since 1.9.9 */
	public function register_product_category_control() {
		$this->add_control('product_category',
			[
				'label' => esc_html__("Sélectionner la catégorie", 'eac-components'),
				'type' => 'eac-select2',
				'object_type' => 'product',
				'query_type' => 'term',
				'query_taxo' => 'product_cat',
				'default' => false,
			]
		);
	}
}