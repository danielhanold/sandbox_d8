<?php

/**
 * @file
 * Contains \Drupal\loremipsum\Form\BlockFormController.
 */

namespace Drupal\loremipsum\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Lorem Ipsum block form.
 */
class LoremIpsumBlockForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'loremipsum_block_form';
  }

  /**
   * {@inheritdoc}
   * Lorem Ipsum generator block.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Determine how many paragraphs to generate.
    $options = array_combine(range(1, 10), range(1, 10));
    $form['paragraphs'] = array(
      '#type' => 'select',
      '#title' => $this->t('Paragraphs'),
      '#options' => $options,
      '#default_value' => 4,
      '#description' => $this->t('How many?'),
    );

    // How many phrases.
    $form['phrases'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Phrases'),
      '#description' => $this->t('Maximum per paragraph'),
      '#default_value' => 20,
    );

    $form['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Generate'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $phrases = $form_state->getValue('phrases');

    // Ensure numeric.
    if (!is_numeric($phrases)) {
      $form_state->setErrorByName('phrases', $this->t('Please use a number'));
    }

    // Ensure no decimals.
    if (floor($phrases) != $phrases) {
      $form_state->setErrorByName('phrases', $this->t('No decimals, please'));
    }

    // Ensure number makes sense.
    if ($phrases < 1) {
      $form_state->setErrorByName('phrases', $this->t('Please use a number greater than zero'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->setRedirect('loremipsum.generate', array(
      'paragraphs' => $form_state->getValue('paragraphs'),
      'phrases' => $form_state->getValue('phrases'),
    ));
  }
}
