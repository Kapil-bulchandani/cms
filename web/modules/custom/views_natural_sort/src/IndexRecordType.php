<?php

namespace Drupal\views_natural_sort;

/**
 * An object that helps define record types for processing in VNS.
 */
class IndexRecordType {

  /**
   * String representing a Drupal entity type.
   *
   * @var string
   */
  protected $entityType;
  /**
   * The machine name for a field or propety on a Drupal Entity.
   *
   * @var string
   */
  protected $field;

  /**
   * Create a new record type.
   *
   * @param string $entity_type_id
   *   A Drupal entity id string.
   * @param string $field_machine_name
   *   A Drupal field or property machine name.
   */
  public function __construct($entity_type_id, $field_machine_name) {
    $this->setEntityType($entity_type_id);
    $this->setField($field_machine_name);
  }

  /**
   * Get the entity type.
   *
   * @return string
   *   A Drupal entity id string.
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * Set the entity type.
   *
   * @param string $entity_type_id
   *   A Drupal entity id string.
   */
  public function setEntityType($entity_type_id) {
    $this->entityType = $entity_type_id;
  }

  /**
   * Get the entity field machine name.
   *
   * @return string
   *   A Drupal field or property machine name.
   */
  public function getField() {
    return $this->field;
  }

  /**
   * Set the entity field machine name.
   *
   * @param string $field_machine_name
   *   A Drupal field or property machine name.
   */
  public function setField($field_machine_name) {
    $this->field = $field_machine_name;
  }

}
