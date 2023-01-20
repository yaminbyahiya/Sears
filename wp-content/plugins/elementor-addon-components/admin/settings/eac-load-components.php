<?php

/*=========================================================================================================
* Description: Gère l'interface d'administration des composantrs EAC 'EAC Components'
* et des options de la BDD.
* Cette class est instanciée dans 'plugin.php' par le rôle administrateur.
* 
* Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants.
* Ajoute l'item 'EAC Components' dans les menus de la barre latérale
* Charge le formulaire HTML de la page d'admin.
*
* @since 0.0.9
* @since 1.4.0	Amélioration de la gestion des options
* @since 1.4.1	Gestion des options Instagram
* @since 1.6.2	Suppression de la gestion et des options Instagram
* @since 1.6.3	Ajout de l'option 'all-components' et 'modal-box'
* @since 1.6.4	Ajout de l'option 'syntax-highlight'
* @since 1.7.1	Ajout de l'option 'html-sitemap'
* @since 1.7.70	Ajout de l'option 'site-thumbnail'
* @since 1.8.0	Ajout de l'option 'table-content'
*				Change le type de valeur des options de int => bool
* @since 1.8.2	Ajout de l'option 'acf-relationship'
* @since 1.8.4	Traitement des options des fonctionnalités
* @since 1.8.5	Ajout du composant 'off-canvas'
* @since 1.8.6	Ajout du composant 'image-hotspots'
* @since 1.8.7	Ajout et vérification des nonces des formulaires
*				Ajout de la fonctionnalité 'acf_json'
* @since 1.8.8	Ajout de l'option 'open-streetmap'
* @since 1.8.9	Ajout de l'options 'pdf-viewer'
* @since 1.9.0	Suppression des composants 'Instagram'
* @since 1.9.1	Ajout de l'options 'team-members'
*				Ajout de l'option 'author-infobox'
* @since 1.9.2	Ajout de l'option 'news-ticker'
*				Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
* @since 1.9.6	Check les droits pour l'ajout du menu 'admin_menu'
* @since 1.9.7	Récupère le nom de la capacité pour le paramétrage du plugin
* @since 1.9.8	Intégration de la configuration avec l'objet 'Eac_Config_Elements'
*=========================================================================================================*/

namespace EACCustomWidgets\Admin\Settings;

if(! defined('ABSPATH')) exit(); // Exit if accessed directly

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

class EAC_Admin_Settings {
    
	private $options_widgets = '';
	private $options_features = '';
	private $widgets_nonce = 'eac_settings_widgets_nonce';	// @since 1.8.7 nonce pour le formulaire des composants
	private $features_nonce = 'eac_settings_features_nonce';		// @since 1.8.7 nonce pour le formulaire des fonctionnalités
	
	private $widgets_keys = array();	// La liste des composants par leur slug
	private $features_keys = array();	// @since 1.8.4 La liste des fonctionnalités par leur slug
	
	/**
	 * Constructor
	 *
	 * @param La liste des composants par leur slug
	 * 
	 * @since 0.0.9
	 */
    public function __construct() {
		
		// Le libellé des options de la BDD
		$this->options_widgets = Eac_Config_Elements::get_widgets_option_name();
		$this->options_features = Eac_Config_Elements::get_features_option_name();
	
		// Affecte les tableaux d'éléments
		$this->widgets_keys = Eac_Config_Elements::get_widgets_active();
		$this->features_keys = Eac_Config_Elements::get_features_active();
		
		// Enregistrement des actions
		add_action('admin_menu', array($this, 'admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'admin_page_scripts'));
		add_action('wp_ajax_save_settings', array($this, 'save_settings'));
		add_action('wp_ajax_save_features', array($this, 'save_features'));
    }
	
	/**
	 * admin_menu
	 *
	 * Création du nouveau menu dans la barre latérale
	 *
	 * @since 0.0.9
	 * @since 1.9.6	Check nouvelle capacité pour afficher le menu
	 * @since 1.9.7	Récupère le nom de la capacité pour le paramétrage du plugin
	 */
    public function admin_menu() {
		$plugin_name = esc_html__('EAC composants', 'eac-components');
		$option = '';
		
		$current_user = wp_get_current_user();
		if($current_user->has_cap(EAC_Plugin::instance()->get_manage_options_name())) {
			$option = EAC_Plugin::instance()->get_manage_options_name();
		} else if($current_user->has_cap('manage_options')) {
			$option = 'manage_options';
		}
		
		if(!empty($option)) {
			add_menu_page($plugin_name, $plugin_name, $option, EAC_DOMAIN, array($this, 'admin_page'), 'dashicons-admin-tools', 100);
		}
    }
	
	/**
	 * admin_page_scripts
	 *
	 * Charge le css 'eac-admin' et le script 'eac-admin' d'administration des composants
	 * Lance le chargement des options
	 *
	 * @since 0.0.9
	 * @since 1.8.4 Simplification du chargement des options
	 * @since 1.8.7	Chargement du script de la boîte de dialogue 'acf-json'
	 */
	public function admin_page_scripts() {
		
		/** Le style de la page de configuration du plugin */
		wp_enqueue_style('eac-admin', EAC_Plugin::instance()->get_register_style_url('eac-admin', true), array(), EAC_ADDONS_VERSION);
		
		/** @since 1.8.7 */
		wp_enqueue_style('wp-jquery-ui-dialog');
		
		/** Le script de la page de configuration du plugin */
		wp_enqueue_script('eac-admin', EAC_Plugin::instance()->get_register_script_url('eac-admin', true), array('jquery', 'jquery-ui-dialog'), EAC_ADDONS_VERSION, true);
	}
	
	/**
	 * admin_page
	 *
	 * Passe les paramètres au script 'eac-admin => eac-admin.js'
	 * Charge les templates de la page d'administration
	 *
	 * @since 0.0.9
	 * @since 1.8.7	Ajout des nonces
	 */
    public function admin_page() {
		// Paramètres passés au script Ajax
        $settings_components = array(
			'ajax_url'		=> admin_url('admin-ajax.php'),	// Le chemin 'admin-ajax.php'
			'ajax_action'	=> 'save_settings',				// Action/Méthode appelé par le script Ajax
			'ajax_nonce'	=> wp_create_nonce($this->widgets_nonce), // Creation du nonce
		);
		wp_localize_script('eac-admin', 'components', $settings_components);
		
		/** ----------- */
		
		// @since 1.8.4 Options features
		$settings_features = array(
			'ajax_url'		=> admin_url('admin-ajax.php'),
			'ajax_action'	=> 'save_features',
			'ajax_nonce'	=> wp_create_nonce($this->features_nonce), // Creation du nonce
		);
		wp_localize_script('eac-admin', 'features', $settings_features);
		
		/** ----------- */
		
		/**
		 * Charge les templates
		 *
		 * @since 1.9.2	Ajout des attributs "noopener noreferrer" dans les formulaires
		 */
		include_once('eac-components_header.php');
		include_once('eac-components_tabs-nav.php');
	?>
		<div class="tabs-stage">
			<?php include_once('eac-components_tab1.php'); ?>
			<?php include_once('eac-components_tab2.php'); ?>
		</div>
		<?php include_once('eac-admin_popup-acf.php'); ?>
		<?php include_once('eac-admin_popup-grant-option.php'); ?>
	<?php
	}
	
	/**
	 * save_features
	 *
	 * Méthode appelée depuis le script 'eac-admin'
	 * Sauvegarde les options dans la table Options de la BDD
	 *
	 * @since 1.8.4
	 * @since 1.8.7	Vérification du nonce
	 * @since 1.9.2	Simplification de la sauvegarde de l'option des features
	 */
    public function save_features() {
		// @since 1.8.7 Vérification du nonce pour cette action
		if(!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], $this->features_nonce)) {
			wp_send_json_error(esc_html__("Les réglages n'ont pu être enregistrés (nonce)", 'eac-components'));
		}
		
		if(!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__("Vous ne pouvez pas modifier les réglages", 'eac-components'));
		}
		
		// Les champs 'fields' sélectionnés 'on' sont serialiés dans 'eac-admin.js'
		if(isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings_on);
		} else {
			wp_send_json_error(esc_html__("Les réglages n'ont pu être enregistrés (champs)", 'eac-components'));
		}
		
		$settings_features = array();
		$keys = array_keys($this->features_keys);
		
		// La liste des fonctionnalités activés
		foreach($keys as $key) {
			$settings_features[$key] = boolval(isset($settings_on[$key]) ? 1 : 0);
		}
		
		// Update de la BDD
		update_option($this->options_features, $settings_features);
		
		// Met à jour les options pour le template template 'tab2'
		$this->features_keys = get_option($this->options_features);
		
		// retourne 'success' au script JS
		wp_send_json_success(esc_html__('Réglages enregistrés', 'eac-components'));
	}
	
	/**
	 * save_settings
	 *
	 * Méthode appelée depuis le script 'eac-admin'
	 * Sauvegarde les options dans la table Options de la BDD
	 *
	 * @since 0.0.9
	 * @since 1.8.7	Vérification du nonce
	 * @since 1.9.2	Simplification de la sauvegarde de l'option des éléments
	 */
    public function save_settings() {
		// @since 1.8.7 Vérification du nonce pour cette action
		if(!isset($_POST["nonce"]) || !wp_verify_nonce($_POST["nonce"], $this->widgets_nonce)) {
			wp_send_json_error(esc_html__("Les réglages n'ont pu être enregistrés (nonce)", 'eac-components'));
		}
		
		if(!current_user_can('manage_options')) {
			wp_send_json_error(esc_html__("Vous ne pouvez pas modifier les réglages", 'eac-components'));
		}
		
		// Les champs 'fields' sélectionnés 'on' sont serializés dans 'eac-admin.js'
		if(isset($_POST['fields'])) {
			parse_str($_POST['fields'], $settings_on);
		} else {
			wp_send_json_error(esc_html__("Les réglages n'ont pu être enregistrés (champs)", 'eac-components'));
		}
		
		$settings_keys = array();
		$keys = array_keys($this->widgets_keys);
		
		// La liste des options de tous les composants activés
		foreach($keys as $key) {
			$settings_keys[$key] = boolval(isset($settings_on[$key]) ? 1 : 0);
		}
		
		// Update de la BDD
		update_option($this->options_widgets, $settings_keys);
		
		// Met à jour les options pour le template template 'tab1'
		$this->widgets_keys = get_option($this->options_widgets);
		
		// retourne 'success' au script JS
		wp_send_json_success(esc_html__('Réglages enregistrés', 'eac-components'));
	}
}