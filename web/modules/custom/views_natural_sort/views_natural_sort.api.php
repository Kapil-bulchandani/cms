<?php

/**
 * @file
 * Hook documentation for Views Natural Sort.
 */

/**
 * Allows alteration of supported properties.
 *
 * This is implemented since automatic detection of fields and their base tables
 * are hard and has been a feature request for years. This at least allows a
 * developer to make headway on implementing it in a very pointed manner using
 * a bit of custom code.
 *
 * @param array $supported_properties
 *   A structure that looks like this:
 *   [
 *     'entity_type' => [
 *       'property_name' => [
 *         'base_table' => 'table',
 *         'schema_field' => 'property',
 *       ]
 *     ]
 *   ].
 *
 * @see ViewsNaturalSortService::getSupportedEntityProperties()
 */
function hook_views_natural_sort_supported_properties_alter(array &$supported_properties) {
  // Allow the vns_field value to be naturally sorted.
  $supported_properties['node']['field_vns_field'] = [
    'base_table' => 'node__field_vns_field',
    'schema_field' => 'field_vns_field_value',
  ];
}

/**
 * Allows alteration of types of transformations to happen for natural sorting.
 *
 * Applications for this may be for use with different languages, or fields
 * holding different data other than 'titles', such as people's names, or
 * abbreviations.
 *
 * @param \Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationInterface[] $transformations
 *   An array of IndexRecordContentTransformation objects.
 * @param \Drupal\views_natural_sort\IndexRecord $record
 *   The current information being transformed so that it sorts naturally.
 */
function hook_views_natural_sort_transformations_alter(array &$transformations, \Drupal\views_natural_sort\IndexRecord $record) {
  if ($record->getEntityType() == 'user' && $record->getField() == 'timezone') {
    // This assumes you made a new IndexRecordContentTransformation plugin with
    // the machine_name of 'timezone' and want to get rid of all the other
    // transformations in order to order timezones in some kind of order.
    $transformations = ['timezone'];
  }
}

/**
 * Provides developers a way to limit the entity properties that vns affects.
 */
function hook_views_natural_sort_get_entry_types() {

}

/**
 * Provides developers a way to alter records before they are saved.
 */
function hook_views_natural_sort_queue_rebuld_data(\Drupal\views_natural_sort\IndexRecordType $entry_type) {

}
