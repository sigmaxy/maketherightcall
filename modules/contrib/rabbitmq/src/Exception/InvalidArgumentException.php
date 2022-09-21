<?php

namespace Drupal\rabbitmq\Exception;

/**
 * RabbitMQ-specific version of an InvalidArgumentException.
 */
class InvalidArgumentException extends Exception {

  /**
   * InvalidArgumentException constructor.
   *
   * @param string $message
   *   The message.
   * @param int $code
   *   The code.
   * @param \Throwable|null $previous
   *   The previous exception to use in stack trace.
   */
  public function __construct(
    $message = '',
    $code = 0,
    \Throwable $previous = NULL
  ) {
    parent::__construct('RabbitMq: ' . $message, $code, $previous);
  }

}
