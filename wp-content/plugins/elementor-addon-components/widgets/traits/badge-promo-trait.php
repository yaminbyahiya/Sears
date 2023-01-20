<?php
namespace EACCustomWidgets\Widgets\Traits;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if(! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

trait Badge_Promo_Trait {
	
	protected function register_promo_content_controls($args = []) {
		
		$this->add_control('promo_format',
			[
				'label'     => esc_html__("Pourcentage", 'eac-components'),
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
		
		$this->add_control('promo_text',
			[
				'label' => esc_html__('Texte', 'eac-components'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('Promo!', 'eac-components'),
				'condition' => ['promo_format' => 'no'],
			]
		);
		
		$this->add_control('promo_position',
			[
				'label'     => esc_html__("Position", 'eac-components'),
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
				'prefix_class' => 'badge-promo-pos-',
			]
		);
	}
	
	protected function register_promo_style_controls($args = []) {
		
		$this->add_control('promo_color',
			[
				'label' => esc_html__('Couleur', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_PRIMARY],
				'selectors' => ['{{WRAPPER}} .badge-promo' => 'color: {{VALUE}};'],
			]
		);
		
		$this->add_control('promo_bg',
			[
				'label' => esc_html__('Couleur du fond', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_SECONDARY],
				'selectors' => ['{{WRAPPER}} .badge-promo' => 'background-color: {{VALUE}};'],
			]
		);
			
		$this->add_group_control(
		Group_Control_Typography::get_type(),
			[
				'name' => 'promo_typo',
				'label' => esc_html__('Typographie', 'eac-components'),
				'global'   => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY],
				'selector' => '{{WRAPPER}} .badge-promo',
			]
		);
		
		$this->add_control('promo_width',
			[
				'label' => esc_html__("Largeur", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 10,
				'max' => 250,
				'step' => 5,
				'default' => 90,
				'selectors' => ['{{WRAPPER}} .badge-promo' => 'width: {{VALUE}}px;'],
				'separator' => 'before',
			]
		);
		
		$this->add_control('promo_height',
			[
				'label' => esc_html__("Hauteur", 'eac-components'),
				'type' => Controls_Manager::NUMBER,
				'min' => 10,
				'max' => 100,
				'step' => 5,
				'default' => 25,
				'selectors' => ['{{WRAPPER}} .badge-promo' => 'height: {{VALUE}}px;'],
			]
		);
			
		$this->add_control('promo_radius',
			[
				'label' => esc_html__('Rayon de la bordure', 'eac-components'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
				'default' => ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true],
				'selectors' => [
					'{{WRAPPER}} .badge-promo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
}