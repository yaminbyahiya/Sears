<?php

/**
 * Plugin Name: Elementor Addon Components
 * Description: Ajouter des composants et des fonctionnalités avancées pour la version gratuite d'Elementor
 * Plugin URI: https://elementor-addon-components.com/
 * Author: Team EAC
 * Version: 1.9.9
 * Elementor tested up to: 3.7.8
 * WC requires at least: 6.9.0
 * WC tested up to: 6.9.4
 * ACF tested up to: 6.0.3
 * Author URI: https://elementor-addon-components.com/
 * Text Domain: eac-components
 * Domain Path: /languages
 * License: GPLv3 or later License
 * URI: http://www.gnu.org/licenses/gpl-3.0.html
 * 'Elementor Addon Components' is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GPL General Public License for more details.
 */
if(!defined('ABSPATH')) exit; // Exit if accessed directly

define('EAC_DOMAIN', 'eac-components');
define('EAC_PLUGIN_NAME', 'Elementor Addon Components');
define('EAC_ADDONS_VERSION', '1.9.9');

define('EAC_CUSTOM_FILE', __FILE__);
define('EAC_ADDONS_URL', plugins_url('/', __FILE__));
define('EAC_ADDONS_PATH', plugin_dir_path(__FILE__));
define('EAC_PLUGIN_BASENAME', plugin_basename(__FILE__));

define('EAC_ELEMENTOR_VERSION_REQUIRED', '3.4.0');
define('EAC_MINIMUM_PHP_VERSION', '7.0');

define('EAC_PATH_ACF_JSON', EAC_ADDONS_PATH . 'includes/acf/acf-json');
define('EAC_ACF_INCLUDES', EAC_ADDONS_PATH . 'includes/acf/');

define('EAC_DYNAMIC_TAGS_PATH', EAC_ADDONS_PATH . 'includes/elementor/dynamic-tags/');
define('EAC_ELEMENTOR_INCLUDES', EAC_ADDONS_PATH . 'includes/elementor/');

define('EAC_WIDGETS_NAMESPACE', 'EACCustomWidgets\\Widgets\\');
define('EAC_WIDGETS_PATH', EAC_ADDONS_PATH . 'widgets/');
define('EAC_WIDGETS_TRAITS_PATH', EAC_ADDONS_PATH . 'widgets/traits/');

define('EAC_SCRIPT_DEBUG', false);				// true = .js ou false = .min.js
define('EAC_STYLE_DEBUG', false);				// true = .css ou false = .min.css
define('EAC_GET_POST_ARGS_IN', false);			// get_display_for_settings de la page en entrée
define('EAC_GET_POST_ARGS_OUT', false);			// arguments formatés pour WP_Query en sortie
define('EAC_GET_META_FILTER_QUERY', false);

final class EAC_Components_Plugin {
	
	private static $_instance = null;
	
	// Singleton
	public static function instance() {
		if(is_null( self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructeur de la class du plugin
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Charge le fichier de traduction
		add_action('init', array($this, 'i18n'));
		
		// Charge le plugin
		add_action('plugins_loaded', array($this, 'plugins_are_loaded'));
	}
	
	/**
     * i18n
     *
     * Load plugin localization files.
     *
     * Fired by 'init' action hook.
     *
     * @since 1.0.0
	 * @since 1.9.0
     */
	public function i18n() {
		// Filtre la local avant le chargement de la langue @since 1.9.0
		add_filter('plugin_locale', array($this, 'i18n_en_US'), 10, 2);
		
		// Charge le fichier language
		load_plugin_textdomain(EAC_DOMAIN, false, basename(dirname(__FILE__)) . '/languages');
	}
	
	/**
	 * i18n_en_US
	 *
	 * Force l'utilisation du language 'en_US' pour le plugin
	 * 
	 * @since 1.9.0
	 */
	public function i18n_en_US($locale, $domain) {
		if($domain === EAC_DOMAIN && get_locale() !== 'fr_FR') {
			$locale = 'en_US';
		}
		return $locale;
	}
	
	/**
	 * plugins_are_loaded
	 *
	 * Différents tests et charge le plugin
	 * 
	 * @since 1.0.0
	 */
	public function plugins_are_loaded() {
		
		/** 
		 * Elementor est chargé
		 * 
		 *  @since 1.0.0
		 */
		if(! did_action('elementor/loaded')) {
			add_action('admin_notices', array($this, 'elementor_not_loaded'));
			return;
		}
		
		/** 
		 * Test de la version d'Elementor
		 * 
		 * @since 1.0.0
		 */
		if(version_compare(ELEMENTOR_VERSION, EAC_ELEMENTOR_VERSION_REQUIRED, '<')) {
			add_action('admin_notices', array($this, 'elementor_bad_version'));
			return;
		}
		
		/** 
		 * Test de la version PHP
		 * 
		 *  @since 1.4.9
		 */
		if(version_compare(PHP_VERSION, EAC_MINIMUM_PHP_VERSION, '<' )) {
			add_action('admin_notices', array($this, 'minimum_php_version'));
			return;
		}
		
		/**
		 * Ajout de la page de réglages du plugin
		 *
		 * @since 1.3.0
		 */
		add_filter('plugin_action_links_' . EAC_PLUGIN_BASENAME, array($this, 'add_settings_action_links'), 10);
		
		/**
		 * Ajout du lien vers le Help center
		 *
		 * @since 1.8.3
		 */
		add_filter('plugin_row_meta', array($this, 'add_row_meta_links'), 10, 2);
		
		/**
		 * Charge le plugin et instancie la class
		 * 
		 *  @since 1.0.0
		 */
		require_once(__DIR__ . '/plugin.php');
	}
	
	/**
	 * Ajout du lien vers la page de réglages du plugin
	 *
	 * @since 1.3.0
	 */
	public function add_settings_action_links($links) {
		$settings_link = array('<a href="' . admin_url('admin.php?page=eac-components') . '">' . esc_html__('Réglages', 'eac-components') . '</a>');
		return array_merge($settings_link, $links);
	}
	
	/**
	 * Ajout du lien vers la page du centre d'aide
	 *
	 * @since 1.8.3
	 * @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
	 */
	public function add_row_meta_links($meta_links, $plugin_file) {
		if(EAC_PLUGIN_BASENAME == $plugin_file) {
            // Help Center
            $settings_link = array('<a href="https://elementor-addon-components.com/help-center/" target="_blank" rel="noopener noreferrer">' . esc_html__("Centre d'aide", "eac-components") . '</a>');
			$meta_links = array_merge($meta_links, $settings_link);
		}
		return $meta_links;
	}
	
	/**
	 * Fonctions de notifications
	 * 
	 * @since 1.0.0
	 */
	public function elementor_not_loaded() { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e("Elementor Addon Components ne fonctionne pas car vous devez activer le plugin Elementor !", 'eac-components'); ?></p>
		</div>
	<?php }

	public function elementor_bad_version() { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e("EAC : Version minimum Elementor : " . EAC_ELEMENTOR_VERSION_REQUIRED, 'eac-components'); ?></p>
		</div>
	<?php }

	public function minimum_php_version() { ?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e("EAC : Version minimum PHP : " . EAC_MINIMUM_PHP_VERSION, 'eac-components'); ?></p>
		</div>
	<?php }
}
EAC_Components_Plugin::instance();