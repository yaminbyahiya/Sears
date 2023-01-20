<?php

/*=============================================================================================================
* Class: Html_Sitemap_Widget
* Name: HTML Sitemap
* Slug: eac-addon-html-sitemap
*
* Description: Construit et affiche un sitemap au format HTML.
* 5 types de sitemap: Par Auteurs, Pages, Archives, Taxonomies et Articles qui peuvent être sélectionnés
* individuellement.
* Chaque type est entièrement configurable.
*
* @since 1.7.1
* @since 1.8.7	Support des custom breakpoints
* @since 1.9.0	Intégration des scripts et des styles dans le constructeur de la class
*=============================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Html_Sitemap_Widget extends Widget_Base {
	
	/**
	 * Constructeur de la class Html_Sitemap_Widget
	 * 
	 * Enregistre les scripts et les styles et applique des filtres
	 *
	 * @since 1.9.0
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_style('eac-html-sitemap', EAC_Plugin::instance()->get_register_style_url('html-sitemap'), array('eac'), '1.7.1');
		
		// Filtre la liste 'orderby' utilisée dans les articles et la taxonomie
		add_filter('eac/tools/post_orderby', function($exclude_orderby) {
			$exclude = ['ID' => 'ID', 'author' => 'author', 'comment_count' => 'comment_count', 'meta_value_num' => 'meta_value_num'];
			return array_diff_key($exclude_orderby, $exclude);
		}, 10);
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'html-sitemap';
	
    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return widget name.
    */
    public function get_name() {
        return Eac_Config_Elements::get_widget_name($this->slug);
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return widget title.
    */
    public function get_title() {
		return Eac_Config_Elements::get_widget_title($this->slug);
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return widget icon.
	* https://char-map.herokuapp.com/
    */
    public function get_icon() {
        return Eac_Config_Elements::get_widget_icon($this->slug);
    }
	
	/* 
	* Affecte le composant à la catégorie définie dans plugin.php
	* 
	* @access public
    *
    * @return widget category.
	*/
	public function get_categories() {
		return ['eac-advanced'];
	}
	
	/* 
	* Load dependent libraries
	* 
	* @access public
    *
    * @return libraries list.
	*/
	public function get_script_depends() {
		return [''];
	}
	
	/* 
	 * Load dependent styles
	 * 
	 * Les styles sont chargés dans le footer !!
     *
     * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-html-sitemap'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return Eac_Config_Elements::get_widget_keywords($this->slug);
	}
	
	/**
	 * Get help widget get_custom_help_url.
	 *
	 * 
	 *
	 * @since 1.7.0
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
        return Eac_Config_Elements::get_widget_help_url($this->slug);
    }
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
	protected function register_controls() {
        
		// @since 1.8.7 Récupère tous les breakpoints actifs
		$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
		
		/**
		 * Generale Content Section
		 */
		 $this->start_controls_section('stm_content_sitemap',
			[
				'label'      => esc_html__('Contenu', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
		    $this->add_control('stm_content_display_author',
				[
					'label' => esc_html__("Sitemap Auteur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_content_display_page',
				[
					'label' => esc_html__("Sitemap Page", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_content_display_archive',
				[
					'label' => esc_html__("Sitemap Archive", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_content_display_taxonomy',
				[
					'label' => esc_html__("Sitemap Taxonomie", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_content_display_post',
				[
					'label' => esc_html__("Sitemap Article", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_author_setting',
			[
				'label'     => esc_html__('Réglage Auteur', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => ['stm_content_display_author' => 'yes'],
			]
		);
			
			$this->add_control('stm_author_titre',
				[
					'label' => esc_html__("Titre", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
					'default' => esc_html__("Auteurs", 'eac-components'),
			        'dynamic' => ['active' => true],
					'label_block' => true,
				]
			);
			
			$this->add_control('stm_author_post_count',
				[
					'label' => esc_html__("Afficher le nombre d'articles", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_author_post_fullname',
				[
					'label' => esc_html__("Afficher le nom complet", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_author_exclude',
				[
					'label' => esc_html__("Exclure des auteurs", 'eac-components'),
					'description' => esc_html__("ID auteurs séparés par une virgule", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
			        'dynamic' => [
						'active' => true,
						'categories' => [
							TagsModule::POST_META_CATEGORY,
						],
					],
					'label_block' => true,
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_page_setting',
			[
				'label'     => esc_html__('Réglage Page', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => ['stm_content_display_page' => 'yes'],
			]
		);
			
			$this->add_control('stm_page_titre',
				[
					'label' => esc_html__("Titre", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
					'default' => esc_html__("Pages", 'eac-components'),
			        'dynamic' => ['active' => true],
					'label_block' => true,
				]
			);
			
			$this->add_control('stm_page_exclude',
				[
					'label' => esc_html__("Exclure des pages", 'eac-components'),
			        'type' => Controls_Manager::SELECT2,
			        'options' => Eac_Tools_Util::get_pages_by_id(),
					'multiple' => true,
					'label_block' => true,
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_archive_setting',
			[
				'label'     => esc_html__('Réglage Archive', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => ['stm_content_display_archive' => 'yes'],
			]
		);
			
			$this->add_control('stm_archive_titre',
				[
					'label' => esc_html__("Titre", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
					'default' => esc_html__("Archives", 'eac-components'),
			        'dynamic' => ['active' => true],
					'label_block' => true,
				]
			);
			
			$this->add_control('stm_archive_post_count',
				[
					'label' => esc_html__("Afficher le nombre d'articles", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('stm_archive_frequence',
				[
					'label' => esc_html__('Publication', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'daily' => esc_html__('Journalière', 'eac-components'),
						'weekly' => esc_html__('Hebdomadaire', 'eac-components'),
						'monthly' => esc_html__('Mensuelle', 'eac-components'),
						'yearly' => esc_html__('Annuelle', 'eac-components'),
					],
					'default' => 'monthly',

				]
			);
			
			$this->add_control('stm_archive_type',
				[
					'label' => esc_html__("Type d'article", 'eac-components'),
			        'type' => Controls_Manager::SELECT,
					'options' => Eac_Tools_Util::get_all_post_types(),
					'default' => 'post',
					'label_block' => true,
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_taxonomy_setting',
			[
				'label'     => esc_html__('Réglage Taxonomie', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => ['stm_content_display_taxonomy' => 'yes'],
			]
		);
			
			$this->add_control('stm_taxonomy_titre',
				[
					'label' => esc_html__("Titre", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
					'default' => esc_html__("Taxonomies", 'eac-components'),
			        'dynamic' => ['active' => true],
					'label_block' => true,
				]
			);
			
			$this->add_control('stm_taxonomy_date',
				[
					'label' => esc_html__("Afficher la date", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_taxonomy_count',
				[
					'label' => esc_html__("Afficher le nombre d'articles", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_taxonomy_comment',
				[
					'label' => esc_html__("Afficher le nombre de commentaires", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_taxonomy_nofollow',
				[
					'label' => esc_html__("Ajouter 'nofollow' aux liens", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_taxonomy_exclude',
				[
					'label' => esc_html__("Exclure des taxonomies", 'eac-components'),
			        'type' => Controls_Manager::SELECT2,
					'options' => Eac_Tools_Util::get_all_taxonomies(),
					'default' => ['post_tag'],
					'multiple' => true,
					'label_block' => true,
					'separator' => 'before',
				]
			);
			
			$this->add_control('stm_taxonomy_id',
				[
					'label' => esc_html__("Afficher les IDs", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$this->add_control('stm_taxonomy_exclude_id',
				[
					'label' => esc_html__('Exclure IDs', 'eac-components'),
					'description' => esc_html__('Les ID séparés par une virgule sans espace','eac-components'),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => '',
				]
			);
			
			$this->add_control('stm_taxonomy_orderby',
				[
					'label' => esc_html__('Triés par', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => Eac_Tools_Util::get_post_orderby(),
					'default' => 'title',
					'separator' => 'before',
				]
			);

			$this->add_control('stm_taxonomy_order',
				[
					'label' => esc_html__('Affichage', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'asc' => esc_html__('Ascendant', 'eac-components'),
						'desc' => esc_html__('Descendant', 'eac-components'),
					],
					'default' => 'asc',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_post_setting',
			[
				'label'     => esc_html__('Réglage Article', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
				'condition' => ['stm_content_display_post' => 'yes'],
			]
		);
			
			$this->add_control('stm_post_titre',
				[
					'label' => esc_html__("Titre", 'eac-components'),
			        'type' => Controls_Manager::TEXT,
					'default' => esc_html__("Articles", 'eac-components'),
			        'dynamic' => ['active' => true],
					'label_block' => true,
				]
			);
			
			$this->add_control('stm_post_date',
				[
					'label' => esc_html__("Afficher la date", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_post_comment',
				[
					'label' => esc_html__("Afficher le nombre de commentaires", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_post_category',
				[
					'label' => esc_html__("Afficher les catégories", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_post_nofollow',
				[
					'label' => esc_html__("Ajouter 'nofollow' aux liens", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('stm_post_type_exclude',
				[
					'label' => esc_html__("Exclure des types d'articles", 'eac-components'),
			        'type' => Controls_Manager::SELECT2,
					'options' => Eac_Tools_Util::get_all_post_types(),
					'default' => ['page', 'attachment', 'revision', 'nav_menu_item', 'elementor_library', 'ae_global_templates', 'sdm_downloads', 'e-landing-page'],
					'multiple' => true,
					'label_block' => true,
					'separator' => 'before',
				]
			);
			
			$this->add_control('stm_post_id',
				[
					'label' => esc_html__("Afficher les IDs", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$this->add_control('stm_post_exclude',
				[
					'label' => esc_html__('Exclure IDs', 'eac-components'),
					'description' => esc_html__('Les ID séparés par une virgule sans espace','eac-components'),
					'type' => Controls_Manager::TEXT,
					'label_block' => true,
					'default' => '',
				]
			);
			
			$this->add_control('stm_post_orderby',
				[
					'label' => esc_html__('Triés par', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => Eac_Tools_Util::get_post_orderby(),
					'default' => 'title',
					'separator' => 'before',
				]
			);

			$this->add_control('stm_post_order',
				[
					'label' => esc_html__('Affichage', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'asc' => esc_html__('Ascendant', 'eac-components'),
						'desc' => esc_html__('Descendant', 'eac-components'),
					],
					'default' => 'asc',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_content_setting',
			[
				'label'     => esc_html__('Disposition', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = [];
			foreach($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
				if(!in_array($breakpoint_name, [Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN, Breakpoints_manager::BREAKPOINT_KEY_LAPTOP])) {
					if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '2'];
					}
				}
			}
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('stm_setting_column',
				[
					'label'   => esc_html__('Nombre de colonnes', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => '3',
					'device_args' => $columns_device_args,
					'options'       => [
						'1'    => '1',
						'2'    => '2',
						'3'    => '3',
						'4'    => '4',
						'5'    => '5',
					],
					'prefix_class' => 'responsive%s-',
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('stm_texte_style',
			[
				'label'      => esc_html__('Texte', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			/*$this->add_control('stm_display_icon',
				[
					'label'             => esc_html__("Pictogrammes", 'eac-components'),
					'type'              => Controls_Manager::ICONS,
					'default' => ['value' => 'fas fa-arrow-right', 'library' => 'fa-solid',],
					'skin' => 'inline',
					'exclude_inline_options' => ['svg'],
					//'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list ul li::before' => 'content: {{VALUE}};'],
				]
			);*/
			
			$this->add_control('stm_texte_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list ul li' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'stm_texte_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list ul li',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_title_style',
			[
				'label'      => esc_html__('Titre', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('stm_title_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts-title h2,
						{{WRAPPER}} .eac-html-sitemap .sitemap-posts-list h3' => 'color: {{VALUE}};']
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'stm_title_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .eac-html-sitemap .sitemap-posts-title h2',
				]
			);
			
			$this->add_control('stm_title_alignment',
				[
					'label' => esc_html__('Alignement', 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__('Gauche', 'eac-components'),
							'icon' => 'eicon-h-align-left',
						],
						'center' => [
							'title' => esc_html__('Centre', 'eac-components'),
							'icon' => 'eicon-h-align-center',
						],
						'right' => [
							'title' => esc_html__('Droite', 'eac-components'),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'left',
					'toggle' => true,
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-posts-title' => 'text-align: {{VALUE}};'],
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('stm_author_style',
			[
				'label'      => esc_html__('Auteur', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['stm_content_display_author' => 'yes'],
			]
		);
			
			$this->add_control('stm_author_background_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#90EE90',
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-authors' => 'box-shadow: inset 50px 0 {{VALUE}};'],
				]
			);
			
			$this->add_control('stm_author_picto_color',
				[
					'label' => esc_html__('Couleur des pictogrammes', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .eac-html-sitemap .sitemap-authors .sitemap-posts-list ul li::before,
						{{WRAPPER}} .eac-html-sitemap .sitemap-authors .sitemap-posts-list ul li span span::after' => 'color: {{VALUE}};'
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_page_style',
			[
				'label'      => esc_html__('Page', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['stm_content_display_page' => 'yes'],
			]
		);
			
			$this->add_control('stm_page_background_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FFC077',
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-pages' => 'box-shadow: inset 50px 0 {{VALUE}};'],
				]
			);
			
			$this->add_control('stm_page_picto_color',
				[
					'label' => esc_html__('Couleur des pictogrammes', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .eac-html-sitemap .sitemap-pages .sitemap-posts-list ul li::before,
						{{WRAPPER}} .eac-html-sitemap .sitemap-pages .sitemap-posts-list ul li span span::after' => 'color: {{VALUE}};'
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_archive_style',
			[
				'label'      => esc_html__('Archive', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['stm_content_display_archive' => 'yes'],
			]
		);
			
			$this->add_control('stm_archive_background_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#FFFF00',
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-archives' => 'box-shadow: inset 50px 0 {{VALUE}};'],
				]
			);
			
			$this->add_control('stm_archive_picto_color',
				[
					'label' => esc_html__('Couleur des pictogrammes', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .eac-html-sitemap .sitemap-archives .sitemap-posts-list ul li::before,
						{{WRAPPER}} .eac-html-sitemap .sitemap-archives .sitemap-posts-list ul li span span::after' => 'color: {{VALUE}};'
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_taxonomy_style',
			[
				'label'      => esc_html__('Taxonomie', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['stm_content_display_taxonomy' => 'yes'],
			]
		);
			
			$this->add_control('stm_taxonomy_background_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#A299FF',
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies' => 'box-shadow: inset 50px 0 {{VALUE}};'],
				]
			);
			
			$this->add_control('stm_taxonomy_picto_color',
				[
					'label' => esc_html__('Couleur des pictogrammes', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies .sitemap-posts-list ul li::before,
						{{WRAPPER}} .eac-html-sitemap .sitemap-taxonomies .sitemap-posts-list ul li span span::after' => 'color: {{VALUE}};'
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('stm_post_style',
			[
				'label'      => esc_html__('Article', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'condition' => ['stm_content_display_post' => 'yes'],
			]
		);
			
			$this->add_control('stm_post_background_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#00FF00',
					'selectors' => ['{{WRAPPER}} .eac-html-sitemap .sitemap-posts' => 'box-shadow: inset 50px 0 {{VALUE}};'],
				]
			);
			
			$this->add_control('stm_post_picto_color',
				[
					'label' => esc_html__('Couleur des pictogrammes', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#000000',
					'selectors' => [
						'{{WRAPPER}} .eac-html-sitemap .sitemap-posts .sitemap-posts-list ul li::before,
						{{WRAPPER}} .eac-html-sitemap .sitemap-posts .sitemap-posts-list ul li span span::after' => 'color: {{VALUE}};'
					],
				]
			);
			
		$this->end_controls_section();
    }

	/*
	* Render widget output on the frontend.
	*
	* Written in PHP and used to generate the final HTML.
	*
	* @access protected
	*/
    protected function render() {
		?>
		<div class="eac-html-sitemap">
			<article id="post-<?php global $post; echo $post->ID; ?>" <?php post_class('site-map-article'); ?>>
				<?php $this->render_sitemap(); ?>
			</article>
		</div>
		<?php
    }
	
	protected function render_sitemap() {
		$settings = $this->get_settings_for_display();
		
		if($settings['stm_content_display_author'] === 'yes') {
			$this->eac_get_html_sitemap_authors();
		}
		if($settings['stm_content_display_page'] === 'yes') {
			$this->eac_get_html_sitemap_pages();
		}
		if($settings['stm_content_display_archive'] === 'yes') {
			$this->eac_get_html_sitemap_archives();
		}
		if($settings['stm_content_display_taxonomy'] === 'yes') {
			$this->eac_get_html_sitemap_taxonomies();
		}
		if($settings['stm_content_display_post'] === 'yes') {
			$this->eac_get_html_sitemap_posts();
		}
	}
	
	/**
	 * eac_get_html_sitemap_authors
	 * 
	 * Description:
	 * 
	 * 
	 * @since 1.7.1
	 */
	protected function eac_get_html_sitemap_authors() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('sitemap_wrapper_author', 'class', 'sitemap-authors');
		?>
		<div <?php echo $this->get_render_attribute_string('sitemap_wrapper_author'); ?>>
			<div class="sitemap-posts-title"><h2><?php echo sanitize_text_field($settings['stm_author_titre']); ?></h2></div>
			<div class="sitemap-posts-list">
				<?php
				$exclude = $settings['stm_author_exclude'];
				$optioncount = $settings['stm_author_post_count'] === 'yes' ? true : false;
				$fullname = $settings['stm_author_post_fullname'] === 'yes' ? true : false;
				?>
				<ul>
					<?php wp_list_authors(array('exclude' => $exclude, 'optioncount' => $optioncount, 'show_fullname' => $fullname)); ?>
				</ul>
			</div>
		</div>
	<?php
	}
	
	/**
	 * eac_get_html_sitemap_pages
	 * 
	 * Description:
	 * 
	 * 
	 * @since 1.7.1
	 */
	protected function eac_get_html_sitemap_pages() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('sitemap_wrapper_page', 'class', 'sitemap-pages');
		?>
		<div <?php echo $this->get_render_attribute_string('sitemap_wrapper_page'); ?>>
			<div class="sitemap-posts-title"><h2><?php echo sanitize_text_field($settings['stm_page_titre']); ?></h2></div>
			<div class="sitemap-posts-list">
				<?php
				// Exclusion de page par leur ID
				$exclude = !empty($settings['stm_page_exclude']) ? implode(',', $settings['stm_page_exclude']) : '';
				?>
				<ul>
					<?php
					wp_list_pages(array('exclude' => $exclude, 'title_li' => ''));
					?>
				</ul>
			</div>
		</div>
	<?php
	}
	
	/**
	 * eac_get_html_sitemap_archives
	 * 
	 * Description:
	 * 
	 * 
	 * @since 1.7.1
	 */
	protected function eac_get_html_sitemap_archives() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('sitemap_wrapper_archive', 'class', 'sitemap-archives');
		?>
		<div <?php echo $this->get_render_attribute_string('sitemap_wrapper_archive'); ?>>
			<div class="sitemap-posts-title"><h2><?php echo sanitize_text_field($settings['stm_archive_titre']); ?></h2></div>
			<div class="sitemap-posts-list">
				<?php
				$post_types = $settings['stm_archive_type'];
				$type = $settings['stm_archive_frequence'];
				$showcount = $settings['stm_archive_post_count'] === 'yes' ? true : false;
				?>
				<ul>
					<?php wp_get_archives(array('post_type' => $post_types, 'type' => $type, 'show_post_count' => $showcount)); ?>
				</ul>
			</div>
		</div>
	<?php
	}
	
	/**
	 * eac_get_html_sitemap_taxonomies
	 * 
	 * Description:
	 * 
	 * 
	 * @since 1.7.1
	 */
	protected function eac_get_html_sitemap_taxonomies() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('sitemap_wrapper_taxo', 'class', 'sitemap-taxonomies');
		?>
		<div <?php echo $this->get_render_attribute_string('sitemap_wrapper_taxo'); ?>>
			<div class="sitemap-posts-title"><h2><?php echo sanitize_text_field($settings['stm_taxonomy_titre']); ?></h2></div>
			<div class="sitemap-posts-list">
				<ul>
				<?php
				global $post;
				
				// Toutes les taxonomies déjà filtrées
				$all_taxos = Eac_Tools_Util::get_all_taxonomies();
				
				// Les taxonomies exclues
				$exclude_taxos = $settings['stm_taxonomy_exclude'];
				
				// Trie
				$orderby = $settings['stm_taxonomy_orderby'];
				
				// Descendant/Ascendant
				$order = $settings['stm_taxonomy_order'];
				
				// Afficher la date
				$has_date = $settings['stm_taxonomy_date'] === 'yes' ? true : false;
				$la_date = '';
				
				// Affiche le nombre de catégories
				$has_cat_count = $settings['stm_taxonomy_count'] === 'yes' ? true : false;
				
				// Affiche le nombre de commentaires
				$has_comment = $settings['stm_taxonomy_comment'] === 'yes' ? true : false;
				$c_count = '';
				
				// Ajout de nofollow aux liens
				$has_nofollow = $settings['stm_taxonomy_nofollow'] === 'yes' ? 'rel="nofollow"' : '';
				
				// Exclusion d'articles
				$has_id = $settings['stm_taxonomy_id'] === 'yes' ? true : false;
				$exclude_post = !empty($settings['stm_taxonomy_exclude_id']) ? explode(',', sanitize_text_field($settings['stm_taxonomy_exclude_id'])) : '';
				
				// Boucle sur les taxonomies
				foreach($all_taxos as $taxo => $value) {
					if(empty($exclude_taxos) || (!empty($exclude_taxos) && !in_array($taxo, $exclude_taxos))) {
						// Les catégories de la taxonomie
						$categories = get_categories(array('taxonomy' => $taxo, 'hide_empty' => true));
										
						// Boucle sur chaque catégorie de la taxonomie
						foreach($categories as $categorie) {
							// Le type de post de la catégorie
							if(!$post_object = get_taxonomy($categorie->taxonomy)) {
								continue;
							}
							
							// Le nombre d'occurrence de la catégorie
							$cat_count = '';
							if($has_cat_count) {
								$cat_count = " (" . $categorie->category_count . ")";
							}
							
							// Arguments de la requête
							$args = array(
								'post_type' => $post_object->object_type, // $post_object->object_type[0]
								'posts_per_page' => -1,
								'orderby' => $orderby,
								'order' => $order,
								'post__not_in' => $exclude_post,
								'tax_query' => array(
									array(
										'taxonomy' => $taxo,
										'field'    => 'id',
										'terms'    => $categorie->cat_ID,
									)
								)
							);
							
							// Les articles
							$posts_array = get_posts($args);
							
							// Il y a des posts
							if(count($posts_array) > 0) {
								// Affiche le nom de la taxonomie
								?>
								<li><span><span><b><?php echo ucfirst($taxo); ?></b></span><a href="<?php echo esc_url(get_category_link($categorie->cat_ID)); ?>" <?php echo $has_nofollow; ?>><?php echo $categorie->cat_name; ?></a><?php echo $cat_count; ?></span>
									<ul>
									<?php
									
									foreach($posts_array as $post) {
										// Renseigne les variables globales de l'article courant
										setup_postdata($post);
										
										// L'ID de l'article
										$id = '';
										if($has_id) {
											$id = " " . get_the_id();
										}
										
										// Affiche la date
										if($has_date) {
											if('modified' === $orderby) {
												$la_date = '<span>' . get_the_modified_date(get_option('date_format')) . '</span>';
											} else {
												$la_date = '<span>' . get_the_date(get_option('date_format')) . '</span>';
											}
										}
										// Affiche le nombre de commentaires
										if($has_comment) {
											$c_count = ' (' . get_comments_number() . ')';
										}
										?>
										<li><span><?php echo $la_date; ?><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" <?php echo $has_nofollow; ?>><?php the_title(); ?></a><?php echo $c_count;?><?php echo $id; ?></span></li>
										<?php 
									}
									wp_reset_postdata();
									?>
									</ul> 
								</li>
							<?php
							}
						}		// End foreach categories
					}			// End If taxonomie exclue
				}				// End foreach taxonomie
				?>
				</ul>
			</div>
		</div>
	<?php
	}
	
	/**
	 * eac_get_html_sitemap_posts
	 * 
	 * Description:
	 * 
	 * 
	 * @since 1.7.1
	 */
	protected function eac_get_html_sitemap_posts() {
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute('sitemap_wrapper_post', 'class', 'sitemap-posts');
		?>
		<div <?php echo $this->get_render_attribute_string('sitemap_wrapper_post'); ?>>
			<div class="sitemap-posts-title"><h2><?php echo sanitize_text_field($settings['stm_post_titre']); ?></h2></div>
			<div class="sitemap-posts-list">
				<?php
				global $post;
				
				// Trie
				$orderby = $settings['stm_post_orderby'];
				
				// Descendant/Ascendant
				$order = $settings['stm_post_order'];
				
				// Afficher la date
				$has_date = $settings['stm_post_date'] === 'yes' ? true : false;
				$la_date = '';
				
				// Affiche le nombre de commentaires
				$has_comment = $settings['stm_post_comment'] === 'yes' ? true : false;
				$c_count = '';
				
				// Ajout de nofollow aux liens
				$has_nofollow = $settings['stm_post_nofollow'] === 'yes' ? 'rel="nofollow"' : '';
				
				// Les catégories par article
				$has_category = $settings['stm_post_category'] === 'yes' ? true : false;
				$categories = '';
				
				// Exclusion d'articles
				$has_id = $settings['stm_post_id'] === 'yes' ? true : false;
				$exclude_post = !empty($settings['stm_post_exclude']) ? explode(',', sanitize_text_field($settings['stm_post_exclude'])) : '';
				
				// Exclure des types d'articles
				$exclude_posttype = array();
				$exclude_posttype = $settings['stm_post_type_exclude'];
				
				// Filtre la taxonomie
				add_filter('eac/tools/taxonomies_by_name', function($filter_taxo) {
					$exclude = ['post_tag'];
					return array_merge($filter_taxo, $exclude);
					//$include = [''];
					//return array_diff($filter_taxo, $include);
					//$exclude_key = ['ID' => 'ID', 'author' => 'author', 'comment_count' => 'comment_count', 'meta_value_num' => 'meta_value_num'];
					//return array_diff_key($exclude_orderby, $exclude_key);
				}, 10);
				
				// Récupère toute la taxonomie filtrée par le nom
				$all_taxo = Eac_Tools_Util::get_all_taxonomies_by_name();
				
				/*highlight_string("<?php\n\$settings =\n" . var_export($all_taxo, true) . ";\n?>");*/
				
				// Récupère tous les types d'articles
				$post_types = get_post_types(array('public' => true), 'objects');
				
				// Boucle sur les types d'articles
				foreach($post_types as $post_type) { 
					if(empty($exclude_posttype) || (!empty($exclude_posttype) && !in_array($post_type->name, $exclude_posttype))) { ?>
						<h3><?php echo $post_type->labels->name; ?></h3>
						<?php
						$args = array(
							'posts_per_page' => -1,
							'post_type' => $post_type->name,
							'post_status' => 'publish',
							'orderby' => $orderby,
							'order' => $order,
							'post__not_in' => $exclude_post,
						);
						
						// Les articles
						$posts_array = get_posts($args); ?>
						<ul>
						<?php
						foreach($posts_array as $post) {
							// Renseigne les variables globales de l'article courant
							setup_postdata($post);
							
							// L'ID de l'article
							$id = '';
							if($has_id) {
								$id = " " . get_the_id();
							}
							
							// Affiche et formate la date
							if($has_date) {
								if('modified' === $orderby) {
									$la_date = '<span>' . get_the_modified_date(get_option('date_format')) . '</span>';
								} else {
									$la_date = '<span>' . get_the_date(get_option('date_format')) . '</span>';
								}
							}
							
							// Affiche le nombre de commentaires
							if($has_comment) {
								$c_count = ' (' . get_comments_number() . ')';
							}
							
							// Les catégories sont renseignées
							if($has_category) {
								// Récupère toutes les catégories de l'article
								$post_categories = get_the_terms(get_the_id(), $all_taxo);
							
								// Recherche les catégories de l'article
								if(!empty($post_categories) && !is_wp_error($post_categories)) {
									$categories = implode(',', wp_list_pluck($post_categories, 'name'));
									$categories = '<span>' . $categories . '</span>';
								}
							}
							?>
							<li><span><?php echo $categories . $la_date; ?><a href="<?php the_permalink(); ?>" <?php echo $has_nofollow; ?>><?php the_title(); ?></a><?php echo $c_count;?><?php echo $id; ?></span></li>
						<?php
						}
						wp_reset_postdata();
						?>
						</ul>
					<?php
					}
				}
				?>
			</div>
		</div>
	<?php
	}
	
	protected function content_template() {}
	
}