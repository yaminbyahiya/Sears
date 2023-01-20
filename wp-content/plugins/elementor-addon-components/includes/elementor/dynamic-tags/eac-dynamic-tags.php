<?php
/*=================================================================================================================
* Class: Eac_Dynamic_Tags
*
* Description: Enregistre les Balises Dynamiques (Dynamic Tags)
* Met à disposition un ensemble de méthodes pour valoriser les options des listes de Tag
* Ref: https://gist.github.com/iqbalrony/7ee129379965082fb6c62cf5db372752
*
* Méthodes	'get_all_meta_post'		Requête SQL sur les metadatas
*			'get_all_posts_url'		Liste des URLs des articles/pages
*			'get_all_chart_url'		Liste des URLs des fichiers TXT des medias
*
* @since 1.6.0 
* @since 1.6.2	Ajout du Dynamic Tags 'Eac_External_Image_Url'
* @since 1.6.3	Suppression du Dynamic Tags 'Shortcode media'
* @since 1.7.0	Ajout du Dynamic Tag 'Eac_Post_Custom_Field_Values'
* @since 1.7.5	Ajout des Dynamic Tags ACF pour le composant Post Grid
* @since 1.7.6	Ajout des Dynamic Tags ACF
* @since 1.8.0	Ajout des types ACF Relationship et Post_object
* @since 1.8.2	Déplacement de l'instanciation de la class 'eac-acf-tags' dans 'plugin.php'
*				Fix des champs ACF non affichés en mode preview
* @since 1.8.3	Ajout du type ACF field Group
* @since 1.8.4	L'enregistrement des tags ACF est transféré dans l'objet 'Eac_Acf_Tags'
*				La méthode 'get_elementor_templates' est tranférée dans l'objet 'Eac_Tools_Util'
* @since 1.9.8	Simplification de l'enregistrement des Tags
*				Deprecated register_tags
*				Deprecated register_tag
* @since 1.9.9	Chargement du traits pour le custom control 'eac-select2'
*=================================================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags;

// Exit if accessed directly
if(!defined('ABSPATH')) { exit; }

// Version PRO Elementor, on sort
if(defined('ELEMENTOR_PRO_VERSION')) { return; }

class Eac_Dynamic_Tags {
	
	const TAG_DIR = __DIR__ . '/tags/';
	const TAG_DIR_TRAITS =  __DIR__ . '/tags/traits/';
    const TAG_NAMESPACE = __NAMESPACE__ . '\\tags\\';
	
	/**
	 * $tags_list
	 *
	 * Liste des tags: Nom du fichier PHP => class
	 */
	private $tags_list = array(
		'url-post' => 'Eac_Posts_Tag',
		'url-cpt' => 'Eac_Cpts_Tag',
		'url-page' => 'Eac_Pages_Tag',
		'url-chart' => 'Eac_Chart_Tag',
		'featured-image-url' => 'Eac_Featured_Image_Url',
		'author-website-url' => 'Eac_Author_Website_Url',
		'url-image-widget' => 'Eac_External_Image_Url',
		'post-by-user' => 'Eac_Post_User',
		'post-custom-field-keys' => 'Eac_Post_Custom_Field_Keys',
		'post-custom-field-values' => 'Eac_Post_Custom_Field_Values',
		'post-elementor-tmpl' => 'Eac_Elementor_Template',
		'post-excerpt' => 'Eac_Post_Excerpt',
		'featured-image' => 'Eac_Featured_Image',
		'user-info' => 'Eac_User_Info',
		'site-url' => 'Eac_Site_URL',
		'site-server' => 'Eac_Server_Var',
		'site-title' => 'Eac_Site_Title',
		'site-tagline' => 'Eac_Site_Tagline',
		'site-logo' => 'Eac_Site_Logo',
		'site-stats' => 'Eac_Post_Stats',
		'cookies' => 'Eac_Cookies_Var',
		'author-info' => 'Eac_Author_Info',
		'author-name' => 'Eac_Author_Name',
		'author-picture' => 'Eac_Author_Picture',
		'author-social-network' => 'Eac_Author_Social_network',
		'featured-image-data' => 'Eac_Featured_Image_Data',
		'user-picture' => 'Eac_User_Picture',
		'shortcode-image' => 'Eac_Shortcode_Image',
	);
	
	/**
	 * Constructeur de la class
	 *
	 * @since 1.6.0
	 *
	 * @access public
	 * @since 1.9.8 register vs register_tags
	 * @since 1.9.9	charge le trait
	 */
	public function __construct() {
		// Charge le trait 'page/post'
		include_once(self::TAG_DIR_TRAITS . 'page-post-trait.php');
		
		if(version_compare(ELEMENTOR_VERSION, '3.5.0', '<')) {
			add_action('elementor/dynamic_tags/register_tags', array($this, 'register_tags'));
		} else {
			add_action('elementor/dynamic_tags/register', array($this, 'register_tags'));
		}
	}
	
	/**
	 * Enregistre les groupes et les balises dynamiques (Dynamic Tags)
	 *
	 * @since 1.6.0
	 * @since 1.9.8 register vs register_tag
	 */
	public function register_tags($dynamic_tags) {
		// Enregistre les nouveaux groupes avant d'enregistrer les Tags
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-author-groupe', ['title' => esc_html__('Auteur', 'eac-components')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-post', ['title' => esc_html__('Article', 'eac-components')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-site-groupe', ['title' => esc_html__('Site', 'eac-components')]);
		\Elementor\Plugin::$instance->dynamic_tags->register_group('eac-url', ['title' => esc_html__('URLs', 'eac-components')]);
		
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
	
	/**
	 * Requête SQL sur les metadatas des POSTS/PAGES/CPT
	 *
	 * @since 1.6.0
	 */
	public static function get_all_meta_post($posttype = 'post') {
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare(
        "SELECT p.post_type, p.post_title, pm.post_id, pm.meta_key, pm.meta_value
            FROM {$wpdb->prefix}posts p,{$wpdb->prefix}postmeta pm 
            WHERE p.post_type = %s
            AND p.ID = pm.post_id
			AND p.post_title != ''
			AND p.post_status = 'publish'
			AND pm.meta_key NOT LIKE 'sdm_%'
			AND pm.meta_key NOT LIKE 'rank_%'
			AND pm.meta_key NOT LIKE '\\_%'
            AND pm.meta_value IS NOT NULL
            AND pm.meta_value != ''
            ORDER BY pm.meta_key", $posttype));
			
        return $result;
    }
	
	/**
	 * Retourne la liste des URLs des articles/pages
	 *
	 * @Param {$posttype} Le type d'article 'post' ou 'page'
	 * @Return Un tableau "URL du post" => "Titre du post"
	 * 
	 * @since 1.6.0
	 */
	public static function get_all_posts_url($posttype = 'post') {
		$post_list = array('' => esc_html__('Select...', 'eac-components'));
		
		$data = get_posts(array(
			'post_type' => $posttype,
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC'
		));
		
		if(!empty($data) && !is_wp_error($data)) {
			foreach($data as $key) {
				//if(!function_exists('pll_the_languages')) {
				    $post_list[esc_url(get_permalink($key->ID))] = $key->post_title;
				/*} else { // PolyLang
				    $post_id_pll = pll_get_post($key->ID);
		            if($post_id_pll) {
			            $post_list[get_permalink($post_id_pll)] = $key->post_title;
		            }
				}*/
			}
		}
		return $post_list;
	}
} new Eac_Dynamic_Tags();