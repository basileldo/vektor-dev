<?php

namespace Vektor\Checkout;

use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'configuration',
        'is_active',
    ];

    protected $casts = [
        'price' => 'float',
        'configuration' => 'array',
        'is_active' => 'boolean',
    ];

    public function setConfigurationAttribute($value)
    {
        $array = [];

        if (!empty($value) && is_array($value)) {
            foreach ($value as $array_key => $array_item) {
                if (!is_null($array_item)) {
                    $array[$array_key] = $array_item;
                }
            }
        }

        if (!empty($array)) {
            $this->attributes['configuration'] = json_encode($array);
        } else {
            $this->attributes['configuration'] = null;
        }
    }
}
