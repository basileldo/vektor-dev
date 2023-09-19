<?php

namespace Vektor\Checkout;
use Vektor\Checkout\Utilities as CheckoutUtilities;

class Formatter
{
    public static function unravelProductConfiguration($product)
    {
        if (isset($product['products']) && !empty($product['products'])) {
            foreach ($product['products'] as &$product_inner) {
                $product_inner = self::unravelProductConfiguration($product_inner);
                if (empty($product_inner['sku'])) { unset($product_inner['sku']); }
                if (empty($product_inner['price'])) { unset($product_inner['price']); } else {
                    if (isset($product['configuration']) && isset($product['configuration']['tax_percentage'])) {
                        $product_inner['display_price'] = CheckoutUtilities::addPercentage($product_inner['price'], $product['configuration']['tax_percentage']);
                    }
                }
                if (empty($product_inner['images'])) { unset($product_inner['images']); } else {
                    $product_inner['images'] = CheckoutUtilities::filenamesToUrl($product_inner['images']);
                }
            }
            unset($product_inner);
        }
        if (isset($product['configuration']) && !empty($product['configuration'])) {
            $product = array_replace($product, $product['configuration']);
            unset($product['configuration']);
        }
        return $product;
    }

    public static function product($_product)
    {
        $_product['images'] = CheckoutUtilities::filenamesToUrl($_product['images']);
        $_product['display_price'] = CheckoutUtilities::addPercentage($_product['price'], $_product['configuration']['tax_percentage']);
        return self::unravelProductConfiguration($_product);
    }

    public static function products($_products)
    {
        $products = [];
        if (!empty($_products)) {
            foreach($_products as $_product) {
                $products[] = self::product($_product);
            }
        }
        return $products;
    }
}
