# RabbitMQ Integration

[![Build Status](https://travis-ci.org/FGM/rabbitmq.svg?branch=travis)](https://travis-ci.org/FGM/rabbitmq)
[![Code Coverage](https://scrutinizer-ci.com/g/bimsonz/rabbitmq/badges/coverage.png?b=8.x-2.x)](https://scrutinizer-ci.com/g/bimsonz/rabbitmq/?branch=8.x-2.x)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bimsonz/rabbitmq/badges/quality-score.png?b=8.x-2.x)](https://scrutinizer-ci.com/g/bimsonz/rabbitmq/?branch=8.x-2.x)

## INTRODUCTION


# REQUIREMENTS

* RabbitMQ server needs to be installed and configured.
* Drupal must be configured with `php-amqplib`  
    * go to the root directory of your site
    * edit `composer.json` (not `core/composer.json`)
    * insert `"php-amqplib/php-amqplib": "^3.1"` in the `require` section of 
      the file.
    * Optional, but recommended: insert `"ext-pcntl": "*"` in the `require` 
      section of the file. Ensure your PHP actually includes that standard
      extension. Without it, the timeout mechanism for the consumer service will
      not be available.
    * Save it.
    * update your `vendor` directory by typing `composer update`.

# INSTALLATION

* Provide connection credentials as part of the `$settings` global variable in 
  `settings.local.php` (see the `example.settings.local.php` provided)

        $settings['rabbitmq_credentials']['default'] = [
          'host' => 'localhost',
          'port' => 5672,
          'vhost' => '/',
          'username' => 'guest',
          'password' => 'guest',
        ];

# CONFIGURATION

* Configure RabbitMQ as the queuing system for the queues you want RabbitMQ to 
  maintain, either as the default queue service, default reliable queue service,
  or specifically for each queue:
    * If you want to set RabbitMQ as the default queue manager, then add the 
      following to your settings.

          $settings['queue_default'] = 'queue.rabbitmq.default';
    * Alternatively you can also set for each queue to use RabbitMQ using one 
      of these formats:

          $settings['queue_service_{queue_name}'] = 'queue.rabbitmq.default';
          $settings['queue_reliable_service_{queue_name}'] = 'queue.rabbitmq.default';


# CUSTOMIZATION

Modules may override queue or exchange defaults built in a custom module by 
implementing `config/install/rabbitmq.config.yml`. See 
`src/Queue/QueueBase.php` and `src/Tests/RabbitMqTestBase::setUp()` for details.

# SSL

It is similar to the normal connection array, but you need to add 2 extra array 
keys.

This is an example of how `settings.php` should look like:

```
$settings['rabbitmq_credentials']['default'] = [
  'host' => 'host',
  'port' => 5672,
  'vhost' => '/',
  'username' => 'guest',
  'password' => 'guest',
  'ssl' => [
    'verify_peer_name' => false,
    'verify_peer' => false,
    'local_pk' => '~/.ssh/id_rsa',
  ],
  'options' => [
    'connection_timeout' => 20,
    'read_write_timeout' => 20,
  ],
];
```
# INSTRUCTIONS FOR RUNNING TESTS

Test execution requires a RabbitMq server to be available at the host
`rabbitmq` with credentials `guest:guest`.

* An example to execute tests with RabbitMQ via Docker:
  * Prepare the Drupal install to run PHPUnit Tests. See
    [Running PHPUnit tests](https://www.drupal.org/docs/automated-testing/phpunit-in-drupal/running-phpunit-tests):
  * Add `127.0.0.1 rabbitmq` to `/etc/hosts`
  * Start RabbitMQ:
    `docker run -p 5672:5672 -d --hostname rabbitmq --name rabbitmq_rabbitmq --rm rabbitmq:3`
  * Run tests as normal:
    `SIMPLETEST_DB=sqlite://sites/default/files/.sqlite vendor/bin/phpunit -c web/core web/modules/contrib/rabbitmq/tests/`
