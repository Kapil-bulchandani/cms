<?php

namespace Drupal\views_natural_sort\Plugin\IndexRecordContentTransformation;

use Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationBase as TransformationBase;

/**
 * Plugin for sorting days of the week in day order.
 *
 * @IndexRecordContentTransformation (
 *   id = "days_of_the_week",
 *   label = @Translation("Days of the Week")
 * )
 */
class DaysOfTheWeek extends TransformationBase {

  /**
   * {@inheritdoc}
   */
  public function transform($string) {
    return $string;
  }

}
