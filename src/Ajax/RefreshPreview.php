<?php

namespace Drupal\svg_sprite\Ajax;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;

/**
 *
 */
class RefreshPreview {

  /**
   *
   */
  public static function render(array &$form, FormStateInterface $form_state) {
    $triggeringElement = $form_state->getTriggeringElement();

    $sprite = '';
    $sprite = \Drupal::service('svg_sprite.renderer')->getRenderArray($triggeringElement['#value'], $triggeringElement['#attributes']['data-drupal-selector']);
    $response = new AjaxResponse();
    // Issue a command that replaces the element #edit-output
    // with the rendered markup of the field created above.
    // id="edit-field-hero-0-subform-field-featured-links-2-subform-field-icon-wrapper".
    // $sprite_preview_element = str_replace("0-sprite", "wrapper", $triggeringElement['#attributes']['data-drupal-selector']);.
    $response->addCommand(new ReplaceCommand("svg." . $triggeringElement['#attributes']['data-drupal-selector'], $sprite));

    return $response;
  }

}
