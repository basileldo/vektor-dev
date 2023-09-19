<?php

namespace Vektor\Checkout;

use Vektor\Checkout\Formatter as CheckoutFormatter;
use Vektor\Utilities\Formatter;

class Utilities
{
    public static function products() {
        $_products = Product::with(['products' => function($query) {
            $query->orderBy('sort_order');
        }])->whereNull('parent_id')->orderBy('sort_order')->get()->toArray();
        return CheckoutFormatter::products($_products);
    }

    public static function addPercentage($value, $percentage)
    {
        if (empty($percentage)) {
            return floatval(Formatter::decimalPlaces($value));
        }
        return floatval(Formatter::decimalPlaces($value * (1 + ($percentage / 100))));
    }

    public static function transformLine($data, $html = false)
    {
        $line_model = null;
        $name = '';
        if ($data['id'] == 'shipping') {
            $name .= $data['name'];
        } else {
            $name_parts = [];
            $line_model = Product::where('id', $data['id'])->first();
            if ($line_model) {
                $name_parts[] = $line_model->sku;
            } else {
                $name_parts[] = $data['name'];
                if (isset($data['options']['color'])) {
                    $name_parts[] = strtoupper(substr($data['options']['color'], 0, 3));
                }
                if (isset($data['options']['size'])) {
                    $name_parts[] = strtoupper($data['options']['size']);
                }
            }
            $name .= implode('-', $name_parts);
        }

        $output = [
            'id' => $data['id'],
            'formatted' => [],
            'name' => $name,
            'quantity' => $data['qty'],
            'unit_price' => $data['price'],
            'std_unit_price' => $data['price'],
            'ext_price' => $data['subtotal'],
            'net_price' => $data['subtotal'],
        ];

        if ($data['id'] == 'shipping') {
            $output['related_type'] = 'ProductCatalog';
            $output['related_id'] = config("onecrm.shipping_related_id");
            $output['mfr_part_no'] = config("onecrm.shipping_mfr_part_no");
            $output['display_price'] = self::addPercentage($data['price'], 20);
            $output['formatted']['display_price'] = Formatter::currency($output['display_price']);

            $comment = '';
            $comment_parts = [];

            if (isset($data['options']['method_name'])) {
                $comment_parts[] = 'Shipping Method: ' . $data['options']['method_name'];
            }

            if (! empty($comment_parts)) {
                if ($html) {
                    $comment .= $data['name'] . '<br/><small>' . implode(', ', $comment_parts) . '</small>';
                } else {
                    $comment .= implode(', ', $comment_parts);
                }
            }

            $output['comment'] = $comment;
        } else {
            $output['display_price'] = $data['price'];
            $output['formatted']['display_price'] = Formatter::currency($output['display_price']);
            if ($line_model && $line_model->configuration) {
                if (isset($line_model->configuration['onecrm_id'])) {
                    $output['related_type'] = 'ProductCatalog';
                    $output['related_id'] = $line_model->configuration['onecrm_id'];
                    $output['mfr_part_no'] = $line_model->sku;
                }
                if (isset($line_model->configuration['onecrm_tax_code_id'])) {
                    $output['tax_class_id'] = $line_model->configuration['onecrm_tax_code_id'];
                }
                if (isset($line_model->configuration['tax_percentage'])) {
                    $output['display_price'] = self::addPercentage($data['price'], $line_model->configuration['tax_percentage']);
                    $output['formatted']['display_price'] = Formatter::currency($output['display_price']);
                }
            }

            if ($line_model && $line_model->attributes) {
                foreach ($line_model->attributes as $attribute) {
                    if (isset($attribute['configuration']) && isset($attribute['configuration']['onecrm_id'])) {
                        if (!isset($output['adjustments'])) {
                            $output['adjustments'] = [];
                        }
                        $output['adjustments'][] = [
                            'id' => $attribute['configuration']['onecrm_id'],
                            'name' => "{$attribute['name_label']} : {$attribute['value_label']}",
                        ];
                    }
                }
            }

            $comment = $data['name'];
            $comment_parts = [];

            if (isset($data['options']['color'])) {
                $comment_parts[] = 'Colour: ' . ucwords($data['options']['color']);
            }
            if (isset($data['options']['size'])) {
                $comment_parts[] = 'Size: ' . strtoupper($data['options']['size']);
            }

            if (! empty($comment_parts)) {
                if ($html) {
                    $comment .= '<br/><small>' . implode(', ', $comment_parts) . '</small>';
                } else {
                    $comment .= ' - ' . implode(', ', $comment_parts);
                }
            }

            $output['comment'] = $comment;

        }

        return $output;
    }

    public static function transformLines($array, $html = false)
    {
        if (! empty($array)) {
            foreach ($array as &$line) {
                $line = self::transformLine($line, $html);
            }
            unset($line);
        }

        return array_values(array_filter($array));
    }

    public static function detectProductLines($item)
    {
        if ($item['id'] != 'shipping') {
            return true;
        }

        return false;
    }

    public static function detectShippingLines($item)
    {
        if ($item['id'] == 'shipping') {
            return true;
        }

        return false;
    }

    public static function cleanHtml($value)
    {
        return trim(preg_replace('/[\s]{2,}/', '', preg_replace('/[\\n\\t\\r]/', ' ', $value)));
    }

    public static function filenameToUrl($filename, $relative = false)
    {
        $files = glob(resource_path('assets/products/*'));
        if (!empty($files)) {
            foreach ($files as $file) {
                $file_pathinfo = pathinfo($file);
                if ($file_pathinfo['filename'] == $filename || $file_pathinfo['basename'] == $filename) {
                    if ($relative) {
                        return $file_pathinfo['filename'];
                    } else {
                        return route('checkout.product_images.product_images', ['filename' => $file_pathinfo['filename']]);
                    }
                    break;
                }
            }
        }
        return null;
    }

    public static function filenamesToUrl($filenames, $relative = false)
    {
        $urls = [];
        if (is_string($filenames)) {
            $filenames_array = array_map('trim', explode(',', $filenames));
        }
        if (is_array($filenames)) {
            $filenames_array = $filenames;
        }
        if (!empty($filenames_array)) {
            foreach ($filenames_array as $filename) {
                $url = self::filenameToUrl($filename, $relative);
                if ($url !== null) {
                    $urls[] = $url;
                }
            }
        }
        return $urls;
    }
}
