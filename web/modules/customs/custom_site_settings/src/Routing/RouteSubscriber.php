<?php

namespace Drupal\custom_site_settings\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('custom_site_settings.setting_form');   
    if ($route) {
      $route->setRequirements([
        '_css_access_check' => 'TRUE',
      ]);
    }
  }

}