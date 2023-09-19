<?php

namespace Vektor\OneCRM\Model;

use Illuminate\Database\Eloquent\Model;
use Vektor\Api\Api;
use Vektor\OneCRM\OneCRM;
use Vektor\OneCRM\OneCRMModel;

class InvoiceLineGroup extends Model
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
        'parent_id',
        'cost',
        'cost_usd',
        'subtotal',
        'subtotal_usd',
        'total',
        'total_usd',
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
        'cost_usd',
        'subtotal_usd',
        'total_usd',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
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
     * Get the model's cost_usd.
     */
    public function getCostUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['cost']) ? $this->attributes['cost'] : null);
    }

    /**
     * Get the model's subtotal_usd.
     */
    public function getSubtotalUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['subtotal']) ? $this->attributes['subtotal'] : null);
    }

    /**
     * Get the model's total_usd.
     */
    public function getTotalUsdAttribute(string|null $value): string|null
    {
        return !empty($value) ? $value : (!empty($this->attributes['total']) ? $this->attributes['total'] : null);
    }

    public function persist()
    {
        $data = $this->toArray();
        unset($data['new_record']);

        $_response = $this->crm_model->create('invoice_line_groups', $data);
        $response = Api::transformResponse($_response);

        if ($response['success']) {
            $this->new_record = true;
            $this->id = $response['data']['record']['id'];

            return $response['data']['record'];
        }

        return null;
    }
}
