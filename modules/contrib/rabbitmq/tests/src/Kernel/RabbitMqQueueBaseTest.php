<?php

namespace Drupal\Tests\rabbitmq\Kernel;

use Drupal\rabbitmq\Queue\Queue;

/**
 * Class RabbitMqQueueTest.
 *
 * @group RabbitMQ
 */
class RabbitMqQueueBaseTest extends RabbitMqTestBase {

  /**
   * The default queue, handled by RabbitMq.
   *
   * @var \Drupal\Core\Queue\QueueInterface
   */
  protected $queue;

  /**
   * The queue factory service.
   *
   * @var \Drupal\Core\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->queueFactory = $this->container->get('queue');
    $this->queue = $this->queueFactory->get($this->queueName);
    $this->assertInstanceOf(Queue::class, $this->queue, 'Queue API settings point to RabbitMQ');
    $this->queue->createQueue();
  }

  /**
   * {@inheritdoc}
   */
  public function tearDown(): void {
    $this->queue->deleteQueue();
    parent::tearDown();
  }

  /**
   * Test queue registration.
   */
  public function testQueueCycle(): void {
    $data = 'foo';
    $this->queue->createItem($data);
    // We call numberOfItems() twice during testing to ensure an accurate value.
    $this->queue->numberOfItems();
    $actual = $this->queue->numberOfItems();
    $expected = 1;
    $this->assertEquals($expected, $actual, 'Queue contains something before deletion');

    $this->queue->deleteQueue();
    $expected = 0;
    // Queue deleted already, calling twice will throw an exception.
    $actual = $this->queue->numberOfItems();
    $this->assertEquals($expected, $actual, 'Queue no longer contains anything after deletion');
  }

  /**
   * Test the queue item lifecycle.
   */
  public function testItemCycle(): void {
    $count = 0;
    $data = 'foo';
    $this->queue->createItem($data);

    // We call numberOfItems() twice during testing to ensure an accurate value.
    $this->queue->numberOfItems();
    $actual = $this->queue->numberOfItems();
    $expected = $count + 1;
    $this->assertEquals($expected, $actual, 'Creating an item increases the item count.');

    $item = $this->queue->claimItem();
    $this->assertTrue(is_object($item), 'Claiming returns an item');

    $expected = $data;
    $actual = $item->data;
    $this->assertEquals($expected, $actual, 'Item content matches submission.');

    // We call numberOfItems() twice during testing to ensure an accurate value.
    $this->queue->numberOfItems();
    $actual = $this->queue->numberOfItems();
    $expected = $count;
    $this->assertEquals($expected, $actual, 'Claiming an item reduces the item count.');

    $this->queue->releaseItem($item);
    // We call numberOfItems() twice during testing to ensure an accurate value.
    $this->queue->numberOfItems();
    $actual = $this->queue->numberOfItems();
    $expected = $count + 1;
    $this->assertEquals($expected, $actual, 'Releasing an item increases the item count.');

    $item = $this->queue->claimItem();
    $this->assertTrue(is_object($item), 'Claiming returns an item');

    $this->queue->deleteItem($item);
    // We call numberOfItems() twice during testing to ensure an accurate value.
    $this->queue->numberOfItems();
    $actual = $this->queue->numberOfItems();
    $expected = $count;
    $this->assertEquals($expected, $actual, 'Deleting an item reduces the item count.');
  }

  /**
   * Validate config for queues can be saved.
   *
   * Validates that all keys in the rabbitmq.schema.yml file are correct.
   */
  public function testQueueConfigSave() {
    $config_factory = \Drupal::configFactory()->getEditable('rabbitmq.config');

    $queues = $config_factory->get('queues');

    $queues['test_queue'] = [
      'auto_delete' => TRUE,
      'durable' => FALSE,
      'exclusive' => TRUE,
      'nowait' => FALSE,
      'passive' => TRUE,
      'routing_keys' => [
        "exchange1.test_queue",
      ],
      'arguments' => [
        'alpha' => [1, 2],
        'beta' => 'value',
      ],
      'ticket' => 12345,
    ];

    $config = $config_factory->set('queues', $queues)->save(TRUE);
    $this->assertIsObject($config, 'Config save returned an object without exception');
  }

}
