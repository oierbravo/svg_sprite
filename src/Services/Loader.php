<?php
namespace Drupal\svg_sprite\Services;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;

class Loader {
    protected $fileContent;
    protected $filePath;

    protected $configFactory;
    protected $messenger;

    public function __construct( ConfigFactoryInterface $configFactory, MessengerInterface $messenger ) {
      $this->configFactory = $configFactory;
      $this->messenger = $messenger;
      $this->filePath = $this->configFactory->get('svg_sprite.settings')->get('path');
    }

    public function getFilePath(){

      return Url::fromUserInput('/' . $this->filePath);
    }

    public function getFileContent() {
      if(!file_exists($this->filePath)){
        $this->messenger->addError(t('SVG Sprite file not found.'));
      }
      if(is_null($this->fileContent)){
        $this->loadFile();
      }
      return $this->fileContent;
    }
    protected function loadFile(){
      $this->fileContent = file_get_contents($this->filePath);
    }

}

