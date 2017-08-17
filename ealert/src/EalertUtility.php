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

}
