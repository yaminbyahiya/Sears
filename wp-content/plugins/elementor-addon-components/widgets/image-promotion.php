<?php

/*=========================================================================================================
* Class: Image_Promotion_Widget
* Name: Promotion de produit
* Slug: eac-addon-image-promo
*
* Description: Image_Promotion_Widget affiche et met en forme
* la promotion d'un produit
*
* @since 0.0.9
* @since 1.7.80	Fix: Le repeater n'est pas correctement configuré
*				Migration du contol 'ICON' par le nouveau control 'ICONS'
* @since 1.8.7	Support des custom breakpoints
* @since 1.9.0	Intégration des scripts et des styles dans le constructeur de la class
* @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
* @since 1.9.8	Implémentation des balises dynamiques
*==========================================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Repeater;
use Elementor\Utils;

if (! defined('ABSPATH')) exit; // Exit if accessed directly
 
class Image_Promotion_Widget extends Widget_Base {
	
	/**
	 * Constructeur de la class Image_Promotion_Widget
	 * 
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_style('eac-image-promotion', EAC_Plugin::instance()->get_register_style_url('image-promotion'), array('eac'), '1.0.0');
		wp_register_style('eac-image-ribbon', EAC_Plugin::instance()->get_register_style_url('image-ribbon'), array('eac', 'eac-image-promotion'), '1.0.0');
		
		wp_register_style('eac-product', EAC_Plugin::instance()->get_register_style_url('product'), array('eac', 'eac-image-promotion'), '1.9.8');
		
		/** Supprime les zéros à la fin des prix */
		//add_filter('woocommerce_price_trim_zeros', '__return_true');
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'image-promotion';
	
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
		return ['eac-elements'];
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
	
	/** 
	 * Load dependent styles
	 * Les styles sont chargés dans le footer
	 * 
	 * @access public
	 *
	 * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-image-promotion', 'eac-image-ribbon'];
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('ip_image_settings',
			[
				'label'     => esc_html__('Image', 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			// Ajout de l'image
			$this->add_control('ip_image_switcher',
				[
					'label' => esc_html__("Ajouter une image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('ip_icon_switcher',
				[
					'label' => esc_html__("Ajouter un pictogramme", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ip_image_switcher!' => 'yes'],
				]
			);
			
			/*$this->add_control('ip_icon_content',
				[
					'label' => esc_html__("Choix de l'icône", 'eac-components'),
					'type' => Controls_Manager::ICON,
					'default' => '',
					'condition' => ['ip_icon_switcher' => 'yes', 'ip_image_switcher' => ''],
				]
			);*/
			
			/** 1.7.80 Utilisation du control ICONS */
			$this->add_control('ip_icon_content_new',
				[
					'label' => esc_html__("Choix du pictogramme", 'eac-components'),
					'type' => Controls_Manager::ICONS,
					'fa4compatibility' => 'ip_icon_content',
					'default' => [
						'value' => 'fas fa-plus-square',
						'library' => 'solid',
					],
					'condition' => ['ip_icon_switcher' => 'yes', 'ip_image_switcher' => ''],
				]
			);
			
			$this->add_control('ip_image_content',
				[
					'label' => esc_html__("Choix de l'image", 'eac-components'),
					'type' => Controls_Manager::MEDIA,
					'dynamic' => [
						'active' => true,
					],
					'default' => [
						'url'	=> Utils::get_placeholder_image_src(),
					],
					'condition' => ['ip_image_switcher' => 'yes'],
				]
			);
			
			// @since 1.8.7 Ajout de la taille de l'image
			$this->add_group_control(
			Group_Control_Image_Size::get_type(),
				[
					'name' => 'ip_image', // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `ip_image_size` and `ip_image_custom_dimension`.
					'default' => 'medium',
					'exclude' => ['custom'],
					'condition' => ['ip_image_switcher' => 'yes'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ip_image_height',
				[
					'label' => esc_html__('Hauteur', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 300, 'unit' => 'px'],
					'tablet_default' => ['size' => 230, 'unit' => 'px'],
					'mobile_default' => ['size' => 250, 'unit' => 'px'],
					'tablet_extra_default' => ['size' => 250, 'unit' => 'px'],
					'mobile_extra_default' => ['size' => 180, 'unit' => 'px'],
					'range' => ['px' => ['min' => 50, 'max' => 1000, 'step' => 10]],
					'selectors' => ['{{WRAPPER}} .ip-image img' => 'height: {{SIZE}}{{UNIT}};'],
					'condition' => ['ip_image_switcher' => 'yes'],
				]
			);
			
			$this->add_control('ip_image_align',
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
					'selectors' => [
						'{{WRAPPER}} .ip-image' => 'text-align: {{VALUE}};',
					],
					'default' => 'center',
					'condition' => ['ip_image_switcher' => 'yes'],
				]
			);
			
			$this->add_control('ip_lightbox',
				[
					'label' => esc_html__("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['ip_image_switcher' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_titre_content',
			[
  				'label' => esc_html__("Titre", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
        
			$this->add_control('ip_title',
				[
				'label'			=> esc_html__('Titre', 'eac-components'),
				'placeholder'	=> esc_html__('Votre titre', 'eac-components'),
				'default'	=> esc_html__('Votre titre', 'eac-components'),
				'type'			=> Controls_Manager::TEXT,
				'dynamic' => ['active' => true],
				'label_block'	=> true,
				]
			);
        
			$this->add_control('ip_title_tag',
				[
					'label'			=> esc_html__('Étiquette', 'eac-components'),
					'type'			=> Controls_Manager::SELECT,
					'default'		=> 'h2',
					'options'       => [
						'h1'    => 'H1',
                        'h2'    => 'H2',
                        'h3'    => 'H3',
                        'h4'    => 'H4',
                        'h5'    => 'H5',
                        'h6'    => 'H6',
						'div'   => 'DIV',
						'p'    => 'P',
                    ],
					'condition' => ['ip_title!' => ''],
				]
			);
			
			$this->add_control('ip_title_url',
				[
					'label'       => esc_html__('Lien sur le titre', 'eac-components'),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'http://your-link.com',
					'dynamic' => ['active' => true],
					'default' => [
						'url' => '#',
						'is_external' => false,
						'nofollow' => false,
					],
					'condition' => ['ip_title!' => ''],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_carac_content',
			[
  				'label' => esc_html__("Caractéristiques", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('ip_carac_hint',
				[
					'label'			=> esc_html__('Éléments', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
				]
			);
			
			/** @since 1.7.80 Reconfiguration du repeater */
			$repeater = new Repeater();
			
			$repeater->add_control('ip_carac_item',
				[
					'label' => esc_html__('Texte', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'placeholder' => esc_html__("Caractéristiques", 'eac-components'),
					'dynamic' => ['active' => true],
					'label_block'	=> true,
				]
			);
			
			$repeater->add_control('ip_carac_inclus',
				[
					'label' => esc_html__("Élément inclus", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('ip_carac_list',
				[
					'label' => esc_html__('Liste des caractéristiques', 'eac-components'),
					'type' => Controls_Manager::REPEATER,
					'fields' => $repeater->get_controls(),
					'default' => [
						[
							'ip_carac_item'	=> esc_html__('Élément liste #1', 'eac-components'),
						],
						[
							'ip_carac_item'	=> esc_html__('Élément liste #2', 'eac-components'),
						],
						[
							'ip_carac_item'	=> esc_html__('Élément liste #3', 'eac-components'),
						],
						[
							'ip_carac_item'	=> esc_html__('Élément liste #4', 'eac-components'),
						]
					],
					'title_field' => '{{{ ip_carac_item }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_price_content',
			[
  				'label' => esc_html__("Prix du produit", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);
			
			/*$this->add_control('ip_price_hint',
				[
					'label'			=> esc_html__('Prix de vente', 'eac-components'),
					'type'			=> Controls_Manager::HEADING,
				]
			);*/
			
			$this->add_control('ip_price',
				[
					'label'			=> esc_html__('Prix de vente', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'placeholder' => esc_html__('Renseigner le prix...', 'eac-components'),
					'default' => esc_html__('XXX €', 'eac-components'),
					'dynamic' => ['active' => true],
					'label_block'	=> true
				]
			);
        
		$this->end_controls_section();
		
		$this->start_controls_section('ip_section_button',
			[
				'label' => esc_html__("Bouton 'En savoir plus'", 'eac-components'),
				'tab'	=> Controls_Manager::TAB_CONTENT,
			]
		);

			$this->add_control('ip_button_text',
				[
					'label' => esc_html__('Texte', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'default' => esc_html__('En savoir plus', 'eac-components'),
					'placeholder' => esc_html__('En savoir plus', 'eac-components'),
				]
			);
			
			$this->add_control('ip_link_to',
				[
					'label' => esc_html__('Type de lien', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'default' => 'none',
					'options' => [
						'none' => esc_html__('Aucun', 'eac-components'),
						'custom' => esc_html__('URL', 'eac-components'),
						'file' => esc_html__('Fichier média', 'eac-components'),
					],
				]
			);
			
			$this->add_control('ip_link_url',
				[
					'label' => esc_html__('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'dynamic' => ['active' => true],
					'placeholder' => 'http://your-link.com',
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
					'condition' => ['ip_link_to' => 'custom'],
				]
			);
			
			$this->add_control('ip_link_page',
				[
					'label' => esc_html__('Lien de page', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'default' => '',
					'options' => Eac_Tools_Util::get_pages_by_name(),
					'condition' => ['ip_link_to' => 'file'],
					
				]
			);
		
		$this->end_controls_section();
		
		$this->start_controls_section('ip_global_settings',
				[
					'label'	=> esc_html__('Ruban', 'eac-components'),
					'tab'	=> Controls_Manager::TAB_CONTENT,
				]
			);
			
			$this->add_control('ip_ribbon_switcher',
				[
					'label' => esc_html__("Ajouter un ruban", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			$this->add_control('ip_ribbon_text',
				[
					'label' => esc_html__('Texte', 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'default' => esc_html__('Ruban', 'eac-components'),
					'placeholder' => esc_html__('Ruban', 'eac-components'),
					'condition' => ['ip_ribbon_switcher' => 'yes'],
				]
			);
			
			$this->add_control('ip_ribbon_position',
				[
					'label' => esc_html__('Alignement', 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'left' => [
							'title' => esc_html__('Gauche', 'eac-components'),
							'icon' => 'eicon-h-align-left',
						],
						'right' => [
							'title' => esc_html__('Droite', 'eac-components'),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'left',
					'condition' => ['ip_ribbon_switcher' => 'yes'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ip_ribbon_margin',
				[
					'label' => esc_html__('Position (px)', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 35, 'unit' => 'px'],
					'range' => ['px' => ['min' => 35, 'max' => 65, 'step' => 5]],
					'selectors' => [
						'{{WRAPPER}} .image-ribbon-inner' => 'margin-top: {{SIZE}}{{UNIT}};
						-webkit-transform:translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);
						-ms-transform:translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);
						transform:translateY(-50%) translateX(-50%) translateX({{SIZE}}{{UNIT}}) rotate(-45deg);',
					],
					'condition' => ['ip_ribbon_switcher' => 'yes'],
				]
			);

		$this->end_controls_section();
		
		$this->start_controls_section('ip_bg_style',
           [
               'label' => esc_html__('Fond', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
           ]
		);
		
			$this->add_control('ip_bg_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'selectors' => ['{{WRAPPER}} .ip-wrapper' => 'background-color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ip_box_shadow',
					'exclude' => [
						'box_shadow_position',
					],
					'selector' => '{{WRAPPER}} .eac-image-promo',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_icone_style',
           [
               'label' => esc_html__('Pictogramme', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ip_icon_switcher' => 'yes', 'ip_image_switcher!' => 'yes'],
           ]
		);
			
			$this->add_control('ip_icone_voir',
				[
					'label' => esc_html__('Afficher', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'default' => esc_html__('Défaut', 'eac-components'),
						'stacked' => esc_html__('Empilé', 'eac-components'),
						'framed' => esc_html__('Encadré', 'eac-components'),
					],
					'default' => 'default',
					'prefix_class' => 'elementor-view-',
				]
			);

			$this->add_control('ip_icone_forme',
				[
					'label' => esc_html__('Forme', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'options' => [
						'circle' => esc_html__('Ronde', 'eac-components'),
						'square' => esc_html__('Carrée', 'eac-components'),
					],
					'default' => 'circle',
					'condition' => ['ip_icone_voir!' => 'default'],
					'prefix_class' => 'elementor-shape-',
				]
			);
			
			$this->add_control('ip_icone_align',
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
					'selectors' => ['{{WRAPPER}} .ip-icone-wrapper' => 'text-align: {{VALUE}};'],
				]
			);
			
			/** @since 1.8.7 Application des breakpoints */
			$this->add_responsive_control('ip_icone_size',
				[
					'label' => esc_html__('Dimension', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 60, 'unit' => 'px'],
					'tablet_default' => ['size' => 40, 'unit' => 'px'],
					'mobile_default' => ['size' => 50, 'unit' => 'px'],
					'tablet_extra_default' => ['size' => 40, 'unit' => 'px'],
					'mobile_extra_default' => ['size' => 50, 'unit' => 'px'],
					'range' => ['px' => ['min' => 20, 'max' => 100,	'step' => 5]],
					'selectors' => ['{{WRAPPER}} .elementor-icon' => 'font-size: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('ip_icone_couleur',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'default' => '#e2bc74',
					'selectors' => [
						'{{WRAPPER}}.elementor-view-stacked .elementor-icon' => 'background-color: {{VALUE}};',
						'{{WRAPPER}}.elementor-view-framed .elementor-icon, {{WRAPPER}}.elementor-view-default .elementor-icon' => 'color: {{VALUE}}; border-color: {{VALUE}};',
					],
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_1,
					],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_title_style',
           [
               'label' => esc_html__('Titre', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ip_title!' => ''],
           ]
		);
		
			$this->add_control('ip_titre_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'selectors' => [
						'{{WRAPPER}} .ip-title' => 'color: {{VALUE}};',
						'{{WRAPPER}} .ip-title:after' => 'border-color: {{VALUE}};'
						],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'ip_title_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .ip-title',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_carac_style',
           [
               'label' => esc_html__('Caractéristiques', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
           ]
		);
			
			$this->add_control('ip_carac_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'selectors' => ['{{WRAPPER}} .ip-description .ip-description-item > span > span,
						{{WRAPPER}} .ip-description .ip-description-item > span > span a,
						{{WRAPPER}} .woocommerce .star-rating:before,
						{{WRAPPER}} .woocommerce .star-rating span:before' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'ip_carac_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .ip-description .ip-description-item',
				]
			);
			
			/*$this->add_control('ip_carac_align',
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
					'selectors' => ['{{WRAPPER}} .ip-description .ip-description-item span > span' => 'text-align: {{VALUE}};'],
				]
			);*/
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_prix_style',
           [
               'label' => esc_html__('Prix du produit', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
           ]
		);
		
			$this->add_control('ip_prix_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'selectors' => ['{{WRAPPER}} .ip-prix' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'ip_prix_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .ip-prix',
				]
			);
			
			$this->add_control('ip_prix_bgcolor',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'selectors' => ['{{WRAPPER}} .ip-prix' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ip_button_style',
           [
               'label' => esc_html__("Bouton 'En savoir plus'", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
           ]
		);
			
			$this->add_control('ip_bouton_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'selectors' => ['{{WRAPPER}} .ip-button' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'ip_bouton_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .ip-button',
				]
			);
			
			$this->add_control('ip_bouton_bgcolor',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'selectors' => ['{{WRAPPER}} .ip-button' => 'background-color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
				[
					'name' => 'ip_bouton_shadow',
					'label' => esc_html__("Effet d'ombre", 'eac-components'),
					'exclude' => [
						'box_shadow_position',
					],
					'selector' => '{{WRAPPER}} .ip-button',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('ribbon_section_style',
           [
               'label' => esc_html__('Ruban', 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			   'condition' => ['ip_ribbon_switcher' => 'yes'],
           ]
		);
			
			$this->add_control('ip_ribbon_inner_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_3,
					],
					'default' => '#FFF',
					'selectors' => ['{{WRAPPER}} .image-ribbon-inner' => 'color: {{VALUE}};'],
				]
			);
			
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name' => 'ip_typography_ribbon_texte',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .image-ribbon-inner',
				]
			);
			
			$this->add_control('ip_ribbon_inner_bgcolor',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' 	=> Color::get_type(),
						'value' => Color::COLOR_1,
					],
					'default' => '#e2bc74',
					'selectors' => ['{{WRAPPER}} .image-ribbon-inner' => 'background-color: {{VALUE}};'],
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
		$this->add_render_attribute('ip_wrapper', 'class', 'ip-wrapper');
	?>
		<div class="eac-image-promo">
			<div <?php echo $this->get_render_attribute_string('ip_wrapper'); ?>>
				<?php $this->render_galerie(); ?>
			</div>
		</div>
	<?php
	}
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		
		$allowed_content = array(
			'strong' => array(),
			'span' => array('class' => array()),
			'span' => array('style' => array()),
			'span:before' => array('class' => array()),
			'p' => array('class' => array()),
			'i' => array('class' => array()),
			'div' => array('class' => array()),
			'div:before' => array('class' => array()),
			'a' => array('href' => array(), 'target' => array(), 'rel' => array())
		);
		
		$this->add_render_attribute('wrapper', 'class', 'ip-icone-wrapper');
		
		// Il y a un titre ?
		$iptitle = $settings['ip_title'] ? $settings['ip_title'] : false;
		$has_title_url = $iptitle && !empty($settings['ip_title_url']['url']) && $settings['ip_title_url']['url'] !== '#' ? true : false;
		
		// Étiquette du titre et affectation d'une class pour le modifier
		$title_tag = $settings['ip_title_tag'];
		$open_title = '<'. $title_tag .' class="ip-title">';
		$close_title = '</'. $title_tag .'>';
		
		// Le prix
		$prix = wp_kses_post($settings['ip_price']);
		
		// Visionneuse pour l'image ?
		$visionneuse = $settings['ip_lightbox'] === 'yes' ? true : false;
		
		// Le ribbon est affiché
		$has_ribbon = $settings['ip_ribbon_switcher'] === 'yes' ? true : false;
		$image_url = '';
		$link_icon = false;
		$link_url = '';
		
		// l'image src, sa class ainsi que les attributs ALT et TITLE
		if(! empty($settings['ip_image_content']['url'])) {
			$image_url = esc_url($settings['ip_image_content']['url']);
			$this->add_render_attribute('ip_image_content', 'src', $image_url);
			$image_alt = Control_Media::get_image_alt($settings['ip_image_content']);
			$this->add_render_attribute('ip_image_content', 'alt', $image_alt);
			$this->add_render_attribute('ip_image_content', 'title', Control_Media::get_image_title($settings['ip_image_content']));
		}
		
		/** 1.7.80 Migration du control ICONS */
		if(! empty($settings['ip_icon_content_new'])) {
			$link_icon = true;
			
			// Check if its already migrated
			$migrated = isset($settings['__fa4_migrated']['ip_icon_content_new']);
			
			// Check if its a new widget without previously selected icon using the old Icon control
			$is_new = empty($settings['ip_icon_content']);
			
			if($is_new || $migrated) {
				$this->add_render_attribute('icon', 'class', $settings['ip_icon_content_new']['value']);
				$this->add_render_attribute('icon', 'aria-hidden', 'true');
			}
		}
		
		// Ajout de la class sur la div du bouton,
		$this->add_render_attribute('wrapper-button', 'class', 'ip-button-wrapper');
		$this->add_render_attribute('button', 'class', 'ip-button elementor-button');
		$this->add_render_attribute('button', 'role', 'button');
		
		/**
		 * Il y a un lien sur le bouton
		 * @since 1.9.2 Ajout des attributs 'noopener noreferrer'
		 */
		if($settings['ip_link_to'] === 'custom') {
			$link_url = esc_url($settings['ip_link_url']['url']);
            $this->add_render_attribute('ip-link-to', 'href', $link_url);
			
            if($settings['ip_link_url']['is_external']) {
                $this->add_render_attribute('ip-link-to', 'target', '_blank');
				$this->add_render_attribute('ip-link-to', 'rel', 'noopener noreferrer');
            }

            if($settings['ip_link_url']['nofollow']) {
                $this->add_render_attribute('ip-link-to', 'rel', 'nofollow');
            }
        } else if($settings['ip_link_to'] === 'file') {
			$link_url = $settings['ip_link_page'];
            $this->add_render_attribute('ip-link-to', 'href', esc_url(get_permalink(get_page_by_title($link_url))));
		}
		
		// Visionneuse demandée sur l'image sélectionnée
		if($visionneuse && !empty($image_url)) {
			// Url de l'image et on force la visionneuse Elementor à 'no'
			$this->add_render_attribute('ip-lightbox', ['href' => $image_url, 'data-elementor-open-lightbox' => 'no']);
			$this->add_render_attribute('ip-lightbox', 'data-fancybox', 'ip-gallery');
			$this->add_render_attribute('ip-lightbox', 'data-caption', $image_alt); // Caption pour la fancybox
		}
		
		// la position du ribbon
		if($has_ribbon) {
			$this->add_render_attribute('ribbon', 'class', "image-ribbon image-ribbon-" . $settings['ip_ribbon_position']);
		}
	?>	
		<!-- Ribbon + lightbox -->
		<?php if($has_ribbon) : ?>
			<?php if($visionneuse && !empty($image_url)) : ?>
				<a <?php echo $this->get_render_attribute_string('ip-lightbox'); ?>>
			<?php endif; ?>
		
			<span <?php echo $this->get_render_attribute_string('ribbon'); ?>>
				<span class="image-ribbon-inner"><?php echo sanitize_text_field($settings['ip_ribbon_text']); ?></span>
			</span>
		
			<?php if($visionneuse && !empty($image_url)) : ?>
				</a>
			<?php endif; ?>
		<?php endif; ?>
		
		<!-- Image + lightbox -->
		<?php if(!empty($image_url)) : ?>
			<figure class="ip-image">
				<?php if($visionneuse) : ?>
					<a <?php echo $this->get_render_attribute_string('ip-lightbox'); ?>>
				<?php endif; ?>
				
				<?php echo Group_Control_Image_Size::get_attachment_image_html($settings, 'ip_image', 'ip_image_content'); ?>
				
				<?php if($visionneuse) : ?>
					</a>
				<?php endif; ?>
			</figure>
		<?php endif; ?>
		
		<!-- Affichage d'une icone -->
		<?php if($link_icon) : ?>
			<div <?php echo $this->get_render_attribute_string('wrapper'); ?>>
				<div class="ip-icone elementor-icon">
					<i <?php echo $this->get_render_attribute_string('icon'); ?>></i>
				</div>
			</div>
		<?php endif; ?>
		
		<!-- Affichage du titre -->
		<?php if($iptitle) : ?>
			<?php if($has_title_url) : ?>
				<a href="<?php echo esc_url($settings['ip_title_url']['url']); ?>">
			<?php endif; ?>
			
				<?php echo $open_title; ?><?php echo sanitize_text_field($settings['ip_title']); ?><?php echo $close_title; ?>
				
			<?php if($has_title_url) : ?>
				</a>
			<?php endif; ?>
		<?php endif; ?>
		
		<!-- Affichage des caractéristiques -->
		<?php if(count($settings['ip_carac_list'])) { ?>
            <div class="ip-description woocommerce">
                <div class="ip-description-item">
                    <?php foreach($settings['ip_carac_list'] as $item) { ?>
						<?php $icone = $item['ip_carac_inclus'] === 'yes' ? '<i class="fa fa-check fa-fw" aria-hidden="true"></i>' : '<i class="fa fa-times fa-fw" aria-hidden="true"></i>' ?>
						<span>
							<span><?php echo $icone; ?></span>
							<span><?php echo wp_kses($item['ip_carac_item'], $allowed_content); ?></span>
							<!--<span><?php //echo $item['ip_carac_item']; ?></span>-->
						</span>
                    <?php } ?>
                </div>
            </div>
		<?php } ?>
		
		<!-- Affichage du prix -->
		<?php if(! empty($prix)) : ?>
			<div class="ip-prix">
				<?php echo $prix; ?>
			</div>
		<?php endif; ?>
		
		<!-- Affichage du bouton linker -->
		<div <?php echo $this->get_render_attribute_string('wrapper-button'); ?>>
			<?php if(! empty($link_url)) : ?>
				<a  <?php echo $this->get_render_attribute_string('ip-link-to')?>>
			<?php endif; ?>
				<span <?php echo $this->get_render_attribute_string('button'); ?>>
					<i class="fa fa-arrow-right" aria-hidden="true"></i><?php echo sanitize_text_field($settings['ip_button_text']); ?>
				</span>
			<?php if(! empty($link_url)) : ?>
				</a>
			<?php endif; ?>
		</div>
	<?php
    }
	
	protected function content_template() {}
	
}