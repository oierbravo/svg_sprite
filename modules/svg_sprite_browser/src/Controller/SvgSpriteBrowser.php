<?php

namespace Drupal\svg_sprite_browser\Controller;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\OpenModalDialogCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class SvgSpriteBrowser extends ControllerBase {
  public function __construct(FormBuilder $formBuilder, CsrfTokenGenerator $csrfToken) {
    $this->formBuilder = $formBuilder;
    $this->csrfToken = $csrfToken;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder'),
      $container->get('csrf_token')
    );
  }
  /**
   * Callback for opening the modal form.
   */
  public function openSearchForm(Request $request, string $field_edit_id, $selected_sprite = '') {
    $response = new AjaxResponse();

    // Get the modal form using the form builder.
    $modal_form = $this->formBuilder->getForm('Drupal\svg_sprite_browser\Form\SearchForm', $field_edit_id,$selected_sprite);

    // Add an AJAX command to open a modal dialog with the form as the content.
    $response->addCommand(new OpenModalDialogCommand($this->t('Sprite selector'), $modal_form, ['width' => '75%', 'heigth' => '75%','classes'=> ['ui-dialog'=>'sprite-browser-modal']]));

    return $response;
  }

  public function setField(Request $request, string $field_edit_id, $selected_sprite = '') {
    $response = new AjaxResponse();


    // Add an AJAX command to open a modal dialog with the form as the content.
//    $response->addCommand(new OpenModalDialogCommand($this->t('Sprite selector'), $modal_form, ['width' => '75%', 'heigth' => '75%','classes'=> ['ui-dialog'=>'sprite-browser-modal']]));

    return $response;
  }

  public function getJson() {
    return [];
  }
}
