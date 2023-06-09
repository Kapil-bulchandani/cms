<?php

namespace Drupal\views_natural_sort\Plugin\IndexRecordContentTransformation;

use Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationBase as TransformationBase;

/**
 * Plugin for removing unsortable symbols.
 *
 * @IndexRecordContentTransformation (
 *   id = "remove_symbols",
 *   label = @Translation("Remove Symbols")
 * )
 */
class RemoveSymbols extends TransformationBase {

  /**
   * {@inheritDoc}
   */
  public function transform($string) {
    $symbols = $this->configuration['settings'];
    if (strlen($symbols) == 0) {
      return $string;
    }
    return preg_replace(
      '/[' . preg_quote($symbols) . ']/u',
      '',
      $string
    );
  }

}
