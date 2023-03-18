<?php

/**
 * @file
 * Wait for RabbitMQ container to start.
 */

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPIOException;

// Autoload vendor classes.
// DrupalCi keeps vendor in the Drupal root.
require_once __DIR__ . '/../../../../../../vendor/autoload.php';

$sleep_time = 5;
$max_attempts = 6;

for ($i = 0; $i < $max_attempts; $i++) {

  try {

    print('Attempting to connect to RabbitMq server' . PHP_EOL);
    new AMQPStreamConnection(
      'rabbitmq',
      5672,
      'guest',
      'guest',
      '/',
      FALSE,
      'AMQPLAIN',
      NULL,
      'en_US',
      1.0,
      1.0
    );
  }
  catch (AMQPIOException $e) {
    print("RabbitMq server unavailable, will wait $sleep_time second(s) before trying again." . PHP_EOL);
    sleep($sleep_time);
    continue;
  }
  catch (\Exception $e) {
    print("An unexpected error occurred. will wait $sleep_time second(s) before trying again." . PHP_EOL);
    sleep($sleep_time);
    continue;
  }

  print('RabbitMq server online' . PHP_EOL);
  exit(0);
}

print("RabbitMQ Server was not online after $max_attempts attempt(s)" . PHP_EOL);
exit(1);
