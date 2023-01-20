<?php

/**
* Tested up to:        5.6.2
* Domain Path:         /languages
* Text Domain:         wiloke-hero-slider
* License URI:         http://www.gnu.org/licenses/gpl-2.0.txt
* License:             GPL-2.0+
* Author URI:          https://wiloke.com
* Author:              wiloke
* Version:             1.0.20
* Description:         Wiloke Hero Slider for Elementor
* Plugin URI:          https://wiloke.com
* Plugin Name:         Wiloke Hero Slider
*/

use WilokeHeroSlider\Controllers\Notification\NotificationController;
use WilokeHeroSlider\Controllers\TaxonomyFeaturedImage\TaxonomyFeaturedImageController;
global $wilokeEnabledTaxonomyFeaturedImage;
define("WILOKE_WILOKEHEROSLIDER_VERSION",
  defined('WP_DEBUG') && WP_DEBUG ? uniqid() : '1.0.20');
define("WILOKE_WILOKEHEROSLIDER_NAMESPACE", "wiloke-hero-slider");
define("WILOKE_WILOKEHEROSLIDER_PREFIX", "wiloke-hero-slider_");
define("WILOKE_WILOKEHEROSLIDER_VIEWS_PATH", plugin_dir_path(__FILE__));
define("WILOKE_WILOKEHEROSLIDER_VIEWS_URL", plugin_dir_url(__FILE__));

add_action("plugins_loaded", "WilokeHeroSliderLoadPluginDomain");
if (!function_exists("WilokeHeroSliderLoadPluginDomain")) {
  function WilokeHeroSliderLoadPluginDomain()
  {
    load_plugin_textdomain("wiloke-hero-slider", false,
      plugin_dir_path(__FILE__) . "languages");
  }
}

require_once plugin_dir_path(__FILE__) . "vendor/autoload.php";

new \WilokeHeroSlider\Controllers\RegistrationController();
new \WilokeHeroSlider\Controllers\HandleAjaxController();
new TaxonomyFeaturedImageController();
new NotificationController();