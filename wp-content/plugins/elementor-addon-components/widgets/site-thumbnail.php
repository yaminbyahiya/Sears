<?php

/*====================================================================================================
* Class: Site_Thumbnails_Widget
* Name: Miniature de site
* Slug: eac-addon-site-thumbnail
*
* Description: Affiche la miniature d'un site web local ou distant
* 
*
* @since 1.7.70
* @since 1.9.0	Intégration des scripts et des styles dans le constructeur de la class
*				Ajout d'un overlay pour couvrir l'ensemble du widget avec le lien
* @since 1.9.2	Ajout des attributs "noopener noreferrer" pour les liens ouverts dans un autre onglet
*====================================================================================================*/
 
namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes\Typography;
use Elementor\Core\Schemes\Color;
use Elementor\Group_Control_Box_Shadow;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Site_Thumbnails_Widget extends Widget_Base {
	
	/**
	 * Constructeur de la class Site_Thumbnails_Widget
	 * 
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.0
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_style('eac-site-thumbnail', EAC_Plugin::instance()->get_register_style_url('site-thumbnail'), array('eac'), '1.7.70');
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'site-thumbnail';
	
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
	
	/* 
	 * Load dependent styles
	 * 
	 * Les styles sont chargés dans le footer !!
     *
     * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-site-thumbnail'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
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
	 * @since 1.7.0
	 * @access public
	 *
	 * @return URL help center
	 */
	public function get_custom_help_url() {
        return Eac_Config_Elements::get_widget_help_url($this->slug);
    }
	
    /**
     * Register widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @access protected
     */
    protected function register_controls() {
		
		$this->start_controls_section('st_site_settings',
			[
				'label'     => esc_html__('Réglages', 'eac-components'),
				'tab'        => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('st_site_url',
				[
					'label' => esc_html__('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'description' => esc_html__("Coller l'URL complète/relative du site", 'eac-components'),
					'placeholder' => 'http://your-link.com',
					'dynamic' => ['active' => true],
					'default' => [
						'url' => '',
						'is_external' => true,
						'nofollow' => true,
					],
				]
			);
			
			$this->add_control('st_site_url_warning',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'eac-editor-panel_warning',
					'raw'  => esc_html__("SAMEORIGIN: Certains sites interdisent le chargement de la ressource dans une iframe en dehors de leur domaine.", "eac-components"),
				]
			);
			
			$this->add_control('st_add_link',
				[
					'label' => esc_html__("Ajouter le lien vers le site", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
				]
			);
			
			
			$this->add_control('st_add_caption',
				[
					'label' => esc_html__("Ajouter une légende", 'eac-components'),
					'type' => Controls_Manager::SWITCHER,
					'label_on' => esc_html__('oui', 'eac-components'),
					'label_off' => esc_html__('non', 'eac-components'),
					'return_value' => 'yes',
					'default' => '',
					'separator' => 'before',
				]
			);
			
			$this->add_control('st_site_caption',
				[
					'label' => esc_html__("Légende", 'eac-components'),
					'type' => Controls_Manager::TEXT,
					'dynamic' => ['active' => true],
					'description' => esc_html__("Coller la légende", 'eac-components'),
					'label_block' => true,
					'condition' => ['st_add_caption' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('st_site_container_style',
			[
				'label'     => esc_html__('Global', 'eac-components'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('st_site_container_margin',
				[
					'label' => esc_html__('Marges', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'bottom'],
					'size_units' => ['px'],
					'default' => ['top' => 0, 'bottom' => 0, 'unit' => 'px', 'isLinked' => true],
					'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .eac-site-thumbnail' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'st_site_container_shadow',
    				'label' => esc_html__('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .site-thumbnail-container',
    			]
    		);
			
		$this->end_controls_section();
		
		$this->start_controls_section('st_site_legende_style',
			[
				'label'     => esc_html__('Légende', 'eac-components'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => ['st_add_caption' => 'yes'],
			]
		);
			
			$this->add_responsive_control('st_site_legende_margin',
				[
					'label' => esc_html__('Espacement', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'size_units' => ['px'],
					'default' => ['size' => 10, 'unit' => 'px'],
					'range' => ['px' => ['min' => 0, 'max' => 50, 'step' => 5]],
					'selectors' => ['{{WRAPPER}} .thumbnail-caption' => 'margin-top: {{SIZE}}{{UNIT}};'],
				]
			);
			
			$this->add_control('st_site_legende_color',
				[
					'label' => esc_html__("Couleur", 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'selectors' => ['{{WRAPPER}} .thumbnail-caption' => 'color: {{VALUE}};'],
					'scheme' => [
						'type' =>Color::get_type(),
						'value' => Color::COLOR_1,
					],
				]
			);
			
			$this->add_group_control(
			Group_Control_Typography::get_type(),
				[
					'name' => 'st_site_legende_typography',
					'label' => esc_html__('Typographie', 'eac-components'),
					'scheme' => Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .thumbnail-caption',
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
		if(empty($settings['st_site_url']['url'])) { return; }
		
		$has_url = $settings['st_add_link'] === 'yes' ? true : false;
		$url = esc_url($settings['st_site_url']['url']);
		$this->add_render_attribute('st-link-to', 'href', $url);
		
		/** @since 1.9.2 Ajout des attributs 'noopener noreferrer' */
		if($settings['st_site_url']['is_external']) {
			$this->add_render_attribute('st-link-to', 'target', '_blank');
			$this->add_render_attribute('st-link-to', 'rel', 'noopener noreferrer');
		}
		if($settings['st_site_url']['nofollow']) {
			$this->add_render_attribute('st-link-to', 'rel', 'nofollow');
		}
		
		$has_caption = $settings['st_add_caption'] === 'yes' && !empty($settings['st_site_caption']);
		?>
		<div class="eac-site-thumbnail">
			<div class="site-thumbnail-container">
				<?php if($has_url) {?>
					<a <?php echo $this->get_render_attribute_string('st-link-to'); ?>>
				<?php }?>
					<span class="site-thumbnail__wrapper-overlay"></span>
				<?php if($has_url) {?>
					</a>
				<?php }?>
				<div class="thumbnail-container" title="<?php echo $url; ?>">
					<div class="thumbnail">
						<iframe src="<?php echo $url; ?>" frameborder="0" onload="var that=this;setTimeout(function() { that.style.opacity=1 }, 500)"></iframe>
					</div>
				</div>
			</div>
			<?php if($has_caption) {?>
				<div class="thumbnail-caption"><?php echo sanitize_text_field($settings['st_site_caption']); ?></div>
			<?php }?>
		</div>
		<?php
    }
	
	protected function content_template() {}
	
}