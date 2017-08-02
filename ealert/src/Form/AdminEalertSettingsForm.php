<?php

namespace Drupal\ealert\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\ealert\EalertUtility;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AdminEalertSettingsForm.
 */
class AdminEalertSettingsForm extends ConfigFormBase {

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * Class constructor.
     */
    public function __construct(EntityTypeManagerInterface $entityTypeManager) {
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [
          'ealert.adminealertsettings',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        // Instantiates this form class.
        return new static(
            // Load the service required to construct this class.
            $container->get('entity_type.manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'admin_ealert_settings_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config('ealert.adminealertsettings');

        $terms = $this->entityTypeManager->getStorage("taxonomy_term")
            ->loadTree('ealert_outreach_taxonomy', $parent = 0, $max_depth = NULL, $load_entities = FALSE);
        foreach ($terms as $term) {
            $form['fieldset_' . $term->tid] = array(
              '#type' => 'details',
              '#title' => $this->t($term->name),
              '#description' => $this->t('Template for email'),
              '#open' => FALSE, // Controls the HTML5 'open' attribute. Defaults to FALSE.
            );
            $form['fieldset_' . $term->tid]['ealert_' . $term->tid] = [
              '#type' => 'textarea',
              '#title' => $this->t('Template of ealert file'),
              '#rows' => 25,
              '#description' => $this->t('Enter header of ealert file.'),
              '#default_value' => $config->get('ealert_' . $term->tid),
            ];
            $form['fieldset_' . $term->tid]['token_tree'] = array(
              '#theme' => 'token_tree_link',
              '#token_types' => array('node'),
            );
        }



        $form['disclaimer_link'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Disclaimer Link'),
          '#description' => $this->t('The link to add body link for redirect.'),
          '#maxlength' => 128,
          '#size' => 64,
          '#default_value' => $config->get('disclaimer_link'),
        ];
        $form['internal_link_pattern'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Internal link pattern'),
          '#description' => $this->t('Enter a regular expression for internal links.'),
          '#maxlength' => 128,
          '#size' => 64,
          '#default_value' => $config->get('internal_link_pattern'),
        ];
        $form['add_disclaimer'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Add Disclaimer'),
          '#description' => $this->t('Added disclaimer link.'),
          '#default_value' => $config->get('add_disclaimer'),
        ];
        $form['sanitize_text'] = [
          '#type' => 'checkbox',
          '#title' => $this->t('Sanitize Text'),
          '#description' => $this->t('Sanitize the boby text.'),
          '#default_value' => $config->get('sanitize_text'),
        ];
        $form['member_name'] = [
          '#type' => 'textfield',
          '#title' => $this->t('Member name'),
          '#description' => $this->t('The name of member, this helps to create google analytic code.'),
          '#maxlength' => 64,
          '#size' => 64,
          '#default_value' => $config->get('member_name'),
        ];
        $form['district'] = [
          '#type' => 'textfield',
          '#title' => $this->t('District'),
          '#description' => $this->t('The name of member, this helps to create google analytic code.'),
          '#maxlength' => 64,
          '#size' => 64,
          '#default_value' => $config->get('district'),
        ];



        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        parent::submitForm($form, $form_state);

        $terms = $this->entityTypeManager->getStorage("taxonomy_term")
            ->loadTree('ealert_outreach_taxonomy', $parent = 0, $max_depth = NULL, $load_entities = FALSE);

        $config = $this->config('ealert.adminealertsettings');
        foreach ($terms as $term) {
            $config->set('ealert_' . $term->tid, $form_state->getValue('ealert_' . $term->tid));
        }

        $config->set('disclaimer_link', $form_state->getValue('disclaimer_link'))
            ->set('internal_link_pattern', $form_state->getValue('internal_link_pattern'))
            ->set('add_disclaimer', $form_state->getValue('add_disclaimer'))
            ->set('sanitize_text', $form_state->getValue('sanitize_text'))
            ->set('member_name', $form_state->getValue('member_name'))
            ->set('district', $form_state->getValue('district'))
            ->save();
    }

}
