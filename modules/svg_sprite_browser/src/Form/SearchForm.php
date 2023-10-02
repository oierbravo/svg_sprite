<?php

namespace Drupal\svg_sprite_browser\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;

/**
 * ModalForm class.
 *
 * To properly inject services, override create() and use the setters provided
 * by the traits to inject the needed services.
 *
 * @code
 * public static function create($container) {
 *   $form = new static();
 *   // In this example we only need string translation so we use the
 *   // setStringTranslation() method provided by StringTranslationTrait.
 *   $form->setStringTranslation($container->get('string_translation'));
 *   return $form;
 * }
 * @endcode
 */
class SearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'svg_sprite_browser_search_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_edit_id = '', $selected_sprite = '') {
    // Do nothing after the form is submitted.
    if (!empty($form_state->getValues())) {
      return [];
    }

    $form['#attached']['library'][] = 'svg_sprite_browser/browser';
    $form['#attached']['library'][] = 'svg_sprite_browser/browser_styles';


    // The status messages that will contain any form errors.
    $form['status_messages'] = [
      '#type' => 'status_messages',
      '#weight' => -10,
    ];
    $form['selected_sprite'] = [
      '#type' => 'hidden',
      '#default_value' => $selected_sprite,
      '#attributes' => [
        'id' => [
          'svg-sprite-browser-selected-sprite',
        ],
      ],
    ];
    $form['field_id'] = [
      '#name' => 'field_id',
      '#type' => 'hidden',
      '#weight' => 80,
      '#value' => $field_edit_id,
      '#attributes' => [
        'id' => [
          'svg-sprite-browser-widget-field-id',
        ],
      ],
    ];
    // Search filter box.
    $form['sprite_search'] = [
      '#type' => 'search',
      '#title' => $this
        ->t('Search'),
      '#size' => 60,
      '#attributes' => [
        'id' => [
          'svg-sprite-browser-search',
        ],
      ],
    ];

    $svg_sprite_renderer = \Drupal::service('svg_sprite.renderer');

    $elements = [];
    foreach($svg_sprite_renderer->getIds() as $id){
      $elements[] =[
        '#theme' => 'svg_sprite_browser_grid_item',
        '#id' => $id,
        '#content' => $svg_sprite_renderer->getRenderArray($id),
      ];
    }
    $form['sprite_grid'] = [
      '#theme' => 'svg_sprite_browser_grid',
      '#elements' => $elements,
    ];

    // Submit button.
    $form['actions'] = ['#type' => 'actions'];

    $form['actions']['send'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#attributes' => [
        'class' => [
          'use-ajax',
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'submitForm'],
        'event' => 'click',
      ],
    ];
/*    $form['actions']['clear'] = [
      '#type' => 'submit',
      '#value' => $this->t('Clear'),
      '#attributes' => [
        'class' => [
          'use-ajax','button--danger'
        ],
      ],
      '#ajax' => [
        'callback' => [$this, 'clearForm'],
        'event' => 'click',
      ],
    ];*/

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    // If there are any form errors, re-display the form.
    if ($form_state->hasAnyErrors()) {
      $response->addCommand(new ReplaceCommand('#entity_reference_tree_wrapper', $form));
    }
    else {
      $response->addCommand(new InvokeCommand(NULL, 'svgSpriteBrowserDialogAjaxCallback', [$form_state->getValue('field_id'), $form_state->getValue('selected_sprite')]));

      $response->addCommand(new CloseModalDialogCommand());
      $response->addCommand(new ReplaceCommand($form_state->getValue('field_id'), $form));
    }

    return $response;
  }

}
