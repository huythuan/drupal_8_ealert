<?php

namespace Drupal\ealert\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\ealert\EalertUtility;

/**
 * Provides a 'EalertInfo' block.
 *
 * @Block(
 *  id = "ealert_info",
 *  admin_label = @Translation("Ealert info"),
 * )
 */
class EalertInfo extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
        $node = \Drupal::routeMatch()->getParameter('node');
        if ($node && $node->getType() == 'ealert') {
            $build = [];
            $build['ealert_info']['#cache']['max-age'] = 0; //disable the cache
            $build['ealert_info']['#markup'] = 'Implement EalertInfo.';
            $host = \Drupal::request()->getHost();
            // You can get nid and anything else you need from the node object.
            $nid = $node->id();
            $file_path = EalertUtility::ealert_get_ealert_file_url($node);
            $download =  '<a href="http://165.107.185.115/samoca/ealert/download/' . $nid .'">Download</a> ';
            $download .=  '| <a href="' . $file_path . '" target="_blank">View</a>';
            $build['ealert_info']['#markup'] = $download;
            return $build;
        }

    }
    /**
 * download e-alert file
 */
function ealert_download_file($nid) {
    $node = node_load($nid);
    $file_path = ealert_get_ealert_file_path($node);
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

}
