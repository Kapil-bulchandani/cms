<?php

namespace Drupal\cms_admin;

use DigitalOceanV2\Client;
use DigitalOceanV2\ResultPager;
use Drupal\Core\Entity\EntityInterface;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;

class doHelper {


  /**
   * @var \DigitalOceanV2\Client
   */
  protected $client;

  /**
   * @var string
   */
  protected string $key;

  /**
   * Construct an object.
   */
  public function __construct() {
    $this->client = new Client();
    $config = \Drupal::configFactory()->get('cms_admin.settings');
    $key_id = $config->get('secret_key');
    if(!$key_id) {
      \Drupal::messenger()->addWarning('Please, add keys for DigitalOcean and GitHub');
      $path = \Drupal\Core\Url::fromRoute('cms_admin.settings')->toString();
      $response = new RedirectResponse($path);
      $response->send();
    }
    $this->key = \Drupal::service('key.repository')->getKey($key_id)->getKeyValue();
  }

  /**
   * Authentication.
   *
   * @return void
   */
  private function auth() {
    $this->client->authenticate($this->key);
  }

  /**
   * Create a new droplet.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *
   * @return \DigitalOceanV2\Entity\Droplet|\DigitalOceanV2\Entity\Droplet[]|null
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function createDroplet(EntityInterface $entity) {

    $name = preg_replace('@[^a-z0-9-]+@', '-', strtolower($entity->label()));
    $path = getcwd() . '/' . \Drupal::service('extension.list.module')
        ->getPath('cms_admin');
    $user_data = $path . '/script/userData.sh';
    $handle = fopen($user_data, "r");
    $user_data_text = fread($handle, filesize($user_data));
    fclose($handle);

    $user_data_text = str_replace('Sitename', $entity->label(), $user_data_text);
    $this->auth();
    $droplet = $this->client->droplet();

    try {
      $created = $droplet->create(
        $name,
        $entity->getRegion(),
        $entity->getServerSize(),
        130191202,
        FALSE,
        FALSE,
        FALSE,
        [],
        $user_data_text,
        TRUE,
        [],
        ['cms:env']
      );

      return $created;
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
    }
    return NULL;
  }

  /**
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function getAllDroplets() {
    $this->auth();

    try {
      $pager = new ResultPager($this->client);
      return $pager->fetchAll($this->client->droplet(), 'getAll');
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
    }
    return NULL;
  }

  /**
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function getSizes() {
    $this->auth();

    try {
      $size = $this->client->size();
      return $size->getAll();
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
    }
    return NULL;
  }

  /**
   * @param $id
   *   Droplet id.
   *
   * @return void
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function deleteDroplet($id) {
    $this->auth();
    try {
    $droplet = $this->client->droplet();
    $droplet->remove($id);
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
    }
  }

  /**
   * Stop Droplet.
   * @param $id
   *   Droplet ID.
   *
   * @return void
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function stopDroplet($id) {
    $this->auth();
    try {
      $droplet = $this->client->droplet();
      $droplet->powerOff($id);
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
    }
  }

  /**
   * Start droplet.
   * @param $id
   *   Droplet id.
   *
   * @return void
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function startDroplet($id) {
    $this->auth();
    try {
      $droplet = $this->client->droplet();
      $droplet->powerOn($id);
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
    }
  }

  /**
   * Resize droplet.
   *
   * @param $id
   *   Droplet id.
   *
   * @return bool
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function resizeDroplet($id, $size) {
    $this->auth();
    try {
      $droplet = $this->client->droplet();
      $droplet->resize($id, $size);
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
      \Drupal::messenger()->addError($e->getMessage());
      return false;
    }
    return true;
  }

  /**
   * Get droplet info.
   *
   * @param $id
   *
   * @return \DigitalOceanV2\Entity\Droplet|false
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function getDropletInfo($id) {
    $this->auth();
    try {
      $droplet = $this->client->droplet();
      return $droplet->getById($id);
    } catch (Exception $e) {
      \Drupal::logger('cms-admin')->error($e->getMessage());
      return false;
    }
  }

  /**
   * Get droplet's IP address.
   *
   * @param $id
   *
   * @return string|null
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function getDropletIp($id) {
    $droplet = $this->getDropletInfo($id);
    foreach ($droplet->networks as $network) {
      if ($network->type == 'public' && $network->version == 4) {
        return $network->ipAddress;
      }
    }
    return NULL;
  }

  /**
   * Get droplet's status.
   *
   * @param $id
   *
   * @return string
   * @throws \DigitalOceanV2\Exception\ExceptionInterface
   */
  public function getDropletStatus($id) {
    $droplet = $this->getDropletInfo($id);
    return $droplet->status;
  }


}
