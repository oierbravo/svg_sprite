<?php
namespace Drupal\svg_sprite\Services;

use Drupal\svg_sprite\Services\Loader;
use Masterminds\HTML5;

class Renderer {
    protected $svgSpriteLoader;
    public function __construct(Loader $svgSpriteLoader) {
      $this->svgSpriteLoader = $svgSpriteLoader;
    }

  public function getIds() : array {
        $svgSpriteData = $this->svgSpriteLoader->getFileContent();
        if(is_null($svgSpriteData))
          return [];
        $serializedData = new \SimpleXMLElement($svgSpriteData);
        $ids = [];
        foreach($serializedData->symbol as $symbol){
          $ids[] = (string) $symbol->attributes()['id'];
        }
        return $ids;
    }
    public function getRenderArray($id,$customClass = '', $size = 96) : array {
      return [
        '#theme' => 'svg_sprite',
        '#id' => $id,
        '#svgSpritePath' => $this->svgSpriteLoader->getFilePath(),
        '#customClass' => $customClass,
        '#size' => $size
      ];
    }
}
