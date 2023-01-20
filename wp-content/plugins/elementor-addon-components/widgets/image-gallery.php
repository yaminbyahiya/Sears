<?php

/*======================================================================================================
* Class: Image_Galerie_Widget
* Name: Galerie d'Images
* Slug: eac-addon-image-galerie
*
* Description: Image_Galerie_Widget affiche des images dans différents modes
* grille, mosaïque et justifiées
*
* @since 0.0.9
* @since 1.4.1  Forcer le chargement des images depuis le serveur
* @since 1.5.3  Modifie l'affectation de 'layoutType'
* @since 1.6.0	Activation de la propriété 'dynamic' des controls de l'image
*				Gestion des images avec des URLs externes par la balise dynamique du control MEDIA
*				Ajout du ratio Image pour le mode Grid
*				La visionneuse peut être activée avec tous les modes
* @since 1.6.5	Ajoute le control Attribut ALT pour les images externes
* @since 1.6.7	Check 'justify' layout type for the grid parameters
*				Ajour du mode Metro
*				La taille de la fonte de l'icone pour le lien image est fixe dans le css
* @since 1.6.8	Ajouter la fonctionnalité des filtres
* @since 1.7.0	Les Custom Fields Keys et Values peuvent être sélectionnés
* @since 1.7.2	Ajout d'une section Image sous l'onglet style
*				Ajout d'un control pour positionner l'image verticalement avec le ratio Image appliqué
*				Fix: Alignement du filtre pour les mobiles
* @since 1.8.2	Ajout de la propriété 'prefix_class' pour modifier le style sans recharger le widget
* @since 1.8.4	Ajout des controles pour modifier le style du filtre
* @since 1.8.7	Support des custom breakpoints
*				Suppression de la méthode 'init_settings'
* @since 1.9.0	Intégration des scripts et des styles dans le constructeur de la class
* @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
* @since 1.9.7	Ajout du traitement du mode 'slider'
* @since 1.9.8	Affiche l'image placeholder Elementor par défaut
*======================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\Widgets\Traits\Slider_Trait;
use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Repeater;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Image_Galerie_Widget extends Widget_Base {
	/** Le slider Trait */
	use Slider_Trait;
	
	/**
	 * Constructeur de la class Image_Galerie_Widget
	 * 
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 * @since 1.9.7	Ajout des styles/scripts du mode slider
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_script('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js', array('jquery'), '8.3.2', true);
		wp_register_style('swiper', 'https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css', array(), '8.3.2');
		
		wp_register_script('isotope', EAC_ADDONS_URL . 'assets/js/isotope/isotope.pkgd.min.js', array('jquery'), '3.0.6', true);
		wp_register_script('eac-collageplus', EAC_ADDONS_URL . 'assets/js/isotope/jquery.collagePlus.min.js', array('jquery'), '0.3.3', true);
		wp_register_script('eac-image-gallery', EAC_Plugin::instance()->get_register_script_url('eac-image-gallery'), array('jquery', 'elementor-frontend', 'isotope', 'eac-collageplus', 'swiper'), '1.0.0', true);
		
		wp_register_style('eac-swiper', EAC_Plugin::instance()->get_register_style_url('swiper'), array('eac', 'swiper'), '1.9.7');
		wp_register_style('eac-image-gallery', EAC_Plugin::instance()->get_register_style_url('image-gallery'), array('eac'), '1.0.0');
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'image-galerie';
	
    /*
    * Retrieve widget name.
    *
    * @access public
    *
    * @return string widget name.
    */
    public function get_name() {
        return Eac_Config_Elements::get_widget_name($this->slug);
    }

    /*
    * Retrieve widget title.
    *
    * @access public
    *
    * @return string widget title.
    */
    public function get_title() {
		return Eac_Config_Elements::get_widget_title($this->slug);
    }

    /*
    * Retrieve widget icon.
    *
    * @access public
    *
    * @return string widget icon.
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
		return ['isotope', 'eac-imagesloaded', 'eac-collageplus', 'swiper', 'eac-image-gallery'];
	}
	
	/** 
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 * 
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return ['swiper', 'eac-swiper', 'eac-image-gallery'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.9.7
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
	 * @since 1.9.7
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
		
		$this->start_controls_section('ig_galerie_settings',
			[
				'label' => esc_html__('Galerie', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$repeater = new Repeater();
			
			$repeater->start_controls_tabs('ig_item_tabs_settings');
				
				$repeater->start_controls_tab('ig_item_image_settings',
					[
						'label'		=> esc_html__('Image', 'eac-components'),
					]
				);
				
					/** @since 1.6.0 */
					$repeater->add_control('ig_item_image',
						[
							'label'   => esc_html__('Image', 'eac-components'),
							'type'    => Controls_Manager::MEDIA,
							'dynamic' => ['active' => true],
							'default' => [
								'url' => Utils::get_placeholder_image_src(),
							],
						]
					);
					
					/** @since 1.6.5 Ajoute le control Attribut ALT */
					$repeater->add_control('ig_item_alt',
						[
							'label' => esc_html__('Attribut ALT', 'eac-components'),
							'type' => Controls_Manager::TEXT,
							'dynamic' => ['active' => true],
							'default' => '',
							'description' => esc_html__("Valoriser l'attribut 'ALT' pour une image externe (SEO)", 'eac-components'),
							'label_block'	=> true,
							'render_type' => 'none',
						]
					);
				
				$repeater->end_controls_tab();
						
				$repeater->start_controls_tab('ig_item_content_settings',
					[
						'label'		=> esc_html__('Contenu', 'eac-components'),
					]
				);
					
					/** @since 1.6.0 */
					$repeater->add_control('ig_item_title',
						[
							'label'   => esc_html__('Titre', 'eac-components'),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => ['active' => true],
							'default' => esc_html__('Image #', 'eac-components'),
						]
					);
					
					/** @since 1.6.0 */
					$repeater->add_control('ig_item_desc',
						[
							'label'   => esc_html__('Description', 'eac-components'),
							'type'    => Controls_Manager::TEXTAREA,
							'dynamic' => ['active' => true],
							'default' => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
							'label_block'	=> true,
						]
					);
					
					/**
					 * @since 1.6.8	Ajoute le champ dans lequel sont saisies les filtres
					 * @since 1.7.0	Dynamic Tags activés
					 */
					$repeater->add_control('ig_item_filter',
						[
							'label' => esc_html__('Labels du filtre', 'eac-components'),
							'type' => Controls_Manager::TEXT,
							'dynamic' => [
								'active' => true,
								'categories' => [
									TagsModule::POST_META_CATEGORY,
								],
							],
							'default' => '',
							'description' => esc_html__("Labels séparés par une virgule", 'eac-components'),
							'label_block'	=> true,
							'separator' => 'before',
							//'condition' => ['ig_layout_type_swiper!' => 'yes'],
							//'render_type' => 'ui',
							//'required' => true,
							//'hide_in_inner' => true,
						]
					);
					
					/** @since 1.9.7 */
					$repeater->add_control('ig_item_title_button',
						[
							'label'   => esc_html__('Label du bouton', 'eac-components'),
							'type'    => Controls_Manager::TEXT,
							'dynamic' => ['active' => true],
							'default' => esc_html__('En savoir plus', 'eac-components'),
							'separator' => 'before',
						]
					);
					
					/**
					 * @since 1.6.0
					 * @since 1.9.7
					 */
					$repeater->add_control('ig_item_url',
						[
							'label'       => esc_html__('Lien du bouton', 'eac-components'),
							'type'        => Controls_Manager::URL,
							'description' => esc_html__('Utiliser les balises dynamiques pour les liens internes', 'eac-components'),
							'placeholder' => 'http://your-link.com',
							'dynamic' => ['active' => true],
							'default' => [
								'url' => '#',
								'is_external' => false,
								'nofollow' => false,
							],
						]
					);
				
				$repeater->end_controls_tab();
					
			$repeater->end_controls_tabs();
			
			$this->add_control('ig_image_list',
				[
					'label'       => esc_html__('Liste des images', 'eac-components'),
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => esc_html__('Image #1', 'eac-components'),
							'ig_item_desc'        => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => esc_html__('Image #2', 'eac-components'),
							'ig_item_desc'        => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => esc_html__('Image #3', 'eac-components'),
							'ig_item_desc'        => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => esc_html__('Image #4', 'eac-components'),
							'ig_item_desc'        => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => esc_html__('Image #5', 'eac-components'),
							'ig_item_desc'        => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
						[
							'ig_item_image'       => ['url' => Utils::get_placeholder_image_src()],
							'ig_item_title'       => esc_html__('Image #6', 'eac-components'),
							'ig_item_desc'        => esc_html__("Le faux-texte en imprimerie, est un texte sans signification, qui sert à calibrer le contenu d'une page...", 'eac-components'),
						],
					],
					'title_field' => '{{{ ig_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ig_layout_type_settings',
			[
				'label' => esc_html__('Disposition', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/**
			 * @since 1.5.3
			 * @since 1.9.7	Ajout de l'option 'slider'
			 */
			$this->add_control('ig_layout_type',
				[
					'label'   => esc_html__('Mode', 'eac-components'),
					'type'    => Controls_Manager::SELECT,
					'default' => 'masonry',
					'options' => [
						'masonry'	=> esc_html__('Mosaïque', 'eac-components'),
						'fitRows'	=> esc_html__('Grille', 'eac-components'),
						'justify'	=> esc_html__('Justifier', 'eac-components'),
						'slider'	=> esc_html__('Slider'),
					],
				]
			);
			
			$this->add_control('ig_layout_ratio_image_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'  => esc_html__("Pour un ajustement parfait vous pouvez appliquer un ratio sur les images dans la section 'Image'", "eac-components"),
					'condition' => ['ig_layout_type' => 'fitRows'],
				]
			);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$columns_device_args = [];
			foreach($active_breakpoints as $breakpoint_name => $breakpoint_instance) {
				if(!in_array($breakpoint_name, [Breakpoints_manager::BREAKPOINT_KEY_WIDESCREEN, Breakpoints_manager::BREAKPOINT_KEY_LAPTOP])) {
					if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					}  else if($breakpoint_name === Breakpoints_manager::BREAKPOINT_KEY_MOBILE_EXTRA) {
						$columns_device_args[$breakpoint_name] = ['default' => '1'];
					} else {
						$columns_device_args[$breakpoint_name] = ['default' => '2'];
					}
				}
			}
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ig_columns',
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
					'render_type' => 'template',
					'condition' => ['ig_layout_type!' => ['justify', 'slider']],
				]
			);
			
			/** @since 1.6.7 Active le mode metro */
			$this->add_control('ig_layout_type_metro',
				[
					'label' => esc_html__("Activer le mode Metro", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'description' => esc_html__('Est appliqué uniquement à la première image', 'eac-components'),
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ig_layout_type' => 'masonry'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * @since 1.9.7 Slider
		 * @since 1.9.8 Les controls du slider Trait
		 */
		$this->start_controls_section('ig_slider_settings',
			[
				'label' => 'Slider',
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => ['ig_layout_type' => 'slider'],
			]
		);
			
			$this->register_slider_content_controls();
		
		$this->end_controls_section();
		
		$this->start_controls_section('ig_gallery_content',
			[
				'label' => esc_html__('Contenu', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/** @since 1.6.8 Ajoute la gestion des filtres */
			$this->add_control('ig_content_filter_display',
				[
					'label' => esc_html__("Afficher les filtres", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ig_layout_type!' => ['justify', 'slider']],
				]
			);
			
			/** 1.7.2 Ajout de la class 'ig-filters__wrapper-select' pour l'alignement du select sur les mobiles */
			$this->add_control('ig_content_filter_align',
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
					'selectors' => [
						'{{WRAPPER}} .ig-filters__wrapper, {{WRAPPER}} .ig-filters__wrapper-select' => 'text-align: {{VALUE}};',
					],
					'condition' => ['ig_content_filter_display' => 'yes', 'ig_layout_type!' => ['justify', 'slider']],
				]
			);
			
			$this->add_control('ig_content_title',
				[
					'label' => esc_html__("Afficher le titre", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('ig_content_description',
				[
					'label' => esc_html__("Afficher la description", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			/**
			 * @since 1.8.0 Ajout du control switcher pour mettre le lien du post sur l'image
			 * @since 1.9.7	
			 */
			$this->add_control('ig_image_link',
				[
					'label' => esc_html__("Lien de l'article sur l'image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ig_image_lightbox!' => 'yes', 'ig_overlay_inout' => 'overlay-out', 'ig_layout_type!' => 'justify'],
				]
			);
			
			/**
			 * @since 1.6.0 La visionneuse peut être activée pour tous les modes
			 * @since 1.9.7	
			 */
			$this->add_control('ig_image_lightbox',
				[
					'label' => esc_html__("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							[
								'terms' => [
									['name' => 'ig_layout_type', 'operator' => '===', 'value' => 'slider'],
									['name' => 'ig_overlay_inout', 'operator' => '===', 'value' => 'overlay-out'],
									['name' => 'ig_image_link', 'operator' => '!==', 'value' => 'yes'],
								]
							],
							[
								'terms' => [
									['name' => 'ig_layout_type', 'operator' => '===', 'value' => 'slider'],
									['name' => 'ig_overlay_inout', 'operator' => '===', 'value' => 'overlay-in'],
								]
							],
							[
								'terms' => [
									['name' => 'ig_layout_type', 'operator' => 'in', 'value' => ['masonry', 'fitRows']],
									['name' => 'ig_overlay_inout', 'operator' => '===', 'value' => 'overlay-out'],
									['name' => 'ig_image_link', 'operator' => '!==', 'value' => 'yes'],
								]
							],
							[
								'terms' => [
									['name' => 'ig_layout_type', 'operator' => 'in', 'value' => ['masonry', 'fitRows']],
									['name' => 'ig_overlay_inout', 'operator' => '===', 'value' => 'overlay-in'],
								]
							],
							[
								'terms' => [
									['name' => 'ig_layout_type', 'operator' => 'in', 'value' => ['justify']],
								]
							],
						]
					]
				]
			);
			
			$this->add_control('ig_overlay_inout',
				[
					'label'			=> esc_html__("Disposition du contenu", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'overlay-in',
					'options'       => [
                        'overlay-in'    => esc_html__("Superposer", 'eac-components'),
                        'overlay-out'   => esc_html__("Carte ", 'eac-components'),
                    ],
					'condition' => ['ig_layout_type!' => 'justify'],
					'separator' => 'before',
				]
			);
			
			/**
			 * @since 1.9.7	Direction de l'overlay
			 */
			$this->add_control('ig_overlay_direction',
				[
					'label' => esc_html__("Direction de l'overlay", 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'bottom' => [
							'title' => esc_html__('Haut', 'eac-components'),
							'icon' => 'eicon-v-align-top',
						],
						'left' => [
							'title' => esc_html__('Gauche', 'eac-components'),
							'icon' => 'eicon-h-align-left',
						],
						'right' => [
							'title' => esc_html__('Droite', 'eac-components'),
							'icon' => 'eicon-h-align-right',
						],
						'top' => [
							'title' => esc_html__('Bas', 'eac-components'),
							'icon' => 'eicon-v-align-bottom',
						],
					],
					'default'		=> 'top',
					'prefix_class' => 'overlay-',
					'conditions' => [
						'relation' => 'or',
						'terms' => [
							[
								'terms' => [
									['name' => 'ig_layout_type', 'operator' => '===', 'value' => 'justify'],
								]
							],
							[
								'terms' => [
									['name' => 'ig_overlay_inout', 'operator' => '===', 'value' => 'overlay-in'],
								]
							],
						]
					],
					'separator' => 'before',
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ig_image_settings',
			[
				'label' => esc_html__('Image', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			/** @since 1.8.7 Suppression du mode responsive */
			$this->add_control('ig_image_size',
				[
					'label'   => esc_html__('Dimension des images', 'eac-components'),
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
			
			/**
			 * Layout type justify. Gère la hauteur des images
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('ig_justify_height',
				[
					'label' => esc_html__("Hauteur de l'image", 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 300, 'unit' => 'px'],
					'range' => ['px' => ['min' => 100, 'max' => 500, 'step' => 10]],
					'condition' => ['ig_layout_type' => 'justify'],
				]
			);
			
			/** @since 1.6.0 Active le ratio image */
			$this->add_control('ig_enable_image_ratio',
				[
					'label' => esc_html__("Activer le ratio image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ig_layout_type' => 'fitRows'],
					'separator' => 'before',
				]
			);
			
			/**
			 * @since 1.6.0 Le ratio appliqué à l'image
			 * @since 1.8.7 Préparation pour les breakpoints
			 */
			$this->add_responsive_control('ig_image_ratio',
				[
					'label' => esc_html__('Ratio', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 1, 'unit' => '%'],
					'range' => ['%' => ['min' => 0.1, 'max' => 2.0, 'step' => 0.1]],
					'selectors' => ['{{WRAPPER}} .image-galerie.image-galerie__ratio .image-galerie__image' => 'padding-bottom:calc({{SIZE}} * 100%);'],
					'render_type' => 'template',
					'condition' => ['ig_layout_type' => 'fitRows', 'ig_enable_image_ratio' => 'yes'],
				]
			);
			
			/**
			 * @since 1.7.2 Positionnement vertical de l'image
			 * @since 1.8.7 Préparation pour les breakpoints
			 */
			$this->add_responsive_control('ig_image_ratio_position_y',
				[
					'label' => esc_html__('Position verticale', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 50, 'unit' => '%'],
					'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .image-galerie.image-galerie__ratio .image-galerie__image .image-galerie__image-instance' => 'object-position: 50% {{SIZE}}%;'],
					'condition' => ['ig_layout_type' => 'fitRows', 'ig_enable_image_ratio' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ig_title_settings',
			[
				'label' => esc_html__('Titre', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => ['ig_content_title' => 'yes'],
			]
		);
			
			$this->add_control('ig_title_tag',
				[
					'label'			=> esc_html__('Étiquette du titre', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h2',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'   => 'div',
						'p'		=> 'p',
                    ],
					'separator' => 'before',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ig_texte_settings',
			[
               'label' => esc_html__("Description", 'eac-components'),
               'tab' => Controls_Manager::TAB_CONTENT,
			   'condition' => ['ig_content_description' => 'yes'],
			]
		);
			
			/** @since 1.9.7 */
			$this->add_responsive_control('ig_texte_width',
				[
					'label' => esc_html__('Largeur', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['%'],
					'default' => ['size' => 100, 'unit' => '%'],
					'range' => ['%' => ['min' => 50, 'max' => 100, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__description' => 'width: {{SIZE}}%;'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('ig_section_general_style',
			[
				'label'      => esc_html__('Général', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
			
			/** @since 1.8.2 */
			$this->add_control('ig_img_style',
				[
					'label'			=> esc_html__("Style", 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'style-1',
					'options'       => [
						'style-0' => esc_html__('Défaut', 'eac-components'),
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
					'prefix_class' => 'image-galerie_wrapper-',
				]
			);
			
			/**
			 * Layout type masonry & grid
			 * @since 1.8.7 Application des breakpoints
			 */
			$this->add_responsive_control('ig_items_margin',
				[
					'label' => esc_html__('Marge entre les images', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 5, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
					'selectors' => [
						'{{WRAPPER}} .image-galerie__inner-wrapper' => 'margin: {{SIZE}}{{UNIT}};',
						'{{WRAPPER}} .swiper-container .swiper-slide .image-galerie__inner-wrapper' => 'height: calc(100% - (2 * {{SIZE}}{{UNIT}}));',
					],
					'condition' => ['ig_layout_type!' => 'justify'],
				]
			);
			
			$this->add_control('ig_container_style_bgcolor',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .swiper-container .swiper-slide, {{WRAPPER}} .image-galerie' => 'background-color: {{VALUE}};'],
				]
			);
			
			/** Articles */
			$this->add_control('ig_items_style',
				[
					'label'			=> esc_html__('Articles', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['ig_overlay_inout' => 'overlay-out'],
				]
			);
			
			$this->add_control('ig_items_bg_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .image-galerie__inner-wrapper' => 'background-color: {{VALUE}};'],
					'condition' => ['ig_overlay_inout' => 'overlay-out'],
				]
			);
			
			/** @since 1.8.4 Modification du style du filtre */
			$this->add_control('ig_filter_style',
				[
					'label'			=> esc_html__('Filtre', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['ig_layout_type!' => ['justify', 'slider'], 'ig_content_filter_display' => 'yes'],
				]
			);
			
			$this->add_control('ig_filter_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [
						'{{WRAPPER}} .ig-filters__wrapper .ig-filters__item, {{WRAPPER}} .ig-filters__wrapper .ig-filters__item a' => 'color: {{VALUE}};',
					],
					'condition' => ['ig_layout_type!' => ['justify', 'slider'], 'ig_content_filter_display' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_filter_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .ig-filters__wrapper .ig-filters__item, {{WRAPPER}} .ig-filters__wrapper .ig-filters__item a',
					'condition' => ['ig_layout_type!' => ['justify', 'slider'], 'ig_content_filter_display' => 'yes'],
				]
			);
			
			/** Titre */
			$this->add_control('ig_titre_section_style',
				[
					'label'			=> esc_html__('Titre', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['ig_content_title' => 'yes'],
				]
			);
			
			/** @since 1.6.0 Applique la couleur à l'icone de la visionneuse */
			$this->add_control('ig_titre_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'default' => '#919CA7',
					'selectors' => [
						'{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre-wrapper,
						{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre' => 'color: {{VALUE}};'
					],
					'condition' => ['ig_content_title' => 'yes'],
				]
			);
			
			/**
			 * @since 1.6.0 Applique la fonte à l'icone de la visionneuse
			 * @since 1.6.7 Suppression de la fonte de l'icone de la visionneuse
			 */
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_titre_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre-wrapper,
									{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__titre',
					'condition' => ['ig_content_title' => 'yes'],
				]
			);
			
			/** Image */
			$this->add_control('ig_image_section_style',
				[
					'label'			=> esc_html__('Image', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
				]
			);
			
			$this->add_control('ig_image_border_radius',
				[
					'label' => esc_html__('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .image-galerie__image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'ig_image_border',
					'selector' => '{{WRAPPER}} .image-galerie__image img',
				]
			);
			
			$this->add_control('ig_texte_section_style',
				[
					'label'			=> esc_html__('Description', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
					'separator' => 'before',
					'condition' => ['ig_content_description' => 'yes'],
				]
			);
			
			$this->add_control('ig_texte_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__description' => 'color: {{VALUE}};'],
					'condition' => ['ig_content_description' => 'yes'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_texte_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .image-galerie__item .image-galerie__content .image-galerie__overlay .image-galerie__description',
					'condition' => ['ig_content_description' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		/**
		 * @since 1.9.7 Ajout de la section slider
		 * @since 1.9.8	Les styles du slider avec le trait
		 */
		$this->start_controls_section('ig_slider_section_style',
			[
				'label' => esc_html__('Contrôles du slider', 'eac-components'),
				'tab' => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'terms' => [
								['name' => 'ig_layout_type', 'operator' => '===', 'value' => 'slider'],
								['name' => 'slider_navigation', 'operator' => '===', 'value' => 'yes']
							]
						],
						[
							'terms' => [
								['name' => 'ig_layout_type', 'operator' => '===', 'value' => 'slider'],
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
		
		/** @since 1.9.7 Ajout de la section bouton lien du slider */
		$this->start_controls_section('ig_button_link_style',
			[
               'label' => esc_html__('Bouton lien', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ig_image_link!' => 'yes'],
			]
		);
			
			$this->add_control('ig_button_link_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .image-galerie__button-link' => 'color: {{VALUE}}',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_button_link_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .image-galerie__button-link',
				]
			);
			
			$this->add_control('ig_button_link_background',
				[
					'label'         => esc_html__('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .image-galerie__button-link'  => 'background-color: {{VALUE}};',],
				]
			);
			
			$this->add_responsive_control('ig_button_link_padding',
				[
					'label' => esc_html__('Marges internes', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .image-galerie__button-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'ig_button_link_shadow',
    				'label' => esc_html__('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .image-galerie__button-link',
    			]
    		);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'ig_button_link_border',
					'selector' => '{{WRAPPER}} .image-galerie__button-link',
				]
			);
			
			$this->add_control('ig_button_link_radius',
				[
					'label' => esc_html__('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'selectors' => [
						'{{WRAPPER}} .image-galerie__button-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
		$this->end_controls_section();
		
		/** @since 1.9.7 Ajout de la section bouton Fancybox du slider */
		$this->start_controls_section('ig_button_lightbox_style',
			[
               'label' => esc_html__('Bouton visionneuse', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'conditions' => [
					'relation' => 'or',
					'terms' => [
						[
							'terms' => [
								['name' => 'ig_layout_type', 'operator' => '!==', 'value' => 'justify'],
								['name' => 'ig_overlay_inout', 'operator' => '===', 'value' => 'overlay-in'],
								['name' => 'ig_image_lightbox', 'operator' => '===', 'value' => 'yes'],
							]
						],
						[
							'terms' => [
								['name' => 'ig_layout_type', 'operator' => '===', 'value' => 'justify'],
								['name' => 'ig_image_lightbox', 'operator' => '===', 'value' => 'yes'],
							]
						],
					]
				]
			]
		);
			
			$this->add_control('ig_button_lightbox_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .image-galerie__button-lightbox' => 'color: {{VALUE}}',],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'ig_button_lightbox_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .image-galerie__button-lightbox',
				]
			);
			
			$this->add_control('ig_button_lightbox_background',
				[
					'label'         => esc_html__('Couleur du fond', 'eac-components'),
					'type'          => Controls_Manager::COLOR,
					'selectors'     => ['{{WRAPPER}} .image-galerie__button-lightbox'  => 'background-color: {{VALUE}};',],
				]
			);
			
			$this->add_responsive_control('ig_button_lightbox_padding',
				[
					'label' => esc_html__('Marges internes', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .image-galerie__button-lightbox' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator' => 'before',
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'ig_button_lightbox_shadow',
    				'label' => esc_html__('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .image-galerie__button-lightbox',
    			]
    		);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'ig_button_lightbox_border',
					'selector' => '{{WRAPPER}} .image-galerie__button-lightbox',
				]
			);
			
			$this->add_control('ig_button_lightbox_radius',
				[
					'label' => esc_html__('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'selectors' => [
						'{{WRAPPER}} .image-galerie__button-lightbox' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$settings = $this->get_settings_for_display();
		if(! $settings['ig_image_list']) {
			return;
		}
		
		$id = "image_galerie_" . $this->get_id();
		$slider_id = "slider_image_galerie_" . $this->get_id();
		$has_swiper = $settings['ig_layout_type'] === 'slider' ? true : false;
		$has_navigation = $has_swiper && $settings['slider_navigation'] === 'yes' ? true : false;
		$has_pagination = $has_swiper && $settings['slider_pagination'] === 'yes' ? true : false;
		$has_scrollbar = $has_swiper && $settings['slider_scrollbar'] === 'yes' ? true : false;
		$layout_mode = in_array($settings['ig_layout_type'], ['masonry', 'fitRows', 'justify', 'slider']) ? $settings['ig_layout_type'] : 'fitRows';
		$ratio = $settings['ig_enable_image_ratio'] === 'yes' ? ' image-galerie__ratio' : '';
		
		if(!$has_swiper) {
			$class = sprintf('image-galerie %s layout-type-%s', $ratio, $layout_mode);
		} else {
			$class = sprintf('image-galerie swiper-wrapper');
		}
		
		$this->add_render_attribute('galerie__instance', 'class', $class);
		$this->add_render_attribute('galerie__instance', 'id', $id);
		$this->add_render_attribute('galerie__instance', 'data-settings', $this->get_settings_json($id));
		
		if($has_swiper) { ?>
			<div id="<?php echo $slider_id; ?>" class="eac-image-galerie swiper-container">
		<?php } else { ?>
			<div class="eac-image-galerie">
		<?php }
				if(! $has_swiper) { echo $this->render_filters(); } ?>
				<div <?php echo $this->get_render_attribute_string('galerie__instance'); ?>>
					<?php if(! $has_swiper) { ?>
						<div class="image-galerie__item-sizer"></div>
					<?php }
					$this->render_galerie(); ?>
				</div>
				<?php if($has_navigation) { ?>
					<div class="swiper-button-next"></div>
					<div class="swiper-button-prev"></div>
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
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		/** Variable du rendu final */
		$html = '';
		
		/** ID de l'article */
		$unique_id = uniqid();
		
		/** Le swiper est actif */
		$has_swiper = $settings['ig_layout_type'] === 'slider' ? true : false;
		
		/** Format du titre */
		$title_tag = $settings['ig_title_tag'];
		
		/** Visionneuse active */
		$has_image_lightbox = $settings['ig_image_lightbox'] === 'yes' ? true : false;
		
		/** Lien sur l'image */
		$has_image_link = !$has_image_lightbox && $settings['ig_image_link'] === 'yes' ? true : false;
		
		/** Le titre */
		$has_title = $settings['ig_content_title'] === 'yes' ? true : false;
		
		/** La description */
		$has_description = $settings['ig_content_description'] === 'yes' ? true : false;
		
		/**
		 * @since 1.9.7 Test sur le Swiper actif
		 * Filtres activés
		 */
		$has_filter = !$has_swiper && $settings['ig_content_filter_display'] === 'yes' ? true : false;
		
		/** La classe du contenu de l'item, image+titre+texte */
		$this->add_render_attribute('galerie__inner', 'class', 'image-galerie__inner-wrapper');
		
		/** Overlay layout == justify, overlay interne par défaut */
		if(in_array($settings['ig_layout_type'], ['justify'])) {
			$overlay = 'overlay-in';
		} else if(!isset($settings['ig_overlay_inout'])) {
			$overlay = '';
		} else {
			$overlay = $settings['ig_overlay_inout'];
		}
		
		/** La classe du titre/texte */
		$this->add_render_attribute('galerie__content', 'class', 'image-galerie__content ' . $overlay);
		
		/** Boucle sur tous les items */
		foreach($settings['ig_image_list'] as $item) {
			
			/** Il y a une image */
			if(!empty($item['ig_item_image']['url'])) {
				
				/**
				 * @since 1.6.8 Filtres activés
				 * @since 1.7.0 Check Custom Fields values Format = key::value
				 */
				if($has_filter && !empty($item['ig_item_filter'])) {
					$sanized = array();
					$filters = explode(',', $item['ig_item_filter']);
					foreach($filters as $filter) {
						if(strpos($filter, '::') != false) {
							$filter = explode('::', $filter)[1];
						}
						$sanized[] = sanitize_title(mb_strtolower($filter, 'UTF-8'));
					}
					/** La classe de l'item + filtres */
					$this->add_render_attribute('galerie__item', 'class', 'image-galerie__item ' . implode(' ', $sanized));
				} else {
					/**
					 * @since 1.9.7	Ajout de la classe 'swiper-slide' pour le slider actif
					 * La classe de l'item
					 */
					$this->add_render_attribute('galerie__item', 'class', $has_swiper ? 'image-galerie__item swiper-slide' : 'image-galerie__item');
				}
				
				/** Une URL */
				$link_url = !empty($item['ig_item_url']['url']) && $item['ig_item_url']['url'] !== '#' ? esc_url($item['ig_item_url']['url']) : false;
				
				/**
				 * Formate les paramètres de l'URL
				 * @since 1.9.2 Ajout des attributs 'noopener noreferrer'
				 */
				if($link_url) {
					$this->add_render_attribute('ig-link-to', 'href', $link_url);
					
					if($item['ig_item_url']['is_external']) {
						$this->add_render_attribute('ig-link-to', 'target', '_blank');
						$this->add_render_attribute('ig-link-to', 'rel', 'noopener noreferrer');
					}
					if($item['ig_item_url']['nofollow']) {
						$this->add_render_attribute('ig-link-to', 'rel', 'nofollow');
					}
				}
				
				/** Le label du bouton */
				$button_label = $link_url ? $item['ig_item_title_button'] : '';
				
				/** Le titre de l'item */
				$item_title = sanitize_text_field($item['ig_item_title']);
				
				/** Le titre */
				$title_with_tag = '<' . $title_tag . ' class="image-galerie__titre">' . $item_title . '</' . $title_tag . '>';
				
				/** Formate le titre avec ou sans icone */
				if(!$link_url) {
					$title = '<span class="image-galerie__titre-wrapper">' . $title_with_tag . '</span>';
				} else {
					$title = '<a ' . $this->get_render_attribute_string('ig-link-to') . '><span class="image-galerie__titre-wrapper">' . $title_with_tag . '</span></a>';
				}
				
				/**
				 * @since 1.6.5 Affecte le titre à l'attribut ALT des images externes si le control 'ig_item_alt' n'est pas valorisé
				 */
				$image_alt = isset($item['ig_item_alt']) && !empty($item['ig_item_alt']) ? sanitize_text_field($item['ig_item_alt']) : $item_title;
				
				/**
				 *
				 * @since 1.4.1 Ajout du paramètre 'ver' à l'image avec un identifiant unique
				 * pour forcer le chargement de l'image du serveur et non du cache pour les MEDIAS
				 *
				 * @since 1.6.0 Gestion des images externes
				 * La balise dynamique 'External image' ne renvoie pas l'ID de l'image
				 *
				 * @since 1.9.8	Affiche l'image par défaut d'Elementor s'il n'y a pas d'image
				 */
				// Récupère les propriétés de l'image avec la version pour les MEDIAS
				if(!empty($item['ig_item_image']['id'])) {
					$image_data = wp_get_attachment_image_src($item['ig_item_image']['id'], $settings['ig_image_size']);
					if(!$image_data) {
						$image_data = array();
						$image_data[0] = plugins_url() . "/elementor/assets/images/placeholder.png";
					}
					$image_url = sprintf("%s?ver=%s", esc_url($image_data[0]), $unique_id);
					$image_alt = Control_Media::get_image_alt($item['ig_item_image']); // 'get_image_alt' renvoie toujours une chaine par défaut
				} else { // Image avec Url externe sans paramètre version
					$image_url = esc_url($item['ig_item_image']['url']);
				}
				
				/**
				 * La visionneuse est activée et pas d'overlay-in
				 * Unique ID pour 'data-fancybox' permet de grouper les images sous le même ID
				 */
				if(!$has_swiper && $has_image_lightbox && $overlay === 'overlay-out') {
					$image = sprintf('<a href="%s" data-elementor-open-lightbox="no" data-fancybox="%s" data-caption="%s">
					<img class="image-galerie__image-instance" src="%s" alt="%s" /></a>', $image_url, $unique_id, $item_title, $image_url, $image_alt);
				} else if($has_image_link && $link_url && $overlay === 'overlay-out') {
					$image = sprintf('<a %s><img class="image-galerie__image-instance" src="%s" alt="%s" /></a>', $this->get_render_attribute_string('ig-link-to'), $image_url, $image_alt);
				} else {
					$image = sprintf('<img class="image-galerie__image-instance" src="%s" alt="%s" />', $image_url, $image_alt);
				}
		
				// On construit le DOM
				$html .= '<div '. $this->get_render_attribute_string('galerie__item') . '>';
					$html .= '<div ' . $this->get_render_attribute_string('galerie__inner') . '>';
						$html .= '<div class="image-galerie__image">';
							$html .= $image;
						$html .= '</div>';
						
						if($has_title || $has_description || ($link_url && !$has_image_link) || ($has_image_lightbox && $overlay === 'overlay-in')) {
							$html .= '<div ' . $this->get_render_attribute_string('galerie__content') . '>';
							
								$html .= '<div class="image-galerie__overlay">';
									if($has_title) { $html .= $title; }
									
									if($has_description) { $html .= '<span class="image-galerie__description">' . sanitize_textarea_field($item['ig_item_desc']) . '</span>'; }
									
									if(($link_url && !$has_image_link) || ($has_image_lightbox && $overlay === 'overlay-in')) {
										$html .= '<div class="image-galerie__buttons-wrapper">';
											/** Un lien on affiche le bouton */
											if($link_url && !$has_image_link) {
												$html .= '<a ' . $this->get_render_attribute_string('ig-link-to') . '>';
												$html .= '<button class="image-galerie__button-link swiper-no-swiping" type="button">' . $button_label . '</button>';
												$html .= '</a>';
											}
											
											/** La visionneuse est activée et l'overlay est sur l'image */
											if($has_image_lightbox && $overlay === 'overlay-in') {
												$html .= '<button class="image-galerie__button-lightbox swiper-no-swiping" data-src="' . $image_url . '" data-caption="' . $image_alt . '" type="button"><i class="far fa-image" aria-hidden="true"></i></button>';
											}
										$html .= '</div>';	// button-wrapper
									}
								$html .= '</div>';		// galerie__overlay
								
							$html .= '</div>';	// galerie__content
						}
					
					$html .= '</div>';		// galerie__inner
				$html .= '</div>';			// galerie__item
			}
			
			// Vide les attributs html du lien
			$this->set_render_attribute('ig-link-to', 'href', null);
			$this->set_render_attribute('ig-link-to', 'target', null);
			$this->set_render_attribute('ig-link-to', 'rel', null);
			// Vide la class du wrapper
			$this->set_render_attribute('galerie__item', 'class', null);
		}
	
	// Affiche le rendu		
	echo $html;
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
	 * @since 0.0.9
	 * @since 1.5.3	Modifie l'affectation de 'layoutType'
	 * 				Suppression de 'order' du control 'ig_image_order'
	 * @since 1.6.7	Check 'justify' layout type for the grid parameters
	 *				Le mode Metro est activé
	 * @since 1.9.7	Ajout des paramètres pour le slider 'data_sw_'
	 */
	protected function get_settings_json($id) {
		$module_settings = $this->get_settings_for_display();
		$layout_mode = in_array($module_settings['ig_layout_type'], ['masonry', 'fitRows', 'justify', 'slider']) ? $module_settings['ig_layout_type'] : 'fitRows';
		$grid_height = !empty($module_settings['ig_justify_height']['size']) ? $module_settings['ig_justify_height']['size'] : 300; // justify Desktop
		
		if(in_array($module_settings['ig_layout_type'], ['justify'])) {
			$overlay = 'overlay-in';
		} else if(!isset($module_settings['ig_overlay_inout'])) {
			$overlay = '';
		} else {
			$overlay = $module_settings['ig_overlay_inout'];
		}
		
		$effect = $module_settings['slider_effect'];
		if(in_array($effect, ['fade', 'creative'])) { $nb_images = 1; }
		// Effet slide pour nombre d'images = 0 = 'auto'
		else if(empty($module_settings['slider_images_number']) || $module_settings['slider_images_number'] == 0) { $nb_images = "auto"; $effect = 'slide'; }
		else $nb_images = $module_settings['slider_images_number'];
		
		$settings = array(
			"data_id"		=> $id,
			"data_layout"    => $layout_mode,
			"gridHeight" 	=> $grid_height,
			"gridHeightT" 	=> !empty($module_settings['ig_justify_height_tablet']['size']) ? $module_settings['ig_justify_height_tablet']['size'] : $grid_height, // justify Tab
			"gridHeightM" 	=> !empty($module_settings['ig_justify_height_mobile']['size']) ? $module_settings['ig_justify_height_mobile']['size'] : $grid_height, // justify Mob
			"data_overlay" 	=> $overlay,
			"data_fancybox" => $module_settings['ig_image_lightbox'] === 'yes' ? true : false,
			"data_metro"	=> $module_settings['ig_layout_type_metro'] === 'yes' ? true : false,
			"data_filtre"	=> $module_settings['ig_content_filter_display'] === 'yes' ? true : false,
			"data_sw_swiper"	=> $module_settings['ig_layout_type'] === 'slider' ? true : false,
			"data_sw_autoplay"	=> $module_settings['slider_autoplay'] === 'yes' ? true : false,
			"data_sw_loop"	=> $module_settings['slider_loop'] === 'yes' ? true : false,
			"data_sw_delay"	=> $module_settings['slider_delay'],
			"data_sw_imgs"	=> $nb_images,
			"data_sw_dir"	=> 'horizontal',
			"data_sw_rtl"	=> $module_settings['slider_rtl'] === 'right' ? true : false,
			"data_sw_effect"	=> $effect,
			"data_sw_free"	=> true,
			"data_sw_pagination_click"	=> $module_settings['slider_pagination'] === 'yes' && $module_settings['slider_pagination_click'] === 'yes' ? true : false,
		);

		$settings = json_encode($settings);
		return $settings;
	}
	
	/**
	 * render_filters
	 * 
	 * Description: Retourne les filtres formaté en HTML en ligne
	 * ou sous forme de liste pour les media query
	 * 
	 * @since 1.6.8
	 * @since 1.7.0 Check Custom Fields values Format = key::value
	 */
	protected function render_filters() {
		$settings = $this->get_settings_for_display();
		// Filtres activés
		$has_filter = $settings['ig_content_filter_display'] === 'yes' ? true : false;
		
		// Filtre activé
		if($has_filter) {
			$filtersName = array();
			$htmFiltres = '';
			
			foreach($settings['ig_image_list'] as $item) {
				if(!empty($item['ig_item_image']['url']) && !empty($item['ig_item_filter'])) {
					$currentFilters = explode(',', $item['ig_item_filter']);
					foreach($currentFilters as $currentFilter) {
						/** @since 1.7.0 */
						if(strpos($currentFilter, '::') != false) {
							$currentFilter = explode('::', $currentFilter)[1];
						}
						$filtersName[sanitize_title(mb_strtolower($currentFilter, 'UTF-8'))] = sanitize_title(mb_strtolower($currentFilter, 'UTF-8'));
					}
				}
			}
			
			// Des filtres
			if(!empty($filtersName)) {
				ksort($filtersName, SORT_FLAG_CASE|SORT_NATURAL);
				
				$htmFiltres .= "<div id='ig-filters__wrapper' class='ig-filters__wrapper'>";
				$htmFiltres .= "<div class='ig-filters__item ig-active'><a href='#' data-filter='*'>" . esc_html__('Tous', 'eac-components') . "</a></div>";
					foreach($filtersName as $filterName) {
						$htmFiltres .= "<div class='ig-filters__item'><a href='#' data-filter='." . sanitize_title($filterName) . "'>" . ucfirst($filterName) . "</a></div>";
					}
				$htmFiltres .= "</div>";
				
				// Filtre dans une liste pour les media query
				$htmFiltres .= "<div id='ig-filters__wrapper-select' class='ig-filters__wrapper-select'>";
				$htmFiltres .= "<select class='ig-filter__select'>";
					$htmFiltres .= "<option value='*' selected>" . esc_html__('Tous', 'eac-components') . "</option>";
					foreach($filtersName as $filterName) {
						$htmFiltres .= "<option value='." . sanitize_title($filterName) . "'>" . ucfirst($filterName) . "</option>";
					}
				$htmFiltres .= "</select>";
				$htmFiltres .= "</div>";
				
				return $htmFiltres;
			}
		}
		return;
	}
	
	protected function content_template() {}
}