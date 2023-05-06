<?php

namespace Drupal\views_natural_sort_test\Plugin\IndexRecordContentTransformation;

use Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationBase as TransformationBase;

/**
 * Plugin for transforming numbers so that they sort naturally as a chapter.
 *
 * This plugin should precede the number plugin. It's purpose is to change the
 * decimal ahead of time so that the number formatter will then not think the
 * chapter is a decimal.
 *
 * @IndexRecordContentTransformation (
 *   id = "chapter_number_prep",
 *   label = @Translation("Chapter Number Preparation")
 * )
 */
class ChapterNumberPrep extends TransformationBase {

  /**
   * Strip out the decimal and make it a space.
   *
   * @param string $string
   *   The string we wish to transform.
   */
  public function transform($string) {
    // Find an optional leading dash (either preceded by whitespace or the first
    // character) followed by either:
    // - an optional series of digits (with optional embedded commas), then a
    //   period, then an optional series of digits
    // - a series of digits (with optional embedded commas)
    return preg_replace_callback(
      '/(\s-|^-)?(?:(\d[\d,]*)?\.(\d+)|(\d[\d,]*))/',
      [$this, 'numberTransformMatch'],
      $string
    );
  }

  /**
   * Transforms a string representing numbers into a special format.
   *
   * This special format can be sorted as if it was a number but in reality is
   *   being sorted alphanumerically.
   *
   * @param array $match
   *   Array of matches passed from preg_replace_callback
   *   $match[0] is the entire matching string
   *   $match[1] if present, is the optional dash, preceded by optional
   *     whitespace
   *   $match[2] if present, is whole number portion of the decimal number
   *   $match[3] if present, is the fractional portion of the decimal number
   *   $match[4] if present, is the integer (when no fraction is matched).
   *
   * @return string
   *   String replacing all the decimals with spaces so it will not sort as a
   *   decimal later.
   */
  private function numberTransformMatch(array $match) {
    return str_replace('.', ' ', $match[0]);
  }
}
