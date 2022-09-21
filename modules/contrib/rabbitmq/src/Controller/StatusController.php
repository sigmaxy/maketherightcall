<?php

namespace Drupal\rabbitmq\Controller;

use Drupal\Core\Site\Settings;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\rabbitmq\Queue\Queue;
use Drupal\rabbitmq\Queue\QueueFactory;
use PhpAmqpLib\Package;
use Symfony\Component\Yaml\Yaml;

/**
 * RabbitMq status controller.
 */
class StatusController {

  use StringTranslationTrait;

  /**
   * Controller for rabbitmq.properties.
   *
   * @return array
   *   A render array.
   */
  public function report(): array {
    try {
      $backend = \Drupal::queue('queue');
    }
    catch (\ErrorException $e) {
      return $this->errorResponse('Could not access the queue.');
    }

    if (!$backend instanceof Queue) {
      return $this->errorResponse(
        'RabbitMQ queue is reachable, but its service is not configured.'
      );
    }

    $serverProperties = $backend
      ->getChannel()
      ->getConnection()
      ->getServerProperties();

    $libraryProperties = [
      $this->t('Product: @product', ['@product' => Package::NAME]),
      $this->t('Version: @version', ['@version' => Package::VERSION]),
    ];

    $build = [
      'server' => [
        '#type' => 'details',
        '#title' => $this->t('Server properties'),
        '#open' => TRUE,
        'properties' => [
          '#markup' => '<pre>' . Yaml::dump($serverProperties, 3, 2) . '</pre>',
        ],
      ],

      'driver' => [
        '#type' => 'details',
        '#title' => $this->t('Driver library properties'),
        '#open' => TRUE,
        'properties' => [
          '#theme' => 'item_list',
          '#items' => $libraryProperties,
        ],
      ],
    ];

    return $build;
  }

  /**
   * Helper to return an error message to page.
   *
   * @param string $message
   *   Error message to return.
   *
   * @return array
   *   Response render array.
   */
  private function errorResponse(string $message): array {
    $build = [
      '#markup' => $this->t('<h2>Error</h2><p>@message Check the <a href=":url">status page</a>.</p>',
        [
          '@message' => $message,
          ':url' => Url::fromRoute('system.status')->toString(),
        ]),
    ];

    if (Settings::get('queue_default') == 'queue.rabbitmq.default') {
      QueueFactory::overrideSettings();
    }

    return $build;
  }

}
