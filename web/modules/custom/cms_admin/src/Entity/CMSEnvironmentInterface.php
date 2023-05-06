<?php

namespace Drupal\cms_admin\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\user\UserInterface;

/**
 * Provides an interface for defining CMS Environment entities.
 *
 * @ingroup cms_admin
 */
interface CMSEnvironmentInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Gets the CMS Environment status.
   *
   * @return bool
   *   TRUE if the CMS Environment is enabled, FALSE otherwise.
   */
  public function getStatus();

  /**
   * Sets the CMS Environment status.
   *
   * @param bool $status
   *   TRUE to enable this CMS Environment, FALSE to disable.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setStatus($status);

  /**
   * Gets the CMS Environment domain name.
   *
   * @return string
   *   Domain name of the CMS Environment.
   */
  public function getDomainName();

  /**
   * Sets the CMS Environment domain name.
   *
   * @param string $domain_name
   *   The CMS Environment domain name.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setDomainName($domain_name);

  /**
   * Gets the CMS Environment region.
   *
   * @return string
   *   Region of the CMS Environment.
   */
  public function getRegion();

  /**
   * Sets the CMS Environment region.
   *
   * @param string $region
   *   The CMS Environment region.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setRegion($region);

  /**
   * Gets the CMS Environment server size.
   *
   * @return string
   *   Server size of the CMS Environment.
   */
  public function getServerSize();

  /**
   * Sets the CMS Environment server size.
   *
   * @param string $server_size
   *   The CMS Environment server size.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setServerSize($server_size);

  /**
   * Gets the CMS Environment IP address.
   *
   * @return string
   *   IP address of the CMS Environment.
   */
  public function getIpAddress();

  /**
   * Sets the CMS Environment IP address.
   *
   * @param string $ip_address
   *   The CMS Environment IP address.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setIpAddress($ip_address);

  /**
   * Gets the CMS Environment Theme.
   *
   * @return string
   *   Theme of the CMS Environment.
   */
  public function getTheme();

  /**
   * Sets the CMS Environment Theme.
   *
   * @param string $theme
   *   The CMS Environment theme.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setTheme($theme);

  /**
   * Gets the CMS Environment User ID.
   *
   * @return string
   *   User ID of the CMS Environment.
   */
  public function getUid();

  /**
   * Sets the CMS Environment User Id.
   *
   * @param UserInterface $uid
   *   The CMS Environment User ID.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setUid(UserInterface $account);

  /**
   * Gets the CMS Environment creation timestamp.
   *
   * @return int
   *   Creation timestamp of the CMS Environment.
   */
  public function getCreatedTime();

  /**
   * Sets the CMS Environment creation timestamp.
   *
   * @param int $timestamp
   *   The CMS Environment creation timestamp.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setCreatedTime($timestamp);


  /**
   * Gets the CMS Environment droplet ID.
   *
   * @return int
   *   Creation timestamp of the CMS Environment.
   */
  public function getDropletId();

  /**
   * Sets the CMS Environment droplet ID.
   *
   * @param int $droplet_id
   *   The CMS Environment droplet ID.
   *
   * @return \Drupal\cms_admin\Entity\CMSEnvironmentInterface
   *   The called CMS Environment entity.
   */
  public function setDropletId($droplet_id);


  /**
   * Gets the CMS Environment status.
   *
   * @return int
   *   Status of the CMS Environment.
   */
  public function status();

}
