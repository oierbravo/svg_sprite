<?php

namespace Drupal\svg_sprite\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\svg_sprite\Services\Renderer;
use Drupal\svg_sprite\SvgSpriteHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 * @FieldWidget(
 *   id = "field_svg_sprite_select_widget",
 *   module = "svg_sprite",
 *   label = @Translation("Svg sprite select"),
 *   field_types = {
 *     "field_svg_sprite"
 *   }
 * )
 */
class FieldSvgSpriteSelectWidget extends WidgetBase {

  protected $svg_sprite_renderer;

  /**
   *
   */
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

    $options = [];
    $sprites_ids = $this->svg_sprite_renderer->getIds();
    if (!$this->fieldDefinition->isRequired()) {
      $options[SvgSpriteHelper::NONE_KEY] = $this->t('- None -');
    }
    foreach ($sprites_ids as $id) {
      $options[$id] = $id;
    }

    $default_value = (isset($item_value['sprite'])) ? $item_value['sprite'] : '';

    $svg_sprite_element = [];

    $svg_sprite_element['#attached']['library'][] = 'svg_sprite/icon_styles';
    $svg_sprite_element['sprite_preview'] = $this->svg_sprite_renderer->getRenderArray($default_value);
    $svg_sprite_element['sprite'] = [
      '#title' => 'Sprite',
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => $default_value,
      '#attributes' => ['class' => ['form--inline', 'clearfix']],
      '#ajax' => [
    // Alternative notation.
        'callback' => ['Drupal\svg_sprite\Ajax\RefreshPreview', 'render'],
    // Or TRUE to prevent re-focusing on the triggering element.
        'disable-refocus' => TRUE,
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Updating sprite...'),
        ],
      ],
    ];

    $element += $svg_sprite_element;
    return $element;
  }

}
