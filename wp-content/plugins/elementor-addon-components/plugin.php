<?php

/*====================================================================================================================================================
* Class: EAC_Plugin
*
* Description:  Active l'administration du plugin avec les droit d'Admin
*               Charge les fichiers CSS des composants
*               Enregistre les scripts JS des composants
*               Enregistre 'eac-components.js' avec le frontend Elementor
*               Enregistre les composants en fonction du paramétrage '$elements_keys'
*				Enregistre les fonctionnalités en fonction du paramétrage '$features_keys'
*               Enregistre la catégorie des composants du plugin
*
* @since 0.0.9
* @since 1.6.0  Ajout de script pour l'implémentation de l'éditeur en ligne du CSS 'Custom CSS' dans l'onglet 'Advanced'
*               Correctif ajout de la lib jqcloud pour le composant Instagram Location
* @since 1.6.2	Force le chargement de la Fancybox pour le 'Shortcode Image' et le Dynamic Tags 'External image'
* @since 1.6.3	Ajout de l'options 'all-components'
* @since 1.6.4	Ajout du composant 'syntax-highlight'
* @since 1.6.7	Correctif sur tous les repeater des widgets. Suppression de 'array_values'
*				Ajout du prefixe de debug sur les scripts
*				Fix: '_content_template' is soft deprecated Elementor 2.9.0
* @since 1.7.0	Ajout d'un fichier CSS pour styliser le panel de l'éditeur
* @since 1.7.1	Ajout du composant 'HTML Sitemap'
* @since 1.7.2	Décomposition de la class 'Eac_Helper_Utils' en deux class 'Eac_Helpers_Util' et 'Eac_Tools_Util'
* @since 1.X.X	Nomme le handler du script ISOTOPE 'isotope-js' pour éviter la collision avec 'PAFE' qui l'intègre aussi
* @since 1.7.70	Ajout du composant 'site-thumbnail'
* @since 1.7.80	Fix: 'Elementor\Scheme_Typography' is soft deprecated Elementor 3.2.2
*				Fix: 'Elementor\Scheme_Color' is soft deprecated Elementor 3.2.2
*				Fix: '_register_controls' is soft deprecated Elementor 3.1.0
*				Fix: 'elementor.config.settings.page' is soft deprecated Elementor 2.9.0 (eac-custom-css.js)
* @since 1.8.0	Ajout du composant 'table-content'
*				Test existence du slug du composant (isset)
* @since 1.8.1	Ajout d'une section (Advanced/EAC sticky effect) pour implémenter la propriété 'sticky'
* @since 1.8.2	Ajout du composant 'ACF relationship'
* @since 1.8.4	Ajout des états aux tableaux d'éléments '$elements_keys'
*				Ajout du tableau des fonctionnalités '$features_keys'
*				Activation/désactivation des fonctionnalités
*				Ajout des pages d'options pour ACF
*				Ajout de la fonctionnalité 'element-link'
* @since 1.8.5	Ajout du composant 'Off Canvas'
*				Déplacement du chargement de chacun des fichiers de style dans le widget correspondant
* @since 1.8.6	Ajout du composant 'Hotspots'
*				Changement de répertoire de la class des pages d'options
* @since 1.8.7	Ajout de la fonctionnalité 'acf-json'
* @since 1.8.8	Ajout du composant 'OpenStreetMap'
*				Force le chargement des fonts Awesomme dans l'éditeur Elementor
* @since 1.8.9	Ajout du groupe de composants 'eac-advanced' et recomposition des groupes
*				Ajout du control 'PDF viewer'
*				Ajout du composant 'PDF viewer'
* @since 1.9.0	Suppression des composants 'Instagram'
*				Transfert des scripts et des styles dans les class d'objet considérées
* @since 1.9.1	Ajout du composant 'Team members'
*				Ajout du composant 'Author Info Box'
*				Gestion des widgets globals
* @since 1.9.2	Ajout du composant 'news-ticker'
*				Transfert du chargement des fonctionnalités dans le fichier 'eac-load-features'
*				Transfert du chargement des scripts et styles dans le fichier 'eac-register-scripts'
* @since 1.9.3	Ajout du composant 'lottie-animations'
*				Ajout de la fonctionnalité 'lottie-background'
* @since 1.9.6	Ajout de la fonctionnalité 'custom-nav-menu'
*				Ajout de la fonctionnalité 'grant-option-page'
*				Ajout de la fonctionnalité 'motion-effects'
*				Ajout d'une nouvelle capacité 'eac_manage_options' pour les rôles 'editor' et 'shop_manager'
* @since 1.9.7	Ajout de la méthode 'get_register_script_url' pour construire le chemin d'accès aux scripts JS
*				Ajout de la méthode 'get_register_style_url' pour construire le chemin d'accès aux fichiers CSS
*				Ajout de la méthode 'get_manage_options_name'
*				Ajout des méthodes 'instance' 'clone' et 'wakeup'
* @since 1.9.8	Déplacer le chargement des Groupes, des Controls et des Composants Elementor dans le fichier 'includes/eac-load-elements'
*				Charge la configuration des composants et des fonctionnalités et transfert des tables 'elements_keys' et 'features_keys'
* @since 1.9.9	Déplacement des fichiers de configuration sous le répertoire '/core'
*====================================================================================================================================================*/

namespace EACCustomWidgets;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

use EACCustomWidgets\Admin\Settings\EAC_Admin_Settings;
use EACCustomWidgets\Core\Eac_Config_Elements;

/**
 * Main Plugin Class
 *
 * @since 0.0.9
 */
class EAC_Plugin {
	
	/**
	 * $admin_settings
	 *
	 * Instance de la page des réglages
	 *
	 * @since 0.0.9
	 */
	private $admin_settings;
	
	/**
	 * $manage_options
	 *
	 * Le libellé de la capacité ajoutée aux rôles 'editor' et 'shop_manager'
	 *
	 * @since 1.9.6
	 */
	private $manage_options = 'eac_manage_options';
	
	/**
	 * @var $instance
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.9.7
	 */
	private static $instance = null;
	
	/**
	 * @var suffix_css
	 * Debug des fichiers CSS
	 *
	 * @since 1.9.7
	 *
	 * @access private
	 */
	private $suffix_css = EAC_STYLE_DEBUG ? '.css' : '.min.css';
	
	/**
	 * @var suffix_js
	 * Debug des fichiers JS
	 *
	 * @since 1.9.7
	 *
	 * @access private
	 */
	private $suffix_js = EAC_SCRIPT_DEBUG ? '.js' : '.min.js';
	
	/**
	 * Constructeur
	 *
	 * @since 0.0.9
	 *
	 * @access public
	 */
	public function __construct() {
		
		/**
		 * @since 1.9.8 En premier charge la configuration des composants et des features
		 * @since 1.9.9	Déplacement du fichier de configuration dans le répertoire '/core'
		 */
		require_once(__DIR__ . '/core/eac-load-config.php');
		
		/**
		 * Ajoute une nouvelle capability 'eac_manage_options' aux rôles "editor' et 'shop_manager'
		 * @since 1.9.6
		 */
		if(current_user_can('manage_options')) {
			$this->set_grant_option_page();
		}
		
		/** Administrateur ou utilisateur avec la capability 'eac_manage_options' */
		if(current_user_can('manage_options') || current_user_can($this->manage_options)) {
			require_once(__DIR__ . '/admin/settings/eac-load-components.php');
			$this->admin_settings = new EAC_Admin_Settings();
		}
		
		/**
		 * @since 1.9.2	Charge les outils, helper, shortcode et les extensions
		 * @since 1.9.3	Ajout de la liste des composants 'elements_keys'
		 * @since 1.9.8	Les fonctionnalités sont chargées automatiquement
		 * @since 1.9.9	Déplacement du chargement des fonctionnalités et des éléments dans le répertoire '/core'
		 */
		require_once(__DIR__ . '/core/eac-load-features.php');
		
		/** @since 1.9.2 Charge les scripts et les styles globaux */
		require_once(__DIR__ . '/core/eac-load-scripts.php');
		
		/** @since 1.9.8 Charge les catégories, les controls et les composants Elementor */
		require_once(__DIR__ . '/core/eac-load-elements.php');
	}
	
	/**
	 * Singletons should not be cloneable.
	 * @since 1.9.7
	 */
	protected function __clone() { }

	/**
	 * Singletons should not be restorable from strings.
	 * @since 1.9.7
	 */
	public function __wakeup() {
		throw new \Exception("Cannot unserialize a singleton.");
	}
	
	/**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.9.7
	 *
	 * @return EAC_Plugin une instance de la class
	 */
	public static function instance() {
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * set_grant_option_page
	 *
	 * Ajoute une nouvelle capability 'eac_manage_options' aux rôles "editor' et 'shop_manager'
	 * 
	 * 'option_name' = 'wp_user_roles' de la table options
	 *
	 * @since 1.9.6
	 */
	public function set_grant_option_page() {
		global $wp_roles;
		
		/** Options ACF Options Page && Grant Options Page sont actives */
		$grant_option_page = Eac_Config_Elements::is_feature_active('acf-option-page') && Eac_Config_Elements::is_feature_active('grant-option-page');
		$role_editor = get_role('editor');
		$role_shop_manager = get_role('shop_manager');
		
		if($grant_option_page) {
			if($role_editor->has_cap($this->manage_options) === false) {
				wp_roles()->add_cap('editor', $this->manage_options);
			}
			
			if(!is_null($role_shop_manager) && $role_shop_manager->has_cap($this->manage_options) === false) {
				wp_roles()->add_cap('shop_manager', $this->manage_options);
			}
		} else {
			if($role_editor->has_cap($this->manage_options) === true) {
				wp_roles()->remove_cap('editor', $this->manage_options);
			}
			
			if(!is_null($role_shop_manager) && $role_shop_manager->has_cap($this->manage_options) === true) {
				wp_roles()->remove_cap('shop_manager', $this->manage_options);
			}
		}
	}
	
	/**
	 * get_register_script_url
	 *
	 * Construit le chemin du fichier et ajoute l'extension relative à la constant globale
	 *
	 * @since 1.9.7
	 *
	 * @return le chemin absolu du fichier JS passé en paramètre
	 */
	public function get_register_script_url($file, $admin = false) {
		if($admin) {
			return EAC_ADDONS_URL . 'admin/js/' . $file . $this->suffix_js;
		} else {
			return EAC_ADDONS_URL . 'assets/js/elementor/' . $file . $this->suffix_js;
		}
	}
	
	/**
	 * get_register_style_url
	 *
	 * Construit le chemin du fichier et ajoute l'extension relative à la constant globale
	 *
	 * @since 1.9.7
	 *
	 * @return le chemin absolu du fichier CSS passé en paramètre
	 */
	public function get_register_style_url($file, $admin = false) {
		if($admin) {
			return EAC_ADDONS_URL . 'admin/css/' . $file . $this->suffix_css;
		} else {
			return EAC_ADDONS_URL . 'assets/css/' . $file . $this->suffix_css;
		}
	}
	
	/**
	 * get_manage_options_name
	 *
	 * @since 1.9.7
	 *
	 * @return le nom de la capacité 'eac_manage_options'
	 */
	public function get_manage_options_name() {
		return $this->manage_options;
	}
	
} EAC_Plugin::instance();