<?php

namespace Drupal\rabbitmq\Queue;

use Drupal\Core\Queue\ReliableQueueInterface;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Queue API Backend implementation on top of AMQPlib.
 *
 * This class only contains the ReliableQueueInterface methods, no lower-level
 * method specific to the implementation: those are in QueueBase.php.
 *
 * @see \Drupal\rabbitmq\Queue\QueueBase
 */
class Queue extends QueueBase implements ReliableQueueInterface {

  /**
   * Array of message objects claimed from the queue.
   *
   * @var array
   */
  protected $messages = [];

  /**
   * {@inheritdoc}
   */
  public function createItem($data): bool {
    $loggerArgs = [
      'channel' => static::LOGGER_CHANNEL,
      '%queue' => $this->name,
    ];

    try {
      $channel = $this->getChannel();
      // Data must be a string.
      $item = new AMQPMessage(json_encode($data), ['delivery_mode' => 2]);

      // Default exchange and routing keys.
      $exchange = '';
      $routingKey = $this->name;

      // Fetch exchange and routing key if defined,
      // only consider the first routing key for now.
      if (isset($this->options['routing_keys'][0])) {
        [$exchange, $routingKey] = explode('.', $this->options['routing_keys'][0], 2);
      }

      $channel->basic_publish($item, $exchange, $routingKey);
      $this->logger->debug('Item sent to queue %queue', $loggerArgs);
      $result = TRUE;
    }
    catch (\Exception $e) {
      $this->logger->error('Failed to send item to queue %queue: @message', $loggerArgs + [
        '@message' => $e->getMessage(),
      ]);
      $result = FALSE;
    }

    return $result;
  }

  /**
   * Retrieve the number of items in the queue.
   *
   * This is intended to provide a "best guess" count of the number of items in
   * the queue. Depending on the implementation and the setup, the accuracy of
   * the results of this function may vary.
   *
   * e.g. On a busy system with a large number of consumers and items, the
   * result might only be valid for a fraction of a second and not provide an
   * accurate representation.
   *
   * @return int
   *   An integer estimate of the number of items in the queue.
   */
  public function numberOfItems(): int {
    // Retrieve information about the queue without modifying it.
    $queueOptions = ['passive' => TRUE];
    $this->queue = NULL;
    $queue = $this->getQueue($this->getChannel(), $queueOptions);
    $jobs = $queue ? array_slice($queue, 1, 1) : [];
    return empty($jobs) ? 0 : $jobs[0];
  }

  /**
   * Claim an item in the queue for processing.
   *
   * @param int $lease_time
   *   How long the processing is expected to take in seconds, defaults to an
   *   hour. After this lease expires, the item will be reset and another
   *   consumer can claim the item. For idempotent tasks (which can be run
   *   multiple times without side effects), shorter lease times would result
   *   in lower latency in case a consumer fails. For tasks that should not be
   *   run more than once (non-idempotent), a larger lease time will make it
   *   more rare for a given task to run multiple times in cases of failure,
   *   at the cost of higher latency.
   *
   * @return object|false
   *   On success we return an item object. If the queue is unable to claim an
   *   item it returns false. This implies a best effort to retrieve an item
   *   and either the queue is empty or there is some other non-recoverable
   *   problem.
   */
  public function claimItem($lease_time = 3600) {
    $this->getChannel()->basic_qos(0, 1, FALSE);
    if (!$msg = $this->getChannel()->basic_get($this->name)) {
      return FALSE;
    }

    $this->messages[$msg->getDeliveryTag()] = $msg;

    $item = (object) [
      'id' => $msg->getDeliveryTag(),
      'data' => json_decode($msg->body, TRUE),
      'expire' => time() + $lease_time,
    ];
    $this->logger->info('Item @id claimed from @queue', [
      'channel' => static::LOGGER_CHANNEL,
      '@id' => $item->id,
      '@queue' => $this->name,
    ]);

    return $item;
  }

  /**
   * Delete a finished item from the queue.
   *
   * @param object $item
   *   An item returned by DrupalQueueInterface::claimItem().
   */
  public function deleteItem($item): void {
    $this->logger->info('Item @id acknowledged from @queue', [
      'channel' => static::LOGGER_CHANNEL,
      '@id' => $item->id,
      '@queue' => $this->name,
    ]);

    /** @var \PhpAmqpLib\Channel\AMQPChannel $channel */
    $channel = $this->messages[$item->id]->getChannel();
    $channel->basic_ack($item->id);
  }

  /**
   * Release an item that the worker could not process.
   *
   * This is so another worker can come in and process it before the timeout
   * expires.
   *
   * @param object $item
   *   An item returned by DrupalQueueInterface::claimItem().
   *
   * @return bool
   *   Always pretend to succeed. Actually, the item will be released back when
   *   the connection closes, so this just eliminates that capability to send an
   *   acknowledgement to the server which would remove the item from the queue.
   */
  public function releaseItem($item): bool {
    /** @var \PhpAmqpLib\Message\AMQPMessage $message */
    $message = $this->messages[$item->id];

    /** @var \PhpAmqpLib\Channel\AMQPChannel $channel */
    $channel = $message->getChannel();

    $channel->basic_nack($message->getDeliveryTag(), FALSE, TRUE);
    unset($this->messages[$item->id]);
    return TRUE;
  }

  /**
   * Create a queue.
   *
   * Called during installation and should be used to perform any necessary
   * initialization operations. This should not be confused with the
   * constructor for these objects, which is called every time an object is
   * instantiated to operate on a queue. This operation is only needed the
   * first time a given queue is going to be initialized (for example, to make
   * a new database table or directory to hold tasks for the queue -- it
   * depends on the queue implementation if this is necessary at all).
   */
  public function createQueue() {
    return $this->getQueue($this->getChannel());
  }

  /**
   * Delete a queue and every item in the queue.
   */
  public function deleteQueue(): void {
    if (empty($this->queue)) {
      return;
    }
    $channel = $this->getChannel();
    $channel->queue_purge($this->name);
    $channel->queue_delete($this->name);
    $this->queue = NULL;
  }

}
