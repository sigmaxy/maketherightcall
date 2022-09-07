<?php

namespace Drupal\chubb_life\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Class AjaxCommand.
 */
class AjaxCommand implements CommandInterface {

  protected $command;
  protected $status;
  protected $result;
  protected $message;
  // Constructs a ReadMessageCommand object.
  public function __construct($command,$status,$result,$message) {
    $this->command = $command;
    $this->status = $status;
    $this->result = $result;
    $this->message = $message;
  }
  /**
   * Render custom ajax command.
   *
   * @return ajax
   *   Command function.
   */
  public function render() {
    return [
      'command' => $this->command,
      'status' => $this->status,
      'result' => $this->result,
      'message' => $this->message,
    ];
  }

}
