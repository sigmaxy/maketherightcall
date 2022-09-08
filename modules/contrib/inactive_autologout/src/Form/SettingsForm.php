<?php
/**
 * @file
 * Contains Drupal\tc_custom_user_account\Form\CustomSettingsForm.
 */

namespace Drupal\inactive_autologout\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\Entity\Role;

class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'inactive_autologout.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'inactive_autologout_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('inactive_autologout.settings');

    $form['autologout'] = [
      '#type' => 'details',
      '#open' => TRUE,
      '#title' => $this->t("Auto Logout"),
    ];

    $form['autologout']['enable'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => ($config->get('enable') !== NULL) ? $config->get('enable') : FALSE,
    ];

    $defaults = 120;
    if (!empty($config->get('timeout'))) {
      $defaults = $config->get('timeout');
    }

    $form['autologout']['timeout'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Timeout value in seconds'),
      '#description' => $this->t('The length of inactivity time, in seconds, before automated log out. Must be 120 seconds or greater. Will not be used if role timeout is activated.'),
      '#default_value' => $defaults,
      '#required' => TRUE,
    ];

    $roles = Role::loadMultiple();
    $options = [];
    foreach ($roles as $role) {
      $options[$role->id()] = $this->t($role->label());
    }
    unset($options['anonymous']);
    unset($options['authenticated']);

    $form['autologout']['roles'] = [
      '#type' => 'checkboxes',
      '#multiple' => TRUE,
      '#options' => $options,
      '#title' => $this->t("Roles"),
      '#description' => $this->t('Check roles for enable auto logout.'),
      '#default_value' => (!empty($config->get('roles'))) ? $config->get('roles') : [],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $timeout = $form_state->getValue('timeout');
    if (!is_numeric($timeout)) {
      $form_state->setErrorByName('timeout', t('Must be a number.'));
    }
    if ($timeout < 120) {
      $form_state->setErrorByName('timeout', t('Must be 120 or greater.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('inactive_autologout.settings')
      ->set('timeout', $form_state->getValue('timeout'))
      ->set('roles', $form_state->getValue('roles'))
      ->set('enable', $form_state->getValue('enable'))
      ->save();
  }
}
