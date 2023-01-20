<?php

/*===========================================================================================
* Class: Eac_Author_Info
*
*
* @return affiche selon la sélection, la bio, l'email, l'URL du site web ou une méta donnée
* de l'auteur de l'article courant
* @since 1.6.0
* @since 1.6.1	Ajout du rôle dans la liste des informations de l'auteur
* @since 1.9.1	Test de la Global $authordata
*============================================================================================*/

namespace EACCustomWidgets\Includes\Elementor\DynamicTags\Tags;

use EACCustomWidgets\Core\Utils\Eac_Tools_Util;
use Elementor\Controls_Manager;
use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as TagsModule;

if(!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class Eac_Author_Info extends Tag {

	const VALS_LENGTH = 25;
	
	public function get_name() {
		return 'eac-addon-author-info';
	}

	public function get_title() {
		return esc_html__('Info Auteur', 'eac-components');
	}

	public function get_group() {
		return 'eac-author-groupe';
	}

	public function get_categories() {
		return [
			TagsModule::TEXT_CATEGORY,
			TagsModule::POST_META_CATEGORY,
		];
	}
    
	public function get_panel_template_setting_key() {
		return 'author_info_type';
	}

	protected function register_controls() {
		$this->add_control('author_info_type',
			[
				'label' => esc_html__('Champ', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
				    '' => esc_html__('Select...', 'eac-components'),
					'role' => esc_html__('Rôle', 'eac-components'),	// @since 1.6.1
					'description' => esc_html__('Bio', 'eac-components'),
					'email' => esc_html__('Email', 'eac-components'),
					'url' => esc_html__('Site Web', 'eac-components'),
					'meta' => esc_html__('Meta auteur', 'eac-components'),
				],
			]
		);
		
		$this->add_control('author_info_meta_key',
			[
				'label' => esc_html__('Clé', 'eac-components'),
				'type' => Controls_Manager::SELECT,
				'options' => $this->get_author_metas(),
				'default' => 'nickname',
				'condition' => ['author_info_type' => 'meta'],
			]
		);
	}
	
	public function render() {
	    // Allow HTML in author bio section 
        //remove_filter('pre_user_description', 'wp_filter_kses');
        $value = '';
        
		$key = $this->get_settings('author_info_type');

		if(empty($key)) { return; }
		
		if($key === 'meta') {
		    $meta = $this->get_settings('author_info_meta_key');
			if(!empty($meta)) {
				$value = get_the_author_meta($meta);
			}
		} else if($key === 'role') { // @since 1.6.1
			$userInfo = new \WP_User(get_the_author_meta('ID'));
			if(!empty($userInfo->roles) && is_array($userInfo->roles)) {
				$value = implode(', ', $userInfo->roles);
			}
		} else {
			$value = get_the_author_meta($key);
		}

		echo wp_kses_post($value);
	}
	
	/**
	 * Retourne la liste des métadonnées de l'auteur de l'article courant
	 *
	 * @since 1.9.1
	 */
	public function get_author_metas() {
		global $authordata;
		$list = array();
		$user_meta_fields = Eac_Tools_Util::get_supported_user_meta_fields();
		
		// @since 1.9.1 Global $authordata n'est pas instancié
        if(!is_object($authordata) || !isset($authordata->ID)) {
			return $list;
		}
		
		/*if(!isset($authordata->ID)) { // La variable globale n'est pas définie
			$post = get_post();
			$authordata = get_userdata($post->post_author);
		}*/
		
		$authormetas = array_map(function($a) { return $a[0]; }, get_user_meta($authordata->ID, '', true));
		
		foreach($authormetas as $key => $vals) {
			if(!is_serialized($vals) && $vals !== '' && $key[0] !== '_' && in_array($key, $user_meta_fields)) {
				if(mb_strlen($vals, 'UTF-8') > self::VALS_LENGTH) {
						$list[$key] = $key . "::" . mb_substr($vals, 0, self::VALS_LENGTH, 'UTF-8') . "...";
				} else {
					$list[$key] = $key . "::" . $vals;
				}
			}
		}
		ksort($list);
		return $list;
	}
}