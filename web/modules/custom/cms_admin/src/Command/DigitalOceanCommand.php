<?php

namespace Drupal\cms_admin\Command;

use DigitalOceanV2\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DigitalOceanCommand extends Command {

  protected static $defaultName = 'cms_admin:digital-ocean';

  /**
   * @var \DigitalOceanV2\Client
   */
  protected $digitalOceanClient;

  public function __construct(Client $digitalOceanClient) {
    parent::__construct();
    $this->digitalOceanClient = $digitalOceanClient;
  }

  protected function configure() {
    $this->setDescription('Example Digital Ocean command');
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    // Implement your Digital Ocean command here using the $this->digitalOceanClient object
    // For example:
    $droplets = $this->digitalOceanClient->droplet()->getAll();
    foreach ($droplets as $droplet) {
      $output->writeln(sprintf('Droplet %d: %s', $droplet->id, $droplet->name));
    }
    return Command::SUCCESS;
  }

}
