<?php

namespace Drupal\rabbitmq_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\rabbitmq\Queue\QueueFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Contribute form.
 */
class ExampleForm extends FormBase {

  /**
   * Queue Factory.
   *
   * @var \Drupal\rabbitmq\Queue\QueueFactory
   */
  protected $queueFactory;

  /**
   * Form constructor.
   *
   * @param \Drupal\rabbitmq\Queue\QueueFactory $queueFactory
   *   Queue factory.
   */
  public function __construct(QueueFactory $queueFactory) {
    $this->queueFactory = $queueFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('queue.rabbitmq.default')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rabbitmq_example_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Send an email address to the queue.'),
    ];
    $form['show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    // Get the data you want to send to the queue.
    $data = $form_state->getValue('email');

    // Get the queue config and send it to the data to the queue.
    $queueName = 'rabbitmq_example_queue';
    $queue = $this->queueFactory->get($queueName);
    $queue->createItem($data);

    // Send some feedback.
    $this->messenger()->addMessage(
      $this->t('You sent the following data: @email to queue: @queue', [
        '@queue' => $queueName,
        '@email' => $form_state->getValue('email'),
      ])
    );
  }

}
