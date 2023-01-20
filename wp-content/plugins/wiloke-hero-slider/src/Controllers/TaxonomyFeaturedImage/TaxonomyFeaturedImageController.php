<?php

namespace WilokeHeroSlider\Controllers\TaxonomyFeaturedImage;

class TaxonomyFeaturedImageController
{
  public function __construct()
  {
    if ($this->hasTermList()) {
      add_action('category_edit_form_fields', [$this, 'addCategoryImage'], 10,
        2);

      foreach ($this->getTaxonomyKeys() as $taxonomy) {
        add_action('edited_' . $taxonomy, [$this, 'updateFeaturedImage'], 10,
          2);
      }

      add_action('admin_enqueue_scripts', [$this, 'loadMedia']);
      add_action('admin_enqueue_scripts', [$this, 'enqueueScripts']);
    }
  }

  private function hasTermList(): bool
  {
    global $wilokeEnabledTaxonomyFeaturedImage;

    if ($wilokeEnabledTaxonomyFeaturedImage) {
      return false;
    }

    $configPath = WILOKE_WILOKEHEROSLIDER_VIEWS_PATH .
      "src/Configs/schema.json";
    if (is_file($configPath)) {
      ob_start();
      include $configPath;
      $content = ob_get_contents();
      ob_end_clean();
      if (strpos($content, "wil_list_terms") !== false) {
        $wilokeEnabledTaxonomyFeaturedImage = true;
        return true;
      }
    }
    return false;
  }

  public function enqueueScripts()
  {
    if (!current_user_can('administrator') | !isset($_GET['taxonomy'])) {
      return;
    }

    wp_enqueue_script(
      'wiloke-upload-image',
      plugin_dir_url(__FILE__) . 'MediaScript.js',
      [
        'jquery'
      ],
      WILOKE_WILOKEHEROSLIDER_VERSION,
      true
    );
  }

  function loadMedia()
  {
    wp_enqueue_media();
  }

  private function getTaxonomyKeys(): array
  {
    $aTaxonomyOptions = get_taxonomies([
      'public' => true
    ]);

    return array_keys($aTaxonomyOptions);
  }

  public function updateFeaturedImage($termId, $taxonomyId)
  {
    if (!current_user_can('administrator')) {
      return;
    }
    if (isset($_POST['wiloke_image_id']) && !empty($_POST['wiloke_image_id'])) {
      update_term_meta($termId, 'wiloke_image_id',
        absint($_POST['wiloke_image_id']));
    } else {
      delete_term_meta($termId, 'wiloke_image_id', '');
    }
  }

  function addCategoryImage($term, $taxonomy)
  {
    ?>
    <tr class="form-field term-group-wrap">
      <th scope="row">
        <label for="image_id"><?php _e('Featured Image',
            'wiloke-hero-slider'); ?></label>
      </th>
      <td>
        <?php $image_id = get_term_meta($term->term_id, 'wiloke_image_id',
          true); ?>
        <input type="hidden" id="wiloke-image-id" name="wiloke_image_id"
               value="<?php echo $image_id; ?>">
        <div id="wiloke-image-wrapper">
          <?php if ($image_id) { ?>
            <?php echo wp_get_attachment_image($image_id); ?>
          <?php } ?>
        </div>

        <div>
          <p>
            <input type="button"
                   class="button button-secondary wiloke-taxonomy-media-button"
                   id="wiloke-taxonomy-media-button"
                   name="wiloke_taxonomy_media_button"
                   value="<?php _e('Add Image', 'wiloke-hero-slider'); ?>">
            <input type="button"
                   class="button button-secondary wiloke-taxonomy-media-remove"
                   id="wiloke-taxonomy-media-remove"
                   name="wiloke_taxonomy_media_remove"
                   value="<?php _e('Remove Image', 'taxt-domain'); ?>">
          </p>
        </div>
      </td>
    </tr>
    <?php
  }
}