<?php

namespace Drupal\views_natural_sort\Plugin\IndexRecordContentTransformation;

use Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationBase as TransformationBase;

/**
 * Plugin for removing words that we shouldn't use during sorting.
 *
 * @IndexRecordContentTransformation (
 *   id = "remove_words",
 *   label = @Translation("Remove Words")
 * )
 */
class RemoveWords extends TransformationBase {

  /**
   * {@inheritdoc}
   */
  public function transform($string) {
    $words = $this->configuration['settings'];
    if (empty($words) || !is_array($words)) {
      return $string;
    }

    array_walk($words, 'preg_quote');
    return preg_replace(
      [
        '/\s(' . implode('|', $words) . ')\s+/iu',
        '/^(' . implode('|', $words) . ')\s+/iu',
      ],
      [
        ' ',
        '',
      ],
      $string
    );
  }

}
