<?php

/**
 * @file
 * Contains \Drupal\language_access\LanguageAccessEventSubscriber.
 */

namespace Drupal\language_access\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\language\ConfigurableLanguageManager;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Class LanguageAccessEventSubscriber.
 *
 * @package Drupal\language_access
 */
class LanguageAccessEventSubscriber implements EventSubscriberInterface {

  /**
   * @var ConfigurableLanguageManager
   */
  protected $languageManager;

  /**
   * @var RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * @var AccountInterface
   */
  private $account;

  /**
   * Constructor.
   * @param ConfigurableLanguageManager $language_manager
   * @param RouteMatchInterface $route_match
   * @param AccountInterface $account
   */
  public function __construct(ConfigurableLanguageManager $language_manager, RouteMatchInterface $route_match, AccountInterface $account) {
    $this->languageManager = $language_manager;
    $this->routeMatch = $route_match;
    $this->account = $account;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST] = ['checkForLanguageAccess'];
    return $events;
  }

  /**
   * This method is called whenever the kernel.request event is
   * dispatched.
   */
  public function checkForLanguageAccess() {
    $currentLanguage = $this->languageManager->getCurrentLanguage();
    $id = $currentLanguage->getId();

    if (!$this->account->hasPermission('access language ' . $id)) {
      $route = $this->routeMatch->getRouteName();

      if ($route == 'user') {
        return;
      }

      // Check if current language is different from the default language
      $defaultLanguage = $this->languageManager->getDefaultLanguage();
      if ($id != $defaultLanguage->getId()) {
        // Redirect to current route in default language
        $url = Url::fromRouteMatch($this->routeMatch);
        $url->setOption('language', $defaultLanguage);
        $response = new RedirectResponse($url->toString());
        $response->send();
      }
    }

  }

}
