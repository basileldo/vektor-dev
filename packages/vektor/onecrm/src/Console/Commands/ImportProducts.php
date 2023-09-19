<?php

namespace Vektor\OneCRM\Console\Commands;

use Illuminate\Console\Command;
use Vektor\Checkout\Product;
use Vektor\OneCRM\Model\WrapperProductCatalog;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onecrm:import_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports products from OneCrm';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $_product_catalog = new WrapperProductCatalog;

        $_product_catalog_response = $_product_catalog->index(config('onecrm.product_category_id'));

        if (!empty($_product_catalog_response)) {
            foreach ($_product_catalog_response as $_product) {
                $product = null;
                if (isset($_product['configuration']) && isset($_product['configuration']['onecrm_id'])) {
                    $product = Product::whereJsonContains('configuration->onecrm_id', $_product['configuration']['onecrm_id'])->first();
                }
                if ($product) {
                    $product->update($_product);
                } else {
                    $product = Product::create($_product);
                }

                if (isset($_product['products']) && !empty($_product['products'])) {
                    foreach ($_product['products'] as $_product_inner) {
                        $product_inner = null;
                        if (isset($_product_inner['configuration']) && isset($_product_inner['configuration']['onecrm_id'])) {
                            $product_inner = Product::whereJsonContains('configuration->onecrm_id', $_product_inner['configuration']['onecrm_id'])->first();
                        }
                        if ($product_inner) {
                            $product_inner->update($_product_inner);
                        } else {
                            $product_inner = Product::create($_product_inner);
                            $product_inner->parent()->associate($product);
                            $product_inner->save();
                        }
                    }
                }
            }
        }
    }
}