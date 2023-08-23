<?php
namespace Drupal\svg_sprite\Services;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;

class Loader {
    protected String $fileContent;
    protected String $filePath;

    protected ConfigFactoryInterface $configFactory;
    protected MessengerInterface $messenger;

    public function __construct( ConfigFactoryInterface $configFactory, MessengerInterface $messenger ) {
      $this->configFactory = $configFactory;
      $this->messenger = $messenger;
      $this->filePath = $this->configFactory->get('svg_sprite.settings')->get('path');
    }

    public function getFilePath() : Url {

      return Url::fromUserInput('/' . $this->filePath);
    }

    public function getFileContent() : ?Array {
      if(!file_exists($this->filePath)){
        $this->messenger->addError(t('SVG Sprite file not found.'));
        return null;
      }
      if(is_null($this->fileContent)){
        $this->loadFile();
      }
      return $this->fileContent;
    }
    protected function loadFile(): void {
      $this->fileContent = file_get_contents($this->filePath);
    }

}

