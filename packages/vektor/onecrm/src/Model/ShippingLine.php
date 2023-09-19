<?php

namespace Vektor\OneCRM\Model;

use Illuminate\Database\Eloquent\Model;
use Vektor\Api\Api;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;

class ShippingLine extends Model
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
        'shipping_id',
        'line_group_id',
        'name',
        'quantity',
        'ext_quantity',
        'cost_price',
        'cost_price_usd',
        'list_price',
        'list_price_usd',
        'unit_price',
        'unit_price_usd',
        'std_unit_price',
        'std_unit_price_usd',
        'ext_price',
        'ext_price_usd',
        'tax_class_id',
        'related_id',
        'related_type',
        'position',
        'mfr_part_no',
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
        'ext_quantity',
        'cost_price_usd',
        'list_price_usd',
        'unit_price_usd',
        'std_unit_price_usd',
        'ext_price_usd',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'related_id' => null,
        'related_type' => null,
        'new_record' => false,
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
     * Get the model's ext_quantity.
     */
    public function getExtQuantityAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['quantity']) ? $this->attributes['quantity'] : null);
    }

    /**
     * Get the model's cost_price_usd.
     */
    public function getCostPriceUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['cost_price']) ? $this->attributes['cost_price'] : null);
    }

    /**
     * Get the model's list_price_usd.
     */
    public function getListPriceUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['list_price']) ? $this->attributes['list_price'] : null);
    }

    /**
     * Get the model's unit_price_usd.
     */
    public function getUnitPriceUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['unit_price']) ? $this->attributes['unit_price'] : null);
    }

    /**
     * Get the model's std_unit_price_usd.
     */
    public function getStdUnitPriceUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['std_unit_price']) ? $this->attributes['std_unit_price'] : null);
    }

    /**
     * Get the model's ext_price_usd.
     */
    public function getExtPriceUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['ext_price']) ? $this->attributes['ext_price'] : null);
    }

    public function persist()
    {
        $data = $this->toArray();
        unset($data['new_record']);

        $_response = $this->crm_model->create('shipping_lines', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->new_record = true;
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }
}
