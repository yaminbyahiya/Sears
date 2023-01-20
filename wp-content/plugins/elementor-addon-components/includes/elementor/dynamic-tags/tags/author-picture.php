<?php

/*===============================================================================
* Class: Eac_Author_Picture
*
*
* @return l'URL de l'avatar/gravatar de l'auteur de l'article courant
* @since 1.6.0
* @since 1.9.1	Test de la Global $authordata
*===============================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Author_Picture extends Data_Tag {

	public function get_name() {
		return 'eac-addon-author-profile-picture';
	}

	public function get_title() {
		return esc_html__("Photo auteur", 'eac-components');
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return [TagsModule::IMAGE_CATEGORY];
	}

	protected function register_controls() {
		$this->add_control('author_picture_size',
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
        global $authordata;
        $size = $this->get_settings('author_picture_size');
        
		// @since 1.9.1 Global $authordata n'est pas instancié
        if(!is_object($authordata) || !isset($authordata->ID)) {
			return ['url' => '', 'id' => ''];
		}
		
		/*if(!isset($authordata->ID)) { // La variable globale n'est pas définie
			$post = get_post();
			$authordata = get_userdata($post->post_author);
		}*/
		
		return [
		    'url' => get_avatar_url((int) get_the_author_meta('ID'), ['size' => $size]),
			'id' => '',
		];
	}
}