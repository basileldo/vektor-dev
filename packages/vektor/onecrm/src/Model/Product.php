<?php

namespace Vektor\OneCRM\Model;

use Illuminate\Database\Eloquent\Model;
use Vektor\Api\Api;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;

class Product extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'name',
        'url',
        'description',
        'description_long',
        'currency_id',
        'exchange_rate',
        'supplier_id',
        'manufacturer_id',
        'model_id',
        'product_category_id',
        'product_type_id',
        'weight_1',
        'weight_2',
        'vendor_part_no',
        'manufacturers_part_no',
        'cost',
        'cost_usdollar',
        'list_price',
        'list_usdollar',
        'purchase_price',
        'purchase_usdollar',
        'support_cost',
        'support_cost_usdollar',
        'support_list_price',
        'support_list_usdollar',
        'support_selling_price',
        'support_selling_usdollar',
        'pricing_formula',
        'support_price_formula',
        'is_available',
        'date_available',
        'ppf_perc',
        'support_ppf_perc',
        'track_inventory',
        'all_stock',
        'tax_code_id',
        'date_entered',
        'date_modified',
        'modified_user_id',
        'created_by',
        'eshop',
        'image_url',
        'thumbnail_url',
        'deleted',
        'purchase_name',
        'image_filename',
        'image_thumb',
        'description_portal',
        'woo_id',
        'woo_locked',
        'new_record',
    ];

    protected $casts = [
        'new_record' => 'boolean',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'date_entered',
        'date_modified',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'new_record' => false,
        'currency_id' => '-99',
        'exchange_rate' => '1',
        'cost' => '0',
        'cost_usdollar' => '0',
        'list_price' => '0',
        'list_usdollar' => '0',
        'purchase_price' => '0',
        'purchase_usdollar' => '0',
        'support_cost' => '0',
        'support_cost_usdollar' => '0',
        'support_list_price' => '0',
        'support_list_usdollar' => '0',
        'support_selling_price' => '0',
        'support_selling_usdollar' => '0',
        'pricing_formula' => 'Fixed Price',
        'support_price_formula' => 'Fixed Price',
        'is_available' => 'yes',
        'ppf_perc' => '0',
        'support_ppf_perc' => '0',
        'track_inventory' => 'untracked',
        'all_stock' => '0',
        'modified_user_id' => '1',
        'created_by' => '1',
        'eshop' => '1',
        'deleted' => '0',
        'woo_locked' => '0',
    ];

    /**
     * The attributes that are not to be used to update.
     *
     * @var array
     */
    protected $excluded_update_attributes = [
    ];

    public $crm;

    public $crm_model;

    public function __construct()
    {
        $this->crm = new OneCRM;
        $this->crm_model = new OneCRMModel;

        return $this;
    }

    /**
     * Get the model's date_entered.
     */
    public function getDateEnteredAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : date('Y-m-d');
    }

    /**
     * Get the model's date_modified.
     */
    public function getDateModifiedAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : date('Y-m-d');
    }

    public function index($data = [])
    {
        $_response = $this->crm_model->index('products', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            return $response['data']['records'];
        }

        return [];
    }

    public function show($id, $data = [])
    {
        $_response = $this->crm_model->show('products', $id, $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }

    public function index_related_productattributes($id, $data = [
        'fields' => [
            'hex_code',
            'img_url',
            'sizes',
            'value',
        ],
    ])
    {
        $_response = $this->crm_model->index_related('products', $id, 'productattributes', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            return $response['data']['records'];
        }

        return [];
    }
}
