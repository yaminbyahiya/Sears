<?php

/*========================================================================================================
* Class: Acf_Relationship_Widget
* Name: ACF Relationship
* Slug: eac-addon-acf-relationship
*
* Description: Affiche et formate les entrées sélectionnées dans le champ Relationship ou Post object
* d'un articles. Les articles sont affichées sous forme de grille.
*
* 
* @since 1.8.2
* @since 1.8.5	Fix: ACF field 'Select multiple values' === 'no' pour le champ 'post_object'
*				Force le changement du type de données en array
* @since 1.8.7	Support des custom breakpoints
* @since 1.9.0	Intégration des scripts et des styles dans le constructeur de la class
* @since 1.9.5	Fix: vsprintf passé des strings en arguments à la place d'un array()
* @since 1.9.7	Ajout du traitement du mode 'slider'
* @since 1.9.8	Ajout du bouton 'En savoir plus'
*========================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Widgets\Traits\Slider_Trait;
use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if(! defined('ABSPATH')) exit; // Exit if accessed directly

class Acf_Relationship_Widget extends Widget_Base {
	/** Le slider Trait */
	use Slider_Trait;
	
	/**
	 * Constructeur de la class Acf_Relationship_Widget
	 * 
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array('jquery'), '8.3.2', true);
		wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.3.2');
		
		wp_register_script('eac-acf-relation', EAC_Plugin::instance()->get_register_script_url('acf-relationship'), array('jquery', 'elementor-frontend', 'swiper'), '1.9.7', true);
		
		wp_register_style('eac-swiper', EAC_Plugin::instance()->get_register_style_url('swiper'), array('eac', 'swiper'), '1.9.7');
		wp_register_style('eac-acf-relation', EAC_Plugin::instance()->get_register_style_url('acf-relationship'), array('eac'), '1.8.2');
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'acf-relationship';
	
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
		return ['swiper', 'eac-acf-relation'];
	}
	
	/* 
	 * Load dependent styles
	 * 
	 * Les styles sont chargés dans le footer !!
     *
     * @return CSS list.
	 */
	public function get_style_depends() {
		return ['swiper', 'eac-swiper', 'eac-acf-relation'];
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
		 * Generale content Section
		 */
        $this->start_controls_section('acf_relation_settings',
			[
				'label'     => esc_html__('Réglages', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('acf_relation_settings_origine',
				[
					'label' => esc_html__('Champ relationnel', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => $this->get_acf_fields_options($this->get_acf_supported_fields(), get_the_ID()),
					'label_block' => true,
				]
			);
			
			$this->add_control('acf_relation_settings_include_type',
				[
					'label' => esc_html__("Sélectionner les types d'articles", 'eac-components'),
					'type' => Controls_Manager::SELECT2,
					'options' => Eac_Tools_Util::get_filter_post_types(),
					'default' => ['post', 'page'],
					'multiple' => true,
					'label_block' => true,
				]
			);
			
			$this->add_control('acf_relation_settings_nombre',
				[
					'label' => esc_html__("Nombre d'articles", 'eac-components'),
					'description' => esc_html__('-1 = Tous','eac-components'),
					'type' => Controls_Manager::NUMBER,
					'default' => 3,
				]
			);
			
			/*$this->add_control('acf_relation_settings_duplicates',
				[
					'label' => esc_html__("Conserver les doublons", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);*/
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_layout',
			[
				'label' => esc_html__('Disposition', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/**
			 * @since 1.9.7	Ajout de l'option 'slider'
			 */
			$this->add_control('acf_relation_layout_type',
				[
					'label'   => esc_html__('Mode', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'fitRows',
					'options' => [
						'fitRows'	=> esc_html__('Grille', 'eac-components'),
						'slider'	=> esc_html__('Slider'),
					],
				]
			);
			
			$this->add_control('acf_relation_ratio_image_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'  => esc_html__("Pour un ajustement parfait vous pouvez appliquer un ratio sur les images dans la section 'Image'", "eac-components"),
					'condition' => ['acf_relation_layout_type' => 'fitRows', 'acf_relation_content_image' => 'yes'],
				]
			);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = [];
			foreach($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
				//if(!in_array($breakpoint_name, [Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN, Breakpoints_manager::BREAKPOINT_KEY_LAPTOP])) {
					if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '3'];
					}
				//}
			}
			
			/**
			 * 'prefix_class' ne fonctionnera qu'avec les flexbox
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_layout_columns',
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
						'6'    => '6',
					],
					'prefix_class' => 'responsive%s-',
					'condition' => ['acf_relation_layout_type' => 'fitRows'],
				]
			);
			
		$this->end_controls_section();
		
		/** @since 1.9.7 Slider */
		$this->start_controls_section('acf_relation_slider_settings',
			[
				'label' => 'Slider',
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => ['acf_relation_layout_type' => 'slider'],
			]
		);
			
			$this->register_slider_content_controls();
		
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_content',
			[
				'label'     => esc_html__('Contenu', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/*$this->add_control('acf_relation_content_parent',
				[
					'label' => esc_html__("Le titre de l'article parent", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);*/
			
			$this->add_control('acf_relation_content_date',
				[
					'label' => esc_html__("Date", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('acf_relation_content_excerpt',
				[
					'label' => esc_html__("Résumé", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('acf_relation_content_image',
				[
					'label' => esc_html__("Image en avant", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('acf_relation_content_image_link',
				[
					'label' => esc_html__("Lien de l'article sur l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			/** @since 1.9.8 */
			$this->add_control('acf_relation_content_button',
				[
					'label' => esc_html__("Bouton 'En savoir plus'", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('acf_relation_content_button_label',
				[
					'label'   => esc_html__('Label du bouton', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'default' => esc_html__('En savoir plus', 'eac-components'),
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_image',
			[
               'label' => esc_html__("Image", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			   'condition' => ['acf_relation_content_image' => 'yes'],
			]
		);
			
			$this->add_control('acf_relation_content_image_dimension',
				[
					'label'   => esc_html__('Dimension', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'medium',
					'options'       => [
						'thumbnail'		=> esc_html__('Miniature', 'eac-components'),
						'medium'		=> esc_html__('Moyenne', 'eac-components'),
						'medium_large'	=> esc_html__('Moyenne-large', 'eac-components'),
						'large'			=> esc_html__('Large', 'eac-components'),
						'full'			=> esc_html__('Originale', 'eac-components'),
					],
					
				]
			);
			
			$this->add_control('acf_relation_image_style_ratio_enable',
				[
					'label' => esc_html__("Activer le ratio image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
					'condition' => ['acf_relation_layout_type' => 'fitRows'],
					'separator' => 'before',
				]
			);
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_image_style_ratio',
				[
					'label' => esc_html__('Ratio', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 0.6, 'unit' => '%'],
					'range' => ['%' => ['min' => 0.1, 'max' => 2, 'step' => 0.1]],
					'selectors' => ['{{WRAPPER}} .acf-relation_container.acf-relation_img-ratio .acf-relation_img' => 'padding-bottom:calc({{SIZE}} * 100%);'],
					'condition' => ['acf_relation_image_style_ratio_enable' => 'yes', 'acf_relation_layout_type' => 'fitRows'],
				]
			);
			
			/**
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('acf_relation_image_ratio_position_y',
				[
					'label' => esc_html__('Position verticale', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 50, 'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .acf-relation_container.acf-relation_img-ratio .acf-relation_img img' => 'object-position: 50% {{SIZE}}%;'],
					'condition' => ['acf_relation_image_style_ratio_enable' => 'yes', 'acf_relation_layout_type' => 'fitRows'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_title',
			[
               'label' => esc_html__("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('acf_relation_title_tag',
				[
					'label'			=> esc_html__('Étiquette', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h3',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'	=> 'div',
						'p'		=> 'p',
                    ],
				]
			);
			
			$this->add_control('acf_relation_title_align',
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
					'default' => 'center',
					'toggle' => true,
					'selectors' => [
						'{{WRAPPER}} .acf-relation_title, {{WRAPPER}} .acf-relation_title-parent' => 'text-align: {{VALUE}};',
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('acf_relation_excerpt',
			[
               'label' => esc_html__("Résumé", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			   'condition' => ['acf_relation_content_excerpt' => 'yes'],
			]
		);
			
			$this->add_control('acf_relation_excerpt_length',
				[
					'label' => esc_html__('Nombre de mots', 'eac-components'),
					'type'  => Controls_Manager::NUMBER,
					'min' => 10,
					'max' => 100,
					'step' => 5,
					'default' => apply_filters('excerpt_length', 25), /** Ce filtre est documenté dans wp-includes/formatting.php */
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('acf_relation_general_style',
			[
				'label'      => esc_html__('Général', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('acf_relation_wrapper_style',
				[
					'label'			=> esc_html__("Style", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'style-1',
					'options'       => [
						'style-0' => esc_html__("Défaut", 'eac-components'),
                        'style-1' => 'Style 1',
                        'style-2' => 'Style 2',
						'style-3' => 'Style 3',
						'style-4' => 'Style 4',
						'style-5' => 'Style 5',
						'style-6' => 'Style 6',
						'style-7' => 'Style 7',
						'style-8' => 'Style 8',
						'style-9' => 'Style 9',
						'style-10' => 'Style 10',
						'style-11' => 'Style 11',
						'style-12' => 'Style 12',
                    ],
					'prefix_class' => 'acf-relation_wrapper-',
				]
			);
			
			/**
			 * @since 1.9.7
			 */
			$this->add_responsive_control('acf_relation_wrapper_style_margin',
				[
					'label' => esc_html__('Marge entre les items', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 7, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
					'selectors' => [
						'{{WRAPPER}} .swiper-container' => 'padding: {{SIZE}}{{UNIT}};',
						//'{{WRAPPER}} .acf-relation_inner-wrapper' => 'height: calc(100% - (2 * {{SIZE}}{{UNIT}}));',
					],
					'condition' => ['acf_relation_layout_type' => 'slider'],
				]
			);
			
			$this->add_control('acf_relation_wrapper_style_bgcolor',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .swiper-container, {{WRAPPER}} .acf-relation_container' => 'background-color: {{VALUE}};'],
				]
			);
			
			/** Articles */
			$this->add_control('al_items_style',
				[
					'label'			=> esc_html__('Articles', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);
			
			$this->add_control('acf_relation_items_bg_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .acf-relation_container article' => 'background-color: {{VALUE}};'],
				]
			);
			
			/** Images */
			$this->add_control('al_images_style',
				[
					'label'			=> esc_html__('Images', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_image_border_radius',
				[
					'label' => esc_html__('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .acf-relation_img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'acf_relation_image_border',
					'selector' => '{{WRAPPER}} .acf-relation_img img',
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			/** Titre */
			$this->add_control('al_title_style',
				[
					'label'			=> esc_html__('Titre', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_title_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .acf-relation_title .acf-relation_title-content' => 'color: {{VALUE}};'],
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_title_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .acf-relation_title .acf-relation_title-content',
					'condition' => ['acf_relation_content_image' => 'yes'],
				]
			);
			
			/** Date */
			$this->add_control('al_date_style',
				[
					'label'			=> esc_html__('Date', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['acf_relation_content_date' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_date_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .acf-relation_date' => 'color: {{VALUE}};'],
					'condition' => ['acf_relation_content_date' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_date_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .acf-relation_date',
					'condition' => ['acf_relation_content_date' => 'yes'],
				]
			);
			
			/** Résumé */
			$this->add_control('al_excerpt_style',
				[
					'label'			=> esc_html__('Résumé', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['acf_relation_content_excerpt' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_excerpt_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .acf-relation_excerpt' => 'color: {{VALUE}};'],
					'condition' => ['acf_relation_content_excerpt' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_excerpt_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .acf-relation_excerpt',
					'condition' => ['acf_relation_content_excerpt' => 'yes'],
				]
			);
			
			/** @since 1.9.8 Bouton */
			$this->add_control('al_button_style',
				[
					'label'			=> esc_html__('Bouton', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_button_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .acf-relation_button' => 'color: {{VALUE}}'],
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'acf_relation_button_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .acf-relation_button',
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_button_background',
				[
					'label'         => esc_html__('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .acf-relation_button'  => 'background-color: {{VALUE}};'],
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
			$this->add_responsive_control('acf_relation_button_padding',
				[
					'label' => esc_html__('Marges internes', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .acf-relation_button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'acf_relation_button_shadow',
    				'label' => esc_html__('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .acf-relation_button',
					'condition' => ['acf_relation_content_button' => 'yes'],
    			]
    		);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'acf_relation_button_border',
					'selector' => '{{WRAPPER}} .acf-relation_button',
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
			$this->add_control('acf_relation_button_radius',
				[
					'label' => esc_html__('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'selectors' => [
						'{{WRAPPER}} .acf-relation_button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition' => ['acf_relation_content_button' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		/** @since 1.9.7 Ajout de la section contrôles du slider */
		$this->start_controls_section('acf_relation_slider_section_style',
			[
				'label' => esc_html__('Contrôles du slider', 'eac-components'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'terms' => [
								['name' => 'acf_relation_layout_type', 'operator' => '===', 'value' => 'slider'],
								['name' => 'slider_navigation', 'operator' => '===', 'value' => 'yes']
							]
						],
						[
							'terms' => [
								['name' => 'acf_relation_layout_type', 'operator' => '===', 'value' => 'slider'],
								['name' => 'slider_pagination', 'operator' => '===', 'value' => 'yes']
							]
						],
					]
				]
			]
		);
			
			/** Slider styles du trait */
			$this->register_slider_style_controls();
			
		$this->end_controls_section();
    }

	/**
	 * Render widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @access protected
	 */
    protected function render() {
		$settings = $this->get_settings_for_display();
		if(empty($settings['acf_relation_settings_origine'])) { return; }
		
		$slider_id = "slider_acf_relationship_" . $this->get_id();
		$has_swiper = $settings['acf_relation_layout_type'] === 'slider' ? true : false;
		$has_navigation = $has_swiper && $settings['slider_navigation'] === 'yes' ? true : false;
		$has_pagination = $has_swiper && $settings['slider_pagination'] === 'yes' ? true : false;
		$has_scrollbar = $has_swiper && $settings['slider_scrollbar'] === 'yes' ? true : false;
		
		if($has_swiper) { ?>
			<div id="<?php echo $slider_id; ?>" class="eac-acf-relationship swiper-container">
		<?php } else { ?>
			<div class="eac-acf-relationship">
		<?php }
				$this->get_relation_by_id();
				if($has_navigation) { ?>
					<div class="swiper-button-prev"></div>
					<div class="swiper-button-next"></div>
				<?php } ?>
				<?php if($has_scrollbar) { ?>
					<div class="swiper-scrollbar"></div>
				<?php } ?>
				<?php if($has_pagination) { ?>
					<div class="swiper-pagination-bullet"></div>
				<?php } ?>
			</div>
		<?php
	}
	
	/**
	 * get_relation_by_id
	 *
	 * 
	 *
	 * @access protected
	 */
	protected function get_relation_by_id() {
		$settings = $this->get_settings_for_display();
		$key = $settings['acf_relation_settings_origine'];
		$items = array();
		$parent_id = get_the_ID();
		$items = $this->get_relations($key, $parent_id);
		
		if(!empty($items)) {
			$this->render_relationship_content($items);
		}
	}
	
	/**
	 * get_relations
	 *
	 * Crée la liste des relationship d'un article
	 *
	 * @access protected
	 */
	protected function get_relations($key, $parent_id) {
		/**
		 * @var $items Array d'articles en relation avec l'article courant
		 */
		$items = array();
		
		/**
		 * @var $items_id Array des articles analysés par leur ID
		 */
		$items_id = array();
		
		/**
		 * @var $loop Variable pour compter le nombre de boucle
		 */
		$loop = 1;
		
		/**
		* @var $max_loops Variable pour limiter le nombre de boucle
		*
		* Nombre de boucle max pour éviter une boucle sans fin
		*/
		$max_loops = 1;
		
		$settings = $this->get_settings_for_display();
		$has_excerpt = $settings['acf_relation_content_excerpt'] === 'yes' ? true : false;
		$has_duplicate = false; //$settings['acf_relation_settings_duplicates'] === 'yes' ? true : false;
		$excerpt_length = $settings['acf_relation_excerpt_length'];
		$include_posttypes = $settings['acf_relation_settings_include_type'];
		$field_value = '';
		
		list($field_key, $meta_key) = array_pad(explode('::', $key), 2, '');
		
		if(empty($field_key)) { return; }
		
		$field = get_field_object($field_key, $parent_id);
		
		if($field && !empty($field['value'])) {
			$image_size = $settings['acf_relation_content_image_dimension'];
			$field_value = $field['value'];
			
			switch($field['type']) {
				case 'relationship':
				case 'post_object':
					$values = array();
					$featured = true;
					$img = '';
					if($field['type'] == 'relationship') {
						$featured = is_array($field['elements']) && !empty($field['elements'][0]) && $field['elements'][0] == 'featured_image' ? true : false;
					}
					/** @since 1.8.5 Fix cast $field_value dans le type tableau */
					$field_value = is_array($field_value) ? $field_value : array($field_value);
					
					// Première boucle on ajoute l'ID du post courant
					if($loop == 1) { $items_id[$parent_id] = $parent_id; }
					
					// Boucle sur tous les relationship posts
					foreach($field_value as $value) {
						$item = array();
						$id = $field['return_format'] == 'object' ? (int) $value->ID : (int) $value;
						
						// Le post_type n'est pas dans la liste
						if(!in_array(get_post_type($id), $include_posttypes)) { continue; }
						
						// Ne conserve pas les doublons et l'ID de l'article est déjà analysé ou c'est l'article courant
						if(!$has_duplicate && in_array($id, $items_id)) { continue; }
						
						// Enregistre les données
						$item[$id]['post_id'] = $id;
						$item[$id]['post_parent_id'] = $parent_id;
						$item[$id]['post_parent_title'] = get_post($parent_id)->post_title;
						$item[$id]['post_type'] = get_post_type($id);
						$item[$id]['post_title'] = $field['return_format'] == 'object' ? esc_html($value->post_title) : esc_html(get_post($id)->post_title);
						$item[$id]['link'] = esc_url(get_permalink(get_post($id)->ID));
						$item[$id]['img'] = $featured ? get_the_post_thumbnail($id, $image_size) : '';
						$item[$id]['post_date'] = get_the_modified_date(get_option('date_format'), $id);
						$item[$id]['post_excerpt'] = in_array(get_post_type($id), ['page', 'attachment']) || !$has_excerpt ? "[...]" : Eac_Tools_Util::get_post_excerpt($id, $excerpt_length);
						$item[$id]['class'] = esc_attr(implode(' ', get_post_class('', $id)));
						$item[$id]['id'] = 'post-' . $id;
						$item[$id]['processed'] = false;
						
						// ID du relationship post + ID du parent pour conserver les doublons
						if($has_duplicate) {
							$items[$id . '-' . $parent_id] = $item[$id];
						} else {
							$items[$id] = $item[$id];
						}
						
						// Ajout de l'ID de l'article à la liste des ID déjà analysé
						$items_id[] = $id;
						
						// Ajout d'une boucle récursive. Plus tard
						$loop++;
					}
					
					if($loop > $max_loops) { return $items; }
			
					// Boucle sur tous les items 
					foreach($items as $post_key => $post_val) {
						//$exp = $items[$post_key]['post_title']."::".$items[$post_key]['processed'];
						
						// L'article n'a pas été analysé
						if($post_val['processed'] == false) {
							$items[$post_key]['processed'] = true;
							
							// Champs ACF relationship (Field-key::Field-name) pour cet article
							$key = $this->get_acf_fields_options($this->get_acf_supported_fields(), $post_val['post_id']);
							
							// Récursivité on analyse l'ID pour chercher les articles en relationship
							if(is_array($key) && !empty($key)) {
								$this->get_relations(array_keys($key)[0], $post_val['post_id']);
							}
						}
					}
				break;
			}
		}
		
		return $items;
	}
	
	/**
	 * render_relationship_content
	 *
	 * Mis en forme des relationship mode grille
	 *
	 * @access protected
	 */
	protected function render_relationship_content($items = array()) {
		$settings = $this->get_settings_for_display();
		$has_image = $settings['acf_relation_content_image'] === 'yes' ? true : false;
		$has_ratio = $settings['acf_relation_image_style_ratio_enable'] === 'yes' ? true : false;
		$has_date = $settings['acf_relation_content_date'] === 'yes' ? true : false;
		$has_excerpt = $settings['acf_relation_content_excerpt'] === 'yes' ? true : false;
		$has_link = $settings['acf_relation_content_image_link'] === 'yes' ? true : false;
		$has_button = $settings['acf_relation_content_button'] === 'yes' ? true : false;
		$has_parent_title = false; //$settings['acf_relation_content_parent'] === 'yes' ? true : false;
		$nb_posts = !empty($settings['acf_relation_settings_nombre']) ? $settings['acf_relation_settings_nombre'] : -1;
		$nb_displayed = 0;
		$has_swiper = $settings['acf_relation_layout_type'] === 'slider' ? true : false;
		
		// Formate le titre avec son tag
		$title_tag = $settings['acf_relation_title_tag'];
		$open_title = '<'. $title_tag .' class="acf-relation_title-content">';
		$close_title = '</'. $title_tag .'>';
		
		$id = $this->get_id();
		
		/**
		 * Le wrapper du container et la class pour le ratio d'image
		 * @since 1.9.5 remplace vsprintf par sprintf
		 * @since 1.9.7	Traitement du mode slider
		 */
		if(! $has_swiper) {
			$class = sprintf("acf-relation_container %s", $has_ratio ? 'acf-relation_img-ratio' : '');
		} else {
			$class = sprintf("acf-relation_container swiper-wrapper");
		}
		
		$this->add_render_attribute('container_wrapper', 'class', $class);
		$this->add_render_attribute('container_wrapper', 'id', $id);
		$this->add_render_attribute('container_wrapper', 'data-settings', $this->get_settings_json());
		
		$container = "<div " . $this->get_render_attribute_string('container_wrapper') . ">";
		
		$values = array();
		
		foreach($items as $item) {
			if($nb_posts != -1 && $nb_displayed >= $nb_posts) { break; }
			$value = '';
			
			if($has_swiper) {
				$item['class'] = $item['class'] . ' swiper-slide';
			}
			$value .= "<article id='" . $item['id'] . "' class='" . $item['class'] . "'>";
				$value .= "<div class='acf-relation_inner-wrapper'>";
			
					/** Affichage de l'image */
					if($has_image && !empty($item['img'])) {
						/** Le lien sur l'image */
						if($has_link) {
							$value .= "<div class='acf-relation_img'><a href='" . $item['link'] . "'>" . $item['img'] . "</a></div>";
						} else {
							$value .= "<div class='acf-relation_img'>" . $item['img'] . "</div>";
						}
					}
						
					/** Affichage du contenu */
					$value .= "<div class='acf-relation_content'>";
					
						/** Affichage du titre */
						$value .= "<div class='acf-relation_title'>";
							$value .= "<a href='" . $item['link'] . "'>" . $open_title . $item['post_title'] . $close_title . "</a>";
						$value .= "</div>";
						
						/** Affichage du titre du parent */
						if($has_parent_title) {
							$value .= "<div class='acf-relation_title-parent'>";
								$value .= $open_title . $item['post_parent_title'] . $close_title;
							$value .= "</div>";
						}
							
						/** Affichage de la date */
						if($has_date) {
							$value .= "<div class='acf-relation_date'>" . $item['post_date'] . "</div>";
						}
							
						/** Affichage du résumé */
						if($has_excerpt) {
							$value .= "<div class='acf-relation_excerpt'>" . $item['post_excerpt'] . "</div>";
						}
						
						/** @since 1.9.8 Affichage du bouton */
						if($has_button) {
							$label = !empty($settings['acf_relation_content_button_label']) ? $settings['acf_relation_content_button_label'] : esc_html__('En savoir plus', 'eac-components');
							$value .= '<div class="acf-relation_button-wrapper">';
								$value .= "<a href='" . $item['link'] . "'>";
									$value .= '<button class="acf-relation_button" type="button">' . $label . '</button>';
								$value .= '</a>';
							$value .= '</div>';
						}
					$value .= "</div>"; // Fin div contenu
				$value .= "</div>"; // Fin div wrapper
			$value .= "</article>"; // Fin article
			
			$values[] =  $value;
			$nb_displayed++;
		}
		echo $container . implode(' ', $values) . "</div>";
	}
	
	/**
	 * get_acf_fields_options
	 *
	 * Retourne Field_id et Field_name pour un article
	 *
	 * @access protected
	 */
	protected function get_acf_fields_options($field_type, $post_id) {
		$groups = array();
		$options = array('' => esc_html__('Select...', 'eac-components'));
		
		// Les groupes pour l'article
		$acf_groups = acf_get_field_groups(array('post_id' => $post_id));
		
		foreach($acf_groups as $group) {
			// Le groupe n'est pas désactivé
			if(!$group['active']) {
				continue;
			}
			
			if(isset($group['ID']) && !empty($group['ID'])) {
				$fields = acf_get_fields($group['ID']);
			} else {
				$fields = acf_get_fields($group);
			}
			
			// Pas de champ
			if(!is_array($fields)) {
				continue;
			}
			
			foreach($fields as $field) {
				// C'est le bon type de champ ACF
				if(!in_array($field['type'], $field_type, true)) {
					continue;
				}
				
				// Clé unique et slug comme indice du tableau
				$key = $field['key'] . '::' . $field['name'];
				$options[$key] = $group['title'] . "::" . $field['label'];
			}
		}
		
		return $options;
	}
	
	/**
	 * get_acf_supported_fields
	 *
	 * La liste des champs supportés
	 *
	 * @access protected
	 */
	protected function get_acf_supported_fields() {
		return [
		'relationship',
		'post_object',
		];
	}
	
	/**
	 * get_settings_json()
	 *
	 * Retrieve fields values to pass at the widget container
     * Convert on JSON format
	 *
	 * @uses      json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access    protected
	 * @since 1.9.7
	 */
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		
		$effect = $module_settings['slider_effect'];
		if(in_array($effect, ['fade', 'creative'])) { $nb_images = 1; }
		// Effet slide pour nombre d'images = 0 = 'auto'
		else if(empty($module_settings['slider_images_number']) || $module_settings['slider_images_number'] == 0) { $nb_images = "auto"; $effect = 'slide'; }
		else $nb_images = $module_settings['slider_images_number'];
		
		$settings = array(
			"data_id"		=> $this->get_id(),
			"data_sw_swiper"	=> $module_settings['acf_relation_layout_type'] === 'slider' ? true : false,
			"data_sw_autoplay"	=> $module_settings['slider_autoplay'] === 'yes' ? true : false,
			"data_sw_loop"	=> $module_settings['slider_loop'] === 'yes' ? true : false,
			"data_sw_delay"	=> $module_settings['slider_delay'],
			"data_sw_imgs"	=> $nb_images,
			"data_sw_dir"	=> "horizontal",
			"data_sw_rtl"	=> $module_settings['slider_rtl'] === 'right' ? true : false,
			"data_sw_effect"	=> $effect,
			"data_sw_free"	=> true,
			"data_sw_pagination_click"	=> $module_settings['slider_pagination'] === 'yes' && $module_settings['slider_pagination_click'] === 'yes' ? true : false,
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}