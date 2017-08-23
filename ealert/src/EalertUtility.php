<?php

namespace Drupal\ealert;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class EalertUtility implements ContainerFactoryPluginInterface {

    /**
     * The file system service.
     *
     * @var \Drupal\Core\File\FileSystemInterface
     */
    protected $fileSystem;

    /**
     * Constructs a download process plugin.
     *
     * @param array $configuration
     *   The plugin configuration.
     * @param string $plugin_id
     *   The plugin ID.
     * @param mixed $plugin_definition
     *   The plugin definition.
     * @param \Drupal\Core\File\FileSystemInterface $file_system
     */
    public function __construct(array $configuration, $plugin_id, array $plugin_definition, FileSystemInterface $file_system) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
        $this->fileSystem = $file_system;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
            $configuration, $plugin_id, $plugin_definition, $container->get('file_system')
        );
    }

    public static function ealert_realpath($data) {
        return \Drupal::service('file_system')->realpath($uri);
    }

    /**
     * download e-alert file
     */
    public static function ealert_download_file($node) {
        $file_path = $this->ealert_get_ealert_file_path($node);
        if (file_exists($file_path)) {
            // Serve file download.
            drupal_add_http_header('Pragma', 'public');
            drupal_add_http_header('Expires', '0');
            drupal_add_http_header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
            drupal_add_http_header('Content-Type', 'application/vnd.ms-excel');
            drupal_add_http_header('Content-Disposition', 'attachment; filename=' . basename($file_path) . ';');
            drupal_add_http_header('Content-Transfer-Encoding', 'binary');
            drupal_add_http_header('Content-Length', filesize($file_path));
            readfile($file_path);
            drupal_exit();
        }
    }

    /**
     * Get e-alert file path
     * @param type object $node
     * return string $path
     */
    public static function ealert_get_ealert_file_path($node) {
        $file_name = date('Ymd', $node->getCreatedTime()) . '_' . $node->get('nid')->value . '.htm';
        $dir_name = \Drupal::service('file_system')->realpath('public://') . '/e_alert';
        if (!is_dir($dir_name)) {
            mkdir($dir_name, 0755);
        }
        $file_path = $dir_name . '/' . $file_name;
        return $file_path;
    }

    /**
     * Get e-alert file path
     * @param type object $node
     * return string $path
     */
    public static function ealert_get_ealert_file_url($node) {
        $file_name = date('Ymd', $node->getCreatedTime()) . '_' . $node->get('nid')->value . '.htm';
        return file_create_url('public://') . '/e_alert' . '/' . $file_name;
        //return $file_path;
    }

    /**
     * 
     * @param type $content
     * Process ealert content
     */
    public static function ealert_process_content($node, $content) {
        //$node->get('field_convert_to_utf_8')->value();
        if ($node->get('field_convert_to_utf_8')->getString() == '1') {
            $content = EalertUtility::ealert_sanitize_content($content);
            $content = EalertUtility::convert_unicode_code_to_charset($content);
        }
        return $content;
    }

    /**
     * sanitize content, remove special chars
     * @param type string $content
     */
    public static function ealert_sanitize_content($content) {
        mb_internal_encoding("UTF-8");
        mb_regex_encoding("UTF-8");
        //Replace characters
        $content = mb_ereg_replace('“', '"', $content);
        $content = mb_ereg_replace('”', '"', $content);
        $content = mb_ereg_replace('’', "'", $content);
        $content = htmlentities($content);
        $content = str_replace('&mdash;', '-', $content);
        $content = str_replace('&amp;ldquo;', '"', $content);
        $content = str_replace('&amp;rdquo;', '"', $content);
        $content = str_replace('&nbsp;', '', $content);
        $content = str_replace('&amp;lsquo;', "'", $content);
        $content = str_replace('&amp;rsquo;', "'", $content);
        $content = str_replace('&ndash;', "-", $content);
        $content = html_entity_decode($content);

        return $content;
    }

    /**
     * Replace unicode to charset
     * return string $content
     */
    public static function convert_unicode_code_to_charset($content) {
        $unicode_charset = array(
          '¡' => '&iexcl;',
          'à' => '&agrave;',
          'á' => '&aacute;',
          'â' => '&acirc;',
          'ã' => '&atilde;',
          'ä' => '&auml;',
          'ó' => '&oacute;',
          'é' => '&eacute;',
          'ñ' => '&ntilde;',
          'í' => '&iacute;',
          'ú' => '&uacute;',
          'ý' => '&yacute;',
          'Á' => '&Aacute;',
          'À' => '&Agrave;',
          'Â' => '&Acirc;',
          'Ã' => '&Atilde;',
          'Ñ' => '&Ntilde;',
          'Ó' => '&Oacute;',
          'Ò' => '&Ograve;',
          'È' => '&Egrave;',
        );
        foreach ($unicode_charset as $unicode => $charset) {
            $content = str_replace($unicode, $charset, $content);
        }
        return $content;
    }

}
