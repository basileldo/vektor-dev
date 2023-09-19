<?php

namespace Vektor\OneCRM\Console\Commands;

use Illuminate\Console\Command;
use Vektor\Checkout\Product;

class ClearProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onecrm:clear_products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear products from OneCrm';

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
        Product::query()->delete();
    }
}