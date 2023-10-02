<?php

namespace Drupal\svg_sprite_browser\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\svg_sprite\Services\Renderer;
use Drupal\svg_sprite\SvgSpriteHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

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
    $form['#attached']['library'][] = 'svg_sprite_browser/ajax';

    $svg_sprite_element = [];

    $default_value = (isset($item_value['sprite'])) ? $item_value['sprite'] : SvgSpriteHelper::NONE_KEY;

    $parents = $element['#field_parents'];


    $id_prefix = '';
    if (!empty($parents)) {
      //Empty check necessary because implode will return the
      //separator when given an empty array.
      $id_prefix = str_replace('_', '-', implode('-', array_merge($parents))) . '-';
    }

    $edit_id = 'edit-' . $id_prefix . str_replace('_', '-', $items->getName()) . '-' . $delta . '-sprite';


    $sprite = Html::getUniqueId('sprite_widget');
    $backgroundEnabledClass = Html::getUniqueId('background');

    $svg_sprite_element['sprite'] = [
      '#title' => $this->fieldDefinition->getLabel(),
      '#type' => 'textfield',
      '#default_value' => $default_value,
      '#attributes' => ['style' => 'display:none'],
      '#prefix' => '<div class="icon-field-inner-wrapper">',
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
    $svg_sprite_element['sprite_preview'] = $this->svg_sprite_renderer->getRenderArray($default_value);




    //$svg_sprite_element['sprite_preview']['#suffix'] = "</div>";
    $svg_sprite_element['actions'] = [];
    $svg_sprite_element['actions']['dialog_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Browse'),
    //  '#suffix' => "</div>",
      '#url' => Url::fromRoute(
        'svg_sprite_browser.widget_form',
        [
          'field_edit_id' => $edit_id,
          'selected_sprite' => (isset($item_value['sprite'])) ? $item_value['sprite'] : $default_value,
        ]),
      '#attributes' => [
        'class' => [
          'use-ajax',
          'button',
        ],
      ],
    ];

    $svg_sprite_element['actions']['clear'] = [
      '#type' => "button",
      '#value' =>  $this->t('Clear'),
      '#edit_id' => $edit_id,
      '#ajax' => [
       'callback' => [$this,'clear'],
        'event' => 'click',
    /*    '#url' => Url::fromRoute(
          'svg_sprite_browser.set_field',
          [
            'field_edit_id' => $edit_id,
            'selected_sprite' => "none",
          ]),*/
      ],
      '#states' => [
      'invisible' => [
        ['#' . $edit_id => ['value' => SvgSpriteHelper::NONE_KEY ]],
      ],
    ]
    ];
    $element += $svg_sprite_element;
    return $element;
  }
  public static function clear(array &$form, FormStateInterface $form_state){
    $response = new AjaxResponse();

    $triggeringElement = $form_state->getTriggeringElement();
    $field_id = $triggeringElement['#edit_id'];
//    $response->addCommand(new ReplaceCommand("#" . $sprite_preview_element . ' svg' , $sprite));
    $response->addCommand(new InvokeCommand(NULL, 'svgSpriteBrowserDialogAjaxCallback', [$field_id, SvgSpriteHelper::NONE_KEY]));


    return $response;
  }
}
