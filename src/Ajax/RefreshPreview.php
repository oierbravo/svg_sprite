<?php


namespace Drupal\svg_sprite\Ajax;


use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;

class RefreshPreview {
    public static function render(array &$form, FormStateInterface $form_state){

      $triggeringElement = $form_state->getTriggeringElement();
      $parent_field_name = $triggeringElement['#parents'][0];
      $sprite = '';
      if ($selectedSprite = $form_state->getValue($parent_field_name)) {
          $sprite = \Drupal::service('svg_sprite.renderer')->getRenderArray($selectedSprite[0]['sprite']);
      }
      $response = new AjaxResponse();
      // Issue a command that replaces the element #edit-output
      // with the rendered markup of the field created above.
      $response->addCommand(new ReplaceCommand('#edit-' .str_replace('_', '-',$parent_field_name) . '-wrapper svg' , $sprite));


      return $response;
    }

}
