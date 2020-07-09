<?php

namespace Drupal\svg_sprite\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 *
 * @FieldType(
 *   id = "field_svg_sprite",
 *   label = @Translation("Svg Sprite"),
 *   module = "svg_sprite",
 *   description = @Translation("Svg sprite."),
 *   default_widget = "field_svg_sprite_select_widget",
 *   default_formatter = "field_svg_sprite_formatter",
 * )
 */
class FieldSvgSpriteItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'sprite' => [
          'type' => 'varchar',
          'length' => 255,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('sprite')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['sprite'] = DataDefinition::create('string')
      ->setLabel(t('Sprite'));

    return $properties;
  }
}
