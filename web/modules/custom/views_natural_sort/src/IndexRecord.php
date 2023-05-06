<?php

namespace Drupal\views_natural_sort;

use Drupal\Core\Database\Connection;

/**
 * An object that houses a VNS record that can be stored in the database.
 */
class IndexRecord {
  /**
   * A Drupal Entity Id.
   *
   * @var mixed
   */
  protected $eid;

  /**
   * A Drupal entity type.
   *
   * @var string
   */
  protected $entityType;

  /**
   * A property name.
   *
   * @var string
   */
  protected $field;

  /**
   * The cardenailty or weight of a property value when there is more than one.
   *
   * @var int
   */
  protected $delta;

  /**
   * The content of the property.
   *
   * @var string
   */
  protected $content;

  /**
   * Allow tranformation on this property.
   *
   * @var \Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationInterface[]
   */
  protected $transformations = [];

  /**
   * Database Connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * Create a new Index Record for views sorting.
   */
  public function __construct(Connection $database, array $values = []) {
    $this->database = $database;
    $this->setEntityId($values['eid']);
    $this->setEntityType($values['entity_type']);
    $this->setField($values['field']);
    $this->setDelta($values['delta']);
    $this->setContent($values['content']);
  }

  /**
   * Set the Entity Id property.
   *
   * @param mixed $eid
   *   A Drupal entity ID.
   */
  public function setEntityId($eid) {
    $this->eid = $eid;
  }

  /**
   * Get the Entity Id property.
   *
   * @return mixed
   *   A Drupal entity Id.
   */
  public function getEntityId() {
    return $this->eid;
  }

  /**
   * Set the Entity Type property.
   *
   * @param string $entity_type
   *   A Drupal entity type name.
   */
  public function setEntityType($entity_type) {
    $this->entityType = $entity_type;
    $this->generateType();
  }

  /**
   * Get the Entity Type property.
   *
   * @return string
   *   A string representing a drupal entity type.
   */
  public function getEntityType() {
    return $this->entityType;
  }

  /**
   * Set the entity field name.
   *
   * @param string $field
   *   The field name.
   */
  public function setField($field) {
    $this->field = $field;
    $this->generateType();
  }

  /**
   * Get the entity field name.
   *
   * @return string
   *   A machine name for a field or property on an entity.
   */
  public function getField() {
    return $this->field;
  }

  /**
   * Set the delta for the property entry.
   *
   * @param int $delta
   *   The number of the value in a property for an entity.
   */
  public function setDelta($delta) {
    $this->delta = $delta;
  }

  /**
   * Get the delta for the property entry.
   *
   * @return int
   *   The number of the value in a property for and entity in this record.
   */
  public function getDelta() {
    return $this->delta;
  }

  /**
   * Set the value of the property for this record.
   *
   * @param string $string
   *   A string representing the content to be transformed to be stored.
   */
  public function setContent($string) {
    $this->content = $string;
  }

  /**
   * Get the value of the property for this record.
   *
   * @return string
   *   A string representing the content to be transformed to be stored.
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * Set the transformations that should happen to the content in this record.
   *
   * @param \Drupal\views_natural_sort\Plugin\IndexRecordTransformationInterface[] $transformations
   *   An array of transformations to apply to the record content.
   */
  public function setTransformations(array $transformations) {
    $this->transformations = $transformations;
  }

  /**
   * Get the transformations that should happen to the content in this record.
   *
   * @return \Drupal\views_natural_sort\Plugin\IndexRecordContentTransformationInterface[]
   *   An array of transformations that will be applied to the record content.
   */
  public function getTransformations() {
    return $this->transformations;
  }

  /**
   * Get the transformed content for this record.
   *
   * @return string
   *   A string representing the transformed content that will allow this record
   *   to be sorted.
   */
  public function getTransformedContent() {
    $transformed_content = $this->content;
    foreach ($this->transformations as $transformation) {
      $transformed_content = $transformation->transform($transformed_content);
    }
    return mb_substr($transformed_content, 0, 255);
  }

  /**
   * A factory to get the index record type.
   */
  private function generateType() {
    $this->type = new IndexRecordType($this->entityType, $this->field);
  }

  /**
   * Get the record type object.
   *
   * @return \Drupal\views_natural_sort\IndexRecordType
   *   The record type object.
   */
  public function getType() {
    return $this->type;
  }

  /**
   * Save the record into the database.
   */
  public function save() {
    $this->database->merge('views_natural_sort')
      ->key([
        'eid' => $this->eid,
        'entity_type' => $this->entityType,
        'field' => $this->field,
        'delta' => $this->delta,
      ])
      ->fields([
        'eid' => $this->eid,
        'entity_type' => $this->entityType,
        'field' => $this->field,
        'delta' => $this->delta,
        'content' => $this->getTransformedContent(),
      ])
      ->execute();
  }

  /**
   * Delete the record from the database.
   */
  public function delete() {
    $this->database->delete('views_natural_sort')
      ->condition('eid', $this->eid)
      ->condition('entity_type', $this->entityType)
      ->condition('field', $this->field)
      ->condition('delta', $this->delta)
      ->execute();
  }

}
