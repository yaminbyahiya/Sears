<?php

/*==========================================================================================================
* Class: Eac_Load_Elements
*
* Description: Charge les groups, controls et les composants actifs Pour Elementor
* 
* @since 1.9.8	Compatibilité des actions et de l'enregistrement des éléments Elementor version >= 3.5.0
*				Deprecated controls_registered
*				Deprecated register_control
*				Deprecated widgets_registered
*				Deprecated register_widget_type
*				Ajout des filtres WooCommerce
===========================================================================================================*/

namespace EACCustomWidgets\Core;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

class Eac_Load_Elements {
	
	/**
	 * Constructeur de la class
	 *
	 * Ajoute les actions pour enregsitrer les goupes, controls et widgets Elementor
	 *
	 * @param $elements	La liste des composants et leur état
	 *
	 * @since 1.9.8
	 */
	public function __construct() {
		
		/**
		 * Les actions 'wp_ajax_xxxxxx' pour le control 'eac-select2' doivent être chargées avant les actions Elementor
		 *
		 * @since 1.9.8
		 */
		require_once(EAC_ADDONS_PATH . 'includes/elementor/controls/eac-select2-actions.php');
		
		/**
		 * @since 1.9.8 Filtres WooCommerce
		 */
		if(Eac_Config_Elements::is_widget_active('woo-product-grid')) {
			require_once(EAC_ADDONS_PATH . 'includes/woocommerce/eac-woo-hooks.php');
		} else {
			// On force la suppression de l'option 'eac_options_woo_hooks' par sécurité
			delete_option('eac_options_woo_hooks');
		}
		
		/**
		 * Création des catégories de composants
		 * 
		 * @since 0.0.9
		 */
		add_action('elementor/elements/categories_registered', array($this, 'register_categories'));
		
		/**
		 * Charge les controls
		 * Enregistre les class des controls
		 *
		 * @since 1.8.9
		 * @since 1.9.8	register vs controls_registered
		 */
		if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
			add_action('elementor/controls/controls_registered', array($this, 'register_controls'));
		} else {
			add_action('elementor/controls/register', array($this, 'register_controls'));
		}
		
		/**
		 * Charge les widgets
		 * Enregistre les class des composants
		 *
		 * @since 0.0.9
		 * @since 1.9.8	register vs widgets_registered
		 */
		if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
			add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
		} else {
			add_action('elementor/widgets/register', array($this, 'register_widgets'));
		}
	}
	
	/**
	 * register_categories
	 *
	 * Crée les catégories des composants
	 * 
	 * @since 0.0.9
	 * @since 1.8.8	Le troisième paramètre est déprécié
	 * @since 1.8.9	Ajout du groupe de composants 'eac-advanced'
	 */
	public function register_categories($elements_manager) {
		$elements_manager->add_category('eac-advanced', array('title' => esc_html__('EAC Avancés', 'eac-components'), 'icon' => 'fa fa-plug'));
		$elements_manager->add_category('eac-elements', array('title' => esc_html__('EAC Basiques', 'eac-components'), 'icon' => 'fa fa-plug'));
	}
	
	/**
	 * register_controls
	 *
	 * Enregistre les nouveaux controls
	 *
	 * Méthode register_control: 'NOM_DU_CONTROL'
	 * Méthode register: pas de nom pour le control
	 * class du control: méthode get_type() { return 'NOM_DU_CONTROL'; }
	 * script gestion du control: elementor.addControlView('NOM_DU_CONTROL', nom object);
	 * widget add_control: 'type' => 'NOM_DU_CONTROL',
	 *
	 * @since 1.8.9
	 * @since 1.9.8 register vs register_control
	 */
	public function register_controls($controls_manager) {
		
		// Enregistre le control 'file-viewer' pour le composant 'PDF viewer'
		require_once(EAC_ADDONS_PATH . 'includes/elementor/controls/file-viewer-control.php');
		
		// Enregistre le control 'eac-select2' pour le control select2
		require_once(EAC_ADDONS_PATH . 'includes/elementor/controls/eac-select2-control.php');
		
		if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
			$controls_manager->register_control('FILE_VIEWER', new \EACCustomWidgets\Includes\Elementor\Controls\Simple_File_Viewer_Control());
			$controls_manager->register_control('eac-select2', new \EACCustomWidgets\Includes\Elementor\Controls\Ajax_Select2_Control());
		} else {
			$controls_manager->register(new \EACCustomWidgets\Includes\Elementor\Controls\Simple_File_Viewer_Control());
			$controls_manager->register(new \EACCustomWidgets\Includes\Elementor\Controls\Ajax_Select2_Control());
		}
	}
	
	/**
	 * register_widgets
	 *
	 * Enregistre les composants actifs
	 *
	 * @since 0.0.9
	 * @since 1.9.0	Suppression des composants 'Instagram'
	 * @since 1.9.8 register vs register_widget_type
	 *				Charge les traits nécessaires pour les composants qui les utilisent
	 */
	public function register_widgets($widgets_manager) {
		
		// Le trait slider pour les composants qui implémente le slider swiper
		if(Eac_Config_Elements::is_widget_active('woo-product-grid') || Eac_Config_Elements::is_widget_active('articles-liste') || Eac_Config_Elements::is_widget_active('acf-relationship') || Eac_Config_Elements::is_widget_active('image-galerie')) {
			require_once(EAC_WIDGETS_TRAITS_PATH . 'slider-trait.php');
		}
		
		// Le composant product grid est activé, on charge les traits
		if(Eac_Config_Elements::is_widget_active('woo-product-grid')) {
			require_once(EAC_WIDGETS_TRAITS_PATH . 'button-read-more-trait.php');
			require_once(EAC_WIDGETS_TRAITS_PATH . 'button-add-to-cart-trait.php');
			require_once(EAC_WIDGETS_TRAITS_PATH . 'badge-new-trait.php');
			require_once(EAC_WIDGETS_TRAITS_PATH . 'badge-promo-trait.php');
			require_once(EAC_WIDGETS_TRAITS_PATH . 'badge-stock-trait.php');
		}
		
		foreach(Eac_Config_Elements::get_widgets_active() as $element => $active) {
			if(Eac_Config_Elements::is_widget_active($element)) {
				$path = Eac_Config_Elements::get_widget_path($element);
				$nameSpace = Eac_Config_Elements::get_widget_namespace($element);
				if($path) {
					require_once($path);
					if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
						$widgets_manager->register_widget_type(new $nameSpace());
					} else {
						$widgets_manager->register(new $nameSpace());
					}
				}
			}
		}
	}
} new Eac_Load_Elements();