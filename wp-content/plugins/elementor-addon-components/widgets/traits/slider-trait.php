<?php
namespace EACCustomWidgets\Widgets\Traits;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;

if(! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

trait Slider_Trait {
	
	/** Les contrôles du slider */
	protected function register_slider_content_controls($args = []) {

		$this->add_control('slider_autoplay',
			[
				'label'     => esc_html__("Lecture automatique", 'eac-components'),
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
			]
		);
			
		$this->add_control('slider_delay',
			[
				'label' => esc_html__("Interval d'affichage (ms)", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 500,
				'max' => 5000,
				'step' => 500,
				'default' => 2000,
				'condition' => ['slider_autoplay' => 'yes'],
			]
		);
			
		$this->add_control('slider_loop',
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
				'condition' => ['slider_autoplay' => 'yes'],
			]
		);
			
		$this->add_control('slider_images_number',
			[
				'label' => esc_html__("Diapositives affichées", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 30,
				'step' => 1,
				'default' => 3,
				'condition' => ['slider_effect!' => ['creative', 'fade']],
			]
		);
			
		$this->add_control('slider_rtl',
			[
				'label'     => esc_html__("Direction", 'eac-components'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left' => [
						'title' => esc_html__('Gauche', 'eac-components'),
						'icon'  => 'eicon-order-start',
					],
					'right' => [
						'title' => esc_html__('Droite', 'eac-components'),
						'icon'  => 'eicon-order-end',
					]
				],
				'default'   => 'left',
				'condition' => ['slider_autoplay' => 'yes', 'slider_effect!' => 'creative'],
			]
		);
			
		$this->add_control('slider_effect',
			[
				'label'			=> esc_html__('Transition', 'eac-components'),
				'type'			=> Controls_Manager::SELECT,
				'default'		=> 'slide',
				'options'       => [
					'slide'		=> esc_html__('Défaut', 'eac-components'),
					'coverflow'	=> 'Coverflow',
					'creative'	=> esc_html__('Créatif', 'eac-components'),
					'fade'		=> 'Fade',
                   ],
			]
		);
			
		$this->add_responsive_control('slider_width',
			[
				'label' => esc_html__("Largeur du slider (%)", 'eac-components'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default' => ['unit' => '%', 'size' => 60],
				'tablet_default' => ['unit' => '%', 'size' => 60],
				'mobile_default' => ['unit' => '%', 'size' => 100],
				'range' => ['%' => ['min' => 20, 'max' => 100, 'step' => 10]],
				'selectors' => ['{{WRAPPER}} .swiper-container' => 'width: {{SIZE}}%;'],
				'render_type' => 'template',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'slider_effect', 'operator' => 'in', 'value' => ['fade', 'creative']],
						['name' => 'slider_images_number', 'operator' => '===', 'value' => 1]
					]
				],
				'separator' => 'before',
			]
		);
			
		$this->add_responsive_control('slider_height',
			[
				'label' => esc_html__("Hauteur du slider (px)", 'eac-components'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default' => ['unit' => 'px', 'size' => 300],
				'range' => ['px' => ['min' => 100, 'max' => 1000, 'step' => 50]],
				'selectors' => ['{{WRAPPER}} .swiper-wrapper .swiper-slide > div' => 'height: {{SIZE}}{{UNIT}}; width: auto;'],
				'render_type' => 'template',
				'conditions' => [
					'terms' => [
						['name' => 'slider_effect', 'operator' => '!in', 'value' => ['fade', 'creative']],
						['name' => 'slider_images_number', 'operator' => '===', 'value' => 0]
					]
				],
				'separator' => 'before',
			]
		);
			
		$this->add_responsive_control('slider_ratio',
			[
				'label'   => esc_html__("Ratio image", 'eac-components'),
				'type'    => Controls_Manager::SELECT,
				'default' => '1-1',
				'tablet_default' => '3-2',
				'mobile_default' => '1-1',
				'options' => [
					'1-1'	=> esc_html__('Défaut', 'eac-components'),
					'4-3'	=> '4:3',
					'3-2'	=> '3:2',
					'16-9'	=> '16:9',
					'21-9'	=> '21:9',
				],
				'prefix_class' => 'slider-ratio%s-',
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'slider_images_number', 'operator' => '>', 'value' => 0],
						['name' => 'slider_effect', 'operator' => 'in', 'value' => ['fade', 'creative']]
					]
				],
				'separator' => 'before',
			]
		);
			
		$this->add_responsive_control('slider_position',
			[
				'label' => esc_html__('Position verticale', 'eac-components'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'default' => ['size' => 50, 'unit' => '%'],
				'tablet_default' => ['size' => 50, 'unit' => '%'],
				'mobile_default' => ['size' => 50, 'unit' => '%'],
				'range' => ['%' => ['min' => 0, 'max' => 100, 'step' => 5]],
				'selectors' => ['{{WRAPPER}} .swiper-wrapper .swiper-slide img' => 'object-position: 50% {{SIZE}}%;'],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'slider_images_number', 'operator' => '>', 'value' => 0],
						['name' => 'slider_effect', 'operator' => 'in', 'value' => ['fade', 'creative']],
					]
				],
			]
		);
			
		$this->add_control('slider_navigation',
			[
				'label'     => esc_html__("Navigation", 'eac-components'),
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
			
		$this->add_control('slider_pagination',
			[
				'label'     => esc_html__("Pagination", 'eac-components'),
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
			]
		);
			
		$this->add_control('slider_pagination_click',
			[
				'label'     => esc_html__("Cliquable", 'eac-components'),
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
				'condition' => ['slider_pagination' => 'yes'],
			]
		);
			
		$this->add_control('slider_scrollbar',
			[
				'label'     => esc_html__("Barre de défilement", 'eac-components'),
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
			]
		);
	}
	
	/** Les styles du slider */
	protected function register_slider_style_controls($args = []) {
		
		$this->add_control('slider_style_navigation',
			[
				'label'	=> esc_html__('Navigation', 'eac-components'),
				'type'	=> Controls_Manager::HEADING,
				'condition' => ['slider_navigation' => 'yes'],
			]
		);
		
		$this->add_control('slider_navigation_size',
			[
				'label' => esc_html__("Dimension", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 45,
				'max' => 100,
				'step' => 10,
				'default' => 45,
				'selectors' => ['{{WRAPPER}} .swiper-button-next:after, {{WRAPPER}} .swiper-button-prev:after' => 'font-size: {{VALUE}}px;'],
				'condition' => ['slider_navigation' => 'yes'],
			]
		);
			
		$this->add_control('slider_navigation_color',
			[
				'label' => esc_html__('Couleur', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_PRIMARY],
				'default' => '#000',
				'selectors' => ['{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev' => 'color: {{VALUE}};'],
				'condition' => ['slider_navigation' => 'yes']
			]
		);
			
		$this->add_control('slider_style_pagination',
			[
				'label' => esc_html__('Pagination', 'eac-components'),
				'type' => Controls_Manager::HEADING,
				'condition' => ['slider_pagination' => 'yes'],
				'separator' => 'before',
			]
		);
		
		$this->add_control('slider_pagination_color',
			[
				'label' => esc_html__('Couleur des puces', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_PRIMARY],
				'default' => 'black',
				'selectors' => ['{{WRAPPER}} .swiper-container .swiper-pagination-bullet.swiper-pagination-bullet' => 'background-color: {{VALUE}};'],
				'condition' => ['slider_pagination' => 'yes'],
			]
		);
			
		$this->add_control('slider_pagination_color_active',
			[
				'label' => esc_html__('Couleur de la puce active', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_PRIMARY],
				'default' => 'red',
				'selectors' => ['{{WRAPPER}} .swiper-container .swiper-pagination-bullet.swiper-pagination-bullet-active' => 'background-color: {{VALUE}};'],
				'condition' => ['slider_pagination' => 'yes'],
			]
		);
			
		$this->add_control('slider_pagination_width',
			[
				'label' => esc_html__("Largeur des puces", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 40,
				'step' => 1,
				'default' => 10,
				'selectors' => ['{{WRAPPER}} .swiper-container .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'width: {{VALUE}}px;'],
				'condition' => ['slider_pagination' => 'yes'],
			]
		);
			
		$this->add_control('slider_pagination_height',
			[
				'label' => esc_html__("Hauteur des puces", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 3,
				'max' => 15,
				'step' => 1,
				'default' => 3,
				'selectors' => [
					'{{WRAPPER}} .swiper-container .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'height: {{VALUE}}px;'
					],
				'condition' => ['slider_pagination' => 'yes'],
			]
		);
			
		$this->add_control('slider_pagination_radius',
			[
				'label' => esc_html__('Rayon de la bordure', 'eac-components'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullets.swiper-pagination-horizontal .swiper-pagination-bullet' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => ['slider_pagination' => 'yes'],
			]
		);
	}
}