<?php

namespace Drupal\inactive_autologout\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for user routes.
 */
class AutologoutController extends ControllerBase {

  /**
   * Logs the current user out.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirection to home page.
   */
  public function logout() {
    user_logout();
    return $this->redirect('user.login');
  }

  /**
   * Logs the current user out.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirection to home page.
   */
  public function autologout() {
    custom_user_logout();
    return $this->redirect('user.login', [
      'autologout' => 'true',
      'absolute' => TRUE,
    ]);
  }

  /**
   * Store the active session.
   */
  public function autologoutActive(Request $request) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $timestamp = $request->query->get('localtimestamp');
      $session = \Drupal::request()->getSession();
      $session->set('timestamp', $timestamp);
      if (!empty($session->get('timestamp'))) {
        $response['timestamp'] = $timestamp;
      }
    }
    else {
      $response['timestamp'] = '';
    }
    return new JsonResponse($response);
  }

  /**
   * Store the active session.
   */
  public function autologoutGetTimestamp() {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $session = \Drupal::request()->getSession();
      $timestamp = $session->get('timestamp');
      $response['timestamp'] = $timestamp;
    }
    else {
      $response['timestamp'] = '';
    }
    return new JsonResponse($response);
  }

}
