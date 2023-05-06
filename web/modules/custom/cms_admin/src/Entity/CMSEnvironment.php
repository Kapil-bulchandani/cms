<?php

namespace Drupal\cms_admin\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\user\UserInterface;

/**
 * Defines the CMS Environment entity.
 *
 * @ingroup cms_admin
 *
 * @ContentEntityType(
 *   id = "cms_environment",
 *   label = @Translation("CMS Environment"),
 *   label_collection = @Translation("CMS Environments"),
 *   label_singular = @Translation("CMS Environment"),
 *   label_plural = @Translation("CMS Environments"),
 *   label_count = @PluralTranslation(
 *     singular = "@count CMS Environment",
 *     plural = "@count CMS Environments",
 *   ),
 *   handlers = {
 *     "storage" = "Drupal\Core\Entity\Sql\SqlContentEntityStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cms_admin\Entity\CMSEnvironmentListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\cms_admin\Form\CMSEnvironmentForm",
 *       "add" = "Drupal\cms_admin\Form\CMSEnvironmentForm",
 *       "edit" = "Drupal\cms_admin\Form\CMSEnvironmentForm",
 *       "delete" = "Drupal\cms_admin\Form\CMSEnvironmentDeleteForm",
 *     },
 *   },
 *   base_table = "cms_environment",
 *   revision_table = "cms_environment_revision",
 *   data_table = "cms_environment_field_data",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "label",
 *     "uid" = "uid",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/cms-environment/{cms_environment}",
 *     "add-form" = "/admin/content/cms-environment/add",
 *     "edit-form" = "/admin/content/cms-environment/{cms_environment}/edit",
 *     "delete-form" = "/admin/content/cms-environment/{cms_environment}/delete",
 *     "collection" = "/admin/content/cms-environment",
 *   },
 *   field_ui_base_route = "entity.cms_environment.settings",
 * )
 */


class CMSEnvironment extends ContentEntityBase implements CMSEnvironmentInterface {
  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'uid' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getUid() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setUid(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDomainName() {
    return $this->get('domain_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDomainName($domain_name) {
    $this->set('domain_name', $domain_name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRegion() {
    return $this->get('region')->value;
  }


  /**
   * {@inheritdoc}
   */
  public function setRegion($region) {
    $this->set('region', $region);
    return $this;
  }


  /**
   * {@inheritdoc}
   */
  public function getServerSize() {
    return $this->get('server_size')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setServerSize($server_size) {
    $this->set('server_size', $server_size);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getIpAddress() {
    return $this->get('ip_address')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setIpAddress($ip_address) {
    $this->set('ip_address', $ip_address);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getTheme() {
    return $this->get('theme')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTheme($theme) {
    $this->set('theme', $theme);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getDropletId() {
    return $this->get('droplet_id')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setDropletId($droplet_id) {
    $this->set('droplet_id', $droplet_id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function status() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the CMS Environment entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the CMS Environment entity.'))
      ->setReadOnly(TRUE);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setDescription(t('The label of the CMS Environment entity.'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the CMS Environment entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDescription(t('Server status.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 90,
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'boolean',
        'weight' => 90,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['domain_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Domain Name'))
      ->setDescription(t('The domain name of the CMS Environment entity.'))
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 2,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['droplet_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Droplet ID'))
      ->setDescription(t('The Digital Ocean droplet id.'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 3,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['region'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Region'))
      ->setDescription(t('The servers region.'))
      ->setDefaultValue('nyc1')
      ->setSetting('allowed_values', [
        'nyc1' => 'New York',
        'sfo3' => 'San Francisco',
      ])
      ->setDisplayOptions('form', [
        'type' => 'select',
        'weight' => 4,
        'options' => [
          'nyc1' => 'New York',
          'sfo3' => 'San Francisco',
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['server_size'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Server Size'))
      ->setDescription(t('The size of the server used by the CMS Environment.'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['ip_address'] = BaseFieldDefinition::create('string')
      ->setLabel(t('IP Address'))
      ->setDescription(t('The IP address of the server used by the CMS Environment.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayConfigurable('view', TRUE);

    $fields['theme'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Theme'))
      ->setDescription(t('The theme used by the CMS Environment.'))
      ->setDefaultValue('light')
      ->setSetting('allowed_values', [
        'dark' => 'Dark',
        'blue' => 'Blue',
        'light' => 'Light',
      ])
      ->setSettings([
        'type' => 'select',
        'weight' => 6,
        'options' => [
          'dark' => 'Dark',
          'blue' => 'Blue',
          'light' => 'Light',
        ],
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['machine_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Machine Name'))
      ->setDescription(t('The machine name of the environment.'))
      ->setSettings([
        'max_length' => 255,
        'is_ascii' => TRUE,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['github_repo'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Github Repo'))
      ->setDescription(t('Github repo for the project.'))
      ->setSettings([
        'max_length' => 255,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 15,
      ])
      ->setDisplayConfigurable('view', TRUE);


    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCanonicalUrl() {
    return \Drupal\Core\Url::fromRoute('entity.cms_environment.canonical', [
      'cms_environment' => $this->id(),
    ])->setAbsolute()->toString();
  }
}
