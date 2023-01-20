<?php

/*===============================================================================================================
* Class: Eac_Tools_Util
*
* Description: Met à disposition un ensemble de méthodes utiles pour les composants
*
* @since 0.0.9
* @since 1.6.0	Filtre sur les métadonnées. Query 'meta_query'
*               La méthode 'get_taxonomies' de 'get_all_taxonomies' retourne un object
*				Filtre sur la taxonomie
*				Filtre sur les post_type
* @since 1.6.4	Ajout de la méthode 'get_palette_colors'
* @since 1.7.0	Ajout de la méthode 'get_pages_by_id'
*				Ajout de la méthode 'get_all_post_types'
*				Ajour de la méthode 'get_all_taxonomies_by_name'
*				Ajout de la méthode 'set_meta_value_date'
*               Ajour de la méthode 'set_wp_format_date'
*				Implémente 'Type de données' et 'Opérateur de comparaison' pour les valeurs
*				Ajout de la méthode 'get_operateurs_comparaison'
*				Ajout de la méthode 'get_all_terms'
* @since 1.7.2	Décomposition de la class 'Eac_Helper_Utils' en deux class 'Eac_Helpers_Util' et 'Eac_Tools_Util'
*				Ajout de la méthode 'get_wp_format_date'
*				Fix: Renvoie la liste complète des post_types
* @since 1.7.3	Ajout de la méthode 'get_unwanted_char' + filtre
*				Ajout des opérateurs de comparaison REGEXP et NOT REGEXP
*				Ajout d'un filtre pour les métadonnées d'un Auteur/User
* @since 1.7.5	Ajout de la liste des champs ACF supportés pour le composant 'Post Grid'
*				Ajout de la méthode 'get_acf_supported_fields'
* @since 1.7.6	Ajout de la méthode 'get_all_social_networks'
* @since 1.8.4	Ajout de la méthode 'get_menus_list'
*				Transfert de la méthode 'get_elementor_templates' de l'objet 'Eac_Dynamic_Tags'
* @since 1.8.5	Ajout de la méthode 'get_widgets_list' ainsi que du tableau des widgets utiles
* @since 1.9.1	Ajout de l'e-mail et du website (url) array $social_networks
*				Ajout de la méthode 'get_all_posts_by_id'
* @since 1.9.5	Ajout de la liste des animations d'un widget/container
*				Ajout de la méthode 'get_directory_files_list'
* @since 1.9.6	Ajout de la méthode 'get_menus_location_list'
* @since 1.9.8	Ajout de la méthode 'get_product_taxonomies' pour la taxonomie des produits
* 				Ajout de la méthode 'get_product_terms' pour les terms des produits
* 				Ajout de la méthode 'get_product_types' pour les types de produits
*				Ajout de la méthode 'wc_get_meta_key_to_props' pour les produits
*				Ajout de la méthode 'isTimestamp' pour checker si la date est un timestamp unix
*				Ajout de la méthode 'get_formated_date_value' pour formatter les dates
*				Traitement de la description/short description d'un produit
*===============================================================================================================*/

namespace EACCustomWidgets\Core\Utils;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

class Eac_Tools_Util {
    
	public static $instance = null;
    
	/**
	 * @var $user_meta_fields
	 *
	 * Liste des metas acceptés pour les informations Auteur et User
	 *
	 * @since 1.6.0
	 */
	private static $user_meta_fields = array(
		'locale',
		'syntax_highlighting',
		'avatar',
		'nickname',
		'first_name',
		'last_name',
		'description',
		'rich_editing',
		'role',
		'twitter',
		'facebook',
		'instagram',
		'linkedin',
		'youtube',
		'pinterest',
		'tumblr',
		'flickr',
		'adrs_address',
		'adrs_city',
		'adrs_zipcode',
		'adrs_country',
		'adrs_occupation',
		'adrs_full',
		'show_admin_bar_front',
	);
	
	/**
	 * @var $filtered_taxonomies
	 *
	 * Exclusion de catégories
	 *
	 * @since 1.6.0
	 */
	private static $filtered_taxonomies = array(
		// CORE
		'nav_menu',
		'link_category',
		'post_format',
		// ELEMENTOR
		'elementor_library_type',
		'elementor_library_category',
		'elementor_font_type',
		// YOAST
		'yst_prominent_words',
		// WOOCOMMERCE
		'product_shipping_class',
		'product_visibility',
		'action-group',
		// LOCO
		'translation_priority',
		// FLAMINGO
		'flamingo_contact_tag',
		'flamingo_inbound_channel',
		// SDM
		'sdm_categories',
		'sdm_tags',
		//WPForms
		'wpforms_log_type',
	);
	
	/**
	 * @var $filtered_posttypes
	 *
	 * Exclusion de types de post
	 *
	 * @since 1.6.0
	 */
	private static $filtered_posttypes = array(
		// WP
		'wp_navigation',
		'wp_template_part',
		'wp_global_styles',
		'wp_template',
		'wp_block',
		'user_request',
		'attachment',
		'revision',
		'oembed_cache',
		'nav_menu_item',
		// 
		'ae_global_templates',
		'sdm_downloads',
		'mailpoet_page',
		'custom_css',
		'customize_changeset',
		'custom-css-js',
		// ELEMENTOR
		'elementor_library',
		'e-landing-page',
		// FLAMINGO
		'flamingo_contact',
		'flamingo_inbound',
		'flamingo_outbound',
		// WPFORMS
		'wpforms',
		'wpforms_log',
		// WPCF7
		'wpcf7_contact_form',
		// FORMINATOR
		'forminator_forms',
		'forminator_polls',
		'forminator_quizzes',
		// ACF
		'acf-field-group',
		'acf-field',
		// EAC Options page
		'eac_options_page',
		// WOOCOMMERCE
	);
	
	/**
	 * @var $operateurs_comparaison
	 *
	 * Les options des opérateurs de comparaison
	 *
	 * @since 1.7.0
	 * @since 1.7.3	Ajout des opérateurs REGEXP et NOT REGEXP
	 */
	private static $operateurs_comparaison = array(
		'IN'		=> 'IN',
		'NOT IN'	=> 'NOT IN',
		'BETWEEN'	=> 'BETWEEN',
		'NOT BETWEEN' => 'NOT BETWEEN',
		'LIKE' => 'LIKE',
		'NOT LIKE' => 'NOT LIKE',
		'REGEXP' => 'REGEXP',
		'NOT REGEXP' => 'NOT REGEXP',
		'='			=> '=',
		'!='		=> '!=',
		'>'			=> '>',
		'>='		=> '>=',
		'<'			=> '<',
		'<='		=> '<=',
	);
	
	/**
	 * @var $acf_field_types
	 *
	 * Les champs ACF supportés
	 *
	 * @since 1.7.5
	 */
	private static $acf_field_types = array(
		'text',
		'textarea',
		'wysiwyg',
		'select',
		'radio',
		'date_picker',
		'number',
		'true_false',
		'range',
		'checkbox',
	);
	
	/**
	 * @var $unwanted_char_array
	 *
	 * Remplacement des caractères accentués et diacritiques
	 *
	 * @since 1.7.0
	 */
	private static $unwanted_char_array = array(
		'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'AE', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
		'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
		'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
		'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
		'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'Ğ'=>'G', 'İ'=>'I', 'Ş'=>'S', 'ğ'=>'g', 'ı'=>'i', 'ş'=>'s',
		'ü'=>'u', 'ă'=>'a', 'Ă'=>'A', 'ș'=>'s', 'Ș'=>'S', 'ț'=>'t', 'Ț'=>'T',
	);
	
	/**
	 * @var $social_networks
	 *
	 * La liste des réseaux sociaux
	 *
	 * @since 1.7.6
	 * @since 1.9.1
	 */
	private static $social_networks = array(
		'email'		=> 'Email',
		'url'		=> 'Website',
		'twitter'	=> 'Twitter',
		'facebook'	=> 'Facebook',
		'instagram'	=> 'Instagram',
		'linkedin'	=> 'Linkedin',
		'youtube'	=> 'Youtube',
		'pinterest'	=> 'Pinterest',
		'tumblr'	=> 'Tumblr',
		'flickr'	=> 'Flickr',
		'reddit'	=> 'Reddit',
		'tiktok'	=> 'TikTok',
		'telegram'	=> 'Telegram',
		'quora'		=> 'Quora',
		'twitch'	=> 'Twitch',
		'github'	=> 'Github',
	);
	
	/**
	 * @var $wp_widgets
	 *
	 * La liste des widgets autorisés pour le composant Off-canvas
	 *
	 * @since 1.8.5
	 */
	private static $wp_widgets = array(
		'WP_Widget_Search',
		'WP_Widget_Pages',
		'WP_Widget_Calendar',
		'WP_Widget_Archives',
		'WP_Widget_Meta',
		'WP_Widget_Categories',
		'WP_Widget_Recent_Posts',
		'WP_Widget_Recent_Comments',
		'WP_Widget_RSS',
		'WP_Widget_Tag_Cloud',
	);
	
	/**
	 * @var $animation_list
	 *
	 * Liste des animations
	 *
	 * @since 1.9.5
	 */
	private static $animation_list = array(
		[
			'label' => 'Default',
			'options' => ['' => 'None'],
		],
		[
			'label' => 'Back',
			'options' => [
				'backInDown'	=> 'Back down',
				'backInLeft'	=> 'Back left',
				'backInRight'	=> 'Back right',
				'backInUp'		=> 'Back up',
			],
		],
		[
			'label' => 'Bounce',
			'options' => [
				'bounceIn'		=> 'Bounce',
				'bounceInDown'	=> 'Bounce down',
				'bounceInLeft'	=> 'Bounce left',
				'bounceInRight'	=> 'Bounce right',
				'bounceInUp'	=> 'Bounce up',
			],
		],
		[
			'label' => 'FadeIn',
			'options' => [
				'fadeIn'		=> 'fadeIn',
				'fadeInDown'	=> 'fadeInDown',
				'fadeInLeft'	=> 'fadeInLeft',
				'fadeInRight'	=> 'fadeInRight',
				'fadeInUp'		=> 'fadeInUp',
			],
		],
		[
			'label' => 'Lightspeed',
			'options' => [
				'Lightspeed'		=> 'Light speed',
				'lightSpeedInRight'	=> 'Light speed right',
				'lightSpeedInLeft'	=> 'Light speed left',
			],
		],
		[
			'label' => 'Slide',
			'options' => [
				'slideInDown'	=> 'Slide down',
				'slideInLeft'	=> 'Slide left',
				'slideInRight'	=> 'Slide right',
				'slideInUp'		=> 'Slide up',
			],
		],
		[
			'label' => 'Zoom',
			'options' => [
				'zoomIn'		=> 'Zoom',
				'zoomInDown'	=> 'Zoom down',
				'zoomInLeft'	=> 'Zoom left',
				'zoomInRight'	=> 'Zoom right',
				'zoomInUp'		=> 'Zoom up',
			],
		],
		[
			'label' => 'Attention seekers',
			'options' => [
				'bounce'	=> 'Bounce',
				'flash'		=> 'flash',
				'rubberBand'=> 'rubberBand',
				'shakeX'	=> 'shakeX',
				'shakeY'	=> 'shakeY',
				'swing'		=> 'swing',
				'tada'		=> 'tada',
				'wobble'	=> 'wobble',
				'jello'		=> 'jello',
				'heartBeat'	=> 'heartBeat',
			],
		],
	);
	
	/**
	 * @var $wc_meta_key_to_props
	 *
	 * Liste des meta_key des produits
	 *
	 * @since 1.9.8
	 */
	private static $wc_meta_key_to_props = array(
		'_sku'                   => 'sku',
		'_regular_price'         => 'regular_price',
		'_sale_price'            => 'sale_price',
		'_sale_price_dates_from' => 'date_on_sale_from',
		'_sale_price_dates_to'   => 'date_on_sale_to',
		'total_sales'            => 'total_sales',
		'_tax_status'            => 'tax_status',
		'_tax_class'             => 'tax_class',
		'_manage_stock'          => 'manage_stock',
		'_backorders'            => 'backorders',
		'_low_stock_amount'      => 'low_stock_amount',
		'_sold_individually'     => 'sold_individually',
		'_weight'                => 'weight',
		'_length'                => 'length',
		'_width'                 => 'width',
		'_height'                => 'height',
		'_upsell_ids'            => 'upsell_ids',
		'_crosssell_ids'         => 'cross_sell_ids',
		'_purchase_note'         => 'purchase_note',
		'_default_attributes'    => 'default_attributes',
		'_virtual'               => 'virtual',
		'_downloadable'          => 'downloadable',
		'_product_image_gallery' => 'gallery_image_ids',
		'_download_limit'        => 'download_limit',
		'_download_expiry'       => 'download_expiry',
		'_thumbnail_id'          => 'image_id',
		'_stock'                 => 'stock_quantity',
		'_stock_status'          => 'stock_status',
		'_wc_average_rating'     => 'average_rating',
		'_wc_rating_count'       => 'rating_counts',
		'_wc_review_count'       => 'review_count',
	);
	
	/** Constructeur */
	public function __construct() {
	    Eac_Tools_Util::instance();
	}
	
	/**
	 * get_directory_files_list
	 *
	 * Retourne la liste des fichiers d'un répertoire sous forme [url] => filename
	 *
	 * @since 1.9.5
	 */
	public static function get_directory_files_list($relative_path = 'includes/config', $mimes = 'application/json') {
		$files_list = array('none' => esc_html__('Aucun', 'eac-components'));
		$path = EAC_ADDONS_PATH . $relative_path;
		if($dir = opendir($path)) {
			while($file = readdir($dir)) {
				if($file != "." && $file != '..') {
					if(!is_dir($path . '/' . $file)) {
						$filetype = wp_check_filetype(basename($file), null);
						if($filetype['type'] === $mimes) {
							// URL comme key et nom de fichier comme value
							$files_list[EAC_ADDONS_URL . $relative_path . '/' . basename($file)] = basename($file);
						}
					}
				}
			}
		}
		
		return $files_list;
	}
	
	/**
	 * get_element_animation
	 *
	 * Retourne la liste des animations
	 *
	 * @since 1.9.5
	 */
	public static function get_element_animation() {
		return self::$animation_list;
	}
	
	/**
	 * get_all_posts_by_id
	 *
	 * Retourne la liste des ID des articles/pages/Cpt
	 *
	 * @Param {$posttype} Le type d'article 'post' ou 'page' ou custom post type
	 * @Return Un tableau "index::ID du post" => "Titre du post"
	 * 
	 * @since 1.9.1
	 */
	public static function get_all_posts_by_id($posttype = 'post') {
		$post_list = array();
		
		$posts = get_posts(array(
			'post_type' => $posttype,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		if(!empty($posts) && !is_wp_error($posts)) {
			foreach($posts as $index => $value) {
				// Ajoute l'index du tableau pour conserver le tri sur le titre
				$post_list[$index . "::" . $value->ID] = esc_html($value->post_title);
			}
		}
		
		return $post_list;
	}
	
	/**
	 * get_elementor_templates
	 *
	 * Retourne la liste des templates Elementor
	 *
	 * @param type de la taxonomie ('page' ou 'section')
	 * @since 1.6.0
	 */
	public static function get_elementor_templates($type = 'page') {
        $post_list = array('' => esc_html__('Select...', 'eac-components'));
        
        $data = get_posts(array(
                'cache_results' => false,
                'post_type' => 'elementor_library',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'orderby' => 'title',
	            'sort_order' => 'ASC',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'elementor_library_type',
                        'field' => 'slug',
                        'terms' => $type,
                    )
                )
            )
        );
		
		if(!empty($data) && !is_wp_error($data)) {
			foreach($data as $key) {
                $post_list[$key->ID] = esc_html($key->post_title);
			}
			ksort($post_list);
		}
		
		return $post_list;
	}
	
	/**
     * get_widgets_list
     * 
     * Retourne la liste des widgets standards
     * 
	 * https://gist.github.com/kingkool68/3418186
	 *
     * @since 1.8.5
     */
	public static function get_widgets_list() {
		global $wp_widget_factory, $wp_registered_sidebars;
		// global $wp_registered_widgets;
		$widgets = self::$wp_widgets;
		$options = array();
		
		//console_log($wp_registered_sidebars);
		//console_log($wp_registered_widgets['media_image-2']);
		
		// Boucle sur les Wigets standards
		foreach($wp_widget_factory->widgets as $key => $widget) {
			if(in_array($key, $widgets)) {
				//$options[$key . "::" . $widget->widget_options['description']] = $widget->name;
				$options[$key] = $widget->name;
			}
		}
		
		// Boucle sur les sidebars
		$sidebars = get_option('sidebars_widgets');
		
		// Boucle sur les sidebars actives et non vides
		foreach($sidebars as $sidebar_id => $sidebar_widgets) {
			if('wp_inactive_widgets' !== $sidebar_id && is_array($sidebar_widgets) && !empty($sidebar_widgets)) {
				$sidebar_name = isset($wp_registered_sidebars[$sidebar_id]['name']) ? $wp_registered_sidebars[$sidebar_id]['name'] : 'No name';
				$options[$sidebar_id . "::" . $sidebar_name] = "Sidebar" . "::" . $sidebar_name;
				
				/*foreach($sidebar_widgets as $widget) {
					$name = $wp_registered_widgets[$widget]['callback'][0]->name;
					$option_name = $wp_registered_widgets[$widget]['callback'][0]->option_name;
					$id_base = $wp_registered_widgets[$widget]['callback'][0]->id_base;
					$key = $wp_registered_widgets[$widget]['params'][0]['number'];
					
					$widget_data = get_option($option_name);
					$data = $widget_data[$key];
					$title = !empty($data['title']) ? $data['title'] : 'Empty title';
					//console_log($title."::".$widget."::".$name."::".$option_name."::".$id_base);
					//console_log($wp_registered_widgets[$widget]);
				}*/
			}
		}
		
		// Widget Search premier indice du tableau
		$search = 'WP_Widget_Search';
		$options = array($search => $options[$search]) + $options;
		
		return $options;
	}
	
	/**
     * get_menus_location_list
     * 
     * Retourne la liste des localisations des menus
     * 
     * @since 1.9.6
     */
		public static function get_menus_location_list() {
		$options = array('' => esc_html__('Select...', 'eac-components'));
		
		$locations = get_registered_nav_menus();
		$menus = get_nav_menu_locations();
        
		foreach($locations as $key => $location_name) {
			if(isset($menus[$key]) && (int)$menus[$key] > 0) {
				$options[$key] = $locations[$key];
				/*$menu_object = wp_get_nav_menu_object($menus[$key]);
				if($menu_object) {
					$slug =  $menu_object->slug;
					$name = $menu_object->name;
					//$options[$key . "::" . $slug] = $locations[$key] . "::" . $name;
					$options[$key] = $locations[$key];
				}*/
			}
		}		
		return $options;
	}
	
	/**
     * get_menus_list
     * 
     * Retourne la liste des menus
     * 
     * @since 1.8.4
     */
	public static function get_menus_list() {
		$menus = wp_get_nav_menus();
		$options = array('' => esc_html__('Select...', 'eac-components'));
		
		foreach($menus as $menu) {
			//if($menu->slug != 'eac-menu') {
				$options[$menu->slug] = $menu->name;
			//}
		}
		return $options;
	}
	
	/**
     * Checks if a plugin is installed
     * 
     * @since 1.0.0
     * @access public
     * 
     * @param $plugin_path string plugin path
     * 
     * @return boolean
     */
	public static function is_plugin_installed($plugin_path) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
		$plugins = get_plugins();
		return isset($plugins[$plugin_path]);
	}
    
	/**
	 * get_all_social_networks
	 *
	 * Retourne la liste des réseaux sociaux
	 *
	 *
	 * @since 1.7.6
	 */
	public static function get_all_social_networks() {
		$options = self::$social_networks;
		
		/**
		 * Liste des réseaux sociaux
		 *
		 * Filtrer la liste des réseaux sociaux
		 *
		 * @since 1.7.6
		 *
		 * @param array $options Liste des réseaux sociaux
		 */
		$options = apply_filters('eac/tools/social_networks', $options);
		
		return $options;
	}
	
	/**
	 * get_unwanted_char
	 *
	 * Retourne la liste des metadonnées supportées par les auteurs/users
	 *
	 * @since 1.6.0
	 * @since 1.7.3	Ajout d'un filtre
	 */
	public static function get_unwanted_char() {
		$unwanted_char = self::$unwanted_char_array;
		
		/**
		 * Liste des caractères de remplacement
		 *
		 * Filtre pour ajouter des caractères de remplacement
		 *
		 * @since 1.7.0
		 *
		 * @param array $unwanted_char Liste des caractères
		 */
		$unwanted_char = apply_filters('eac/tools/unwanted_char', $unwanted_char);
		
		return $unwanted_char;
	}
	
	/**
	 * get_supported_user_meta_fields
	 *
	 * Retourne la liste des metadonnées supportées par les auteurs/users
	 *
	 * @since 1.6.0
	 * @since 1.7.3	Ajout d'un filtre
	 */
	public static function get_supported_user_meta_fields() {
		$user_fields = self::$user_meta_fields;
		
		/**
		 * Liste des métadonnées supportées pour un auteur/user
		 *
		 * Filtrer/Ajouter métadonnées
		 *
		 * @since 1.7.3
		 *
		 * @param array $user_fields Liste des métadonnées
		 */
		$user_fields = apply_filters('eac/tools/user_meta_fields', $user_fields);
		
		return $user_fields;
	}
	
	/**
	 * get_acf_supported_fields
	 *
	 * Retourne la liste des champs ACF supportés
	 * 
	 * @since 1.7.5
	 */
	public static function get_acf_supported_fields() {
		$acf_fields = self::$acf_field_types;
		
		/**
		 * Liste des types de champs supportés
		 *
		 * Filtrer/Ajouter des champ ACF
		 *
		 * @since 1.7.5
		 *
		 * @param array $acf_fields Liste des champs par leur slug
		 */
		$acf_fields = apply_filters('eac/tools/acf_field_types', $acf_fields);
		
		return $acf_fields;
	}
	
	/**
	 * get_operateurs_comparaison
	 *
	 * Retourne la liste des opérateur de comparaison
	 * 
	 *
	 * @since 1.7.0
	 */
	public static function get_operateurs_comparaison() {
		$operateurs = self::$operateurs_comparaison;
		
		/**
		 * Liste des opérateurs de comparaison des meta_query
		 *
		 * Filtrer/Ajouter des opérateurs de comparaison
		 *
		 * @since 1.7.0
		 *
		 * @param array $operateurs Liste des opérateurs de comparaison.
		 */
		$operateurs = apply_filters('eac/tools/operateurs_by_key', $operateurs);
		
		return $operateurs;
	}
	
	/**
	 * get_palette_colors
	 * 
	 * Retourne une liste de toutes les couleurs personnalisées et système
	 * Couleur format hexadecimal sans #
	 * Les 10 premières couleurs personnalisées
	 * 
	 * @param {$custom}	Bool: Ajouter les couleurs personnalisées
	 * @param {$system}	Bool: Ajouter les couleurs système
	 *
	 * @since 1.6.4
	 */
	public static function get_palette_colors($custom = true, $system = false) {
		$palette = array();
		
		// Option de la base de données 'elementor_active_kit'
		$elementor_active_kit = get_option('elementor_active_kit', $default = false);
		
		// Post meta qui contient les réglages du Kit avec la clé '_elementor_page_settings'
		$active_kit_settings = get_post_meta($elementor_active_kit, '_elementor_page_settings', $single = true);
		
		// Les custom_color existent
		if(is_array($active_kit_settings) && maybe_unserialize($active_kit_settings) && isset($active_kit_settings['custom_colors']) && $custom) {
			$custom_colors = $active_kit_settings['custom_colors'];
			// Boucle sur les couleurs personnalisées
			foreach($custom_colors as $key => $custom_color) {
				if($key < 10) { // Pas plus de 10
					$palette[] = $custom_color['color'];
				}
			}
		}
		
		// Les system_colors existent
		if(is_array($active_kit_settings) && maybe_unserialize($active_kit_settings) && isset($active_kit_settings['system_colors']) && $system) {
			$system_colors = $active_kit_settings['system_colors'];
			// Boucle sur les couleurs système
			foreach($system_colors as $system_color) {
				$palette[] = $system_color['color'];
			}
		}
		
		/*highlight_string("<?php\n\CColor =\n" . var_export(implode(',', $palette), true) . ";\n?>");*/
		if(empty($palette)) { return $palette; }
		
		return implode(',', $palette);
	}
	
	/**
	 * get_filter_post_types
	 *
	 * Retourne tous les types d'articles publics filtrés
	 *
	 * @since 1.0.0
	 * @since 1.6.0	Affiche le couple 'name::label' dans la liste
	 *				Exclusion de certain post_type Ex: Elementor
	 * @since 1.7.1	Ajout d'un filtre pour Ajouter/Supprimer des types d'article
	 * @since 1.7.2	Fix: Changer "get_post_types(array(), 'objects')" en "get_post_types('', 'objects')"
	 *				pour obtenir la liste complète des post_types 
	 */
	public static function get_filter_post_types() {
	    $options = array();
		$posttypes = self::$filtered_posttypes;
		
		/**
		 * Liste des opérateurs de comparaison des meta_query
		 *
		 * Ajouter/Supprimer des types d'articles
		 *
		 * @since 1.7.1
		 *
		 * @param array $posttypes Liste des types d'articles
		 */
		$posttypes = apply_filters('eac/tools/post_types', $posttypes);
		
		$post_types = get_post_types('', 'objects');
		
		foreach($post_types as $post_type) {
			if(is_array($posttypes) && !in_array(esc_attr($post_type->name), $posttypes)) {
				$options[esc_attr($post_type->name)] = esc_attr($post_type->name) . "::" . esc_attr($post_type->label);
			}
		}
		ksort($options);
		return $options;
	}

	/**
	 * get_all_post_types
	 *
	 * Retourne tous les types d'articles publics non filtrés
	 *
	 * @since 1.7.0
	 */
	public static function get_all_post_types() {
	    $options = array();
		$post_types = get_post_types('', 'objects');
		
		foreach($post_types as $post_type) {
			$options[esc_attr($post_type->name)] = esc_attr($post_type->name) . "::" . esc_attr($post_type->label);
		}
		return $options;
	}
	
	/**
	 * get_post_excerpt
	 *
	 * Lecture du résumé ou du contenu pour un post et réduction au nombre de mots
	 *
	 * @param {$post_id} ID du post
	 * @param {$excerpt_length}	Le nombre de mots à extraire
	 *
	 * @since 1.0.0
	 * @since 1.9.8	Traitement de la description/short description d'un produit
	 *				Utilisation de la fonction 'wp_trim_words'
	 */
	public static function get_post_excerpt($post_id, $excerpt_length) {
		$the_post = get_post($post_id); // Post/Page/Product ID
		$the_excerpt = null;
		$the_post_type = $the_post->post_type;
		
		/* Intégration du type produit */
		if($the_post_type === 'product') {
			$product = wc_get_product($post_id);
			$the_excerpt = $product->get_description() ? $product->get_description() : $product->get_short_description();
		} else if($the_post) {
			$the_excerpt = $the_post->post_excerpt ? $the_post->post_excerpt : $the_post->post_content;
		} else {
			return "Error... 'get_post_excerpt'";
		}
		
		if(strlen($the_excerpt) == 0) {
			return;
		}
		
		//On supprime tous les tags html ou shortcode du résumé
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
		
		
		/** 1.9.8  */
		return wp_trim_words($the_excerpt, $excerpt_length, '[...]');
		
		/*$words = explode(' ', $the_excerpt, $excerpt_length + 1);
		if(count($words) > $excerpt_length) {
			 array_pop($words);
			 $the_excerpt = implode(' ', $words);
			 $the_excerpt .= '[...]';	 // Aucun espace avant
		}
		
		return $the_excerpt;*/
	}
	
	/**
	 * get_thumbnail_sizes
	 *
	 * Format des images
	 *
	 * @since 1.0.0
	 */
	public static function get_thumbnail_sizes() {
	    $options = array();
		$sizes = get_intermediate_image_sizes();
		foreach($sizes as $s){
			$options[$s] = ucfirst($s);
		}
		return $options;
	}

	/**
	 * get_post_orderby
	 *
	 * Les options de tri des articles
	 *
	 * @since 1.0.0
	 * @since 1.7.0	Ajout d'un filtre pour les options du control 'orderby'
	 */
	public static function get_post_orderby() {
		$options = array(
			'ID' =>				esc_html__('Id', 'eac-components'),
			'author' =>			esc_html__('Auteur', 'eac-components'),
			'title' =>			esc_html__('Titre', 'eac-components'),
			'date' =>			esc_html__('Date', 'eac-components'),
			'modified' =>		esc_html__('Dernière modification', 'eac-components'),
			'comment_count' =>	esc_html__('Nombre de commentaires', 'eac-components'),
			'meta_value_num' =>	esc_html__('Valeur meta numérique', 'eac-components'),
		);
		
		/**
		 * Liste des options de tri
		 *
		 * Filtrer les options de tri
		 *
		 * @since 1.7.0
		 *
		 * @param array $options Liste des options de tri
		 */
		$options = apply_filters('eac/tools/post_orderby', $options);
		
		return $options;
	}
	
	/**
	 * get_all_terms
	 *
	 * Retourne un tableau filtré de tous les terms de WP
	 * 
	 *
	 * @since 1.7.0
	 */
	public static function get_all_terms() {
		$all_terms = array();
		$taxos = array();
		$filtered_taxo = self::$filtered_taxonomies;
		
		$taxonomies = get_taxonomies(array(), 'objects'); // Retourne un tableau d'objets
		
		// Boucle sur les taxonomies
		foreach($taxonomies as $taxonomy) {
			if(is_array($filtered_taxo) && !in_array(esc_attr($taxonomy->name), $filtered_taxo)) {
				$taxos[] = esc_attr($taxonomy->name);
			}
		}
		
		// Boucle sur les terms d'une taxonomie
		if(!empty($taxos)) {
			foreach($taxos as $taxo) {
				$terms = get_terms(array('taxonomy' => $taxo, 'hide_empty' => true));
				
				if(!is_wp_error($terms) && count($terms) > 0) {
					foreach($terms as $term) {
						$all_terms[$taxo . "::" . $term->slug] = $taxo . "::" . esc_attr($term->name);
					}
				}
			}
		}
		return $all_terms;
	}
	
	/**
	 * get_product_types
	 *
	 * Retourne un tableau de tous les types de produits WooCommerce
	 * 
	 * @since 1.9.8
	 */
	public static function get_product_post_types() {
		$options = array();
		$products = array(
			'product' => esc_html('Produits', 'eac-components'),
			/*'product_variation' => esc_html('Variations', 'eac-components'),
			'shop_coupon' => esc_html('Codes promo', 'eac-components'),
			'shop_order' => esc_html('Commandes', 'eac-components'),
			'shop_order_placehold' => esc_html('Articles', 'eac-components'),
			'shop_order_refund' => esc_html('Remboursements', 'eac-components'),*/
		);
		
		foreach($products as $key => $val) {
			$options[esc_attr($key)] = esc_attr($key) . "::" . esc_attr($val);
		}
		return $options;
	}
	
	/**
	 * get_product_taxonomies
	 *
	 * Retourne un tableau filtré de toutes les taxonomies d'un produit
	 * 
	 * @since 1.9.8
	 */
	public static function get_product_taxonomies() {
		$options = array();
		
		$taxonomies = get_taxonomies(['object_type' => ['product']], 'objects'); // Retourne un objet
		
		foreach($taxonomies as $taxonomy) {
			$options[esc_attr($taxonomy->name)] = esc_attr($taxonomy->name) . "::" . esc_attr($taxonomy->label);
		}
		return $options;
	}
	
	/**
	 * get_product_terms
	 *
	 * Retourne un tableau filtré de tous les terms d'un produit
	 * 
	 * @since 1.9.8
	 */
	public static function get_product_terms() {
		$all_terms = array();
		$taxos = array();
		
		$taxonomies = get_taxonomies(['object_type' => ['product']], 'objects'); // Retourne un tableau d'objets
		
		// Boucle sur les taxonomies
		foreach($taxonomies as $taxonomy) {
			$taxos[] = esc_attr($taxonomy->name);
		}
		
		// Boucle sur les terms d'une taxonomie
		if(!empty($taxos)) {
			foreach($taxos as $taxo) {
				$terms = get_terms(array('taxonomy' => $taxo, 'hide_empty' => true));
				
				if(!is_wp_error($terms) && count($terms) > 0) {
					foreach($terms as $term) {
						$all_terms[$taxo . "::" . $term->slug] = $taxo . "::" . esc_attr($term->name);
					}
				}
			}
		}
		return $all_terms;
	}
	
	/**
	 * wc_get_meta_key_to_props
	 *
	 * Retourne la propriété d'une meta_key
	 * 
	 * @since 1.9.8
	 */
	public static function wc_get_meta_key_to_props($key) {
		$meta_key = '';
		
		if(array_key_exists($key, self::$wc_meta_key_to_props)) {
			$meta_key = self::$wc_meta_key_to_props[$key];
		}
		return $meta_key;
	}
	
	/**
	 * get_all_taxonomies
	 *
	 * Retourne un tableau filtré de toutes les taxonomies de WP
	 * Méthode 'get_taxonomies' retourne 'objects' vs 'names' et affiche le couple 'name::label' dans la liste
	 * 
	 * @since 1.0.0
	 * @since 1.6.0	Filtre la taxonomie
	 * @since 1.7.0	Ajout d'un filtre
	 */
	public static function get_all_taxonomies() {
		$options = array();
		$filtered_taxo = self::$filtered_taxonomies;
		
		$taxonomies = get_taxonomies('', 'objects'); // Retourne un objet
		
		foreach($taxonomies as $taxonomy) {
			if(is_array($filtered_taxo) && !in_array(esc_attr($taxonomy->name), $filtered_taxo)) {
				$options[esc_attr($taxonomy->name)] = esc_attr($taxonomy->name) . "::" . esc_attr($taxonomy->label);
			}
		}
		return $options;
	}
	
	/**
	 * get_all_taxonomies_by_name
	 *
	 * Retourne un tableau filtré de toutes les taxonomies par leur nom
	 * 
	 *
	 * @since 1.7.0
	 */
	public static function get_all_taxonomies_by_name() {
		$options = array();
		$filtered_taxo = self::$filtered_taxonomies;
		
		/**
		 * Liste des taxonomies
		 *
		 * Filtre pour ajouter des taxonomies à exclure
		 *
		 * @since 1.7.0
		 *
		 * @param array $filtered_taxo Liste des taxonomies
		 */
		$filtered_taxo = apply_filters('eac/tools/taxonomies_by_name', $filtered_taxo);
		
		$taxonomies = get_taxonomies('', 'objects'); // Retourne un objet
		
		foreach($taxonomies as $taxonomy) {
			if(is_array($filtered_taxo) && !in_array(esc_attr($taxonomy->name), $filtered_taxo)) {
				$options[] = esc_attr($taxonomy->name);
			}
		}
		return $options;
	}
	
	/**
	 * get_pages_by_name
	 *
	 * Retourne un array de toutes les pages avec le titre pour clé
	 *
	 * @since 1.0.0
	 */
	public static function get_pages_by_name() {
		$select_pages = array('' => esc_html__('Select...', 'eac-components'));
		$args = array('sort_order' => 'ASC', 'sort_column' => 'post_title');
		$pages = get_pages($args);
		
		foreach($pages as $page) {
			$select_pages[$page->post_title] = esc_html(ucfirst($page->post_title));
		}
		return $select_pages;
	}
	
	/**
	 * get_pages_by_id
	 *
	 * Retourne un array de toutes les pages avec l'ID pour clé 
	 *
	 * @since 1.7.0
	 */
	public static function get_pages_by_id() {
		$select_pages = array('' => esc_html__('Select...', 'eac-components'));
		$args = array('sort_order' => 'DESC', 'sort_column' => 'post_title');
		$pages = get_pages($args);
		
		foreach($pages as $page) {
			$select_pages[$page->ID] = esc_html(ucfirst($page->post_title));
		}
		return $select_pages;
	}
	
	/**
	 * set_wp_format_date
	 *
	 * La date à convertir au format des réglages WP
	 *
	 * @param {$ori_date}	(string) La date à convertir
	 * @since 1.7.0
	 * @since 1.9.8	Check si la date est un timestamp
	 */
	public static function set_wp_format_date($ori_date) {
		if(self::isTimestamp($ori_date)) {
			return date(get_option('date_format'), $ori_date);
		}
		
		if(!strtotime($ori_date)) { return $ori_date; }
		
		return date_i18n(get_option('date_format'), strtotime($ori_date));
	}
	
	/**
	 * get_wp_format_date
	 *
	 * Recherche le format de la date
	 *
	 * @param {$ori_date}	(string) La date dont on recherche le format
	 * @since 1.7.2
	 */
	public static function get_wp_format_date($ori_date) {
		if(preg_match("/^[0-9]{4}(0[1-9]|1[0-2])(0[1-9]|[1-2][0-9]|3[0-1])$/", $ori_date)) {
			return 'Ymd';
		} else if(preg_match("/^[0-9]{4}[\/]{1}(0[1-9]|1[0-2])[\/]{1}(0[1-9]|[1-2][0-9]|3[0-1])$/", $ori_date)) {
			return 'Y/m/d';
		} else if(preg_match("/^[0-9]{4}[\-]{1}(0[1-9]|1[0-2])[\-]{1}(0[1-9]|[1-2][0-9]|3[0-1])$/", $ori_date)) {
			return 'Y-m-d';
		}
		
		// Format WP définit dans le paramétrage
		return get_option('date_format');
	}
	
	/**
	 * set_meta_value_date
	 *
	 * La date à convertir au format attendu (YYYY-MM-DD) par la propriété 'value' d'un 'meta_query'
	 *
	 * @param {$ori_date}	(string) La date à convertir
	 * @since 1.7.0
	 * @since 1.9.8	Check si la date est un timestamp
	 */
	public static function set_meta_value_date($ori_date) {
		$wp_format_entree = get_option('date_format');	// Settings/General/Date Format (m-d-Y, m/d/Y, d-m-Y, d/m/Y, n/j/y=7/23/21)
		$wp_format_sortie = 'Y-m-d';					// Format sortie attendu: AAAA-MM-DD
		
		$dateMAJ = date_create_from_format($wp_format_entree, $ori_date);
		
		if($dateMAJ == false) {
			return $ori_date;
		}
		
		return $dateMAJ->format($wp_format_sortie);
	}
	
	/**
	 * get_formated_date_value
	 *
	 * Formatte la date si c'est une constante strtotime
	 *
	 * @param {$la_date}	(string) La date à checker
	 * @since 1.9.8
	 */
	public static function get_formated_date_value($la_date) {
		
        // Constante date du jour, -+1 mois, -+1 trimestre, -+1 an
		if($la_date === 'today') {
			return date_i18n('Y-m-d');
		} else if($la_date === 'today-1w') {
			return date_i18n('Y-m-d', strtotime('-1 week'));
		} else if($la_date === 'today-1m') {
			return date_i18n('Y-m-d', strtotime('-1 month'));
		} else if($la_date === 'today-1q') {
			return date_i18n('Y-m-d', strtotime('-3 month'));
		} else if($la_date === 'today-1y') {
			return date_i18n('Y-m-d', strtotime('-1 year'));
		} else if($la_date === 'today+1w') {
			return date_i18n('Y-m-d', strtotime('+1 week'));
		} else if($la_date === 'today+1m') {
			return date_i18n('Y-m-d', strtotime('+1 month'));
		} else if($la_date === 'today+1q') {
			return date_i18n('Y-m-d', strtotime('+3 month'));
		} else if($la_date === 'today+1y') {
			return date_i18n('Y-m-d', strtotime('+1 year'));
		} else if(self::isTimestamp($la_date)) {
			return (string)$la_date;
		} else {
			return self::set_meta_value_date($la_date);
		}
    }
	
	/**
	 * isTimestamp
	 *
	 * Check si la date est un timestamp unix
	 *
	 * @param {$la_date}	(string) La date à checker
	 * @since 1.9.8
	 */
	public static function isTimestamp($la_date) {
        if (!is_numeric($la_date)) {
            return false;
        }
		
        try {
            new \DateTime('@' . $la_date);
        } catch (\Exception $e) {
            return false;
        }
		
        return true;
    }
	
    /**
	 * instance.
	 *
	 * Garantir une seule instance de la class
	 *
	 * @since 1.6.0
	 *
	 * @return Eac_Tools_Util une instance de la class
	 */
	public static function instance() {
		if(is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}