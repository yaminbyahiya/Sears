<?php
namespace EACCustomWidgets\Widgets\Traits;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;

if(! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

trait Button_Read_More_Trait {
	
	/** Les contrôles du bouton */
	protected function register_button_more_content_controls($args = []) {
		
		$this->add_control('button_more_label',
			[
				'label' => esc_html__('Label', 'eac-components'),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__('En savoir plus', 'eac-components'),
			]
		);
			
		$this->add_control('button_add_more_picto',
			[
				'label' => esc_html__("Ajouter un pictogramme", 'eac-components'),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__('oui', 'eac-components'),
				'label_off' => esc_html__('non', 'eac-components'),
				'return_value' => 'yes',
				'default' => '',
			]
		);
		
		$this->add_control('button_more_picto',
			[
				'label' => esc_html__('Pictogramme', 'eac-components'),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'exclude_inline_options' => ['svg'],
				'default' => ['value' => 'fas fa-eye', 'library' => 'fa-solid'],
				'condition' => ['button_add_more_picto' => 'yes'],
			]
		);
		
		$this->add_control('button_more_position',
			[
				'label'     => esc_html__("Position", 'eac-components'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'before' => [
						'title' => esc_html__('Avant', 'eac-components'),
						'icon'  => 'eicon-h-align-left',
					],
					'after' => [
						'title' => esc_html__('Après', 'eac-components'),
						'icon'  => 'eicon-h-align-right',
					]
				],
				'default' => 'before',
				'condition' => ['button_add_more_picto' => 'yes'],
			]
		);
			
		$this->add_control('button_more_marge',
			[
				'label' => esc_html__('Marges', 'eac-components'),
				'type' => Controls_Manager::DIMENSIONS,
				'allowed_dimensions' => ['left', 'right'],
				'default' => ['left' => 0, 'right' => 0, 'unit' => 'px', 'isLinked' => false],
				'range' => ['px' => ['min' => 0, 'max' => 20, 'step' => 1]],
				'selectors' => ['{{WRAPPER}} .shop-product__button-readmore i' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'],
				'condition' => ['button_add_more_picto' => 'yes'],
			]
		);
	}
	
	/** Les styles du bouton */
	protected function register_button_more_style_controls($args = []) {
		
		$this->add_control('button_more_color',
			[
				'label' => esc_html__('Couleur', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_PRIMARY],
				'selectors' => ['{{WRAPPER}} .shop-product__button-readmore' => 'color: {{VALUE}}',],
			]
		);
		
		$this->add_group_control(
		Group_Control_Typography::get_type(),
			[
				'name' => 'button_more_typo',
				'label' => esc_html__('Typographie', 'eac-components'),
				'global'   => ['default' => Global_Typography::TYPOGRAPHY_SECONDARY],
				'selector' => '{{WRAPPER}} .shop-product__button-readmore',
			]
		);
			
		$this->add_control('button_more_bg',
			[
				'label' => esc_html__('Couleur du fond', 'eac-components'),
				'type' => Controls_Manager::COLOR,
				'global'    => ['default' => Global_Colors::COLOR_SECONDARY],
				'selectors' => ['{{WRAPPER}} .shop-product__button-readmore'  => 'background-color: {{VALUE}};',],
			]
		);
		
		$this->add_responsive_control('button_more_padding',
			[
				'label' => esc_html__('Marges internes', 'eac-components'),
				'type' => Controls_Manager::DIMENSIONS,
				'selectors' => [
					'{{WRAPPER}} .shop-product__button-readmore' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		
		$this->add_group_control(
   			Group_Control_Box_Shadow::get_type(),
   			[
   				'name' => 'button_more_shadow',
   				'label' => esc_html__('Ombre', 'eac-components'),
   				'selector' => '{{WRAPPER}} .shop-product__button-readmore',
   			]
   		);
		
		$this->add_group_control(
		Group_Control_Border::get_type(),
			[
				'name' => 'button_more_border',
				'selector' => '{{WRAPPER}} .shop-product__button-readmore',
			]
		);
		
		$this->add_control('button_more_radius',
			[
				'label' => esc_html__('Rayon de la bordure', 'eac-components'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'allowed_dimensions' => ['top', 'right', 'bottom', 'left'],
				'selectors' => [
					'{{WRAPPER}} .shop-product__button-readmore' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
	}
}