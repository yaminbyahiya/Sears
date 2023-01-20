<?php

/*===============================================================================
* Class: Eac_User_Picture
*
* 
* @return l'URL de l'avatar/gravatar de l'utilisateur courant logué
* @since 1.6.0
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_User_Picture extends Data_Tag {

	public function get_name() {
		return 'eac-addon-user-profile-picture';
	}

	public function get_title() {
		return esc_html__("Photo utilisateur", 'eac-components');
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return [TagsModule::IMAGE_CATEGORY];
	}
    
	protected function register_controls() {
		$this->add_control('user_picture_size',
			[
				'label' => esc_html__('Dimension', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'default' => '96',
				'options' => [
					'80' => '80',
					'96' => '96',
					'120' => '120',
					'140' => '140',
				],
			]
		);
	}
	public function get_value(array $options = []) {
	   $size = $this->get_settings('user_picture_size');
	   
		return [
		    'url' => get_avatar_url((int) get_current_user_id(), ['size' => $size]),
			'id' => '',
		];
	}
}