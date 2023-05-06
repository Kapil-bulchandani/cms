<?php

namespace Drupal\cms_admin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;
use Drupal\cms_admin\doHelper;
use Drupal\Core\Render\HtmlResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for CMS Environment entities.
 */
class CMSEnvironmentController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructor for CMSEnvironmentController.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns the title for a CMS Environment entity page.
   *
   * @param \Drupal\Core\Entity\EntityInterface $cms_environment
   *   The CMS Environment entity.
   *
   * @return string
   *   The page title.
   */
  public function title(EntityInterface $cms_environment) {
    return $cms_environment->label();
  }

  /**
   * Update droplet's status.
   *
   * @param \Drupal\Core\Entity\EntityInterface $cms_environment
   *   The CMS Environment entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   Redirect to site info page.
   */
  public function updateStatus(EntityInterface $cms_environment) {

    if ($droplet_id = $cms_environment->getDropletId()) {
      $do_save = false;
      $doHelper = new doHelper();
      $status = $doHelper->getDropletStatus($droplet_id);
      if($status === 'off' && $cms_environment->getStatus()) {
        $cms_environment->setStatus(false);
        $do_save = true;
      }
      elseif ($status !== 'off' && !$cms_environment->getStatus()) {
        $cms_environment->setStatus(true);
        $do_save = true;
      }

      // Set IP address
      if (!$cms_environment->getIpAddress()) {
        $ip = $doHelper->getDropletIp($droplet_id);
        if ($ip) {
          $cms_environment->setIpAddress($ip);
          $do_save = true;
        }
      }

      // Save CMS Environment node.
      if ($do_save) {
        $cms_environment->save();
      }

    }
    $response = $this->redirect('entity.cms_environment.canonical', ['cms_environment' => $cms_environment->id()]);

    return $response;
  }

  public function testPage() {

    $doHelper = new doHelper();
$sizes = $doHelper->getSizes();
    $rows = [];
    foreach ($sizes as $size) {
      $rows[] = [
        'data' => [
          $size->slug,
          $size->memory,
          $size->vcpus,
          $size->disk,
          $size->transfer,
          $size->price_monthly,
          $size->price_hourly,
          $size->description,
        ],
      ];
    }

    $header = [
      'Slug',
      'Memory',
      'vCPUs',
      'Disk',
      'Transfer',
      'Price Monthly',
      'Price Hourly',
      'Description',
    ];

    $table = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => [
        'class' => ['digitalocean-sizes-table'],
      ],
    ];
$content = \Drupal::service('renderer')->render($table);
    return new Response($content);

  }

}
