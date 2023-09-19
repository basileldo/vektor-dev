<?php

namespace Vektor\OneCRM\Model;

use Vektor\Checkout\Utilities;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;

class WrapperProductCatalog
{
    public $crm;

    public $crm_model;

    public $_tax_code;

    public $_product;

    public $_product_category;

    public function __construct()
    {
        $this->crm = new OneCRM;
        $this->crm_model = new OneCRMModel;

        $this->_tax_code = new TaxCode;
        $this->_product = new Product;
        $this->_product_category = new ProductCategory;

        return $this;
    }

    private function iso8859_1_to_utf8(string $s): string {
        $s .= $s;
        $len = \strlen($s);

        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80": $s[$j] = $s[$i]; break;
                case $s[$i] < "\xC0": $s[$j] = "\xC2"; $s[++$j] = $s[$i]; break;
                default: $s[$j] = "\xC3"; $s[++$j] = \chr(\ord($s[$i]) - 64); break;
            }
        }

        return substr($s, 0, $j);
    }

    private function fetchProductAttributes($_product_id)
    {
        $_attributes = [];

        $_product_attributes = $this->_product->index_related_productattributes($_product_id);

        if (!empty($_product_attributes)) {
            foreach ($_product_attributes as $_product_attribute) {
                $_product_attribute['name'] = trim($_product_attribute['name']);
                $_product_attribute['value'] = trim($_product_attribute['value']);
                $_attribute = [
                    'name' => strtolower($_product_attribute['name']),
                    'name_label' => $_product_attribute['name'],
                    'value' => strtolower(preg_replace('/[\s]/', '', $_product_attribute['value'])),
                    'value_label' => $_product_attribute['value'],
                    'configuration' => [
                        'onecrm_id' => $_product_attribute['id'],
                    ]
                ];

                if (isset($_product_attribute['hex_code']) && !empty($_product_attribute['hex_code'])) {
                    $_attribute['configuration']['color'] = trim($_product_attribute['hex_code']);
                }

                $_attributes[strtolower($_product_attribute['name'])] = $_attribute;
            }

            ksort($_attributes);
            $_attributes = array_values($_attributes);
        }

        return $_attributes;
    }

    private function transformProductCategoryRelatedProducts($_category_products = [], $category_id = null)
    {
        $category_products = [];
        if (! empty($_category_products)) {
            $tax_codes = $this->_tax_code->index();
            foreach ($_category_products as $_category_product) {
                $_product = $this->_product->show($_category_product['id']);

                $product = [
                    'name' => $_product['name'],
                    'name_label' => $_product['name'],
                    'sku' => $_product['manufacturers_part_no'],
                    'price' => floatval($_product['purchase_price']),
                    'images' => Utilities::filenamesToUrl($_product['img_url'], true),
                    'configuration' => [
                        'onecrm_id' => $_product['id'],
                        'onecrm_parent_id' => !empty($_product['parent_product_id']) ? $_product['parent_product_id'] : null,
                        'onecrm_product_class' => !empty($_product['product_class']) ? $_product['product_class'] : 'simple',
                        'onecrm_category_id' => $category_id,
                        'onecrm_tax_code_id' => $_product['tax_code_id'],
                        'onecrm_tax_code' => isset($tax_codes[$_product['tax_code_id']]) ? $tax_codes[$_product['tax_code_id']] : null,

                        'tax_percentage' => isset($tax_codes[$_product['tax_code_id']]) && in_array($tax_codes[$_product['tax_code_id']], ['ZERO RATED', 'Tax Exempt']) ? 0 : 20,
                        'weight' => $_product['weight_1'],
                        'description' => Utilities::cleanHtml($_product['description']),
                        'size_guide' => json_decode($this->iso8859_1_to_utf8(Utilities::cleanHtml($_product['size_guide'])), JSON_UNESCAPED_SLASHES),
                        'size_guide_note' => $_product['size_guide_note'],
                        'shipping' => Utilities::cleanHtml($_product['shipping']),
                        'qty_per_order' => ($_product['qty_per_order'] != 0) ? intval($_product['qty_per_order']) : null,
                        'qty_per_order_grouping' => null,
                    ],
                    'attributes' => $this->fetchProductAttributes($_product['id']),
                    'sort_order' => $_product['sort_order'],
                ];

                $category_products[$_product['id']] = $product;
            }
        }

        return $category_products;
    }

    public function index($category_id)
    {
        $_category_products = $this->_product_category->index_related_products($category_id, [
            'fields' => ['product_category_id', 'image_url'],
            'per_page' => 100
        ]);
        $category_products = $this->transformProductCategoryRelatedProducts($_category_products, $category_id);

        $hierarchical_category_products = [];

        if (!empty($category_products)) {
            foreach ($category_products as $category_product_key => $category_product) {
                if (
                    isset($category_product['configuration'])
                    && array_key_exists('onecrm_parent_id', $category_product['configuration'])
                    && $category_product['configuration']['onecrm_parent_id'] === null
                    && isset($category_product['configuration']['onecrm_id'])
                ){
                    $hierarchical_category_products[$category_product['configuration']['onecrm_id']] = $category_product;
                    unset($category_products[$category_product_key]);
                }
            }
        }

        if (!empty($category_products)) {
            foreach ($category_products as $category_product_key => $category_product) {
                if (
                    isset($category_product['configuration'])
                    && array_key_exists('onecrm_parent_id', $category_product['configuration'])
                    && isset($category_product['configuration']['onecrm_parent_id'])
                ){
                    $hierarchical_category_products[$category_product['configuration']['onecrm_parent_id']]['products'][] = $category_product;
                }
            }
        }

        $hierarchical_category_products = array_values($hierarchical_category_products);

        return $hierarchical_category_products;
    }
}
