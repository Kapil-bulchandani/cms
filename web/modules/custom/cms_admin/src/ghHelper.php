<?php
namespace Drupal\cms_admin;

use Exception;
use Github\Client;
use Github\AuthMethod;
use Github\Exception\RuntimeException;
use Symfony\Component\HttpClient\HttplugClient;

class ghHelper {

  private $client;

  public function __construct($accessToken) {
    $this->client = new Client();
    $this->client->authenticate($accessToken, null, AuthMethod::ACCESS_TOKEN);
  }

  public function getAllRepos() {
  }

  /**
   * @throws \Exception
   */
  public function forkRepo($owner, $repo, $params = []) {
    try {
      $response = $this->client->api('repo')->forks()->create($owner, $repo, $params);
      return $response['full_name'];
    } catch (RuntimeException $e) {
      throw new Exception('Failed to fork repository: ' . $e->getMessage());
    }
  }

  /**
   * @throws \Exception
   */
  public function removeRepo($owner, $repo) {
    try {
      return $this->client->api('repo')->remove($owner, $repo);
    } catch (RuntimeException $e) {
      throw new Exception('Failed to fork repository: ' . $e->getMessage());
    }
  }


}
