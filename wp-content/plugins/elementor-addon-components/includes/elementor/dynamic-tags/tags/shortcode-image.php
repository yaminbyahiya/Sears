<?php
/*===============================================================================
* Class: Eac_Shortcode
*
*  
* @return exécute le shortcode Image et affiche le résultat
* @since 1.6.0
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Shortcode_Image extends Tag {
	public function get_name() {
		return 'eac-addon-shortcode';
	}

	public function get_title() {
		return esc_html__('Shortcode', 'eac-components');
	}

	public function get_group() {
		return 'eac-site-groupe';
	}

	public function get_categories() {
		return [TagsModule::TEXT_CATEGORY];
	}

	protected function register_controls() {
		$this->add_control('shortcode',
			[
				'label' => esc_html__('Shortcode', 'eac-components'),
				'type'  => Controls_Manager::TEXTAREA,
				'default' => '[eac_img src="https://www.cestpascommode.fr/wp-content/uploads/2020/04/chaise-victoria-01.jpg" link="https://www.cestpascommode.fr/realisations/chaise-victoria" caption="Chaise Victoria"]',
				'rows'	=> 8,
			]
		);
	}
	
	public function render() {
		$settings = $this->get_settings();

		if(empty($settings['shortcode'])) {
			return;
		}

		$shortcode_string = $settings['shortcode'];
		$value = do_shortcode(shortcode_unautop($shortcode_string));
		
		echo $value;
	}
}