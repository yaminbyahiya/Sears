<?php

namespace WilokeHeroSlider\Share;

use WP_Query;

class HandleListTerms
{
  use TraitImageSizes;

  private function getLastFivePosts(array $aConfiguration, $termId): array
  {
    if (empty($aConfiguration['postType'])) {
      return [];
    }

    $args = [
      'post_type'      => $aConfiguration['postType'],
      'tax_query'      => [
        [
          'taxonomy' => $aConfiguration['taxonomy'],
          'field'    => 'term_id',
          'terms'    => $termId
        ]
      ],
      'post_status'    => 'publish',
      'posts_per_page' => 5
    ];
    $query = new WP_Query($args);

    $aPostList = [];
    if ($query->have_posts()) {
      while ($query->have_posts()) {
        $query->the_post();
        $aPost = [
          'link'  => get_permalink($query->post->ID),
          'title' => $query->post->post_title
        ];

        if (has_post_thumbnail($query->post->ID)) {
          $postThumbnailId = get_post_thumbnail_id($query->post->ID);
          foreach ($this->aDefineSizeImage as $imgSizeKey => $frontendKey) {
            $aImageSize = wp_get_attachment_image_src($postThumbnailId, $imgSizeKey);
            if (!empty($aImageSize)) {
              $aPost['featuredImage'][$frontendKey] = [
                'src'    => $aImageSize[0],
                'width'  => $aImageSize[1],
                'height' => $aImageSize[2]
              ];
            }
          }
        }

        $aPostList[] = $aPost;
      }
    }
    wp_reset_postdata();

    return $aPostList;
  }

  public function handle($aField, $aSettings): array
  {
    $aConfiguration = $aSettings[$aField['id']] ?? $aField['default'];
    $aConfiguration = wp_parse_args($aConfiguration, $aField['default']);

    $aArgs = [
      'order'      => $aConfiguration['order'],
      'order_by'   => $aConfiguration['orderBy'],
      'hide_empty' => $aConfiguration['hideEmpty'],
      'number'     => empty($aConfiguration['categories']) ? $aConfiguration['limit'] :
        count($aConfiguration['categories']),
      'taxonomy'   => $aConfiguration['taxonomy']
    ];

    if (!empty($aConfiguration['categories'])) {
      $aArgs['include'] = array_map(function ($aTerm) {
        return $aTerm['id'];
      }, $aConfiguration['categories']);
    }

    $aTerms = get_terms($aArgs);
    if (empty($aTerms) || is_wp_error($aTerms)) {
      return ['items' => []];
    }

    $aItems = [];
    foreach ($aTerms as $oTerm) {
      $aTerm = get_object_vars($oTerm);
      $featuredImageId = get_term_meta($oTerm->term_id, 'wiloke_image_id', true);
      if ($featuredImageId) {
        foreach ($this->aDefineSizeImage as $imgSizeKey => $frontendKey) {
          $aImageSize = wp_get_attachment_image_src($featuredImageId, $imgSizeKey);
          if (!empty($aImageSize)) {
            $aTerm['featuredImage'][$frontendKey] = [
              'src'    => $aImageSize[0],
              'width'  => $aImageSize[1],
              'height' => $aImageSize[2]
            ];
          }
        }
      }

      if ($aConfiguration['postType']) {
        $aTerm['posts'] = $this->getLastFivePosts($aConfiguration, $oTerm->term_id);
      }

      $aItems[] = $aTerm;
    }

    return $aItems;
  }
}