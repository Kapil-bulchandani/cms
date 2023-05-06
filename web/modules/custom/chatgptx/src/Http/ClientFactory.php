<?php

namespace Drupal\chatgptx\Http;

use Drupal\Core\Config\ConfigFactoryInterface;
use chatgptx\Client;

/**
 * Service for generating OpenAI clients.
 */
class ClientFactory {

  /**
   * The config settings object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * Constructs a new ClientFactory instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->config = $config_factory->get('chatgptx.settings');
  }

  /**
   * Creates a new OpenAI client instance.
   *
   * @return \chatgptx\Client
   *   The client instance.
   */
  public function create(): Client {
    return \chatgptx::client($this->config->get('api_key'), $this->config->get('api_org'));
  }

}
