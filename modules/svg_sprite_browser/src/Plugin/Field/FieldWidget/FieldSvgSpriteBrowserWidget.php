<?php

namespace Drupal\svg_sprite_browser\Plugin\Field\FieldWidget;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\svg_sprite\Services\Renderer;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @FieldWidget(
 *   id = "field_svg_sprite_browser_widget",
 *   module = "svg_sprite_browser",
 *   label = @Translation("Svg sprite browser"),
 *   field_types = {
 *     "field_svg_sprite"
 *   }
 * )
 */
class FieldSvgSpriteBrowserWidget extends WidgetBase {

  protected $svg_sprite_renderer;


  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, Renderer $svg_sprite_renderer) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->svg_sprite_renderer = $svg_sprite_renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('svg_sprite.renderer')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $item_value = $items[$delta]->getValue();

    $form['#attached']['library'][] = 'core/drupal.dialog.ajax';

    $svg_sprite_element = [];

    $default_value = (isset($item_value['sprite'])) ? $item_value['sprite'] : '';

    $id_prefix = '';
    if (!empty($parents)) {
      //Empty check necessary because implode will return the
      //separator when given an empty array.
      $id_prefix = str_replace('_', '-', implode('-', array_merge($parents))) . '-';
    }

    $edit_id = 'edit-' . $id_prefix . str_replace('_', '-', $items->getName()) . '-' . $delta . '-sprite';


    $svg_sprite_element['sprite_preview'] = $this->svg_sprite_renderer->getRenderArray($default_value);
    $svg_sprite_element['sprite'] = [
      '#title' => 'Sprite',
      '#type' => 'textfield',
      '#default_value' => $default_value,
      '#attributes' => ['style' => 'display:none'],
      '#ajax' => [
        'callback' => ['Drupal\svg_sprite\Ajax\RefreshPreview', 'render'],
        'disable-refocus' => TRUE, // Or TRUE to prevent re-focusing on the triggering element.
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Updating sprite...'),
        ],
      ]
    ];


    $parents = $element['#field_parents'];



    $svg_sprite_element['dialog_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Browse'),
      '#url' => Url::fromRoute(
        'svg_sprite_browser.widget_form',
        [
          'field_edit_id' => $edit_id,
          'selected_sprite' => (isset($item_value['sprite'])) ? $item_value['sprite'] : '',
        ]),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
        ],
      ],
    ];
    $element += $svg_sprite_element;
    return $element;
  }

}
