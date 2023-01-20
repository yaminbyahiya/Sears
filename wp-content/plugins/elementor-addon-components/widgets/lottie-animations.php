<?php

/*=======================================================================================
* Class: Lottie_Animations_Widget
* Name: Lottie animation
* Slug: eac-addon-lottie-animations
*
* Description: Implémente les animations Lottie
*
* @since 1.9.3
*=======================================================================================*/

namespace EACCustomWidgets\Widgets;

use EACCustomWidgets\EAC_Plugin;
use EACCustomWidgets\Core\Eac_Config_Elements;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;

class Lottie_Animations_Widget extends Widget_Base {
	
	/**
	 * Constructeur de la class Lottie_Animations_Widget
	 * 
	 * Enregistre les scripts et les styles
	 *
	 * @since 1.9.3
	 */
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);
		
		wp_register_script('lottie-animation', 'https://cdnjs.cloudflare.com/ajax/libs/bodymovin/5.8.1/lottie.min.js', array(), '5.8.1', true);
		wp_register_script('eac-lottie-anim', EAC_Plugin::instance()->get_register_script_url('eac-lottie-animations'), array('jquery', 'elementor-frontend'), '1.9.3', true);
		wp_register_style('eac-lottie-anim', EAC_Plugin::instance()->get_register_style_url('lottie-animations'), array('eac'), '1.9.3');
	}
	
	/**
     * $slug
     *
     * @access private
     *
     * Le nom de la clé du composant dans le fichier de configuration
     */
	private $slug = 'lottie-animations';
	
	/**
	 * Retrieve widget name
	 *
	 *
	 * @since 1.9.3
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return Eac_Config_Elements::get_widget_name($this->slug);
	}

	/**
	 * Retrieve widget title.
	 *
	 *
	 * @since 1.9.3
	 * @access public
	 *
	 * @return string Widget title.
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
		return ['lottie-animation', 'eac-lottie-anim'];
	}
	
	/* 
	 * Load dependent styles
	 * 
	 * Les styles sont chargés dans le footer !!
     *
     * @return CSS list.
	 */
	public function get_style_depends() {
		return ['eac-lottie-anim'];
	}
	
	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.9.3
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
	 * @since 1.9.3
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

		$this->start_controls_section('lottie_settings_section',
			[
				'label' => esc_html__('Réglages', 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('lottie_settings_source',
				[
					'label'     => esc_html__("Origine", 'eac-components'),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'file' => [
							'title' => esc_html__('Fichier média', 'eac-components'),
							'icon'  => 'eicon-document-file',
						],
						'url' => [
							'title' => esc_html__('URL', 'eac-components'),
							'icon'  => 'eicon-editor-link',
						]
					],
					'default' => 'file',
				]
			);
			
			$this->add_control('lottie_settings_media_file',
				[
					'label' => esc_html__('Sélectionner le fichier', 'eac-components'),
					'type'	=> 'FILE_VIEWER',
					'library_type' => ['application/json'], // propriété utilisée par le script 'eac-file-viewer-control.js'
					'description' => esc_html__('Sélectionner le fichier de la librairie des médias', 'eac-components'),
					'condition' => ['lottie_settings_source' => 'file'],
				]
			);
			
			$this->add_control('lottie_settings_media_url',
				[
					'label' => esc_html__('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'description' => __("Obtenez l'URL de l'animation <a href='https://lottiefiles.com/' target='_blank' rel='nofollow noopener noreferrer'>ici</a>", "eac-components"),
					'placeholder' => 'https://lottiefiles.com/anim.json/',
					'show_external' => true,
					'default' => [
						'is_external' => true,
						'nofollow' => true,
					],
					'dynamic' => [
						'active' => true,
					],
					'condition' => ['lottie_settings_source' => 'url'],
				]
			);
			
			$this->add_responsive_control('lottie_settings_animation_size',
				[
					'label'       => esc_html__('Dimension', 'eac-components'),
					'type'        => Controls_Manager::SLIDER,
					'size_units'  => ['px'],
					'default'     => ['unit' => 'px', 'size' => 200],
					'range'       => ['px' => ['min' => 50, 'max' => 800, 'step' => 50]],
					'separator'   => 'before',
					'render_type' => 'template',
					'selectors'   => [
						'{{WRAPPER}}.lottie-anim_render-canvas .lottie-anim_wrapper' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
						'{{WRAPPER}}.lottie-anim_render-svg .lottie-anim_wrapper' => 'width: {{SIZE}}{{UNIT}} !important; height: auto !important;'
					],
				]
			);
			
			$this->add_control('lottie_settings_animation_rotate',
				[
					'label' => esc_html__('Rotation', 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'default' => ['size' => 0, 'unit' => 'px'],
					'range' => ['px' => ['min' => -180, 'max' => 180, 'step' => 10]],
					'selectors' => ['{{WRAPPER}} .lottie-anim_wrapper' => 'transform: rotate({{SIZE}}deg);'],
				]
			);
					
			$this->add_control('lottie_settings_animation_align',
				[
					'label' => esc_html__('Alignement', 'eac-components'),
					'type' => Controls_Manager::CHOOSE,
					'options' => [
						'flex-start' => [
							'title' => esc_html__('Gauche', 'eac-components'),
							'icon' => 'eicon-h-align-left',
						],
						'space-around' => [
							'title' => esc_html__('Centre', 'eac-components'),
							'icon' => 'eicon-h-align-center',
						],
						'flex-end' => [
							'title' => esc_html__('Droite', 'eac-components'),
							'icon' => 'eicon-h-align-right',
						],
					],
					'default' => 'space-around',
					'selectors' => ['{{WRAPPER}} .eac-lottie-animations' => 'justify-content: {{VALUE}};'],
				]
			);
			
			$this->add_control('lottie_settings_link_display',
				[
					'label'     => esc_html__("Ajouter un lien sur l'animation", 'eac-components'),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'yes' => [
							'title' => esc_html__('Oui', 'eac-components'),
							'icon'  => 'fa fa-check',
						],
						'no' => [
							'title' => esc_html__('Non', 'eac-components'),
							'icon'  => 'fa fa-ban',
						]
					],
					'default' => 'no',
					'separator' => 'before',
				]
			);
			
			$this->add_control('lottie_settings_link',
				[
					'label' => esc_html__('URL', 'eac-components'),
					'type' => Controls_Manager::URL,
					'placeholder' => 'https://you-site-url.com/',
					'show_external' => true,
					'default' => [
						'is_external' => true,
						'nofollow' => true,
					],
					'dynamic' => [
						'active' => true,
					],
					'condition' => ['lottie_settings_link_display' => 'yes'],
				]
			);
			
		$this->end_controls_section();
		
		$this->start_controls_section('lottie_settings_animation',
			[
				'label' => esc_html__("Animation", 'eac-components'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
			
			$this->add_control('lottie_settings_loop',
				[
					'label'     => esc_html__("Lire en boucle", 'eac-components'),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'yes' => [
							'title' => esc_html__('Oui', 'eac-components'),
							'icon'  => 'fa fa-check',
						],
						'no' => [
							'title' => esc_html__('Non', 'eac-components'),
							'icon'  => 'fa fa-ban',
						]
					],
					'default' => 'yes',
					'render_type' => 'template',
				]
			);
			
			$this->add_control('lottie_settings_reverse',
				[
					'label'     => esc_html__("Inverser le sens", 'eac-components'),
					'type'      => Controls_Manager::CHOOSE,
					'options'   => [
						'yes' => [
							'title' => esc_html__('Oui', 'eac-components'),
							'icon'  => 'fa fa-check',
						],
						'no' => [
							'title' => esc_html__('Non', 'eac-components'),
							'icon'  => 'fa fa-ban',
						]
					],
					'default' => 'no',
					'render_type' => 'template',
				]
			);
			
			$this->add_control('lottie_settings_speed',
				[
					'label'   => esc_html__("Vitesse", 'eac-components'),
					'type'    => Controls_Manager::NUMBER,
					'default' => 1,
					'min'     => 0.1,
					'max'     => 3,
					'step'    => 0.1,
					'render_type' => 'template',
				]
			);
			
			$this->add_control('lottie_settings_render',
				[
					'label' => esc_html__('Type de rendu', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'description' => esc_html__("Problèmes de performance ? Essayer la méthode 'Canvas'", 'eac-components'),
					'default' => 'svg',
					'options' => [
						'canvas' => esc_html__('Canvas', 'eac-components'),
						'svg'    => esc_html__('SVG', 'eac-components'),
					],
					'render_type'  => 'template',
					'prefix_class' => 'lottie-anim_render-',
				]
			);
			
			$this->add_control('lottie_settings_trigger',
				[
					'label' => esc_html__('Déclencheur', 'eac-components'),
					'type' => Controls_Manager::SELECT,
					'description' => esc_html__("Déclencheur de l'animation", 'eac-components'),
					'default' => 'none',
					'options' => [
						'none' => esc_html__('Aucun', 'eac-components'),
						'hover' => esc_html__('Au survol', 'eac-components'),
						'viewport' => esc_html__('Fenêtre visible', 'eac-components'),
					],
					'render_type'  => 'template',
				]
			);
			
			
			$this->add_control('lottie_settings_trigger_info',
				[
					'type' => Controls_Manager::RAW_HTML,
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					'raw'  => esc_html__("Lance l'animation dans la partie visible de la fenêtre", 'eac-components'),
					'condition' => ['lottie_settings_trigger' => 'viewport'],
				]
			);
			
			/*$this->add_control('lottie_settings_viewport',
				[
					'label'     => esc_html__('Viewport', 'eac-components'),
					'type'      => Controls_Manager::SLIDER,
					'default'   => ['sizes' => ['start' => 0, 'end' => 200], 'unit'  => 'px'],
					'labels'    => [
						esc_html__('Bas', 'eac-components'),
						esc_html__('Haut', 'eac-components'),
					],
					'scales'    => 1,
					'handles'   => 'range',
					'condition' => ['lottie_settings_trigger' => 'viewport'],
				]
			);*/
			
		$this->end_controls_section();
		
		/**
		 * Generale Style Section
		 */
		$this->start_controls_section('lottie_style_animation',
			[
				'label'		=> esc_html__('Animation', 'eac-components'),
				'tab'		 => Controls_Manager::TAB_STYLE,
			]
		);
			
			$this->add_control('lottie_style_bgcolor',
				[
					'label' => esc_html__('Couleur du fond', 'eac-components'),
					'type' => Controls_Manager::COLOR,
					'global'    => ['default' => Global_Colors::COLOR_PRIMARY],
					'selectors' => ['{{WRAPPER}} .lottie-anim_wrapper' => 'background-color: {{VALUE}};'],
				]
			);
			
			$this->add_control('lottie_style_opacity',
				[
					'label' => esc_html__("Opacitée", 'eac-components'),
					'type' => Controls_Manager::SLIDER,
					'default' => ['size' => 1],
					'range' => ['px' => ['max' => 1, 'min' => 0.1, 'step' => 0.1]],
					'selectors' => ['{{WRAPPER}} .lottie-anim_wrapper' => 'opacity: {{SIZE}};'],
				]
			);
			
			$this->add_group_control(
			Group_Control_Border::get_type(),
				[
					'name' => 'lottie_style_border',
					'separator' => 'before',
					'selector' => '{{WRAPPER}} .lottie-anim_wrapper',
				]
			);
			
			$this->add_control('lottie_style_border_radius',
				[
					'label' => esc_html__('Rayon de la bordure', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'size_units' => ['px', '%'],
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .lottie-anim_wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
			$this->add_group_control(
    			Group_Control_Box_Shadow::get_type(),
    			[
    				'name' => 'lottie_style_shadow',
    				'label' => esc_html__('Ombre', 'eac-components'),
    				'selector' => '{{WRAPPER}} .lottie-anim_wrapper',
    			]
    		);
			
			$this->add_responsive_control('lottie_style_padding',
				[
					'label' => esc_html__('Marges internes', 'eac-components'),
					'type' => Controls_Manager::DIMENSIONS,
					'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
					'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
					'selectors' => [
						'{{WRAPPER}} .lottie-anim_wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			
		$this->end_controls_section();
	}
	
	/*
	* Render widget output on the frontend.
	*
	* Written in PHP and used to generate the final HTML.
	* https://assets7.lottiefiles.com/packages/lf20_bsatc9vq.json
	* @access protected
	*/
	protected function render() {
		$settings = $this->get_settings_for_display();
		if(empty($settings['lottie_settings_media_file']) && empty($settings['lottie_settings_media_url']['url']))  { return; }
		?>
		<div class="eac-lottie-animations">
			<?php $this->render_lottie(); ?>
		</div>
		<?php
	}
	
	protected function render_lottie() {
		$settings = $this->get_settings_for_display();
		$has_link = false;
		$url = 'file' === $settings['lottie_settings_source'] ? $settings['lottie_settings_media_file'] : $settings['lottie_settings_media_url']['url'];
		
		// 
		$this->add_render_attribute('lottie_anime',
			array(
				'class'			=> 'lottie-anim_wrapper',
				'data-src'		=> $url,
				'data-autoplay'	=> $settings['lottie_settings_trigger'] === 'none' ? 'true' : 'false', // Pas d'autoplay pour 'hover' et 'viewport'
				'data-loop'		=> $settings['lottie_settings_loop'] === 'yes' ? 'true' : 'false',
				'data-speed'	=> $settings['lottie_settings_speed'],
				'data-reverse'	=> $settings['lottie_settings_reverse'] === 'yes' ? '-1' : '1',
				'data-renderer'	=> $settings['lottie_settings_render'],
				'data-trigger'	=> $settings['lottie_settings_trigger'],
				'data-name'		=> 'lottie_' . $this->get_id(),
				'data-elem-id'	=> $this->get_id(),
				//'data-start'    => isset($settings['lottie_settings_viewport']['sizes']['start']) ? $settings['lottie_settings_viewport']['sizes']['start'] : '0',
				//'data-end'      => isset($settings['lottie_settings_viewport']['sizes']['end']) ? 100 - $settings['lottie_settings_viewport']['sizes']['end'] : '100',
			)
		);
		
		// 
		if($settings['lottie_settings_link_display'] === 'yes' && !empty($settings['lottie_settings_link']['url'])) {
			$has_link = true;
			
			$link_url = esc_url($settings['lottie_settings_link']['url']);
			$this->add_render_attribute('lottie_url', 'href', $link_url);
			
			if($settings['lottie_settings_link']['is_external']) {
				$this->add_render_attribute('lottie_url', 'target', '_blank');
				$this->add_render_attribute('lottie_url', 'rel', 'noopener noreferrer');
			}
			if($settings['lottie_settings_link']['nofollow']) {
				$this->add_render_attribute('lottie_url', 'rel', 'nofollow');
			}
		}
		?>
		<div <?php echo $this->get_render_attribute_string('lottie_anime'); ?>>
			<?php if($has_link) : ?>
				<a <?php echo wp_kses_post($this->get_render_attribute_string('lottie_url')); ?>><span class="lottie-anim_wrapper-url"></span></a>
			<?php endif; ?>
		</div>
		<?php
	}
	
	protected function content_template() {}
	
}