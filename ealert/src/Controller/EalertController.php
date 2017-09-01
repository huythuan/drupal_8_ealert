<?php

namespace Drupal\ealert\Controller;

use Drupal\ealert\EalertUtility;
use Drupal\system\FileDownloadController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\file\Entity\File;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Component\Utility\Unicode;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class EalertController.
 */
class EalertController extends FileDownloadController {

    /**
     * Download.
     *
     * @return string
     *   Return Hello string.
     */
    public function ealertDownload($nid) {
        $node = node_load($nid);
        $file_info = EalertUtility::ealert_file_uri($node);
        $file_path = $file_info['uri'];
        $filename = $file_info['filename'];
        $headers = [
          'Content-Type' => 'text/html',
          'Content-Disposition' => 'attachment; filename=' . $filename,
          'Content-Length' => filesize($file_path),
          'Content-Transfer-Encoding' => 'binary',
          'Pragma' => 'no-cache',
          'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
          'Expires' => '0',
          'Accept-Ranges' => 'bytes',
        ];
        return new BinaryFileResponse($file_path, 200, $headers);
    }

}
