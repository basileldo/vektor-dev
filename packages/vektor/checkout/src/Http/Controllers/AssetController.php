<?php

namespace Vektor\Checkout\Http\Controllers;

use Illuminate\Http\Request;
use Vektor\Api\Http\Controllers\ApiController;

class AssetController extends ApiController
{
    public function product_images(Request $request, $filename)
    {
        $files = glob(resource_path('assets/products/*'));
        if (!empty($files)) {
            foreach ($files as $file) {
                $file_pathinfo = pathinfo($file);
                if ($file_pathinfo['filename'] == $filename) {
                    $file_mime_type = mime_content_type($file);
                    return response(file_get_contents($file))
                            ->header('Content-Type', $file_mime_type)
                            ->header('Content-Disposition', 'inline; filename="' . $file_pathinfo['basename'] . '"');
                }
            }
        }
    }
}
