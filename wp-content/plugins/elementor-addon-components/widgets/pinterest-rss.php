<?php

/*=====================================================================================
* Class: Pinterest_pin_Widget
* Name: Lecteur RSS
* Slug: eac-addon-lecteur-rss
*
* Description: Affiche la liste des flux
* d'un user ou du board d'un user au format RSS
*
* @since 1.2.0
* @since 1.8.7	Application des breakpoints
*				Suppression de la méthode 'init_settings'
* @since 1.9.0	Intégration des scripts et des styles dans le constructeur de la class
*				Sécurité passe un nonce en paramètre au script JS
* @since 1.9.3	Envoie le nonce dans un champ 'input hidden'
*======================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Control_Media;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Repeater;
use Elementor\Core\Breakpoints\Manager as Breakpoints_manager;
use Elementor\Plugin;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Pinterest_Rss_Widget extends Widget_Base {
	
	/**
	 * Constructeur de la class Pinterest_Rss_Widget
	 * 
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.8.9
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_script('eac-pinterest-rss', EAC_Plugin::instance()->get_register_script_url('eac-pinterest-rss'), array('jquery', 'elementor-frontend'), '1.2.0', true);
		wp_register_style('eac-pinterest-rss', EAC_Plugin::instance()->get_register_style_url('pinterest-rss'), array('eac'), '1.2.0');
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'pinterest-rss';
	
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
		return ['eac-pinterest-rss'];
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
		return ['eac-pinterest-rss'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.9.0
	 * @access public
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return Eac_Config_Elements::get_widget_keywords($this->slug);
	}
	
    /*
    * Register widget controls.
    *
    * Adds different input fields to allow the user to change and customize the widget settings.
    *
    * @access protected
    */
    protected function register_controls() {
		
		$this->start_controls_section('pin_galerie_settings',
			[
				'label'     => esc_html__('Pinterest RSS', 'eac-components'),
			]
		);
			
			$repeater = new Repeater();
			
			$repeater->add_control('pin_item_title',
				[
					'label'   => esc_html__('Titre', 'eac-components'),
					'type'    => Controls_Manager::TEXT,
				]
			);
			
			$repeater->add_control('pin_item_url',
				[
					'label'       => esc_html__('URL', 'eac-components'),
					'type'        => Controls_Manager::URL,
					'placeholder' => 'https://pinterest.com',
					'default' => [
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);
			
			$repeater->add_control('pin_item_user',
				[
					'label'   => esc_html__("Utilisateur", 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'separator' => 'before',
				]
			);
			
			$repeater->add_control('pin_switch_board',
				[
					'label' => esc_html__("Tableau", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$repeater->add_control('pin_item_board',
				[
					'label'   => esc_html__("Nom du tableau", 'eac-components'),
					'type'    => Controls_Manager::TEXT,
					'condition' => ['pin_switch_board' => 'yes'],
					//'label_block' => true,
				]
			);
			
			$this->add_control('pin_pinterest_list',
				[
					'type'        => Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => [
						[
							'pin_item_title'	=> 'Pablo Picasso - Board',
							'pin_item_user'		=> 'leariana',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'pablo-picasso',
						],
						[
							'pin_item_title'	=> 'Pablo Picasso - Board 2',
							'pin_item_user'		=> 'martinetempervi',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'pablo-picasso',
						],
						[
							'pin_item_title'	=> 'Impressionnisme - Board',
							'pin_item_user'		=> 'davidbuis',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'impressionnisme',
						],
						[
							'pin_item_title'	=> 'Vincent Van Gogh - Board',
							'pin_item_user'		=> 'bruntherese',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'vincent-van-gogh',
						],
						[
							'pin_item_title'	=> 'Alfred Sisley - Board',
							'pin_item_user'		=> 'margaretbrotchie',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'art-alfred-sisley-1839-1899',
						],
						[
							'pin_item_title'	=> 'Paul Gauguin - Board',
							'pin_item_user'		=> 'tarahutton0120',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'art-paul-gauguin',
						],
						[
							'pin_item_title'	=> 'Pointillisme - Board',
							'pin_item_user'		=> 'charbonnelgigi2',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'artpointillisme',
						],
						[
							'pin_item_title'	=> 'Georges Seurat - Board',
							'pin_item_user'		=> 'gerarddelmas',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'seurat-georges',
						],
						[
							'pin_item_title'	=> 'Georges Seurat - Board 2',
							'pin_item_user'		=> 'Francois_Sierzputowski',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> '1859-91-georges-seurat',
						],
						[
							'pin_item_title'	=> 'Henry Edmond Cross - Board',
							'pin_item_user'		=> 'mademoisellerut',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'henry-edmond-cross',
						],
						[
							'pin_item_title'	=> 'Gustave Courbet - Board',
							'pin_item_user'		=> 'odefay',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'gustave-courbet',
						],
						[
							'pin_item_title'	=> 'Le Douanier Rousseau - Board',
							'pin_item_user'		=> 'ncochart',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'le-douanier-rousseau',
						],
						[
							'pin_item_title'	=> 'Amedeo Modigliani - Board',
							'pin_item_user'		=> 'tarahutton0120',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'art-amedeo-modigliani',
						],
						[
							'pin_item_title'	=> 'Berthe Morisot - Board',
							'pin_item_user'		=> 'olgakemp123',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'berthe-morisot',
						],
						[
							'pin_item_title'	=> 'Rosalba Carriera - Board',
							'pin_item_user'		=> 'rinascieuropa',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'rosalba-carriera',
						],
						[
							'pin_item_title'	=> 'Colette - Board',
							'pin_item_user'		=> 'gmlthomas',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'colette',
						],
						[
							'pin_item_title'	=> 'Camille Claudel - Board',
							'pin_item_user'		=> 'andisiha',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'camille-claudel',
						],
						[
							'pin_item_title'	=> 'La collection Courtauld - Board',
							'pin_item_user'		=> 'keewegoparis',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'la-collection-courtauld-fondation-louis-vuitton',
						],
						[
							'pin_item_title'	=> 'Affiches URSS - Board',
							'pin_item_user'		=> 'kilvendoneyjess',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'posters-cccp2',
						],
						[
							'pin_item_title'	=> 'Affiches Constructivisme Russe - Board',
							'pin_item_user'		=> 'alvinkherraz',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'constructivisme-russe',
						],
						[
							'pin_item_title'	=> 'Affiches URSS Constructivisme - Board',
							'pin_item_user'		=> 'lpjmag',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'constructivisme',
						],
						[
							'pin_item_title'	=> 'Les rues de Paris',
							'pin_item_user'		=> 'parisrues',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> '',
							'pin_item_board'	=> '',
						],
						[
							'pin_item_title'	=> 'Les rues de Paris - Board',
							'pin_item_user'		=> 'parisrues',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'paris-19e-arr-plaques-de-rues',
						],
						[
							'pin_item_title'	=> 'Mois mes souliers',
							'pin_item_user'		=> 'moimessouliers',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> '',
							'pin_item_board'	=> '',
						],
						[
							'pin_item_title'	=> 'Mois mes souliers - Board',
							'pin_item_user'		=> 'moimessouliers',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'japon',
						],
						
						[
							'pin_item_title'	=> 'Street Art',
							'pin_item_user'		=> 'artgirl67',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> '',
							'pin_item_board'	=> '',
						],
						[
							'pin_item_title'	=> 'Street Art - Board',
							'pin_item_user'		=> 'artgirl67',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'street-art',
						],
						[
							'pin_item_title'	=> 'Street Art - Board 2',
							'pin_item_user'		=> 'travelaar',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'street-art',
						],
						[
							'pin_item_title'	=> 'Street Art - Board 3',
							'pin_item_user'		=> 'ixiartgallery',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'coups-de-coeur-street-art',
						],
						[
							'pin_item_title'	=> 'Street Art - Board 4',
							'pin_item_user'		=> 'envoyezvotrepub',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'street-art',
						],
						[
							'pin_item_title'	=> 'Street Art - Board 5',
							'pin_item_user'		=> 'atasteoftravel',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'street-art',
						],
						[
							'pin_item_title'	=> 'Insolite - Board',
							'pin_item_user'		=> 'jeanpierreguillery',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'insolite-unusual',
						],
						[
							'pin_item_title'	=> 'Armchairs',
							'pin_item_user'		=> 'florence7777',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> '',
							'pin_item_board'	=> '',
						],
						[
							'pin_item_title'	=> 'Armchairs - Board',
							'pin_item_user'		=> 'florence7777',
							'pin_item_url'		=> ['url' => 'https://www.pinterest.fr'],
							'pin_switch_board'	=> 'yes',
							'pin_item_board'	=> 'armchair',
						],
					],
					'title_field' => '{{{ pin_item_title }}}',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_items_content',
				[
					'label'     => esc_html__('Contenu', 'eac-components'),
				]
			);
			
			$this->add_control('pin_item_nombre',
				[
					'label' => esc_html__("Nombre d'articles", 'eac-components'),
					'type' => Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 30,
					'step' => 5,
					'default' => 20,
				]
			);
			
			$this->add_control('pin_item_length',
				[
					'label' => esc_html__('Nombre de mots', 'eac-components'),
					'description' => esc_html__('Légende', 'eac-components'),
					'type'  => Controls_Manager::NUMBER,
					'min' => 5,
					'max' => 50,
					'step' => 5,
					'default' => 25,
				]
			);
			
			$this->add_control('pin_item_image',
				[
					'label' => esc_html__("Image", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
			$this->add_control('pin_item_lightbox',
				[
					'label' => esc_html__("Visionneuse", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'condition' => ['pin_item_image' => 'yes'],
					'separator' => 'after',
				]
			);
			
			$this->add_control('pin_item_date',
				[
					'label' => esc_html__("Date de Publication/Auteur", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => 'yes',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_layout_type_settings',
			[
				'label' => esc_html__('Disposition', 'eac-components'),
			]
		);
			
			// @since 1.8.7 Add default values for all active breakpoints.
			$active_breakpoints = Plugin::$instance->breakpoints->get_active_breakpoints();
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
			$this->add_responsive_control('pin_columns',
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
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_general_style',
			[
				'label'      => esc_html__('Global', 'eac-components'),
				'tab'        => Controls_Manager::TAB_STYLE,
			]
		);
		
			$this->add_control('pin_wrapper_style',
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
                    ],
				]
			);
			
			$this->add_control('pin_wrapper_margin',
				[
					'label' => esc_html__('Marge entre les colonnes', 'eac-components'),
					'type'  => Controls_Manager::SLIDER,
					'size_units' => ['em'],
					'range' => ['em' => ['min' => 0, 'max' => 5, 'step' => .2]],
					'default' => ['unit' => 'em', 'size' => .5],
					'selectors' => [ '{{WRAPPER}} .pin-galerie' => 'column-gap: {{SIZE}}{{UNIT}};' ],
				]
			);
			
			$this->add_control('pin_wrapper_bg_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .eac-pin-galerie' => 'background-color: {{VALUE}};' ],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_items_style',
			[
               'label' => esc_html__("Articles", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('pin_items_bg_color',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .pin-galerie__item' => 'background-color: {{VALUE}};'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_title_style',
			[
               'label' => esc_html__("Titre", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('pin_titre_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .pin-galerie__item-titre' => 'color: {{VALUE}};' ],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'pin_titre_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .pin-galerie__item-titre',
				]
			);
			
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_excerpt_style',
			[
               'label' => esc_html__("Légende", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('pin_excerpt_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => [ '{{WRAPPER}} .pin-galerie__item-description p' => 'color: {{VALUE}};' ],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'pin_excerpt_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_4,
					'selector' => '{{WRAPPER}} .pin-galerie__item-description p',
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('pin_icone_style',
			[
               'label' => esc_html__("Pictogrammes", 'eac-components'),
               'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('pin_icone_color',
				[
					'label' => esc_html__('Couleur', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'scheme' => [
						'type' => Color::get_type(),
						'value' => Color::COLOR_4,
					],
					'selectors' => ['{{WRAPPER}} .pin-galerie__item-date i,
						{{WRAPPER}} .pin-galerie__item-auteur i,
						{{WRAPPER}} .pin-galerie__item-date,
						{{WRAPPER}} .pin-galerie__item-auteur' => 'color: {{VALUE}};'],
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
		if(! $settings['pin_pinterest_list']) {
			return;
		}
		
		$this->add_render_attribute('pin_galerie', 'class', 'pin-galerie');
		$this->add_render_attribute('pin_galerie', 'data-settings', $this->get_settings_json());
		/** @since 1.9.3 'input hidden' */
		?>
		<div class="eac-pin-galerie">
			<input type="hidden" id="pin_nonce" name="pin_nonce" value="<?php echo wp_create_nonce("eac_rss_feed_" . $this->get_id()); ?>" />
			<?php $this->render_galerie(); ?>
			<div <?php echo $this->get_render_attribute_string('pin_galerie'); ?>></div>
		</div>
		<?php
    }
	
    protected function render_galerie() {
		$settings = $this->get_settings_for_display();
		$user = '/feed.rss';
		$board = '.rss';
		
		?>
		<div class="pin-select-item-list">
			<div class="pin-options-items-list">
				<select id="pin_options_items" class="pin-options-items">
					<?php foreach($settings['pin_pinterest_list'] as $item) { ?>
						<?php $has_board = $item['pin_switch_board'] === 'yes' && !empty($item['pin_item_board']) ? true : false ?>
						<?php if(! empty($item['pin_item_url']['url']) && ! empty($item['pin_item_user'])) : ?>
							<?php if($has_board) : ?>
								<?php $url = esc_url($item['pin_item_url']['url']) . '/' . sanitize_text_field($item['pin_item_user']) . '/' . sanitize_text_field($item['pin_item_board']) . $board; ?>
							<?php else : ?>
								<?php $url = esc_url($item['pin_item_url']['url']) . '/' . sanitize_text_field($item['pin_item_user']) . $user; ?>
							<?php endif; ?>
							<option value="<?php echo $url; ?>"><?php echo sanitize_text_field($item['pin_item_title']); ?></option>
						<?php endif; ?>
					<?php } ?>
				</select>
			</div>
			<div class="eac__button">
				<button id="pin__read-button" class="eac__read-button"><?php esc_html_e('Lire le flux', 'eac-components'); ?></button>
			</div>
			<div id="pin__loader-wheel" class="eac__loader-spin"></div>
		</div>
		<div class="pin-item-header"></div>
		<?php
	}
	
	/**
	 * get_settings_json()
	 *
	 * Retrieve fields values to pass at the widget container
     * Convert on JSON format
     * Read by 'eac-components.js' file when the component is loaded on the frontend
	 *
	 * @uses      json_encode()
	 *
	 * @return    JSON oject
	 *
	 * @access protected
	 * @since 1.0.0
	 * @since 1.9.0	Ajout du nonce et id du composant
	 */
	
	protected function get_settings_json() {
		$module_settings = $this->get_settings_for_display();
		
		$settings = array(
			"data_id" => $this->get_id(),
			"data_nombre"	=> $module_settings['pin_item_nombre'],
			"data_longueur"	=> $module_settings['pin_item_length'],
			"data_style"	=> $module_settings['pin_wrapper_style'],
			"data_date"		=> $module_settings['pin_item_date'] === 'yes' ? true : false,
			"data_img"		=> $module_settings['pin_item_image'] === 'yes' ? true : false,
			"data_lightbox"		=> $module_settings['pin_item_lightbox'] === 'yes' ? true : false,
		);
		
		$settings = json_encode($settings);
		return $settings;
	}
	
	protected function content_template() {}
	
}