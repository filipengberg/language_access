<?php

namespace Drupal\language_access;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LanguagePermissions implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The langauge manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new FilterPermissions instance.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface  $language_manager
   *   The entity manager.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('language_manager'));
  }

  /**
   * @return array language permissions
   */
  public function permissions() {
    $permissions = [];

    $languages = $this->languageManager->getLanguages();
    foreach ($languages as $language) {
      $permissions['access language ' . $language->getId()] = [
        'title' => $this->t('Access language @language', ['@language' => $language->getName()]),
      ];
    }
    return $permissions;
  }

}
