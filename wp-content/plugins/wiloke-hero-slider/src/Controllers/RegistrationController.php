<?php

namespace WilokeHeroSlider\Controllers;


use Elementor\Controls_Manager;
use WilokeHeroSlider\Controllers\CategoryPostControl\CustomCategoryPostControl;
use WilokeHeroSlider\Controllers\SelectTaxonomyControl\SelectTaxonomyControl;
use WilokeHeroSlider\Controllers\CategoryProductControl\CustomCategoryProductControl;
use WilokeHeroSlider\Controllers\PostControl\CustomPostControl;
use WilokeHeroSlider\Controllers\ProductControl\ProductControl;
use WilokeHeroSlider\Controllers\SingleProductControl\SingleProductControl;
use WilokeHeroSlider\Share\App;

class RegistrationController
{
  public static string $WilokeHeroSlider = 'WilokeHeroSlider';
  private string       $appDomain  = ".netlify.app";

  public function __construct()
  {
    $aConfigs = json_decode(file_get_contents(plugin_dir_path(__FILE__) .
      '../Configs/config.json'), true);
    App::bind('dataConfig', $aConfigs);

    $this->registerScriptKeys();
    add_action('wp_enqueue_scripts', [$this, 'registerScripts']);
    add_action('elementor/elements/categories_registered',
      [$this, 'registerCategories']);
    add_action('elementor/widgets/register', [$this, 'registerAddon']);
    add_action('elementor/controls/register', [$this, 'registerControls']);
  }

  public function registerCategories($oElementsManager)
  {
    $key = App::get('dataConfig')['category']['key'] ?? 'wiloke-category';
    if (!array_key_exists($key, $oElementsManager->get_categories())) {
      $oElementsManager->add_category(
        $key,
        [
          'title' => App::get('dataConfig')['category']['title'] ??
            esc_html__('Wiloke', 'wiloke-hero-slider'),
          'icon'  => App::get('dataConfig')['category']['icon'] ?? 'eicon-font',
        ]
      );
    }
  }

  public function registerScriptKeys()
  {
    if (isset(App::get('dataConfig')['css'])) {
      $aHandleCss[] = WILOKE_WILOKEHEROSLIDER_NAMESPACE .
        md5(App::get('dataConfig')['css']);
      $aHandleJs[] = WILOKE_WILOKEHEROSLIDER_NAMESPACE .
        md5(App::get('dataConfig')['js']);
    } else {
      $aHandleCss[] = WILOKE_WILOKEHEROSLIDER_NAMESPACE;
      $aHandleJs[] = WILOKE_WILOKEHEROSLIDER_NAMESPACE;
    }

    App::bind('handleCss', $aHandleCss);
    App::bind('handleJs', $aHandleJs);
  }

  private function getStylesheetUrl()
  {
    if (isset(App::get('dataConfig')['css'])) {
      return App::get('dataConfig')['css'];
    }

    $domain = App::get('dataConfig')['filename'] ?? WILOKE_WILOKEHEROSLIDER_NAMESPACE;

    return esc_url(
      "https://" . $domain . $this->appDomain .
      "/styles/styles.css"
    );
  }

  private function getScriptUrl()
  {
    if (isset(App::get('dataConfig')['js'])) {
      return App::get('dataConfig')['js'];
    }

    $domain = App::get('dataConfig')['filename'] ?? WILOKE_WILOKEHEROSLIDER_NAMESPACE;

    return esc_url(
      "https://" . $domain . $this->appDomain .
      "/js/scripts.js",
    );
  }

  public function registerScripts()
  {
    wp_register_style(
      WILOKE_WILOKEHEROSLIDER_NAMESPACE,
      $this->getStylesheetUrl(),
      [],
      WILOKE_WILOKEHEROSLIDER_VERSION);

    wp_register_script(
      WILOKE_WILOKEHEROSLIDER_NAMESPACE,
      $this->getScriptUrl(),
      ['elementor-frontend'],
      WILOKE_WILOKEHEROSLIDER_VERSION,
      true
    );

    wp_localize_script('jquery', 'WilokeHeroSlider', [
      'prefix'  => WILOKE_WILOKEHEROSLIDER_NAMESPACE,
      'userID'  => get_current_user_id(),
      'ajaxUrl' => admin_url('admin-ajax.php')
    ]);
  }

  public function registerAddon($oWidgetManager)
  {
    $oWidgetManager->register(new PluginAddon());
  }

  public function registerControls(Controls_Manager $oControlManager)
  {
    
    
    
    
    
    
  }
}